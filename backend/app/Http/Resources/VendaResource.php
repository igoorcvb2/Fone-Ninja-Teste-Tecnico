<?php

namespace App\Http\Resources;

use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Venda
 */
class VendaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'cliente' => $this->cliente,
            'total' => (float) $this->total,
            'lucro' => (float) $this->lucro,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'cancelada_em' => $this->cancelada_em?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'itens' => ItemVendaResource::collection($this->whenLoaded('itens')),
        ];
    }
}
