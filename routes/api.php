<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AgendamentoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas públicas (para os modais)
Route::get('/servicos', [AgendamentoController::class, 'servicos']);

// Rotas protegidas por autenticação
Route::middleware('auth')->group(function () {
    Route::get('/datas-disponiveis', [AgendamentoController::class, 'datasDisponiveis']);
    Route::get('/horarios-disponiveis', [AgendamentoController::class, 'horariosDisponiveis']);
    Route::post('/agendamentos', [AgendamentoController::class, 'store']);
});
