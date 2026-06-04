<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'preco_venda',
        'custo_medio',
        'estoque',
    ];

    protected $casts = [
        'preco_venda' => 'decimal:2',
        'custo_medio' => 'decimal:4',
        'estoque' => 'integer',
    ];

    public function itensCompra(): HasMany
    {
        return $this->hasMany(ItemCompra::class);
    }

    public function itensVenda(): HasMany
    {
        return $this->hasMany(ItemVenda::class);
    }
}
