#!/usr/bin/env sh
# Exponential 6 container image: healthy when the server answers on 8080.
# Apache with PHP-FPM: a static file (Apache) and the PHP-FPM ping (PHP).
# Velocity: its own /Q/health answer.
set -eu
port="${EXP_PORT:-8080}"
if [ "${EXP_SERVER:-apache}" = velocity ]; then
    curl -fsS -o /dev/null --max-time 4 "http://127.0.0.1:${port}/Q/health"
else
    curl -fsS -o /dev/null --max-time 4 "http://127.0.0.1:${port}/robots.txt"
    curl -fsS --max-time 4 "http://127.0.0.1:${port}/fpm-ping" | grep -q pong
fi
