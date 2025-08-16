<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agendamento;
use App\Models\Servico;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        $hoje = Carbon::today();
        
        $stats = [
            'agendamentos_hoje' => Agendamento::whereDate('data_agendamento', $hoje)->count(),
            'agendamentos_mes' => Agendamento::whereMonth('data_agendamento', $hoje->month)
                                            ->whereYear('data_agendamento', $hoje->year)
                                            ->count(),
            'total_servicos' => Servico::where('ativo', true)->count(),
            'total_clientes' => User::where('is_admin', false)->count()
        ];
        
        $agendamentosHoje = Agendamento::with(['user', 'servico'])
                                      ->whereDate('data_agendamento', $hoje)
                                      ->orderBy('hora_inicio')
                                      ->get();
        
        $agendamentosRecentes = Agendamento::with(['user', 'servico'])
                                          ->orderBy('created_at', 'desc')
                                          ->limit(10)
                                          ->get();
        
        return view('admin.dashboard', compact('stats', 'agendamentosHoje', 'agendamentosRecentes'));
    }
}
