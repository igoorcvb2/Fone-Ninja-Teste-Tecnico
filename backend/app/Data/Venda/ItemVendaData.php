<?php

namespace App\Data\Venda;

use Spatie\LaravelData\Data;

class ItemVendaData extends Data
{
    public function __construct(
        public int $id,
        public int $quantidade,
        public float $preco_unitario,
    ) {}
}
