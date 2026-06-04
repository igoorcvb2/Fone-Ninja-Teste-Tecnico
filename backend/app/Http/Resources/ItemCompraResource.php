<?php

namespace App\Http\Resources;

use App\Models\ItemCompra;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemCompra
 */
class ItemCompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'produto_id' => $this->produto_id,
            'produto' => $this->whenLoaded('produto', fn () => [
                'id' => $this->produto->id,
                'nome' => $this->produto->nome,
            ]),
            'quantidade' => $this->quantidade,
            'preco_unitario' => (float) $this->preco_unitario,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}
