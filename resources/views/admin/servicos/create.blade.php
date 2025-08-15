@extends('layouts.admin')

@section('title', 'Novo Serviço - Painel Admin')
@section('page-title', 'Novo Serviço')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Criar Novo Serviço</h3>
        </div>
        
        <form method="POST" action="{{ route('admin.servicos.store') }}" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- Nome -->
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">
                        Nome do Serviço *
                    </label>
                    <input type="text" 
                           id="nome" 
                           name="nome" 
                           value="{{ old('nome') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nome') border-red-500 @enderror"
                           placeholder="Ex: Corte de Cabelo"
                           required>
                    @error('nome')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Descrição -->
                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700 mb-2">
                        Descrição
                    </label>
                    <textarea id="descricao" 
                              name="descricao" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descricao') border-red-500 @enderror"
                              placeholder="Descreva o serviço oferecido">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Preço -->
                    <div>
                        <label for="preco" class="block text-sm font-medium text-gray-700 mb-2">
                            Preço (R$)
                        </label>
                        <input type="number" 
                               id="preco" 
                               name="preco" 
                               value="{{ old('preco') }}"
                               step="0.01"
                               min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('preco') border-red-500 @enderror"
                               placeholder="0,00">
                        @error('preco')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Deixe em branco para serviço gratuito</p>
                    </div>
                    
                    <!-- Duração -->
                    <div>
                        <label for="duracao_minutos" class="block text-sm font-medium text-gray-700 mb-2">
                            Duração (minutos) *
                        </label>
                        <input type="number" 
                               id="duracao_minutos" 
                               name="duracao_minutos" 
                               value="{{ old('duracao_minutos', 30) }}"
                               min="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('duracao_minutos') border-red-500 @enderror"
                               required>
                        @error('duracao_minutos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="ativo" 
                               value="1"
                               {{ old('ativo', true) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">Serviço ativo</span>
                    </label>
                    <p class="text-xs text-gray-500 mt-1">Serviços inativos não aparecerão para agendamento</p>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.servicos.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                    Criar Serviço
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

