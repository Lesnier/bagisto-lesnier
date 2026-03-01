#!/bin/bash

# =============================================================
# deploy.sh — Script de despliegue para Bagisto en producción
# Ubicación en el servidor: /home/keywordcv/test.keywordcv.com/deploy.sh
# =============================================================

set -e  # Detiene el script si cualquier comando falla

# Cargar el entorno del usuario (necesario en sesiones SSH no-interactivas)
export HOME="${HOME:-/home/keywordcv}"
[ -f "$HOME/.bashrc" ] && source "$HOME/.bashrc"
[ -f "$HOME/.bash_profile" ] && source "$HOME/.bash_profile"

# Asegurar que ~/bin y rutas comunes de composer estén en el PATH
export PATH="$HOME/bin:/usr/local/bin:/usr/bin:/bin:$PATH"

# Detectar composer: global, local, cPanel o como phar
if command -v composer &>/dev/null; then
    COMPOSER="composer"
elif [ -f "$HOME/bin/composer" ]; then
    COMPOSER="$HOME/bin/composer"
elif [ -f "/usr/local/bin/composer" ]; then
    COMPOSER="/usr/local/bin/composer"
elif [ -f "/opt/cpanel/ea-wappspector/composer.phar" ]; then
    COMPOSER="php /opt/cpanel/ea-wappspector/composer.phar"
elif [ -f "$(pwd)/composer.phar" ]; then
    COMPOSER="php $(pwd)/composer.phar"
else
    echo "❌ Composer no encontrado. Instálalo primero."
    exit 1
fi

APP_DIR="/home/keywordcv/test.keywordcv.com"

echo "🚀 Iniciando deploy..."

cd "$APP_DIR"

# 1. Activar modo mantenimiento para que los usuarios vean una pantalla amigable
echo "⏸️  Modo mantenimiento ON"
php artisan down --refresh=15 --retry=60

# 2. Traer los últimos cambios de main
echo "📦 Pulling desde GitHub..."
git pull origin main

# 3. Instalar/actualizar dependencias PHP (sin dev en producción)
echo "📚 Instalando dependencias..."
$COMPOSER install --no-dev --optimize-autoloader --no-interaction

# 4. Limpiar todos los cachés de Laravel
echo "🧹 Limpiando cachés..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# 5. Ejecutar migraciones pendientes
echo "🗄️  Ejecutando migraciones..."
php artisan migrate --force

# 6. Re-cachear todo para producción
echo "⚡ Cacheando configuración..."
php artisan config:cache
php artisan route:cache
php artisan event:cache
php artisan view:cache

# 7. Enlace del storage público
echo "🔗 Enlazando storage..."
php artisan storage:link 2>/dev/null || true

# 8. Ajustar permisos
echo "🔒 Ajustando permisos..."
chmod -R 775 storage bootstrap/cache
chown -R keywordcv:keywordcv storage bootstrap/cache 2>/dev/null || true

# 9. Desactivar modo mantenimiento
echo "✅ Modo mantenimiento OFF"
php artisan up

echo "🎉 Deploy completado exitosamente."
