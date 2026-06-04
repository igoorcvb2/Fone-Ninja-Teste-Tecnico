<?php

use App\Http\Controllers\Api\CriarProdutoController;
use App\Http\Controllers\Api\ListarProdutosController;
use Illuminate\Support\Facades\Route;

Route::get('/produtos', ListarProdutosController::class);
Route::post('/produtos', CriarProdutoController::class);
