#!/bin/bash

# Sair em caso de erro
set -e

echo "🚀 Iniciando configuração do Laravel..."

# Copiar o .env se não existir
if [ ! -f /var/www/.env ]; then
    cp /var/www/.env.example /var/www/.env
fi

# Criar o banco SQLite se não existir
if [ ! -f /var/www/database/database.sqlite ]; then
    mkdir -p /var/www/database
    touch /var/www/database/database.sqlite
fi

# Instalar dependências do Composer
composer install --no-interaction --optimize-autoloader

# Gerar chave do Laravel
php artisan key:generate

# Rodar migrations
php artisan migrate --force

# Ajustar permissões para o Laravel
chmod -R 777 /var/www/storage /var/www/bootstrap/cache

# Iniciar PHP-FPM
exec "$@"