<?php

use Illuminate\Support\Facades\Route;

// Livewire (página pública)
use App\Livewire\AgendamentoPage;

// Auth + Admin
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ServicoController;
use App\Http\Controllers\Admin\HorarioDisponivelController;
use App\Http\Controllers\Admin\ConfiguracaoController;

// (Opcional) Caso ainda use o controller de agendamento para integrações externas
use App\Http\Controllers\Api\AgendamentoController;

/*
|--------------------------------------------------------------------------
| Público (sem autenticação)
|--------------------------------------------------------------------------
| Página de agendamento agora é um componente Livewire full-page.
| Clientes acessam sem login.
*/
Route::get('/', AgendamentoPage::class)->name('agendamento1');

/*
|--------------------------------------------------------------------------
| Autenticação
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Painel autenticado
|--------------------------------------------------------------------------
| Se quiser manter um "home" autenticado separado do público, use /home.
*/
Route::middleware('auth')->group(function () {
    Route::get('/home', [AdminController::class, 'dashboard'])->name('home');

    // Área administrativa
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::resource('servicos', ServicoController::class);
        Route::resource('horarios-disponiveis', HorarioDisponivelController::class);
        Route::resource('configuracoes', ConfiguracaoController::class);
    });
});

/*
|--------------------------------------------------------------------------
| (Opcional) Endpoints de compatibilidade / integrações
|--------------------------------------------------------------------------
| Se a nova página Livewire não depender mais de endpoints REST,
| você pode REMOVER os dois abaixo. Deixe-os apenas se houver integrações
| externas chamando a API (ex.: outro front, chatbot, etc.).
|
| 1) Confirmar agendamento por API (confiar no CSRF do 'web' + throttling)
| 2) Listar horários disponíveis (se ainda houver algum consumidor externo)
*/

// 1) Criar agendamento via API externa (sem exigir login):
// Route::post('/agendamentos', [AgendamentoController::class, 'store'])
//     ->middleware(['throttle:10,1'])
//     ->name('api.agendamentos.store');

// 2) Listar horários disponíveis via API externa (sem exigir login):
// Route::get('/listar-horarios-disponiveis', [AgendamentoController::class, 'horariosDisponiveis'])
//     ->middleware(['throttle:30,1'])
//     ->name('api.horarios.disponiveis');
