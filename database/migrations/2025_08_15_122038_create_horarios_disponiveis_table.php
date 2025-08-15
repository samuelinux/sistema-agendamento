<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios_disponiveis', function (Blueprint $table) {
    $table->id();
    $table->unsignedTinyInteger('dia_semana');
    $table->time('hora_inicio');
    $table->time('hora_fim');
    $table->string('tipo')->default('trabalho'); // novo campo
    $table->boolean('ativo')->default(true);
    $table->timestamps();
});

    }

    public function down(): void
    {
        Schema::table('horarios_disponiveis', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
