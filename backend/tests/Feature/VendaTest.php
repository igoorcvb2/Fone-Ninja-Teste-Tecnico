<?php

use App\Enums\StatusVenda;
use App\Models\Produto;

it('registra venda, baixa estoque e calcula lucro pelo custo médio', function () {
    // Cenário do enunciado: custo médio 35, venda 5un a 80 -> lucro 225
    $produto = Produto::factory()->comEstoque(20, 35)->create();

    $resposta = postJson('/api/vendas', [
        'cliente' => 'Fulano da Silva',
        'produtos' => [
            ['id' => $produto->id, 'quantidade' => 5, 'preco_unitario' => 80],
        ],
    ]);

    $resposta->assertCreated()
        ->assertJsonPath('data.cliente', 'Fulano da Silva')
        ->assertJsonPath('data.total', 400.0)
        ->assertJsonPath('data.lucro', 225.0)
        ->assertJsonPath('data.status', StatusVenda::Concluida->value);

    expect($produto->fresh()->estoque)->toBe(15);
});

it('soma o lucro corretamente com múltiplos itens', function () {
    $p1 = Produto::factory()->comEstoque(10, 10)->create();
    $p2 = Produto::factory()->comEstoque(10, 50)->create();

    postJson('/api/vendas', [
        'cliente' => 'Cliente Multi',
        'produtos' => [
            ['id' => $p1->id, 'quantidade' => 2, 'preco_unitario' => 25],
            ['id' => $p2->id, 'quantidade' => 1, 'preco_unitario' => 100],
        ],
    ])->assertCreated()
        ->assertJsonPath('data.total', 150.0)
        ->assertJsonPath('data.lucro', 80.0);
});

it('rejeita venda quando estoque é insuficiente', function () {
    $produto = Produto::factory()->comEstoque(3, 10)->create();

    $resposta = postJson('/api/vendas', [
        'cliente' => 'Cliente',
        'produtos' => [
            ['id' => $produto->id, 'quantidade' => 10, 'preco_unitario' => 50],
        ],
    ]);

    $resposta->assertStatus(422)
        ->assertJsonPath('erro', 'estoque_insuficiente')
        ->assertJsonPath('produto_id', $produto->id)
        ->assertJsonPath('disponivel', 3)
        ->assertJsonPath('solicitado', 10);

    expect($produto->fresh()->estoque)->toBe(3);
});

it('valida payload de venda', function () {
    postJson('/api/vendas', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['cliente', 'produtos']);
});

it('cancela venda revertendo estoque sem mexer no custo médio', function () {
    $produto = Produto::factory()->comEstoque(20, 35)->create();

    $venda = postJson('/api/vendas', [
        'cliente' => 'A reverter',
        'produtos' => [['id' => $produto->id, 'quantidade' => 5, 'preco_unitario' => 80]],
    ])->json('data');

    expect($produto->fresh()->estoque)->toBe(15);

    postJson("/api/vendas/{$venda['id']}/cancelar")
        ->assertOk()
        ->assertJsonPath('data.status', StatusVenda::Cancelada->value)
        ->assertJsonPath('data.cliente', 'A reverter');

    $produto->refresh();
    expect($produto->estoque)->toBe(20)
        ->and((float) $produto->custo_medio)->toBe(35.0);
});

it('não cancela venda já cancelada', function () {
    $produto = Produto::factory()->comEstoque(10, 5)->create();

    $venda = postJson('/api/vendas', [
        'cliente' => 'Y',
        'produtos' => [['id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 10]],
    ])->json('data');

    postJson("/api/vendas/{$venda['id']}/cancelar")->assertOk();

    postJson("/api/vendas/{$venda['id']}/cancelar")
        ->assertStatus(422)
        ->assertJsonPath('erro', 'venda_ja_cancelada');
});

it('lista vendas e filtra por status', function () {
    $produto = Produto::factory()->comEstoque(20, 5)->create();

    $v1 = postJson('/api/vendas', [
        'cliente' => 'A',
        'produtos' => [['id' => $produto->id, 'quantidade' => 1, 'preco_unitario' => 10]],
    ])->json('data');

    postJson('/api/vendas', [
        'cliente' => 'B',
        'produtos' => [['id' => $produto->id, 'quantidade' => 2, 'preco_unitario' => 10]],
    ])->assertCreated();

    postJson("/api/vendas/{$v1['id']}/cancelar")->assertOk();

    getJson('/api/vendas')->assertJsonCount(2, 'data');
    getJson('/api/vendas?status=cancelada')->assertJsonCount(1, 'data');
    getJson('/api/vendas?status=concluida')->assertJsonCount(1, 'data');
});
