<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HorarioDisponivel extends Model
{
    use HasFactory;

    protected $table = 'horarios_disponiveis';

    protected $fillable = [
        'dia_semana',
        'hora_inicio',
        'hora_fim',
        'ativo',
        'tipo' // novo campo
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i'
    ];
}
