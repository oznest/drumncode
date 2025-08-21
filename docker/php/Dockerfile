FROM dunglas/frankenphp

WORKDIR /app

RUN install-php-extensions \
    pdo_mysql \
    opcache \
    curl \
    apcu \
    xdebug \
    amqp \
    intl \
    opentelemetry

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libpq-dev

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
COPY apcu.ini /usr/local/etc/php/conf.d/apcu.ini

# Symfony CLI (по желанию)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony