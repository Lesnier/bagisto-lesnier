#!/bin/bash

# =============================================================
# deploy.sh — Script de despliegue para Bagisto en producción
# Ubicación en el servidor: /home/keywordcv/test.keywordcv.com/deploy.sh
# =============================================================

set -e

APP_DIR="/home/keywordcv/test.keywordcv.com"

echo "🚀 Iniciando deploy..."

cd "$APP_DIR"

echo "⏸️  Modo mantenimiento ON"
php artisan down --refresh=15 --retry=60

echo "📦 Pulling desde GitHub..."
git pull origin main

echo "📚 Instalando dependencias..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "🧹 Limpiando cachés..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

echo "🗄️  Ejecutando migraciones..."
php artisan migrate --force

echo "⚡ Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache

echo "🔗 Enlazando storage..."
php artisan storage:link 2>/dev/null || true

echo "🔒 Ajustando permisos..."
chmod -R 775 storage bootstrap/cache

echo "✅ Modo mantenimiento OFF"
php artisan up

echo "🎉 Deploy completado exitosamente."
