ARG PHP_VERSION=8.1-fpm-alpine3.16
ARG NGINX_VERSION=1.22-alpine
ARG TRAEFIK_VERSION=v2.10

### ### ###
FROM php:${PHP_VERSION} AS php_base

RUN echo "UTC" > /etc/timezone

RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
    	postgresql-dev \
        php8-intl \
    	php8-pecl-apcu \
        php8-json \
        php8-xml \
	    bash \
		make \
		curl \
	    gcc \
		g++ \
		icu-dev \
	    autoconf

RUN docker-php-ext-configure intl && docker-php-ext-install intl
RUN docker-php-ext-install pdo pdo_pgsql pdo_mysql bcmath pcntl

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

COPY --from=composer:2.6.5 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/php.ini

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | bash \
    && apk add symfony-cli
RUN symfony -V
RUN symfony check:requirements
RUN symfony server:ca:install

EXPOSE 9000

WORKDIR /app

### ### ###
FROM php_base AS composer_base

COPY ./app/composer.* .

### ### ###
FROM composer_base AS composer_install

CMD composer install --prefer-dist


### ### ###
FROM composer_base AS composer_update

CMD composer update --prefer-dist

### ### ###
FROM nginx:${NGINX_VERSION} AS nginx_server

WORKDIR /app

CMD ["nginx"]

EXPOSE 80

### ### ###
FROM traefik:${TRAEFIK_VERSION} AS reverse_proxy

RUN apk update && apk add apache2-utils
