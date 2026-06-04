<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemVenda extends Model
{
    use HasFactory;

    protected $table = 'itens_venda';

    protected $fillable = [
        'venda_id',
        'produto_id',
        'quantidade',
        'preco_unitario',
        'custo_unitario',
        'subtotal',
        'lucro_item',
    ];

    protected $casts = [
        'quantidade' => 'integer',
        'preco_unitario' => 'decimal:2',
        'custo_unitario' => 'decimal:4',
        'subtotal' => 'decimal:2',
        'lucro_item' => 'decimal:2',
    ];

    public function venda(): BelongsTo
    {
        return $this->belongsTo(Venda::class);
    }

    public function produto(): BelongsTo
    {
        return $this->belongsTo(Produto::class);
    }
}
