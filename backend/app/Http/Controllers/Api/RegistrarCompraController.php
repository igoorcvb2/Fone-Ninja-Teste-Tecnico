<?php

namespace App\Http\Controllers\Api;

use App\Data\Compra\RegistrarCompraData;
use App\Http\Controllers\Controller;
use App\Http\Resources\CompraResource;
use App\Services\CompraService;
use Illuminate\Http\JsonResponse;

class RegistrarCompraController extends Controller
{
    public function __construct(private readonly CompraService $compras) {}

    public function __invoke(RegistrarCompraData $data): JsonResponse
    {
        $compra = $this->compras->registrar($data);

        return CompraResource::make($compra)
            ->response()
            ->setStatusCode(201);
    }
}
