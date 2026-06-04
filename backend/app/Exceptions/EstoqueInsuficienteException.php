<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class EstoqueInsuficienteException extends Exception
{
    public function __construct(
        public readonly int $produtoId,
        public readonly string $produtoNome,
        public readonly int $disponivel,
        public readonly int $solicitado,
    ) {
        parent::__construct("Estoque insuficiente para o produto '{$produtoNome}'");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'erro' => 'estoque_insuficiente',
            'produto_id' => $this->produtoId,
            'produto_nome' => $this->produtoNome,
            'disponivel' => $this->disponivel,
            'solicitado' => $this->solicitado,
        ], 422);
    }
}
