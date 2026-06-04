<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProdutoResource;
use App\Services\ProdutoService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListarProdutosController extends Controller
{
    public function __construct(private readonly ProdutoService $produtos) {}

    public function __invoke(): AnonymousResourceCollection
    {
        return ProdutoResource::collection($this->produtos->listar());
    }
}
