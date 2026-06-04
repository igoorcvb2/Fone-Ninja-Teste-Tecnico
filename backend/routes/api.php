<?php

use App\Http\Controllers\Api\CriarProdutoController;
use App\Http\Controllers\Api\ListarProdutosController;
use App\Http\Controllers\Api\RegistrarCompraController;
use App\Http\Controllers\Api\RegistrarVendaController;
use Illuminate\Support\Facades\Route;

Route::get('/produtos', ListarProdutosController::class);
Route::post('/produtos', CriarProdutoController::class);

Route::post('/compras', RegistrarCompraController::class);

Route::post('/vendas', RegistrarVendaController::class);
