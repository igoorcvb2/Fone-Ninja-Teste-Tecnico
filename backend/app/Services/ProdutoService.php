<?php

namespace App\Services;

use App\Data\Produto\CriarProdutoData;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Collection;

class ProdutoService
{
    public function listar(): Collection
    {
        return Produto::query()->orderBy('nome')->get();
    }

    public function criar(CriarProdutoData $data): Produto
    {
        return Produto::create([
            'nome' => $data->nome,
            'preco_venda' => $data->preco_venda,
            'custo_medio' => 0,
            'estoque' => 0,
        ]);
    }
}
