<?php

use App\Http\Controllers\Api\CancelarVendaController;
use App\Http\Controllers\Api\CriarProdutoController;
use App\Http\Controllers\Api\ListarComprasController;
use App\Http\Controllers\Api\ListarProdutosController;
use App\Http\Controllers\Api\ListarVendasController;
use App\Http\Controllers\Api\RegistrarCompraController;
use App\Http\Controllers\Api\RegistrarVendaController;
use Illuminate\Support\Facades\Route;

Route::get('/produtos', ListarProdutosController::class);
Route::post('/produtos', CriarProdutoController::class);

Route::get('/compras', ListarComprasController::class);
Route::post('/compras', RegistrarCompraController::class);

Route::get('/vendas', ListarVendasController::class);
Route::post('/vendas', RegistrarVendaController::class);
Route::post('/vendas/{venda}/cancelar', CancelarVendaController::class);
