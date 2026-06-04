<?php

namespace App\Services;

use App\Data\Compra\RegistrarCompraData;
use App\Models\Compra;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class CompraService
{
    public function registrar(RegistrarCompraData $data): Compra
    {
        return DB::transaction(function () use ($data) {
            $compra = Compra::create([
                'fornecedor' => $data->fornecedor,
                'total' => 0,
            ]);

            $total = 0.0;

            foreach ($data->produtos as $item) {
                $produto = Produto::lockForUpdate()->findOrFail($item->id);

                $subtotal = round($item->quantidade * $item->preco_unitario, 2);
                $total += $subtotal;

                $this->atualizarCustoMedio($produto, $item->quantidade, $item->preco_unitario);

                $compra->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                    'subtotal' => $subtotal,
                ]);
            }

            $compra->update(['total' => round($total, 2)]);

            return $compra->fresh(['itens.produto']);
        });
    }

    /**
     * Média ponderada entre o estoque atual (ao custo médio antigo) e a entrada.
     *
     *   novo_custo = (estoque_atual * custo_atual + qtd_entrada * preco_entrada)
     *                / (estoque_atual + qtd_entrada)
     */
    private function atualizarCustoMedio(Produto $produto, int $quantidade, float $precoUnitario): void
    {
        $estoqueAtual = $produto->estoque;
        $custoAtual = (float) $produto->custo_medio;
        $novoEstoque = $estoqueAtual + $quantidade;

        $novoCusto = $novoEstoque > 0
            ? (($estoqueAtual * $custoAtual) + ($quantidade * $precoUnitario)) / $novoEstoque
            : 0;

        $produto->update([
            'estoque' => $novoEstoque,
            'custo_medio' => round($novoCusto, 4),
        ]);
    }
}
