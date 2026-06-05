#!/bin/sh
set -e

cd /var/www/html

# Garante .env (cópia do .env.example se ainda não existir).
if [ ! -f .env ]; then
  cp .env.example .env
fi

# Gera APP_KEY se vier sem chave.
if ! grep -q "^APP_KEY=base64:" .env; then
  php artisan key:generate --force
fi

# Storage publicado (idempotente).
php artisan storage:link 2>/dev/null || true

# Migra ao subir (idempotente — se não há nada novo, é no-op).
php artisan migrate --force

exec "$@"
