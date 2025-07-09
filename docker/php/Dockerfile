FROM dunglas/frankenphp

WORKDIR /app

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install opcache pdo_mysql

RUN pecl install apcu \
    && docker-php-ext-enable apcu

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Symfony CLI (по желанию)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony