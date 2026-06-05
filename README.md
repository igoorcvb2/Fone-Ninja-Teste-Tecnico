# ERP Estoque

Pequeno ERP para teste técnico: cadastro de produtos, registro de compras (com cálculo
de custo médio ponderado) e vendas (com cálculo de lucro e validação de estoque).

## Stack

- **Backend:** Laravel 13 (PHP 8.3) + MySQL 8 + Spatie Laravel Data
- **Frontend:** Vue 3 + Vite + TypeScript + Tailwind CSS v4 + Reka UI
- **Infra:** Docker Compose
- **Testes:** Pest (feature tests cobrindo as regras de negócio)

## Estrutura

```
.
├── docker-compose.yml
├── docker/
│   └── mysql-init.sql      # cria o banco de testes (erp_test)
├── backend/
│   ├── Dockerfile
│   ├── docker/entrypoint.sh
│   └── app/                # API Laravel
└── frontend/
    ├── Dockerfile
    └── src/                # SPA Vue
```

## Como rodar

Pré-requisitos: Docker e Docker Compose.

```bash
docker compose up --build
```

Na primeira execução o backend já roda `migrate` automaticamente. Quando os três
serviços estiverem prontos:

- Frontend: <http://localhost:5173>
- API: <http://localhost:8000/api>
- MySQL: `localhost:3306` (usuário `erp`, senha `erp`, bancos `erp` e `erp_test`)

### Dados de exemplo (seed)

Pra não precisar cadastrar produto + compras na mão antes de testar o fluxo de venda,
rode o seeder:

```bash
docker compose exec backend php artisan db:seed
```

Isso popula 5 produtos com estoques e custos variados, incluindo **Camiseta básica
com custo médio 35**, o estado exato do cenário do enunciado, pronto pra demonstrar
uma venda de 5un a R$80 retornando lucro de R$225. Também inclui um produto **sem
estoque** (Boné esportivo), que aparece desabilitado no select de vendas.

Pra zerar tudo e popular de novo:

```bash
docker compose exec backend php artisan migrate:fresh --seed --force
```

### Sem Docker

Se preferir rodar local sem container, precisa ter PHP 8.3, MySQL e Node 20:

```bash
# backend
cd backend
cp .env.example .env
# ajuste DB_HOST=127.0.0.1 no .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve

# frontend (em outro terminal)
cd frontend
npm install
VITE_API_URL=http://localhost:8000/api npm run dev
```

## Endpoints

| Método | URL | Descrição |
|---|---|---|
| `GET` | `/api/produtos` | Lista produtos ordenados por nome |
| `POST` | `/api/produtos` | Cadastra produto (estoque e custo zerados) |
| `GET` | `/api/compras` | Lista compras (mais recentes primeiro) |
| `POST` | `/api/compras` | Registra compra (entra com estoque + atualiza custo médio) |
| `GET` | `/api/vendas` | Lista vendas (filtro opcional `?status=concluida\|cancelada`) |
| `POST` | `/api/vendas` | Registra venda (baixa estoque + calcula lucro) |
| `POST` | `/api/vendas/{id}/cancelar` | Cancela venda (devolve estoque) |

### Exemplos

Cadastrar produto:

```bash
curl -X POST http://localhost:8000/api/produtos \
  -H 'Content-Type: application/json' \
  -d '{"nome": "Camiseta", "preco_venda": 79.90}'
```

Registrar compra:

```bash
curl -X POST http://localhost:8000/api/compras \
  -H 'Content-Type: application/json' \
  -d '{
    "fornecedor": "Fornecedor X",
    "produtos": [
      {"id": 1, "quantidade": 10, "preco_unitario": 30}
    ]
  }'
```

Registrar venda:

```bash
curl -X POST http://localhost:8000/api/vendas \
  -H 'Content-Type: application/json' \
  -d '{
    "cliente": "Fulano da Silva",
    "produtos": [
      {"id": 1, "quantidade": 5, "preco_unitario": 80}
    ]
  }'
```

## Regras de negócio

### Custo médio ponderado

Toda compra atualiza o custo médio do produto:

```
custo_novo = (estoque_atual * custo_atual + qtd_entrada * preco_entrada)
             / (estoque_atual + qtd_entrada)
```

Persistido em `decimal(12,4)` para evitar drift.

### Lucro da venda

No momento da venda, o custo médio atual é **snapshot** no `custo_unitario` do item
e nunca é alterado depois. Assim, compras futuras que mudem o custo médio do produto
não distorcem o lucro histórico das vendas anteriores.

```
lucro_item = (preco_venda_efetivo - custo_unitario_snapshot) * quantidade
lucro_venda = soma dos lucro_item
```

### Cancelamento de venda

Cancelamento simplesmente **repõe a quantidade** no estoque e marca a venda com
status `CANCELADA`. O custo médio **não é recalculado**, é uma decisão deliberada
para preservar a legibilidade histórica em troca de uma imprecisão mínima.

### Concorrência

Compras e vendas usam `DB::transaction` + `Produto::lockForUpdate()` para evitar
race conditions em operações simultâneas no mesmo produto.

## Testes

```bash
docker compose exec backend ./vendor/bin/pest
```

A suíte cobre:

- Cadastro/listagem/validação de produtos
- Cálculo do custo médio ponderado em compras subsequentes
- Cálculo do lucro com snapshot do custo
- Erro 422 quando estoque é insuficiente
- Cancelamento revertendo estoque sem mexer no custo médio
- Filtro de vendas por status

Os testes rodam contra o banco `erp_test` (separado do banco de desenvolvimento).

## Notas

- O frontend usa o `custo_medio` do produto para mostrar **lucro estimado em tempo real**
  enquanto a venda é digitada. O número final retornado pelo backend pode diferir
  ligeiramente se houver compras concorrentes entre a digitação e o submit.
- As validações Laravel retornam status 422 com `{message, errors}`. A exception
  `EstoqueInsuficienteException` retorna 422 com `{erro, produto_id, disponivel, solicitado}`
  para o front exibir mensagens específicas.


<img width="1512" height="982" alt="image" src="https://github.com/user-attachments/assets/ab8b9b96-9aa6-4c5e-a0fa-27bf97ccd188" />


