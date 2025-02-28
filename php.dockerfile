FROM php:8.3.7-fpm-alpine

RUN apk add --no-cache linux-headers
RUN apk --no-cache upgrade && \
    apk --no-cache add bash git sudo openssh libxml2-dev oniguruma-dev autoconf gcc g++ make npm freetype-dev libjpeg-turbo-dev libpng-dev libzip-dev ssmtp libssh2 libssh2-dev icu-dev libpq-dev

# PHP: Install php extensions
RUN pecl channel-update pecl.php.net
RUN pecl install pcov ssh2 swoole
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install mbstring xml pcntl gd zip sockets pdo  pdo_mysql bcmath soap pgsql pdo_pgsql intl
RUN docker-php-ext-enable mbstring xml gd zip pcov pcntl sockets bcmath pdo  pdo_mysql soap swoole

RUN docker-php-ext-install pdo pdo_mysql sockets
RUN curl -sS https://getcomposer.org/installer​ | php -- \
     --install-dir=/usr/local/bin --filename=composer

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY --from=spiralscout/roadrunner:2.4.2 /usr/bin/rr /usr/bin/rr

WORKDIR /

EXPOSE  8000