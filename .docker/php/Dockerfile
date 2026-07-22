FROM php:8.4-fpm-alpine

ARG TZ='America/Sao_Paulo'
ENV DEFAULT_TZ ${TZ}

ENV PHPIZE_DEPS \
    autoconf \
    dpkg-dev dpkg \
    file \
    g++ \
    gcc \
    libc-dev \
    make \
    pkgconf \
    re2c \
    build-base \
    openssl-dev

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/bin/

RUN set -euxo pipefail ;\
    apk upgrade --update-cache ;\
    apk add --no-cache \
    bash \
    nano \
    tzdata \
    git \
    zip \
    unzip \
    icu-dev \
    libzip-dev \
    oniguruma-dev

RUN docker-php-ext-install pdo pdo_mysql bcmath intl zip pcntl

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis \
    && apk del -f .build-deps

COPY ./conf/90-xdebug.ini "${PHP_INI_DIR}/conf.d/90-xdebug.ini"

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod a+x /usr/local/bin/composer

RUN set -euxo pipefail ;\
    cp /usr/share/zoneinfo/${DEFAULT_TZ} /etc/localtime ;\
    apk del tzdata ;\
    rm -rf /var/cache/apk/* ;\
    rm -rf /root/.cache

RUN addgroup -g 1000 web && adduser -G web -g web -s /bin/sh -D web

RUN chown -R web:web /var/www/html

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

ADD ./conf/fpm-www.conf /usr/local/etc/php-fpm.d/www.conf
ADD ./conf/ini-changes.ini /usr/local/etc/php/conf.d/ini-changes.ini
ADD ./conf/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
