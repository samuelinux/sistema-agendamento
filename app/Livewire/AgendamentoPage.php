<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Servico;
use App\Models\Agendamento;
use App\Services\DisponibilidadeService;

#[Layout('layouts.app')]
#[Title('Agendamento - Sistema de Agendamento')]
class AgendamentoPage extends Component
{
    /** 0=início, 1=serviço, 2=data, 3=horário */
    public int $step = 0;

    /** Modais */
    public bool $showServiceModal = false;
    public bool $showDateModal = false;
    public bool $showTimeModal = false;

    /** Seleções */
    public ?int $selectedServiceId = null;
    public ?string $selectedDate = null;  // 'YYYY-MM-DD'
    public ?string $selectedTime = null;  // 'HH:mm'

    /** Dados */
    /** @var array<int,array<string,mixed>> */
    public array $servicos = [];

    /**
     * Opção A: dias com slots (para UI)
     * @var array<int,array{date:string,formatted:string,dayName:string,weekday:int,isToday:bool,slots:array<int,string>,slotsAvailable:int}>
     */
    public array $diasComSlots = [];

    /** @var array<int,string> */
    public array $horariosDisponiveis = [];

    public function render()
    {
        return view('livewire.agendamento-page');
    }

    public function mount(): void
    {
        $this->carregarServicos();
    }

    protected function disponibilidade(): DisponibilidadeService
    {
        return app(DisponibilidadeService::class);
    }

    /* Modais */
    public function openServiceModal(): void { $this->showServiceModal = true; }
    public function closeServiceModal(): void { $this->showServiceModal = false; }
    public function openDateModal(): void { $this->showDateModal = true; }
    public function closeDateModal(): void { $this->showDateModal = false; }
    public function openTimeModal(): void { $this->showTimeModal = true; }
    public function closeTimeModal(): void { $this->showTimeModal = false; }

    /* Fluxo */
    public function carregarServicos(): void
    {
        $this->servicos = Servico::query()
            // ->where('ativo', true)
            ->orderBy('nome')
            ->get(['id','nome','descricao','preco','duracao_minutos'])
            ->map(fn ($s) => $s->toArray())
            ->all();
    }

    public function selectService(int $servicoId): void
    {
        $this->selectedServiceId = $servicoId;
        $this->step = 1;
        $this->closeServiceModal();

        $janelaDias = (int) config('agendamento.janela_dias', 7);
        $this->diasComSlots = $this->disponibilidade()->calendarioDisponivel($servicoId, $janelaDias);

        // reset seleções seguintes
        $this->selectedDate = null;
        $this->selectedTime = null;
        $this->horariosDisponiveis = [];

        $this->openDateModal();
    }

    public function selectDate(string $dateYmd): void
    {
        $this->selectedDate = $dateYmd;
        $this->step = 2;
        $this->closeDateModal();

        $dia = collect($this->diasComSlots)->firstWhere('date', $dateYmd);
        $this->horariosDisponiveis = $dia['slots'] ?? [];
        $this->selectedTime = null;

        $this->openTimeModal();
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
        $this->step = 3;
        $this->closeTimeModal();
    }

    public function confirmarAgendamento(): void
    {
        if (!$this->selectedServiceId || !$this->selectedDate || !$this->selectedTime) {
            session()->flash('error', 'Por favor, selecione serviço, data e horário.');
            return;
        }

        // Verificação prévia
        if (!$this->disponibilidade()->temDisponibilidade($this->selectedServiceId, $this->selectedDate, $this->selectedTime)) {
            session()->flash('error', 'O horário escolhido não está mais disponível.');
            $this->horariosDisponiveis = $this->disponibilidade()->horariosDisponiveis($this->selectedServiceId, $this->selectedDate);
            $this->openTimeModal();
            return;
        }

        try {
            DB::transaction(function () {
                // Revalida dentro da transação (anti-corrida)
                if (!$this->disponibilidade()->temDisponibilidade($this->selectedServiceId, $this->selectedDate, $this->selectedTime)) {
                    throw new \RuntimeException('sem_disponibilidade');
                }

                Agendamento::create([
                    'servico_id'       => $this->selectedServiceId,
                    'data_agendamento' => $this->selectedDate,
                    'hora_inicio'      => $this->selectedTime,
                ]);
            });

            session()->flash('success', 'Agendamento confirmado com sucesso!');

            // Reset padrão
            $this->step = 0;
            $this->selectedServiceId = null;
            $this->selectedDate = null;
            $this->selectedTime = null;
            $this->horariosDisponiveis = [];
            $this->diasComSlots = [];

            $this->carregarServicos();
        } catch (\Throwable $e) {
            if ($e instanceof \RuntimeException && $e->getMessage() === 'sem_disponibilidade') {
                session()->flash('error', 'O horário acabou de ser ocupado. Escolha outro.');
            } else {
                Log::error('Erro ao confirmar agendamento', ['exception' => $e]);
                session()->flash('error', 'Não foi possível confirmar o agendamento.');
            }

            if ($this->selectedServiceId && $this->selectedDate) {
                $this->horariosDisponiveis = $this->disponibilidade()->horariosDisponiveis($this->selectedServiceId, $this->selectedDate);
                $this->openTimeModal();
            }
        }
    }
}
