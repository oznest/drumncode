FROM php:8.2-fpm

# Устанавливаем рабочую директорию
WORKDIR /var/www/symfony

# Устанавливаем необходимые пакеты
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install opcache

## Устанавливаем Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN git config --global user.email "oznest@i.ua" \
    && git config --global user.name "Olexiy"

ENV PATH="$HOME/.symfony5/bin:$PATH"
#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug
# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Открываем порт
EXPOSE 9000

CMD ["php-fpm"]