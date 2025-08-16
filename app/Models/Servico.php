<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'duracao_minutos',
        'ativo'
    ];
    
    protected $casts = [
        'preco' => 'decimal:2',
        'ativo' => 'boolean'
    ];
    
    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
