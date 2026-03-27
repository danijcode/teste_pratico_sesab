#!/bin/bash
set -e

echo "==> Configurando ambiente..."
cp .env.example .env

# Injeta variáveis do Render no .env
[ -n "$APP_KEY" ]      && sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" .env
[ -n "$APP_URL" ]      && sed -i "s|APP_URL=.*|APP_URL=$APP_URL|" .env
[ -n "$FRONTEND_URL" ] && echo "FRONTEND_URL=$FRONTEND_URL" >> .env

# Força modo produção
sed -i "s|APP_ENV=.*|APP_ENV=production|"   .env
sed -i "s|APP_DEBUG=.*|APP_DEBUG=false|"    .env
sed -i "s|SESSION_DRIVER=.*|SESSION_DRIVER=file|" .env
sed -i "s|CACHE_STORE=.*|CACHE_STORE=file|" .env

# Gera APP_KEY se não fornecida via variável de ambiente
if [ -z "$APP_KEY" ]; then
  echo "==> Gerando APP_KEY..."
  php artisan key:generate --force
fi

# Cria o banco SQLite
echo "==> Criando banco de dados SQLite..."
touch /app/database/database.sqlite

# Limpa caches de config
php artisan config:clear
php artisan cache:clear

# Migrations + Seeders (sempre parte do zero — demo stateless)
echo "==> Executando migrations e seeders..."
php artisan migrate:fresh --seed --force

echo "==> Servidor iniciando na porta ${PORT:-8000}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
