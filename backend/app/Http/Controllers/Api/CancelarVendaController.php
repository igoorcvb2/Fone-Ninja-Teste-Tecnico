<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\VendaResource;
use App\Models\Venda;
use App\Services\VendaService;

class CancelarVendaController extends Controller
{
    public function __construct(private readonly VendaService $vendas) {}

    public function __invoke(Venda $venda): VendaResource
    {
        return VendaResource::make($this->vendas->cancelar($venda));
    }
}
