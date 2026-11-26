#!/usr/bin/env bash
# Exponential 6 container image: the test the image workflow runs before it
# publishes anything, and that anyone can run against an image built locally.
#
#   docker/test-image.sh <image> [sqlite|mysql|pgsql]...
#
# Each database starts a fresh container (and, for mysql and pgsql, a database
# server beside it on a private network), waits until it is healthy, and
# checks the answers: with exp:install (6.0.15 and later) the installation
# runs and the site's front page and the administration's login page answer;
# without it (6.0.14 and earlier) the setup wizard answers. EXP_SERVER of the
# image decides whether Apache with PHP-FPM or Velocity serves. Ends with PASS
# or FAIL; everything it started is removed again.
set -euo pipefail

image="${1:?usage: $0 <image> [sqlite|mysql|pgsql]...}"
shift
dbs=( "$@" )
[ "${#dbs[@]}" -gt 0 ] || dbs=( sqlite )

# Pinned database servers for the test
MARIADB_IMAGE="${MARIADB_IMAGE:-mariadb:11.8}"
POSTGRES_IMAGE="${POSTGRES_IMAGE:-postgres:17}"

run_id="exptest-$$"
net="$run_id-net"
fail=0
started=()

cleanup() {
    for c in "${started[@]}"; do
        docker rm -f "$c" >/dev/null 2>&1 || true
    done
    docker network rm "$net" >/dev/null 2>&1 || true
}
trap cleanup EXIT

say() { printf '%s\n' "$*"; }

server=$(docker image inspect -f '{{range .Config.Env}}{{println .}}{{end}}' "$image" | sed -n 's/^EXP_SERVER=//p')
has_installer=$(docker run --rm --entrypoint sh "$image" -c 'test -f bin/php/install.php && echo yes || echo no')
say "image $image: server ${server:-apache}, exp:install $has_installer"
docker network create "$net" >/dev/null

wait_healthy() {
    local c="$1" limit="${2:-420}" t=0 state
    while [ "$t" -lt "$limit" ]; do
        # A stopped container keeps its last health status: look at the state first
        state=$(docker inspect -f '{{if ne .State.Status "running"}}{{.State.Status}}{{else if .State.Health}}{{.State.Health.Status}}{{else}}running{{end}}' "$c" 2>/dev/null || echo gone)
        case "$state" in
            healthy) return 0 ;;
            exited|dead|gone) return 1 ;;
        esac
        sleep 3; t=$((t + 3))
    done
    return 1
}

# fetch <container> <path>: prints the HTTP status, the body goes to a file
fetch() {
    docker exec "$1" curl -sS -o /tmp/body -w '%{http_code}' --max-time 60 "http://127.0.0.1:8080$2" || echo 000
}

check() {
    local c="$1" what="$2" path="$3" want="$4" pattern="$5" code
    code=$(fetch "$c" "$path")
    if [[ "$code" =~ ^($want)$ ]] && docker exec "$c" grep -qiE "$pattern" /tmp/body; then
        say "PASS  $what: $path -> $code"
    else
        say "FAIL  $what: $path -> $code (wanted $want and /$pattern/)"
        docker exec "$c" head -c 600 /tmp/body 2>/dev/null || true; echo
        fail=1
    fi
}

for db in "${dbs[@]}"; do
    c="$run_id-$db"
    env=( -e EXP_DB="$db" -e EXP_ADMIN_EMAIL=admin@example.com -e EXP_SITE_URL=localhost:8080 )
    case "$db" in
        sqlite) ;;
        mysql)
            docker run -d --name "$run_id-mariadb" --network "$net" \
                -e MARIADB_DATABASE=exponential -e MARIADB_USER=exponential \
                -e MARIADB_PASSWORD=test-only-password -e MARIADB_RANDOM_ROOT_PASSWORD=1 \
                "$MARIADB_IMAGE" --character-set-server=utf8mb4 --collation-server=utf8mb4_unicode_ci >/dev/null
            started+=( "$run_id-mariadb" )
            env+=( -e EXP_DB_HOST="$run_id-mariadb" -e EXP_DB_NAME=exponential -e EXP_DB_USER=exponential -e EXP_DB_PASSWORD=test-only-password )
            ;;
        pgsql)
            docker run -d --name "$run_id-postgres" --network "$net" \
                -e POSTGRES_DB=exponential -e POSTGRES_USER=exponential -e POSTGRES_PASSWORD=test-only-password \
                "$POSTGRES_IMAGE" >/dev/null
            started+=( "$run_id-postgres" )
            env+=( -e EXP_DB_HOST="$run_id-postgres" -e EXP_DB_NAME=exponential -e EXP_DB_USER=exponential -e EXP_DB_PASSWORD=test-only-password )
            ;;
        *) say "FAIL  unknown database $db"; fail=1; continue ;;
    esac

    say "--- $db"
    docker run -d --name "$c" --network "$net" "${env[@]}" "$image" >/dev/null
    started+=( "$c" )
    if ! wait_healthy "$c"; then
        say "FAIL  $db: the container did not become healthy"
        docker logs --tail 80 "$c" 2>&1 || true
        fail=1
        continue
    fi
    say "PASS  $db: healthy"
    if [ "$has_installer" = yes ]; then
        check "$c" "$db front page" / '200' '<html'
        check "$c" "$db administration" /admin/user/login '200' 'password'
        if docker exec "$c" test -s var/log/initial-admin-password; then
            say "PASS  $db: generated administrator password stored"
        else
            say "FAIL  $db: no var/log/initial-admin-password"; fail=1
        fi
        if docker exec "$c" sh -c '[ "$(id -u)" != 0 ] && [ ! -w kernel ]'; then
            say "PASS  $db: runs as $(docker exec "$c" id -un), code read-only"
        else
            say "FAIL  $db: runs as root or the code is writable"; fail=1
        fi
    else
        check "$c" "$db setup wizard" / '200' 'setup|install'
    fi
    if [ "$fail" != 0 ]; then
        docker logs --tail 80 "$c" 2>&1 || true
    fi
    docker rm -f "$c" >/dev/null 2>&1 || true
done

if [ "$fail" = 0 ]; then
    say "PASS: $image (${dbs[*]})"
else
    say "FAIL: $image (${dbs[*]})"
fi
exit "$fail"
