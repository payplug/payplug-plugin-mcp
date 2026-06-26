FROM composer:2.2 AS composer

FROM php:7.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install xdebug-3.1.6 \
    && docker-php-ext-enable xdebug

RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_port=9003"              >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.start_with_request=trigger"    >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

RUN useradd -m -u 1000 appuser && chown appuser:appuser /app

HEALTHCHECK --interval=30s --timeout=10s --retries=3 CMD ["php", "--version"]

USER appuser

CMD ["php", "-a"]
