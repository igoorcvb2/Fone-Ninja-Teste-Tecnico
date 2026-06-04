<?php

namespace App\Data\Compra;

use Spatie\LaravelData\Data;

class ItemCompraData extends Data
{
    public function __construct(
        public int $id,
        public int $quantidade,
        public float $preco_unitario,
    ) {}
}
