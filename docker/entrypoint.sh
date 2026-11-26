#!/usr/bin/env bash
# Exponential 6 container image: the entry point.
#
#   serve      (default) install once when EXP_INSTALL=auto and no installation
#              is there yet, then serve on port 8080: Apache with PHP-FPM
#              (EXP_SERVER=apache) or Velocity (EXP_SERVER=velocity)
#   install    install once, as above, and stop
#   cron       run runcronjobs.php every EXP_CRON_INTERVAL seconds (300)
#   <command>  anything else runs as given, for example
#              bin/php/console exp:velocity ext check
#
# The installation is the test exp:install itself makes: a [DatabaseSettings]
# block in settings/override/site.ini.append.php. Nothing secret is in the
# image; the database password comes from EXP_DB_PASSWORD or, better, from a
# file named by EXP_DB_PASSWORD_FILE (a Docker or Podman secret). Every
# variable: doc/eng-us/help/hosting/models/containers-docker-and-podman.md.
set -euo pipefail

root=/var/www/exponential
cd "$root"
console=bin/php/console
override=settings/override/site.ini.append.php

log() { printf 'exponential: %s\n' "$*" >&2; }

# VAR_FILE wins over VAR, so a secret never has to be in the environment
file_env() {
    local var="$1" file_var="${1}_FILE"
    if [ -n "${!file_var:-}" ]; then
        [ -r "${!file_var}" ] || { log "$file_var names ${!file_var}, which cannot be read"; exit 1; }
        export "$var"="$(cat "${!file_var}")"
    fi
}

installed() {
    [ -f "$override" ] && grep -q '^[[:space:]]*\[DatabaseSettings\]' "$override"
}

setup_check_settings() {
    # The setup's permission check, as it applies in this image: the code is
    # root's, and only these directories are written (the default list also
    # wants every file in design/, extension/ and settings/ writable)
    local setup_ini=settings/override/setup.ini.append.php
    if [ ! -f "$setup_ini" ]; then
        cat > "$setup_ini" <<'EOF_INI'
<?php /* #?ini charset="utf-8"?
# Written by the container image: the directories the installation writes.
[directory_permissions]
CheckList=settings/override;settings/siteaccess;var;var/cache;var/cache/codepages;var/cache/content;var/cache/ini;var/cache/override;var/cache/template;var/cache/template/process;var/cache/template/tree;var/cache/texttoimage;var/cache/translation;var/log;var/storage;var/storage/original;var/storage/reference;var/storage/variations;var/autoload

[settings_permission]
CheckList=settings/override
*/ ?>
EOF_INI
    fi

}

install_once() {
    if installed; then
        return 0
    fi
    # Before any setup runs: exp:install here, or the web setup wizard
    setup_check_settings
    case "${EXP_INSTALL:-auto}" in
        auto|yes|1|true) ;;
        *) log "no installation yet and EXP_INSTALL=${EXP_INSTALL}: serving the setup wizard"; return 0 ;;
    esac
    if [ ! -f bin/php/install.php ]; then
        # Releases before 6.0.15 have no exp:install: the web setup wizard
        # installs them (open the site in a browser).
        log "no installation yet: open the site in a browser to run the setup wizard"
        return 0
    fi

    file_env EXP_DB_PASSWORD
    file_env EXP_ADMIN_PASSWORD
    local db="${EXP_DB:-sqlite}" action="${EXP_DB_ACTION:-}"
    if [ -z "$action" ]; then
        # A fresh SQLite file may be emptied; a database server is never
        # emptied unless asked (EXP_DB_ACTION=remove)
        case "$db" in sqlite|sqlite3) action=remove ;; *) action=ignore ;; esac
    fi
    # No crontab: a container runs its cron jobs with the command "cron"
    local args=( --apply "--db=$db" "--db-action=$action" --no-crontab )
    [ -n "${EXP_DB_HOST:-}" ] && args+=( "--db-host=$EXP_DB_HOST" )
    [ -n "${EXP_DB_PORT:-}" ] && args+=( "--db-port=$EXP_DB_PORT" )
    [ -n "${EXP_DB_NAME:-}" ] && args+=( "--db-name=$EXP_DB_NAME" )
    [ -n "${EXP_DB_USER:-}" ] && args+=( "--db-user=$EXP_DB_USER" )
    [ -n "${EXP_SITE_URL:-}" ] && args+=( "--url=$EXP_SITE_URL" )
    [ -n "${EXP_SITE_TITLE:-}" ] && args+=( "--title=$EXP_SITE_TITLE" )
    [ -n "${EXP_LANGUAGE:-}" ] && args+=( "--language=$EXP_LANGUAGE" )
    [ -n "${EXP_TIMEZONE:-}" ] && args+=( "--timezone=$EXP_TIMEZONE" )
    [ -n "${EXP_ADMIN_EMAIL:-}" ] && args+=( "--email=$EXP_ADMIN_EMAIL" )
    [ -n "${EXP_ADMIN_PASSWORD:-}" ] && args+=( "--password=$EXP_ADMIN_PASSWORD" )
    if [ -n "${EXP_INSTALL_ARGS:-}" ]; then
        # shellcheck disable=SC2206
        args+=( $EXP_INSTALL_ARGS )
    fi
    # exp:install reads the database password from here: not on its command line
    if [ -n "${EXP_DB_PASSWORD:-}" ]; then
        export EXP_INSTALL_DB_PASSWORD="$EXP_DB_PASSWORD"
    fi

    # A database server in another container may still be starting
    local wait="${EXP_DB_WAIT:-90}" started
    started=$(date +%s)
    log "installing (database $db, action $action)"
    until php bin/php/install.php "${args[@]}"; do
        if installed; then
            break
        fi
        if [ "$db" = sqlite ] || [ "$db" = sqlite3 ] || [ $(( $(date +%s) - started )) -ge "$wait" ]; then
            log "the installation failed; see var/log/setup.log and var/log/kickstart.log"
            exit 1
        fi
        log "the installation failed, retrying in 5 seconds (the database may still be starting)"
        sleep 5
    done
    unset EXP_INSTALL_DB_PASSWORD
    if [ -f var/log/initial-admin-password ]; then
        log "the administrator password is in var/log/initial-admin-password (readable by www-data only)"
    fi
}

serve_apache() {
    # Debian's envvars reads variables that may be unset
    set +u
    # shellcheck disable=SC1091
    . /etc/apache2/envvars
    set -u
    php-fpm --nodaemonize &
    local fpm=$!
    apache2 -DFOREGROUND &
    local web=$!
    trap 'kill -TERM "$fpm" "$web" 2>/dev/null || true' TERM INT
    # When either one ends, the container ends, so the runtime can restart it
    local status=0
    wait -n "$fpm" "$web" || status=$?
    kill -TERM "$fpm" "$web" 2>/dev/null || true
    wait || true
    exit "$status"
}

serve_velocity() {
    if [ ! -f "$console" ] || [ ! -d vendor/se7enxweb/exponential-velocity ]; then
        log "this release has no Velocity (it is part of Exponential from 6.0.15): use EXP_SERVER=apache"
        exit 1
    fi
    local ini=settings/override/velocity.ini.append.php
    if [ ! -f "$ini" ]; then
        # The container's defaults, written once; change them with
        # exp:velocity config set, they are kept on the settings volume
        cat > "$ini" <<EOF
<?php /* #?ini charset="utf-8"?
# Written by the container image on the first start. Port 8080 inside the
# container; publish it as you like (docker run -p 80:8080 ...).
[ServerSettings]
Engine=qbix
Host=0.0.0.0
Port=${EXP_PORT:-8080}
HTTPSPort=8443
Workers=${EXP_VELOCITY_WORKERS:-4}
*/ ?>
EOF
    fi
    php "$console" exp:velocity start
    trap 'php "$console" exp:velocity stop >/dev/null 2>&1 || true; exit 0' TERM INT
    local logfile=var/vc/qbix/run/console.log
    if [ -f "$logfile" ]; then
        tail -n 0 -F "$logfile" &
    fi
    # exp:velocity start returns once the server answers; status exits 0
    # while it runs. When it stops, the container ends.
    while php "$console" exp:velocity status >/dev/null 2>&1; do
        sleep 15 &
        wait $! || true
    done
    log "Velocity stopped"
    exit 1
}

run_cron() {
    install_once
    local interval="${EXP_CRON_INTERVAL:-300}"
    trap 'exit 0' TERM INT
    while true; do
        if installed; then
            php runcronjobs.php || log "runcronjobs.php ended with an error"
        fi
        sleep "$interval" &
        wait $! || true
    done
}

case "${1:-serve}" in
    serve)
        install_once
        case "${EXP_SERVER:-apache}" in
            apache) serve_apache ;;
            velocity) serve_velocity ;;
            *) log "EXP_SERVER must be apache or velocity, not ${EXP_SERVER}"; exit 1 ;;
        esac
        ;;
    install)
        install_once
        ;;
    cron)
        run_cron
        ;;
    *)
        exec "$@"
        ;;
esac
