<?php

namespace App\Services;

use App\Models\Servico;
use App\Models\HorarioDisponivel;
use App\Models\Agendamento;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DisponibilidadeService
{
    /**
     * Retorna dias com slots livres (apenas janelas de trabalho).
     * Bloqueios (almoço etc.) vêm de HorarioDisponivel onde tipo != 'trabalho'.
     *
     * @return array<int,array{date:string,formatted:string,dayName:string,weekday:int,isToday:bool,slots:array<int,string>,slotsAvailable:int}>
     */
    public function calendarioDisponivel(int $servicoId, ?int $janelaDias = null): array
    {
        $servico    = Servico::findOrFail($servicoId);
        $tz         = config('app.timezone', 'America/Sao_Paulo');
        $janelaDias = $janelaDias ?? (int) config('agendamento.janela_dias', 7);

        $hoje      = Carbon::now($tz)->startOfDay();
        $fimJanela = $hoje->copy()->addDays($janelaDias - 1);

        // Carrega janelas ativas e agrupa por dia da semana
        $configs = HorarioDisponivel::query()
            ->where('ativo', true)
            ->get()
            ->groupBy('dia_semana'); // 0..6

        // Agendamentos do serviço na janela
        $agendamentos = Agendamento::query()
            ->where('servico_id', $servico->id)
            ->whereDate('data_agendamento', '>=', $hoje->toDateString())
            ->whereDate('data_agendamento', '<=', $fimJanela->toDateString())
            ->get(['data_agendamento', 'hora_inicio', 'hora_fim'])
            ->groupBy(fn ($a) => (string) $a->data_agendamento);

        $dias = [];

        for ($i = 0; $i < $janelaDias; $i++) {
            $data    = $hoje->copy()->addDays($i);
            $dateYmd = $data->toDateString();
            $weekday = $data->dayOfWeek;

            $todasDoDia = $configs->get($weekday, collect());
            if ($todasDoDia->isEmpty()) {
                continue;
            }

            // Separa janelas de TRABALHO e BLOQUEIO (ex.: almoco)
            $janelasTrabalho = $todasDoDia->where('tipo', 'trabalho')->values();
            $janelasBloqueio = $todasDoDia->where('tipo', '!=', 'trabalho')->values();

            if ($janelasTrabalho->isEmpty()) {
                continue;
            }

            // Intervalos ocupados: agendamentos + janelas de bloqueio
            $intervalosOcupados = array_merge(
                $this->intervalosOcupadosDoDia($servico, $data, $agendamentos->get($dateYmd, collect())),
                $this->intervalosBloqueioDeJanelas($data, $janelasBloqueio)
            );

            // Slots livres apenas nas janelas de trabalho
            $slots = $this->construirSlotsLivresDoDia(
                $servico,
                $data,
                $janelasTrabalho,
                $intervalosOcupados
            );

            if (!empty($slots)) {
                $dias[] = [
                    'date'           => $dateYmd,
                    'formatted'      => $data->translatedFormat('d/m/Y'),
                    'dayName'        => $data->locale('pt_BR')->translatedFormat('l'),
                    'weekday'        => $weekday,
                    'isToday'        => $data->isToday(),
                    'slots'          => $slots,
                    'slotsAvailable' => count($slots),
                ];
            }
        }

        return $dias;
    }

    /** Horários livres para uma data (usa as mesmas regras). */
    public function horariosDisponiveis(int $servicoId, string $dataYmd): array
    {
        $servico = Servico::findOrFail($servicoId);
        $tz      = config('app.timezone', 'America/Sao_Paulo');
        $data    = Carbon::parse($dataYmd, $tz)->startOfDay();

        $todasDoDia = HorarioDisponivel::query()
            ->where('ativo', true)
            ->where('dia_semana', $data->dayOfWeek)
            ->get();

        $janelasTrabalho = $todasDoDia->where('tipo', 'trabalho')->values();
        $janelasBloqueio = $todasDoDia->where('tipo', '!=', 'trabalho')->values();

        if ($janelasTrabalho->isEmpty()) {
            return [];
        }

        $agendamentos = Agendamento::query()
            ->where('servico_id', $servico->id)
            ->whereDate('data_agendamento', $data->toDateString())
            ->get(['hora_inicio', 'hora_fim']);

        $intervalos = array_merge(
            $this->intervalosOcupadosDoDia($servico, $data, $agendamentos),
            $this->intervalosBloqueioDeJanelas($data, $janelasBloqueio)
        );

        return $this->construirSlotsLivresDoDia(
            $servico,
            $data,
            $janelasTrabalho,
            $intervalos
        );
    }

    /** Checa disponibilidade de um horário específico. */
    public function temDisponibilidade(int $servicoId, string $dataYmd, string $horaInicio): bool
    {
        $livres = $this->horariosDisponiveis($servicoId, $dataYmd);
        return in_array($horaInicio, $livres, true);
    }

    /* ====================== Helpers ====================== */

    /**
     * Constrói slots livres dentro das janelas de TRABALHO, aplicando:
     * - passo = duração do serviço
     * - corte de passado no dia de hoje (arredondando para o grid)
     * - remoção de colisões com agendamentos e bloqueios
     *
     * @param  Collection<int,\App\Models\HorarioDisponivel> $janelasTrabalho
     * @param  array<int,array{0:Carbon,1:Carbon}> $intervalosOcupados
     * @return array<int,string> ex.: ['08:30','09:00',...]
     */
    protected function construirSlotsLivresDoDia(
        Servico $servico,
        Carbon $data,
        Collection $janelasTrabalho,
        array $intervalosOcupados
    ): array {
        $tz      = $data->getTimezone();
        $duracao = (int) $servico->duracao_minutos;

        $agora         = Carbon::now($tz)->seconds(0);
        $minInicioHoje = null;

        if ($data->isSameDay($agora)) {
            $minInicioHoje = $agora->copy()->seconds(0);
            // Arredonda para o próximo múltiplo da duração
            $minutosDesdeMeiaNoite = $minInicioHoje->diffInMinutes($data->copy()->startOfDay());
            $resto = $minutosDesdeMeiaNoite % $duracao;
            if ($resto !== 0) {
                $minInicioHoje->addMinutes($duracao - $resto);
            }
        }

        $slots = [];

        foreach ($janelasTrabalho as $cfg) {
            $janelaIni = Carbon::parse($data->toDateString().' '.$cfg->getRawOriginal('hora_inicio'), $tz)->seconds(0);
            $janelaFim = Carbon::parse($data->toDateString().' '.$cfg->getRawOriginal('hora_fim'),    $tz)->seconds(0);

            $cursor = $janelaIni->copy();
            if ($minInicioHoje && $cursor < $minInicioHoje) {
                $cursor = $minInicioHoje->copy();
            }

            while ($cursor->copy()->addMinutes($duracao) <= $janelaFim) {
                $slotIni = $cursor->copy();
                $slotFim = $cursor->copy()->addMinutes($duracao);

                $colide = false;
                foreach ($intervalosOcupados as [$aIni, $aFim]) {
                    if ($slotIni < $aFim && $slotFim > $aIni) { // overlap
                        $colide = true;
                        break;
                    }
                }

                if (!$colide) {
                    $slots[] = $slotIni->format('H:i');
                }

                $cursor->addMinutes($duracao);
            }
        }

        $slots = array_values(array_unique($slots));
        sort($slots);

        return $slots;
    }

    /**
     * Converte janelas de bloqueio (tipo != 'trabalho') em intervalos [ini, fim).
     *
     * @param  Collection<int,\App\Models\HorarioDisponivel> $janelasBloqueio
     * @return array<int,array{0:Carbon,1:Carbon}>
     */
    protected function intervalosBloqueioDeJanelas(Carbon $data, Collection $janelasBloqueio): array
    {
        if ($janelasBloqueio->isEmpty()) return [];

        $tz = $data->getTimezone();
        $bloqueios = [];

        foreach ($janelasBloqueio as $cfg) {
            $ini = Carbon::parse($data->toDateString().' '.$cfg->getRawOriginal('hora_inicio'), $tz)->seconds(0);
            $fim = Carbon::parse($data->toDateString().' '.$cfg->getRawOriginal('hora_fim'),    $tz)->seconds(0);
            if ($ini < $fim) {
                $bloqueios[] = [$ini, $fim];
            }
        }

        return $bloqueios;
    }

    /**
     * Normaliza agendamentos de um dia em intervalos [ini,fim).
     * Se hora_fim vier nula, calcula como hora_inicio + duração do serviço.
     *
     * @param  Collection<int,\App\Models\Agendamento> $agendamentosDoDia
     * @return array<int,array{0:Carbon,1:Carbon}>
     */
    protected function intervalosOcupadosDoDia(Servico $servico, Carbon $data, Collection $agendamentosDoDia): array
    {
        $tz      = $data->getTimezone();
        $duracao = (int) $servico->duracao_minutos;

        return $agendamentosDoDia->map(function ($a) use ($data, $tz, $duracao) {
            $ini = Carbon::parse($data->toDateString().' '.$a->hora_inicio, $tz)->seconds(0);
            $fim = $a->hora_fim
                ? Carbon::parse($data->toDateString().' '.$a->hora_fim, $tz)->seconds(0)
                : $ini->copy()->addMinutes($duracao);

            return [$ini, $fim];
        })->all();
    }
}
