<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class VendaJaCanceladaException extends Exception
{
    public function __construct(public readonly int $vendaId)
    {
        parent::__construct("A venda #{$vendaId} já foi cancelada.");
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'erro' => 'venda_ja_cancelada',
            'venda_id' => $this->vendaId,
        ], 422);
    }
}
