<?php

namespace App\Http\Resources;

use App\Models\ItemVenda;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ItemVenda
 */
class ItemVendaResource extends JsonResource
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
            'custo_unitario' => (float) $this->custo_unitario,
            'subtotal' => (float) $this->subtotal,
            'lucro_item' => (float) $this->lucro_item,
        ];
    }
}
