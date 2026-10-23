# mira
Mapa de incidencia, reportes y atención

## Entorno de desarrollo (Docker)

Tres contenedores, basados en la guía "Laravel 13 con Docker" del curso:

| Servicio | Imagen | Función |
|---|---|---|
| `app` | PHP 8.4-FPM (Dockerfile) | Ejecuta Laravel, Composer y Artisan |
| `nginx` | nginx 1.27 | Sirve `public/` y pasa PHP a `app:9000`. Puerto `8080` |
| `db` | MySQL 8.0 | Base de datos. **Sin puerto publicado**: solo se accede desde la red de Compose |

```
mira/
├── docker-compose.yml
├── Dockerfile
├── .env.example         variables de Compose (copiar a .env)
├── docker/nginx/default.conf
└── src/                 proyecto Laravel
```

### Primera vez

Requisito: Docker Desktop corriendo.

```bash
cp .env.example .env                 # y cambiar las contraseñas
docker compose up -d --build
docker compose ps                    # app, nginx y db en "Up" (db en "healthy")
docker compose exec app bash
```

Dentro del contenedor, solo si `src/` todavía no tiene el proyecto:

```bash
composer create-project laravel/laravel .
chmod -R 775 storage bootstrap/cache && chown -R www-data:www-data storage bootstrap/cache
```

En `src/.env`, el bloque de la base tiene que coincidir con el `.env` de la raíz:

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=mira
DB_USERNAME=mira_app
DB_PASSWORD=<la misma de .env>
```

`DB_HOST=db` es el nombre del servicio en la red de Docker (no `localhost`), y el puerto es el interno de MySQL.

```bash
php artisan key:generate
php artisan migrate
```

Abrir http://localhost:8080: si aparece la bienvenida de Laravel, el circuito Navegador → Nginx → PHP-FPM → Laravel → MySQL funciona.

### Uso diario

```bash
docker compose up -d
docker compose exec app php artisan <comando>
docker compose down              # apaga; los datos de MySQL quedan en el volumen dbdata
```

### Pruebas

PHPUnit sobre una base MySQL separada, `<DB_DATABASE>_test` (por ejemplo `mira_test`), para no borrar los datos de desarrollo. Se usa MySQL y no SQLite porque las migraciones tienen tipos espaciales y CHECK.

```bash
docker compose exec app php artisan test
docker compose exec app php artisan test --filter=LoginTest   # una clase
```

La base de pruebas la crea `docker/mysql/init/01-base-de-pruebas.sh` cuando MySQL inicializa un volumen vacío. En un volumen que ya existía, crearla una vez a mano:

```bash
docker compose exec -T db sh < docker/mysql/init/01-base-de-pruebas.sh
```

Los nombres de las pruebas llevan el ID del plan de pruebas (`test_cp15_...`, PU-XX en el docblock).

### Script SQL

`src/database/sql/esquema.sql` (las 10 tablas del dominio) y `datos_prueba.sql` (roles, área, categorías y usuarios demo) se generan con `mysqldump` a partir de las migraciones y los seeders, que siguen siendo la fuente. Sirven para crear la base sin Laravel:

```bash
docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < src/database/sql/esquema.sql
docker compose exec -T db sh -c 'mysql -uroot -p"$MYSQL_ROOT_PASSWORD" "$MYSQL_DATABASE"' < src/database/sql/datos_prueba.sql
```

`esquema.sql` borra y recrea las tablas: no correrlo sobre una base con datos que se quieran conservar.

### Errores comunes

- **"port is already allocated"**: cambiar `APP_PORT` en `.env`. Para ver quién usa el puerto: `netstat -ano | findstr :8080`.
- **502 Bad Gateway**: permisos de `storage` y `bootstrap/cache` (ver "Primera vez").
- **"connection refused" al migrar**: revisar `DB_HOST=db` y `DB_PORT=3306`, o esperar a que `db` figure como `healthy`.

### Ramas y commits

Una rama por tarea desde `main` (`feature/T-111-reporte-mapa`, `infra/T-112-shorewall`), commits con el código de trazabilidad (`T-108-DFB-V0.1: ...`) y PR a `main` revisado por el otro integrante.
