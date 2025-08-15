<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ServicoController;
use App\Http\Controllers\Admin\HorarioDisponivelController;
use App\Http\Controllers\Admin\ConfiguracaoController;
use App\Http\Controllers\Api\AgendamentoController;

Route::post('/agendamentos', [AgendamentoController::class, 'store'])->middleware('auth');


// Rotas de autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas por autenticação
Route::get('/listar-horarios-disponiveis', [AgendamentoController::class, 'horariosDisponiveis'])->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('agendamento.index');
    })->name('home');

    // Rotas do painel admin
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');

        // CRUD de serviços
        Route::resource('servicos', ServicoController::class);

        // CRUD de horários disponíveis
        Route::resource('horarios-disponiveis', HorarioDisponivelController::class);

        // CRUD de configurações
        Route::resource('configuracoes', ConfiguracaoController::class);
    });
});
