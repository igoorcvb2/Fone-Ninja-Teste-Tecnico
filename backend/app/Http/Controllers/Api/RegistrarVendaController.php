<?php

namespace App\Http\Controllers\Api;

use App\Data\Venda\RegistrarVendaData;
use App\Http\Controllers\Controller;
use App\Http\Resources\VendaResource;
use App\Services\VendaService;
use Illuminate\Http\JsonResponse;

class RegistrarVendaController extends Controller
{
    public function __construct(private readonly VendaService $vendas) {}

    public function __invoke(RegistrarVendaData $data): JsonResponse
    {
        $venda = $this->vendas->registrar($data);

        return VendaResource::make($venda)
            ->response()
            ->setStatusCode(201);
    }
}
