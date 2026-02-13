# Plataforma Prof. Jubilut - Laravel
FROM php:8.2-cli

# Dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    default-mysql-client \
    && docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Cópia do projeto (ignorando vendor e .env no build; serão gerados no run)
# Com dev deps para o seed (Faker) e ferramentas funcionarem
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

COPY . .
# Remove cache para Laravel redescobrir pacotes na imagem
RUN rm -f bootstrap/cache/config.php bootstrap/cache/packages.php bootstrap/cache/services.php
RUN cp .env.docker .env 2>/dev/null || true
RUN composer dump-autoload --optimize --no-scripts --ignore-platform-reqs
RUN mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache && chmod -R 775 storage bootstrap/cache

# Script que espera o MySQL, roda migrações e inicia o servidor
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
