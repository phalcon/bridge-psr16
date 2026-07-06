# syntax=docker/dockerfile:1
ARG PHP_VERSION=8.5

FROM php:${PHP_VERSION}-cli AS base

ARG PHP_VERSION=8.5
ARG PHALCON_VARIANT=package
ARG PHALCON_V5_CONSTRAINT="^5.0"
ARG UID=1000
ARG GID=1000
ARG USER=phalcon
ARG GROUP=phalcon

# System packages + the PHP extensions a Phalcon bridge needs + an unprivileged user.
RUN <<EOT
set -eux
groupadd -g "${GID}" "${GROUP}"
useradd -l -m -u "${UID}" -g "${GID}" -s /bin/bash "${USER}"
apt-get update -y
apt-get install -yq --no-install-recommends \
    curl git unzip zip libonig-dev libzip-dev locales
sed -i -e 's/# en_US.UTF-8 UTF-8/en_US.UTF-8 UTF-8/' /etc/locale.gen
dpkg-reconfigure --frontend=noninteractive locales
update-locale LANG=en_US.UTF-8
docker-php-ext-install mbstring zip
git config --system --add safe.directory /srv
apt-get clean -y
rm -rf /var/lib/apt/lists/*
EOT

COPY --from=composer/composer:2 /usr/bin/composer /usr/bin/composer

# pcov (coverage) + optional cphalcon C extension (ext backend only).
RUN <<EOT
set -eux
apt-get update -y
apt-get install -yq --no-install-recommends ${PHPIZE_DEPS} libtool
curl -fsSL https://github.com/php/pie/releases/latest/download/pie.phar -o /usr/local/bin/pie
chmod +x /usr/local/bin/pie
pie install --no-interaction pecl/pcov
if [ "${PHALCON_VARIANT}" = "ext" ]; then
    pie install --no-interaction phalcon/cphalcon:${PHALCON_V5_CONSTRAINT}
    php -m | grep -i phalcon
fi
apt-get clean -y
rm -rf /var/lib/apt/lists/*
EOT

COPY resources/docker/extra.ini /usr/local/etc/php/conf.d/zz-bridge-psr16.ini
COPY --chown=${UID}:${GID} resources/docker/.bashrc /home/${USER}/.bashrc

ENV HOME=/home/${USER} \
    COMPOSER_HOME=/home/${USER}/.composer \
    LANG=en_US.UTF-8 \
    LANGUAGE=en_US.UTF-8 \
    LC_ALL=en_US.UTF-8

RUN install -d -m 0775 -o "${USER}" -g "${GROUP}" /srv
WORKDIR /srv
USER ${USER}

# No app code or vendor baked in: the compose bind mount provides /srv at runtime,
# and `composer install` runs there. Keepalive so `docker compose up -d` holds the
# container open for `exec`; `docker compose run --rm app <cmd>` overrides for one-offs.
CMD ["sleep", "infinity"]
