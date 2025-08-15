<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Servico;
use App\Models\HorarioDisponivel;
use App\Models\Configuracao;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Criar usuário admin
        User::create([
            'name' => 'Administrador',
            'celular' => '11999999999',
            'email' => 'admin@sistema.com',
            'is_admin' => true
        ]);

        // Criar usuário cliente de exemplo
        User::create([
            'name' => 'João Silva',
            'celular' => '11988888888',
            'is_admin' => false
        ]);

        // Criar serviços de exemplo
        Servico::create([
            'nome' => 'Corte de Cabelo',
            'descricao' => 'Corte masculino tradicional',
            'preco' => 25.00,
            'duracao_minutos' => 30,
            'ativo' => true
        ]);

        Servico::create([
            'nome' => 'Barba',
            'descricao' => 'Aparar e modelar barba',
            'preco' => 15.00,
            'duracao_minutos' => 20,
            'ativo' => true
        ]);

        Servico::create([
            'nome' => 'Corte + Barba',
            'descricao' => 'Pacote completo de corte e barba',
            'preco' => 35.00,
            'duracao_minutos' => 45,
            'ativo' => true
        ]);

        // Segunda a Sexta
for ($dia = 1; $dia <= 5; $dia++) {
    // Manhã: 8:00 às 12:00
    HorarioDisponivel::create([
        'dia_semana' => $dia,
        'hora_inicio' => '08:00',
        'hora_fim' => '12:00',
        'tipo' => 'trabalho',
        'ativo' => true
    ]);

    // Almoço: 12:00 às 14:00
    HorarioDisponivel::create([
        'dia_semana' => $dia,
        'hora_inicio' => '12:00',
        'hora_fim' => '14:00',
        'tipo' => 'almoco',
        'ativo' => true
    ]);

    // Tarde: 14:00 às 18:00
    HorarioDisponivel::create([
        'dia_semana' => $dia,
        'hora_inicio' => '14:00',
        'hora_fim' => '18:00',
        'tipo' => 'trabalho',
        'ativo' => true
    ]);
}

// Sábado
HorarioDisponivel::create([
    'dia_semana' => 6,
    'hora_inicio' => '08:00',
    'hora_fim' => '12:00',
    'tipo' => 'trabalho',
    'ativo' => true
]);


        // Configurações do sistema
        Configuracao::create([
            'chave' => 'horario_almoco_inicio',
            'valor' => '12:00',
            'descricao' => 'Horário de início do almoço'
        ]);

        Configuracao::create([
            'chave' => 'horario_almoco_fim',
            'valor' => '14:00',
            'descricao' => 'Horário de fim do almoço'
        ]);
    }
}
