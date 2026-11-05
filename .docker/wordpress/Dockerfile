ARG PHP_VERSION=8.2
ARG WP_VERSION=6
ARG HOST_UID=1000
ARG HOST_GID=1000

FROM wordpress:${WP_VERSION}-php${PHP_VERSION}

ARG HOST_UID
ARG HOST_GID

# The container user writes in the package folder, map it to the host user
RUN set -eux; \
    groupmod -o -g "${HOST_GID}" www-data; \
    usermod -o -u "${HOST_UID}" -g "${HOST_GID}" www-data; \
    chown -R www-data:www-data /var/www

# pcov for the code coverage, built from the GitHub source, pecl.php.net refuses the CI runners
ARG PCOV_VERSION=1.0.12
RUN set -eux; \
    curl -fsSL "https://github.com/krakjoe/pcov/archive/refs/tags/v${PCOV_VERSION}.tar.gz" | tar -xz -C /tmp; \
    cd "/tmp/pcov-${PCOV_VERSION}"; \
    phpize; \
    ./configure --enable-pcov; \
    make -j"$(nproc)"; \
    make install; \
    cd /; \
    rm -rf "/tmp/pcov-${PCOV_VERSION}"; \
    docker-php-ext-enable pcov

RUN set -eux; \
    apt-get update && apt-get install -y git unzip zip; \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql

RUN curl -sS https://getcomposer.org/installer -o /tmp/composer-setup.php && \
    php /tmp/composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm /tmp/composer-setup.php
