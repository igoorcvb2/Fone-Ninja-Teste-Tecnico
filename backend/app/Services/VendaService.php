<?php

namespace App\Services;

use App\Data\Venda\RegistrarVendaData;
use App\Enums\StatusVenda;
use App\Exceptions\EstoqueInsuficienteException;
use App\Exceptions\VendaJaCanceladaException;
use App\Models\Produto;
use App\Models\Venda;
use Illuminate\Support\Facades\DB;

class VendaService
{
    public function registrar(RegistrarVendaData $data): Venda
    {
        return DB::transaction(function () use ($data) {
            $venda = Venda::create([
                'cliente' => $data->cliente,
                'total' => 0,
                'lucro' => 0,
                'status' => StatusVenda::Concluida,
            ]);

            $total = 0.0;
            $lucro = 0.0;

            foreach ($data->produtos as $item) {
                $produto = Produto::lockForUpdate()->findOrFail($item->id);

                if ($produto->estoque < $item->quantidade) {
                    throw new EstoqueInsuficienteException(
                        produtoId: $produto->id,
                        produtoNome: $produto->nome,
                        disponivel: $produto->estoque,
                        solicitado: $item->quantidade,
                    );
                }

                $custoUnit = (float) $produto->custo_medio;
                $subtotal = round($item->quantidade * $item->preco_unitario, 2);
                $lucroItem = round(($item->preco_unitario - $custoUnit) * $item->quantidade, 2);

                $total += $subtotal;
                $lucro += $lucroItem;

                $produto->decrement('estoque', $item->quantidade);

                $venda->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                    'custo_unitario' => $custoUnit,
                    'subtotal' => $subtotal,
                    'lucro_item' => $lucroItem,
                ]);
            }

            $venda->update([
                'total' => round($total, 2),
                'lucro' => round($lucro, 2),
            ]);

            return $venda->fresh(['itens.produto']);
        });
    }

    /**
     * Cancelamento simples: repõe a quantidade no estoque, mas não recalcula
     * o custo médio. A alternativa "correta" (média ponderada reversa) é
     * incomum em ERPs e complica análise — preferimos a imprecisão pequena
     * em favor do histórico legível.
     */
    public function cancelar(Venda $venda): Venda
    {
        if ($venda->isCancelada()) {
            throw new VendaJaCanceladaException($venda->id);
        }

        return DB::transaction(function () use ($venda) {
            $venda->load('itens');

            foreach ($venda->itens as $item) {
                Produto::lockForUpdate()
                    ->whereKey($item->produto_id)
                    ->increment('estoque', $item->quantidade);
            }

            $venda->update([
                'status' => StatusVenda::Cancelada,
                'cancelada_em' => now(),
            ]);

            return $venda->fresh(['itens.produto']);
        });
    }
}
