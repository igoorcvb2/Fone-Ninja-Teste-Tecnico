<?php

namespace App\Http\Controllers\Api;

use App\Enums\StatusVenda;
use App\Http\Controllers\Controller;
use App\Http\Resources\VendaResource;
use App\Models\Venda;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ListarVendasController extends Controller
{
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $vendas = Venda::query()
            ->with(['itens.produto'])
            ->when(
                $request->filled('status'),
                fn ($q) => $q->where('status', StatusVenda::from(strtoupper($request->string('status')->value())))
            )
            ->orderByDesc('created_at')
            ->get();

        return VendaResource::collection($vendas);
    }
}
