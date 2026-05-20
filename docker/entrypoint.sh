#!/bin/bash
set -e

DB_HOST="${DB_HOST:-db}"
DB_PORT="${DB_PORT:-3306}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-root}"

echo "Aguardando MySQL em ${DB_HOST}:${DB_PORT}..."
for i in $(seq 1 60); do
  if mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; then
    break
  fi
  sleep 2
done

if [ -f /var/www/html/database/schema.sql ]; then
  echo "Aplicando schema (se necessário)..."
  mysql -h "$DB_HOST" -P "$DB_PORT" -u"$DB_USERNAME" -p"$DB_PASSWORD" < /var/www/html/database/schema.sql 2>/dev/null || true
fi

if [ -f /var/www/html/bin/migrate.php ]; then
  echo "Executando migrations pendentes..."
  php /var/www/html/bin/migrate.php || true
fi

exec apache2-foreground
