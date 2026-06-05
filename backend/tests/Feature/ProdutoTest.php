<?php

use App\Models\Produto;

use function Pest\Laravel\{getJson, postJson};

it('cadastra um produto com nome e preço de venda', function () {
    $resposta = postJson('/api/produtos', [
        'nome' => 'Camiseta',
        'preco_venda' => 79.90,
    ]);

    $resposta->assertCreated()
        ->assertJsonPath('data.nome', 'Camiseta')
        ->assertJsonPath('data.preco_venda', 79.90)
        ->assertJsonPath('data.custo_medio', 0)
        ->assertJsonPath('data.estoque', 0);

    expect(Produto::count())->toBe(1);
});

it('rejeita produto sem nome ou com nome curto', function () {
    postJson('/api/produtos', ['preco_venda' => 10])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('nome');

    postJson('/api/produtos', ['nome' => 'ab', 'preco_venda' => 10])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('nome');
});

it('rejeita produto com preço negativo ou zerado', function () {
    postJson('/api/produtos', ['nome' => 'Caneca', 'preco_venda' => 0])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('preco_venda');

    postJson('/api/produtos', ['nome' => 'Caneca', 'preco_venda' => -5])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('preco_venda');
});

it('lista produtos ordenados por nome', function () {
    Produto::factory()->create(['nome' => 'Z Produto']);
    Produto::factory()->create(['nome' => 'A Produto']);
    Produto::factory()->create(['nome' => 'M Produto']);

    $resposta = getJson('/api/produtos');

    $resposta->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.nome', 'A Produto')
        ->assertJsonPath('data.1.nome', 'M Produto')
        ->assertJsonPath('data.2.nome', 'Z Produto');
});
