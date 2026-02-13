#!/bin/sh
set -e

# Esperar o MySQL ficar disponível
echo "Aguardando MySQL..."
until php -r "
    try {
        new PDO(
            'mysql:host=${DB_HOST:-mysql};port=${DB_PORT:-3306}',
            '${DB_USERNAME:-laravel}',
            '${DB_PASSWORD:-secret}'
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    sleep 2
done
echo "MySQL disponível."

# Gerar chave se não existir
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    php artisan key:generate --force
fi

# Migrações e seed
php artisan migrate --force
php artisan db:seed --force

exec "$@"
