<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->date('data_agendamento');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->enum('status', ['agendado', 'confirmado', 'cancelado', 'concluido'])->default('agendado');
            $table->text('observacoes')->nullable();
            $table->timestamps();
            
            // Índice único para evitar agendamentos duplicados no mesmo horário
            $table->unique(['data_agendamento', 'hora_inicio', 'hora_fim']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
