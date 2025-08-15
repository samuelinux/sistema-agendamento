@extends('layouts.app')

@section('title', 'Login - Sistema de Agendamento')

@section('content')
<div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-6">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Entrar no Sistema</h2>
        <p class="text-gray-600 mt-2">Digite seu número de celular para continuar</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Celular -->
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

    <!-- Campo Nome (aparece apenas para novos usuários) -->
    @if(session('new_user'))
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

    <!-- Botão único -->
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
@endsection

