FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
        libcurl4-openssl-dev \
        libbrotli-dev \
        libc-ares-dev \
        libssl-dev \
        apt-utils \
        curl \
        wget \
        zip \
        git \
        nano \
        supervisor \
        npm

RUN docker-php-ext-configure pcntl --enable-pcntl
RUN docker-php-ext-install pdo pdo_mysql pcntl

RUN pecl install xdebug \
        redis

RUN printf "\n" | pecl install swoole

RUN docker-php-ext-enable xdebug \
        redis \
        swoole

RUN mkdir -p /var/log/supervisor

COPY . /app
COPY ./docker/supervisord /etc/supervisor/conf.d/
COPY ./docker/php "${PHP_INI_DIR}/conf.d/"

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY ./docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]