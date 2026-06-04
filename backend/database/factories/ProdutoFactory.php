<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    protected $model = Produto::class;

    public function definition(): array
    {
        return [
            'nome' => $this->faker->words(2, true),
            'preco_venda' => $this->faker->randomFloat(2, 5, 500),
            'custo_medio' => 0,
            'estoque' => 0,
        ];
    }

    public function comEstoque(int $quantidade, float $custo): self
    {
        return $this->state(fn () => [
            'estoque' => $quantidade,
            'custo_medio' => $custo,
        ]);
    }
}
