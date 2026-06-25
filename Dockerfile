FROM composer:2.2 AS composer

FROM php:7.1-cli

# Debian Buster is EOL — redirect to archive repos before any apt call
RUN sed -i 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' /etc/apt/sources.list \
    && sed -i 's|http://security.debian.org/debian-security|http://archive.debian.org/debian-security|g' /etc/apt/sources.list \
    && sed -i '/buster-updates/d' /etc/apt/sources.list

RUN apt-get update && apt-get install -y --no-install-recommends \
    git=1:2.20.1-2+deb10u8 \
    unzip=6.0-23+deb10u3 \
    curl=7.64.0-4+deb10u9 \
    && rm -rf /var/lib/apt/lists/*

# Xdebug 2.9.8 — last release supporting PHP 7.1 (Xdebug 3.x requires PHP >=7.2)
RUN pecl install xdebug-2.9.8 \
    && docker-php-ext-enable xdebug

# Xdebug 2.x config — debug off by default
# To enable step-debug: XDEBUG_CONFIG="remote_enable=1 remote_autostart=1"
RUN echo "xdebug.remote_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_port=9003"              >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.remote_autostart=0"            >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /app

RUN useradd -m -u 1000 appuser && chown appuser:appuser /app

HEALTHCHECK --interval=30s --timeout=10s --retries=3 CMD ["php", "--version"]

USER appuser

CMD ["php", "-a"]
