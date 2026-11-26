# Exponential 6 container image: the installation built with Composer on the
# official PHP image (Debian), served by Apache with PHP-FPM (target "apache",
# the default) or by Velocity (target "velocity"). Both run as www-data and
# listen on port 8080. Published as ghcr.io/se7enxweb/exponential.
#
#   docker build -f docker/Dockerfile -t exponential:local .
#   docker build -f docker/Dockerfile --target velocity -t exponential:local-velocity .
#   docker build -f docker/Dockerfile --build-arg PHP_VERSION=8.3 -t exponential:php8.3 .
#
# PHP_VERSION is 8.0 to 8.5 (8.5 by default); DEBIAN is the base release of
# the official PHP image (bookworm; bullseye for PHP 8.0, which has no newer
# one). Nothing secret is built in: the database settings are written at the
# first start, from the environment (docker/entrypoint.sh). Guide:
# doc/eng-us/help/hosting/models/containers-docker-and-podman.md.

ARG PHP_VERSION=8.5
ARG DEBIAN=bookworm

FROM composer:2.11.2 AS composer
FROM mlocati/php-extension-installer:2.13.1 AS extension-installer

# ---------------------------------------------------------------------------
# base: PHP-FPM, Apache and every PHP extension Exponential and Velocity use
# ---------------------------------------------------------------------------
FROM php:${PHP_VERSION}-fpm-${DEBIAN} AS base

COPY --from=extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN set -eux; \
    apt-get update; \
    apt-get install -y --no-install-recommends apache2 tini curl ca-certificates unzip; \
    install-php-extensions \
        apcu bcmath exif gd intl mysqli opcache pcntl pdo_mysql pdo_pgsql pgsql \
        redis sockets soap xsl zip; \
    php -m | grep -qi '^xsl$'; \
    apt-get clean; \
    rm -rf /var/lib/apt/lists/* /tmp/* /usr/src/php*; \
    a2enmod -q rewrite headers expires proxy proxy_fcgi setenvif remoteip; \
    a2dissite -q 000-default; \
    a2disconf -q other-vhosts-access-log serve-cgi-bin 2>/dev/null || true

COPY docker/php/exponential.ini "$PHP_INI_DIR/conf.d/zz-exponential.ini"
COPY docker/php/zzz-exponential-fpm.conf /usr/local/etc/php-fpm.d/zzz-exponential.conf
COPY docker/apache/exponential.conf /etc/apache2/sites-available/exponential.conf
COPY docker/apache/ports.conf /etc/apache2/ports.conf
RUN set -eux; \
    a2ensite -q exponential; \
    # Apache and PHP-FPM run as www-data: their run, lock and log
    # directories belong to it, and both log to the container's output.
    mkdir -p /run/php /var/run/apache2 /var/lock/apache2 /var/log/apache2; \
    chown www-data:www-data /run/php /var/run/apache2 /var/lock/apache2 /var/log/apache2; \
    # Velocity's configuration tree, laid out like Debian's /etc/apache2
    # (vc.conf, ports.conf, sites-available ...), written by exp:velocity
    # on every start, and its state, log and run directories.
    mkdir -p /etc/vc /var/lib/vc /var/log/vc /var/cache/vc /run/vc; \
    chown www-data:www-data /etc/vc /var/lib/vc /var/log/vc /var/cache/vc /run/vc; \
    rm -f "$PHP_INI_DIR/php.ini"; cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# ---------------------------------------------------------------------------
# build: composer install of the checked-out sources
# ---------------------------------------------------------------------------
FROM base AS build
ARG COMPOSER_ROOT_VERSION=dev-main
ENV COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_ROOT_VERSION=${COMPOSER_ROOT_VERSION}
COPY --from=composer /usr/bin/composer /usr/local/bin/composer
WORKDIR /var/www/exponential
COPY . .
RUN set -eux; \
    for attempt in 1 2 3; do \
        composer install --no-dev --prefer-dist --no-progress --no-interaction \
            --ignore-platform-req=ext-mongodb --ignore-platform-req=ext-oci8 && break; \
        [ "$attempt" = 3 ] && exit 1; sleep $((attempt * 15)); \
    done; \
    composer clear-cache; \
    # What the image must not carry: the Git metadata and the test suites.
    rm -rf .git tests; \
    # The extension autoloads of the image's own extensions; the first
    # installation brings them up to date again.
    php bin/php/ezpgenerateautoloads.php -e >/dev/null 2>&1 || true; \
    mkdir -p var settings/override settings/siteaccess

# ---------------------------------------------------------------------------
# runtime: what both targets share
# ---------------------------------------------------------------------------
FROM base AS runtime
ARG EXP_IMAGE_VERSION=dev
LABEL org.opencontainers.image.title="Exponential" \
      org.opencontainers.image.description="Exponential 6 CMS, served by Apache with PHP-FPM or by Velocity" \
      org.opencontainers.image.source="https://github.com/se7enxweb/exponential" \
      org.opencontainers.image.url="https://doc.se7enx.com/" \
      org.opencontainers.image.vendor="7x" \
      org.opencontainers.image.licenses="GPL-2.0-or-later" \
      org.opencontainers.image.version="${EXP_IMAGE_VERSION}"

COPY --from=build --chown=root:root /var/www/exponential /var/www/exponential
COPY docker/entrypoint.sh /usr/local/bin/exponential-entrypoint
COPY docker/healthcheck.sh /usr/local/bin/exponential-healthcheck
RUN set -eux; \
    chmod 0755 /usr/local/bin/exponential-entrypoint /usr/local/bin/exponential-healthcheck; \
    cd /var/www/exponential; \
    # The code belongs to root and is read-only for the server; what an
    # installation writes belongs to www-data: var/ (storage, caches, logs,
    # the SQLite database), the settings the installer writes and the
    # autoload arrays it regenerates.
    chown -R www-data:www-data var settings/override settings/siteaccess autoload; \
    # exp:install writes its kickstart.ini into the root for the run and
    # removes it again, and the setup's permission check wants the root,
    # design/, extension/ and settings/ writable: these directories (not
    # their contents) are group-writable for www-data with the sticky bit, so
    # www-data can add and remove files of its own there but never remove or
    # replace a file of the code.
    chown root:www-data . design extension settings; \
    chmod 1775 . design extension settings; \
    echo "${EXP_IMAGE_VERSION}" > /usr/local/share/exponential-image-version

ENV EXP_SERVER=apache \
    EXP_INSTALL=auto
WORKDIR /var/www/exponential
VOLUME ["/var/www/exponential/var", "/var/www/exponential/settings/override", "/var/www/exponential/settings/siteaccess"]
USER www-data
EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=300s --retries=3 \
    CMD ["/usr/local/bin/exponential-healthcheck"]
ENTRYPOINT ["/usr/bin/tini", "--", "/usr/local/bin/exponential-entrypoint"]
CMD ["serve"]

# ---------------------------------------------------------------------------
# velocity: the installation served by Velocity (its qbix engine)
# ---------------------------------------------------------------------------
FROM runtime AS velocity
ENV EXP_SERVER=velocity

# ---------------------------------------------------------------------------
# apache: Apache with PHP-FPM. The last stage, so the default target.
# ---------------------------------------------------------------------------
FROM runtime AS apache
