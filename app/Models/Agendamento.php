<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'servico_id',
        'data_agendamento',
        'hora_inicio',
        'hora_fim',
        'status',
        'observacoes'
    ];
    
    protected $casts = [
        'data_agendamento' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fim' => 'datetime:H:i'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
}
