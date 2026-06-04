<?php

namespace App\Models;

use App\Enums\StatusVenda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venda extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente',
        'total',
        'lucro',
        'status',
        'cancelada_em',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'lucro' => 'decimal:2',
        'status' => StatusVenda::class,
        'cancelada_em' => 'datetime',
    ];

    public function itens(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }

    public function scopeConcluidas(Builder $query): Builder
    {
        return $query->where('status', StatusVenda::Concluida);
    }

    public function isCancelada(): bool
    {
        return $this->status === StatusVenda::Cancelada;
    }
}
