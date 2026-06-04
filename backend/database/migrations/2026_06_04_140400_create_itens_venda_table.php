<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itens_venda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venda_id')->constrained('vendas')->cascadeOnDelete();
            $table->foreignId('produto_id')->constrained('produtos');
            $table->unsignedInteger('quantidade');
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('custo_unitario', 12, 4);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('lucro_item', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itens_venda');
    }
};
