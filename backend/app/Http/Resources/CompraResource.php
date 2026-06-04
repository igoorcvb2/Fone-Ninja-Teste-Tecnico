<?php

namespace App\Http\Resources;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Compra
 */
class CompraResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'fornecedor' => $this->fornecedor,
            'total' => (float) $this->total,
            'created_at' => $this->created_at?->toIso8601String(),
            'itens' => ItemCompraResource::collection($this->whenLoaded('itens')),
        ];
    }
}
