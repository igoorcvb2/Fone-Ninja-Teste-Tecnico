<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompraResource;
use App\Models\Compra;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListarComprasController extends Controller
{
    public function __invoke(): AnonymousResourceCollection
    {
        $compras = Compra::query()
            ->with(['itens.produto'])
            ->orderByDesc('created_at')
            ->get();

        return CompraResource::collection($compras);
    }
}
