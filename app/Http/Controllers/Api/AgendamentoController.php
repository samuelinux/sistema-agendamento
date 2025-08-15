<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Servico;
use App\Models\Agendamento;
use App\Models\HorarioDisponivel;
use App\Models\Configuracao;
use Carbon\Carbon;

class AgendamentoController extends Controller
{
    public function servicos()
    {
        $servicos = Servico::where('ativo', true)->get();
        return response()->json($servicos);
    }

    public function datasDisponiveis(Request $request)
    {
        $servicoId = $request->get('servico_id');
        $servico = Servico::find($servicoId);

        if (!$servico) {
            return response()->json(['error' => 'Serviço não encontrado'], 404);
        }

        $datasDisponiveis = [];
        $hoje = Carbon::now();

        // Verificar próximos 30 dias
        for ($i = 0; $i < 30; $i++) {
            $data = $hoje->copy()->addDays($i);
            $diaSemana = $data->dayOfWeek; // 0=domingo, 1=segunda, etc.

            // Verificar se há horários disponíveis para este dia da semana
            $horariosConfig = HorarioDisponivel::where('dia_semana', $diaSemana)
                                               ->where('ativo', true)
                                               ->get();

            if ($horariosConfig->count() > 0) {
                $horariosOcupados = Agendamento::where('data_agendamento', $data->format('Y-m-d'))
                                               ->pluck('hora_inicio')
                                               ->toArray();

                $slotsDisponiveis = $this->calcularSlotsDisponiveis($horariosConfig, $horariosOcupados, $servico->duracao_minutos, $data);

                if (count($slotsDisponiveis) > 0) {
                    $datasDisponiveis[] = [
                        'date' => $data->format('Y-m-d'),
                        'formatted' => $data->format('d/m/Y'),
                        'dayName' => $data->translatedFormat('l'),
                        'slotsAvailable' => count($slotsDisponiveis)
                    ];
                }
            }
        }

        return response()->json($datasDisponiveis);
    }

    public function horariosDisponiveis(Request $request)
{
    $servicoId = $request->get('servico_id');
    $data = $request->get('data');

    $servico = Servico::find($servicoId);
    if (!$servico) {
        return response()->json(['error' => 'Serviço não encontrado'], 404);
    }

    $dataCarbon = Carbon::parse($data);
    $diaSemana = $dataCarbon->dayOfWeek;

    // Buscar horários de trabalho configurados para o dia
    $horariosConfig = HorarioDisponivel::where('dia_semana', $diaSemana)
        ->where('ativo', true)
        ->where('tipo', 'trabalho')
        ->get();

    // Buscar horários já agendados nessa data
    $horariosOcupados = Agendamento::where('data_agendamento', $data)
        ->pluck('hora_inicio')
        ->toArray();

    // Calcular slots disponíveis
    $horariosDisponiveis = $this->calcularSlotsDisponiveis(
        $horariosConfig,
        $horariosOcupados,
        $servico->duracao_minutos,
        $dataCarbon
    );

    return response()->json(array_values($horariosDisponiveis));
}


    public function store(Request $request)
    {
        $request->validate([
            'servico_id' => 'required|exists:servicos,id',
            'data_agendamento' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required|date_format:H:i'
        ]);

        $servico = Servico::find($request->servico_id);
        $dataAgendamento = Carbon::parse($request->data_agendamento);
        $horaInicio = Carbon::parse($request->hora_inicio);
        $horaFim = $horaInicio->copy()->addMinutes($servico->duracao_minutos);

        // Verificar se o horário ainda está disponível
        $conflito = Agendamento::where('data_agendamento', $request->data_agendamento)
                               ->where(function($query) use ($request, $horaFim) {
                                   $query->where('hora_inicio', $request->hora_inicio)
                                         ->orWhere(function($q) use ($request, $horaFim) {
                                             $q->where('hora_inicio', '<', $horaFim->format('H:i'))
                                               ->where('hora_fim', '>', $request->hora_inicio);
                                         });
                               })
                               ->exists();

        if ($conflito) {
            return response()->json(['error' => 'Horário não disponível'], 422);
        }

        $agendamento = Agendamento::create([
            'user_id' => auth()->id(),
            'servico_id' => $request->servico_id,
            'data_agendamento' => $request->data_agendamento,
            'hora_inicio' => $request->hora_inicio,
            'hora_fim' => $horaFim->format('H:i'),
            'status' => 'agendado'
        ]);

        return response()->json($agendamento->load(['servico', 'user']), 201);
    }

private function calcularSlotsDisponiveis($horariosConfig, $horariosOcupados, $duracaoMinutos, $data)
{
    $slots = [];
    $agora = Carbon::now();

    foreach ($horariosConfig as $horario) {
        $inicio = Carbon::parse($horario->hora_inicio);
        $fim = Carbon::parse($horario->hora_fim);

        while ($inicio->copy()->addMinutes($duracaoMinutos)->lte($fim)) {
            $horaSlot = $inicio->format('H:i');

            // Se já está ocupado, pula
            if (in_array($horaSlot, $horariosOcupados)) {
                $inicio->addMinutes($duracaoMinutos);
                continue;
            }

            // Se for hoje, pula horários passados
            if ($data->isToday()) {
                $inicioSlot = $data->copy()->setTimeFromTimeString($horaSlot);
                if ($inicioSlot->lte($agora)) {
                    $inicio->addMinutes($duracaoMinutos);
                    continue;
                }
            }

            $slots[] = $horaSlot;
            $inicio->addMinutes($duracaoMinutos);
        }
    }

    return $slots;
}




}
