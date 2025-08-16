<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Agendar Horário</h1>
        <p class="text-gray-600">Selecione o serviço, dia e horário desejado</p>
    </div>

    {{-- Feedbacks --}}
    @if (session('success'))
        <div class="mb-6 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    {{-- Progresso --}}
    <div class="mb-8">
        <div class="flex items-center justify-center space-x-4">
            <div class="flex items-center">
                <div @class([
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                        'bg-blue-600 text-white' => $step >= 1, 'bg-gray-200 text-gray-600' => $step < 1
                    ])>1</div>
                <span @class(['ml-2 text-sm font-medium','text-blue-600' => $step >= 1,'text-gray-500' => $step < 1])>
                    Serviço
                </span>
            </div>

            <div @class(['w-16 h-0.5', 'bg-blue-600' => $step >= 2, 'bg-gray-200' => $step < 2])></div>

            <div class="flex items-center">
                <div @class([
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                        'bg-blue-600 text-white' => $step >= 2, 'bg-gray-200 text-gray-600' => $step < 2
                    ])>2</div>
                <span @class(['ml-2 text-sm font-medium','text-blue-600' => $step >= 2,'text-gray-500' => $step < 2])>
                    Data
                </span>
            </div>

            <div @class(['w-16 h-0.5', 'bg-blue-600' => $step >= 3, 'bg-gray-200' => $step < 3])></div>

            <div class="flex items-center">
                <div @class([
                        'w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium',
                        'bg-blue-600 text-white' => $step >= 3, 'bg-gray-200 text-gray-600' => $step < 3
                    ])>3</div>
                <span @class(['ml-2 text-sm font-medium','text-blue-600' => $step >= 3,'text-gray-500' => $step < 3])>
                    Horário
                </span>
            </div>
        </div>
    </div>

    {{-- Resumo --}}
    @if ($selectedServiceId || $selectedDate || $selectedTime)
        <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Resumo do Agendamento</h3>
            <div class="space-y-2">
                @if ($selectedServiceId)
                    @php $servSel = collect($servicos)->firstWhere('id', $selectedServiceId); @endphp
                    <div>
                        <span class="text-sm text-gray-600">Serviço:</span>
                        <span class="ml-2 font-medium">{{ $servSel['nome'] ?? '' }}</span>
                        @if (!empty($servSel['preco']))
                            <span class="ml-2 text-green-600 font-medium">R$ {{ $servSel['preco'] }}</span>
                        @endif
                    </div>
                @endif

                @if ($selectedDate)
                    @php
                        $dia = collect($diasComSlots)->firstWhere('date', $selectedDate);
                        $formatted = $dia['formatted'] ?? \Carbon\Carbon::parse($selectedDate)->translatedFormat('d/m/Y');
                        $dayName = $dia['dayName'] ?? \Carbon\Carbon::parse($selectedDate)->locale('pt_BR')->translatedFormat('l');
                    @endphp
                    <div>
                        <span class="text-sm text-gray-600">Data:</span>
                        <span class="ml-2 font-medium">{{ $dayName }}, {{ $formatted }}</span>
                    </div>
                @endif

                @if ($selectedTime)
                    <div>
                        <span class="text-sm text-gray-600">Horário:</span>
                        <span class="ml-2 font-medium">{{ $selectedTime }}</span>
                    </div>
                @endif
            </div>

            <button
                wire:click="confirmarAgendamento"
                @disabled(!($selectedServiceId && $selectedDate && $selectedTime))
                class="mt-4 w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Confirmar Agendamento
            </button>
        </div>
    @endif

    {{-- Botão inicial --}}
    <div class="text-center">
        <button
            wire:click="openServiceModal"
            class="bg-blue-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200"
        >
            Iniciar Agendamento
        </button>
    </div>

    {{-- Modal: Serviço --}}
    @if ($showServiceModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="closeServiceModal">
            <div class="bg-white rounded-lg max-w-md w-full mx-4 max-h-96 overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Escolha o Serviço</h3>
                        <button wire:click="closeServiceModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-3">
                        @forelse ($servicos as $servico)
                            <div class="border rounded-lg p-4 cursor-pointer hover:bg-gray-50 transition duration-200"
                                 wire:click="selectService({{ $servico['id'] }})">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-medium text-gray-900">{{ $servico['nome'] }}</h4>
                                        @if(!empty($servico['descricao']))
                                            <p class="text-sm text-gray-600 mt-1">{{ $servico['descricao'] }}</p>
                                        @endif
                                        @if(!empty($servico['duracao_minutos']))
                                            <p class="text-xs text-gray-500 mt-1">Duração: {{ $servico['duracao_minutos'] }} minutos</p>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <span class="text-lg font-semibold text-green-600">
                                            {{ !empty($servico['preco']) ? 'R$ '.$servico['preco'] : 'Gratuito' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-sm text-gray-500">Nenhum serviço disponível.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Data (usa $diasComSlots – Opção A) --}}
    @if ($showDateModal)
        <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" wire:click.self="closeDateModal">
            <div class="bg-white rounded-lg max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Escolha a Data</h3>
                        <button wire:click="closeDateModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-3">
                        @forelse ($diasComSlots as $dia)
                            <button class="text-left border rounded-lg p-4 hover:bg-gray-50 transition duration-200"
                                    wire:click="selectDate('{{ $dia['date'] }}')">
                                <div class="font-medium text-gray-900">
                                    {{ $dia['formatted'] }}
                                </div>
                                <div class="text-sm text-gray-600">
                                    {{ $dia['dayName'] }} {{ $dia['isToday'] ? '(hoje)' : '' }}
                                </div>
                                <div class="text-xs text-green-600 mt-1">
                                    {{ $dia['slotsAvailable'] }} horários disponíveis
                                </div>
                            </button>
                        @empty
                            <div class="text-sm text-gray-500">Nenhuma data disponível.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal: Horário --}}
    {{-- Modal: Horário --}}
@if ($showTimeModal)
  <div
    wire:key="modal-time-{{ $selectedServiceId }}-{{ $selectedDate ?? 'none' }}"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    wire:click.self="closeTimeModal"
    wire:keydown.escape.window="closeTimeModal"
  >
    <div class="bg-white rounded-lg max-w-md w-full mx-4 max-h-96 overflow-y-auto">
      <div class="p-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Escolha o Horário</h3>
          <button type="button" wire:click="closeTimeModal" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="grid grid-cols-2 gap-3">
          @forelse ($horariosDisponiveis as $horario)
            <button
              type="button"
              wire:key="slot-{{ $selectedDate }}-{{ str_replace(':','',$horario) }}"
              class="border rounded-lg p-3 text-center hover:bg-blue-50 hover:border-blue-300 transition duration-200"
              wire:click="selectTime('{{ $horario }}')"
            >
              <span class="font-medium text-gray-900">{{ $horario }}</span>
            </button>
          @empty
            <div class="col-span-2 text-sm text-gray-500">Nenhum horário disponível.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endif

</div>
