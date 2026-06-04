<?php

namespace App\Data\Compra;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class RegistrarCompraData extends Data
{
    public function __construct(
        public string $fornecedor,
        #[DataCollectionOf(ItemCompraData::class)]
        public DataCollection $produtos,
    ) {}

    public static function rules(): array
    {
        return [
            'fornecedor' => ['required', 'string', 'min:2', 'max:191'],
            'produtos' => ['required', 'array', 'min:1'],
            'produtos.*.id' => ['required', 'integer', 'exists:produtos,id'],
            'produtos.*.quantidade' => ['required', 'integer', 'min:1'],
            'produtos.*.preco_unitario' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
