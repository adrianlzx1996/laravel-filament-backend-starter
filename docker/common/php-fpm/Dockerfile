FROM php:8.4-fpm-alpine AS composer_builder

RUN apk --no-cache add \
    bash \
    curl \
    zip \
    unzip \
    icu-dev \
    libpq-dev \
    libzip-dev \
    openssh-client \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    pcntl \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2.10 /usr/bin/composer /usr/bin/composer

COPY . /app/
WORKDIR /app

ARG APP_ENV=production
ENV APP_ENV=$APP_ENV

# Dev mode: install dev dependencies + Xdebug
RUN if [ "$APP_ENV" = "local" ] ; then \
        composer install --no-interaction ; \
    else \
        composer install --no-interaction --no-dev --optimize-autoloader ; \
    fi

FROM node:24-alpine AS node_builder

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci

COPY . .
COPY --from=composer_builder /app/vendor /app/vendor
RUN npm run build

FROM php:8.4-fpm-alpine AS php_fpm

WORKDIR /var/www/app

RUN apk add --no-cache \
        bash \
        curl \
        zip \
        unzip \
        icu-dev \
        libpq-dev \
        libzip-dev \
        openssh-client \
    && docker-php-ext-install \
    pdo_pgsql \
    pgsql \
    intl \
    zip \
    pcntl \
    && rm -rf /var/cache/apk/*

ARG UID=1000
ARG GID=1000
ARG APP_ENV=production
ENV APP_ENV=$APP_ENV

# Create a new user with the specified UID and GID, reusing an existing group if GID exists
RUN if getent group ${GID}; then \
      group_name=$(getent group ${GID} | cut -d: -f1); \
    else \
      group_name=www && \
      addgroup -g ${GID} www; \
    fi  && \
    adduser -D -u ${UID} -G ${group_name} www  && \
    sed -i "s/user = www-data/user = www/g" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/group = www-data/group = $group_name/g" /usr/local/etc/php-fpm.d/www.conf

RUN if [ "$APP_ENV" = "local" ] ; then \
        apk add --no-cache $PHPIZE_DEPS linux-headers && \
        pecl install xdebug && \
        docker-php-ext-enable xdebug \
        && echo "xdebug.mode=develop,debug,coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
        && echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini ; \
    fi

RUN if [ "$APP_ENV" = "local" ] ; then \
        cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" ; \
    else \
        cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" ; \
    fi

COPY docker/common/php-fpm/conf.d/php.ini "$PHP_INI_DIR/conf.d/custom.ini"

COPY . /var/www/app
COPY --from=composer_builder /app/vendor /var/www/app/vendor
COPY --from=node_builder /app/public/build /var/www/app/public/build

RUN chown -R www:www /var/www/app

USER www

EXPOSE 9000
