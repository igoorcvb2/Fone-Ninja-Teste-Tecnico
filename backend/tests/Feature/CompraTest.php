<?php

use App\Models\Produto;

use function Pest\Laravel\{getJson, postJson};

it('registra compra, entra com estoque e define custo médio inicial', function () {
    $produto = Produto::factory()->create(['preco_venda' => 80]);

    $resposta = postJson('/api/compras', [
        'fornecedor' => 'Fornecedor X',
        'produtos' => [
            ['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 30],
        ],
    ]);

    $resposta->assertCreated()
        ->assertJsonPath('data.fornecedor', 'Fornecedor X')
        ->assertJsonPath('data.total', 300);

    expect($produto->fresh())
        ->estoque->toBe(10)
        ->and((float) $produto->fresh()->custo_medio)->toBe(30.0);
});

it('recalcula o custo médio ponderado em compras subsequentes', function () {
    // Cenário do enunciado: 10 @ 30 + 10 @ 40 -> custo médio 35
    $produto = Produto::factory()->create();

    postJson('/api/compras', [
        'fornecedor' => 'Fornecedor A',
        'produtos' => [['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 30]],
    ])->assertCreated();

    postJson('/api/compras', [
        'fornecedor' => 'Fornecedor B',
        'produtos' => [['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 40]],
    ])->assertCreated();

    $produto->refresh();
    expect($produto->estoque)->toBe(20)
        ->and((float) $produto->custo_medio)->toBe(35.0);
});

it('aceita múltiplos produtos numa mesma compra', function () {
    $p1 = Produto::factory()->create();
    $p2 = Produto::factory()->create();

    postJson('/api/compras', [
        'fornecedor' => 'Multi',
        'produtos' => [
            ['id' => $p1->id, 'quantidade' => 5, 'preco_unitario' => 10],
            ['id' => $p2->id, 'quantidade' => 3, 'preco_unitario' => 20],
        ],
    ])->assertCreated()
        ->assertJsonPath('data.total', 110);

    expect($p1->fresh()->estoque)->toBe(5)
        ->and($p2->fresh()->estoque)->toBe(3);
});

it('valida payload de compra', function () {
    postJson('/api/compras', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['fornecedor', 'produtos']);

    $produto = Produto::factory()->create();
    postJson('/api/compras', [
        'fornecedor' => 'X',
        'produtos' => [['id' => $produto->id, 'quantidade' => 0, 'preco_unitario' => 10]],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('produtos.0.quantidade');
});

it('rejeita compra de produto inexistente', function () {
    postJson('/api/compras', [
        'fornecedor' => 'X',
        'produtos' => [['id' => 9999, 'quantidade' => 1, 'preco_unitario' => 10]],
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('produtos.0.id');
});

it('lista compras ordenadas por mais recentes', function () {
    $produto = Produto::factory()->create();

    postJson('/api/compras', [
        'fornecedor' => 'Antiga',
        'produtos' => [['id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 10]],
    ]);

    postJson('/api/compras', [
        'fornecedor' => 'Recente',
        'produtos' => [['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 20]],
    ]);

    getJson('/api/compras')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.fornecedor', 'Recente')
        ->assertJsonPath('data.1.fornecedor', 'Antiga');
});
