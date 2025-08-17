<div>
@if ($open)
<div
  class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
  wire:keydown.escape="close"
  wire:click.self="close"
>
  <div class="bg-white rounded-lg shadow-md w-full max-w-md mx-4">
    <div class="p-6">
      <div class="flex justify-between items-start mb-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Entrar no Sistema</h2>
          <p class="text-gray-600 mt-2">Digite seu número de celular para continuar</p>
        </div>
        <button type="button" class="text-gray-400 hover:text-gray-600" wire:click="close" aria-label="Fechar">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      {{-- Mantém o POST para sua rota existente --}}
      <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Celular --}}
        <div class="mb-4">
          <label for="celular" class="block text-sm font-medium text-gray-700 mb-2">
            Número do Celular
          </label>
          <input
            type="tel"
            id="celular"
            name="celular"
            value="{{ old('celular') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            placeholder="(11) 99999-9999"
          >
          @error('celular')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>

        {{-- Nome (apenas para novos usuários) --}}
        @if($novoUsuario)
        <div class="mb-4">
          <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
            Nome
          </label>
          <input
            type="text"
            id="nome"
            name="nome"
            value="{{ old('nome') }}"
            required
            class="w-full px-3 py-2 border border-gray-300 rounded-md"
            placeholder="Digite seu nome"
          >
          @error('nome')
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
          @enderror
        </div>
        @endif

        {{-- Botão --}}
        <div>
          <button
            type="submit"
            class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-600 transition duration-200"
          >
            Entrar
          </button>
        </div>
      </form>

      <div class="mt-6 text-center">
        <p class="text-xs text-gray-500">
          Ao continuar, você concorda com nossos termos de uso.
        </p>
      </div>
    </div>
  </div>
</div>
@endif
</div>
