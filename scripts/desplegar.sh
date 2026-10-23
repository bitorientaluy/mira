#!/usr/bin/env bash
# Despliega o actualiza MIRA en el servidor (SRV-POSIX, /opt/mira).
# Se puede correr las veces que haga falta: las migraciones y los seeders no repiten lo ya hecho.
#
# Uso:  sudo bash scripts/desplegar.sh          (despliegue normal)
#       sudo bash scripts/desplegar.sh --demo   (además crea o actualiza los usuarios demo)

set -euo pipefail

# Docker equivale a root, por eso el usuario mira no está en el grupo docker: el script corre con sudo
if [ "$(id -u)" -ne 0 ]; then
    exec sudo bash "$0" "$@"
fi

cd "$(dirname "$0")/.."

DEMO=0
[ "${1:-}" = "--demo" ] && DEMO=1

paso() { printf '\n==> %s\n' "$1"; }
artisan() { docker compose exec -T app php artisan "$@"; }

for archivo in .env src/.env; do
    if [ ! -f "$archivo" ]; then
        echo "Falta $archivo: sin él MySQL arranca sin contraseña. Copiarlo desde $archivo.example." >&2
        exit 1
    fi
done

paso "Levantando contenedores (espera a que la base esté healthy)"
# --wait cubre la primera inicialización de MySQL, que en la VM tarda más que el healthcheck
docker compose up -d --remove-orphans --wait --wait-timeout 300

paso "Instalando dependencias (vendor/ no está en el repo)"
docker compose exec -T app composer install --no-dev --optimize-autoloader --no-interaction

if grep -qE '^APP_KEY=\s*$' src/.env; then
    paso "Generando APP_KEY"
    artisan key:generate --force
fi

# Sin esto, una configuración cacheada vieja puede seguir usando datos anteriores del .env
paso "Limpiando cachés"
artisan optimize:clear

paso "Migraciones"
artisan migrate --force

paso "Datos base (roles, área y categorías)"
artisan db:seed --force

if [ "$DEMO" -eq 1 ]; then
    paso "Usuarios demo (clave: DEMO_PASSWORD de src/.env)"
    artisan db:seed --class=UsuarioDemoSeeder --force
fi

paso "Cacheando configuración, rutas y vistas"
artisan optimize

# Va al final: artisan corre como root dentro del contenedor y PHP-FPM (www-data) tiene que poder escribir
paso "Permisos de storage y bootstrap/cache"
docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache

paso "Estado"
docker compose ps
artisan migrate:status
