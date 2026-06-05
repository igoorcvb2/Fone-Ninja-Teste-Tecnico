<?php

namespace Database\Seeders;

use App\Models\Produto;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $produtos = [
            ['nome' => 'Camiseta',     'preco_venda' => 79.90,  'estoque' => 20, 'custo_medio' => 35.0000],
            ['nome' => 'Caneca personalizada', 'preco_venda' => 29.90,  'estoque' => 50, 'custo_medio' => 12.5000],
            ['nome' => 'Mochila de escola',     'preco_venda' => 189.00, 'estoque' => 30, 'custo_medio' => 80.0000],
            ['nome' => 'Tênis casual',        'preco_venda' => 349.00, 'estoque' => 8,  'custo_medio' => 150.0000],
            ['nome' => 'Boné esportivo',      'preco_venda' => 59.90,  'estoque' => 0,  'custo_medio' => 0.0000],
        ];

        foreach ($produtos as $p) {
            Produto::create($p);
        }
    }
}
