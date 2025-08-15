@extends('layouts.app')

@section('title', 'Agendamento - Sistema de Agendamento')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8" x-data="agendamentoApp()">
    <!-- Header da página -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Agendar Horário</h1>
        <p class="text-gray-600">Selecione o serviço, dia e horário desejado</p>
    </div>

    <!-- Progresso do agendamento -->
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                     :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                    1
                </div>
                <span class="ml-2 text-sm font-medium" :class="step >= 1 ? 'text-blue-600' : 'text-gray-500'">
                    Serviço
                </span>
            </div>

            <div class="w-16 h-0.5" :class="step >= 2 ? 'bg-blue-600' : 'bg-gray-200'"></div>

            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                     :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                    2
                </div>
                <span class="ml-2 text-sm font-medium" :class="step >= 2 ? 'text-blue-600' : 'text-gray-500'">
                    Data
                </span>
            </div>

            <div class="w-16 h-0.5" :class="step >= 3 ? 'bg-blue-600' : 'bg-gray-200'"></div>

            <div class="flex items-center">
                <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium"
                     :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600'">
                    3
                </div>
                <span class="ml-2 text-sm font-medium" :class="step >= 3 ? 'text-blue-600' : 'text-gray-500'">
                    Horário
                </span>
            </div>
        </div>
    </div>

    <!-- Resumo da seleção -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6" x-show="selectedService || selectedDate || selectedTime">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Agendamento</h3>

        <div class="space-y-2">
            <div x-show="selectedService">
                <span class="text-sm text-gray-600">Serviço:</span>
                <span class="ml-2 font-medium" x-text="selectedService?.nome"></span>
                <span class="ml-2 text-green-600 font-medium" x-text="selectedService?.preco ? 'R$ ' + selectedService.preco : ''"></span>
            </div>

            <div x-show="selectedDate">
                <span class="text-sm text-gray-600">Data:</span>
                <span class="ml-2 font-medium" x-text="formatDate(selectedDate)"></span>
            </div>

            <div x-show="selectedTime">
                <span class="text-sm text-gray-600">Horário:</span>
                <span class="ml-2 font-medium" x-text="selectedTime"></span>
            </div>
        </div>

        <button
            x-show="selectedService && selectedDate && selectedTime"
            @click="confirmarAgendamento()"
            class="mt-4 w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200"
        >
            Confirmar Agendamento
        </button>
    </div>

    <!-- Botão para iniciar agendamento -->
    <div class="text-center">
        <button
            @click="openServiceModal()"
            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
        >
            Iniciar Agendamento
        </button>
    </div>

    <!-- Modal de Seleção de Serviço -->
    <div x-show="showServiceModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeServiceModal()">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 max-h-96 overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Escolha o Serviço</h3>
                    <button @click="closeServiceModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <template x-for="servico in servicos" :key="servico.id">
                        <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition duration-200"
                             @click="selectService(servico)">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium text-gray-900" x-text="servico.nome"></h4>
                                    <p class="text-sm text-gray-600 mt-1" x-text="servico.descricao"></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Duração: <span x-text="servico.duracao_minutos"></span> minutos
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-semibold text-green-600"
                                          x-text="servico.preco ? 'R$ ' + servico.preco : 'Gratuito'"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Seleção de Data -->
    <div x-show="showDateModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeDateModal()">
        <div class="bg-white rounded-lg max-w-md w-full mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Escolha a Data</h3>
                    <button @click="closeDateModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <template x-for="data in datasDisponiveis" :key="data.date">
                        <button class="text-left border rounded-lg p-4 hover:bg-gray-50 transition duration-200"
                                @click="selectDate(data.date)">
                            <div class="font-medium text-gray-900" x-text="data.formatted"></div>
                            <div class="text-sm text-gray-600" x-text="data.dayName"></div>
                            <div class="text-xs text-green-600 mt-1" x-text="data.slotsAvailable + ' horários disponíveis'"></div>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Seleção de Horário -->
    <div x-show="showTimeModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="closeTimeModal()">
        <div class="bg-white rounded-lg max-w-md w-full mx-4 max-h-96 overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Escolha o Horário</h3>
                    <button @click="closeTimeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <template x-for="horario in horariosDisponiveis" :key="horario">
                        <button class="border rounded-lg p-3 text-center hover:bg-blue-50 hover:border-blue-300 transition duration-200"
                                @click="selectTime(horario)">
                            <span class="font-medium text-gray-900" x-text="horario"></span>
                        </button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function agendamentoApp() {
    return {
        step: 0,
        showServiceModal: false,
        showDateModal: false,
        showTimeModal: false,

        selectedService: null,
        selectedDate: null,
        selectedTime: null,

        servicos: [],
        datasDisponiveis: [],
        horariosDisponiveis: [],

        init() {
            this.loadServicos();
        },

        async loadServicos() {
            try {
                const response = await fetch('/api/servicos');
                this.servicos = await response.json();
            } catch (error) {
                console.error('Erro ao carregar serviços:', error);
                // Dados de exemplo para desenvolvimento
                this.servicos = [
                    { id: 1, nome: 'Corte de Cabelo', descricao: 'Corte masculino tradicional', preco: '25.00', duracao_minutos: 30 },
                    { id: 2, nome: 'Barba', descricao: 'Aparar e modelar barba', preco: '15.00', duracao_minutos: 20 },
                    { id: 3, nome: 'Corte + Barba', descricao: 'Pacote completo', preco: '35.00', duracao_minutos: 45 }
                ];
            }
        },

        openServiceModal() {
            this.showServiceModal = true;
        },

        closeServiceModal() {
            this.showServiceModal = false;
        },

        selectService(servico) {
            this.selectedService = servico;
            this.step = 1;
            this.closeServiceModal();
            this.loadDatasDisponiveis();
            this.showDateModal = true;
        },

        loadDatasDisponiveis() {
            // Gerar próximos 7 dias como exemplo
            const hoje = new Date();
            this.datasDisponiveis = [];

            for (let i = 0; i < 7; i++) {
                const data = new Date(hoje);
                data.setDate(hoje.getDate() + i);

                this.datasDisponiveis.push({
                    date: data.toISOString().split('T')[0],
                    formatted: data.toLocaleDateString('pt-BR'),
                    dayName: data.toLocaleDateString('pt-BR', { weekday: 'long' }),
                    slotsAvailable: Math.floor(Math.random() * 8) + 3 // 3-10 horários
                });
            }
        },

        closeDateModal() {
            this.showDateModal = false;
        },

        selectDate(date) {
            this.selectedDate = date;
            this.step = 2;
            this.closeDateModal();
            this.loadHorariosDisponiveis();
            this.showTimeModal = true;
        },

        async loadHorariosDisponiveis() {
    try {
        const response = await fetch(`/listar-horarios-disponiveis?servico_id=${this.selectedService.id}&data=${this.selectedDate}`, {
    credentials: 'same-origin'
});

        this.horariosDisponiveis = await response.json();
    } catch (error) {
        console.error('Erro ao carregar horários disponíveis:', error);
        this.horariosDisponiveis = [];
    }
}

,

        closeTimeModal() {
            this.showTimeModal = false;
        },

        selectTime(time) {
            this.selectedTime = time;
            this.step = 3;
            this.closeTimeModal();
        },

        formatDate(dateString) {
        const [year, month, day] = dateString.split('-').map(Number);
        const date = new Date(year, month - 1, day); // mês começa do 0 no JS
        return date.toLocaleDateString('pt-BR', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}
,

        async confirmarAgendamento() {
    if (!this.selectedService || !this.selectedDate || !this.selectedTime) {
        alert('Por favor, selecione todos os campos');
        return;
    }

    try {
        const response = await fetch('/agendamentos', { // ← trocado de /api/agendamentos para /agendamentos
            method: 'POST',
            credentials: 'same-origin', // ← envia cookies/sessão
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                servico_id: this.selectedService.id,
                data_agendamento: this.selectedDate,
                hora_inicio: this.selectedTime
            })
        });

        if (response.ok) {
            alert('Agendamento confirmado com sucesso!');
            location.reload();
        } else {
            const error = await response.json();
            alert('Erro ao confirmar agendamento 01: ' + (error.message || 'Erro desconhecido'));
        }
    } catch (error) {
        console.error('Erro:', error);
        alert('Erro ao confirmar agendamento 02');
    }
}

    }
}
</script>
@endpush
@endsection

