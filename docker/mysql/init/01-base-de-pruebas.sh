#!/bin/sh
# Crea la base de pruebas (<DB_DATABASE>_test) que usa phpunit.xml, para que los tests
# no borren los datos de desarrollo. MySQL ejecuta este script solo al inicializar un volumen vacío;
# en un volumen existente se corre a mano (ver README).
set -e

mysql -u root -p"$MYSQL_ROOT_PASSWORD" <<SQL
CREATE DATABASE IF NOT EXISTS \`${MYSQL_DATABASE}_test\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON \`${MYSQL_DATABASE}_test\`.* TO '${MYSQL_USER}'@'%';
FLUSH PRIVILEGES;
SQL
