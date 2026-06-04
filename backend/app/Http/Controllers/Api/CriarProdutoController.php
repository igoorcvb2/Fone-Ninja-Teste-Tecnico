<?php

namespace App\Http\Controllers\Api;

use App\Data\Produto\CriarProdutoData;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProdutoResource;
use App\Services\ProdutoService;
use Illuminate\Http\JsonResponse;

class CriarProdutoController extends Controller
{
    public function __construct(private readonly ProdutoService $produtos) {}

    public function __invoke(CriarProdutoData $data): JsonResponse
    {
        $produto = $this->produtos->criar($data);

        return ProdutoResource::make($produto)
            ->response()
            ->setStatusCode(201);
    }
}
