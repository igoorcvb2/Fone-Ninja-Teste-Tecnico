<?php

namespace App\Data\Produto;

use Spatie\LaravelData\Data;

class CriarProdutoData extends Data
{
    public function __construct(
        public string $nome,
        public float $preco_venda,
    ) {}

    public static function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'min:3', 'max:191'],
            'preco_venda' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
