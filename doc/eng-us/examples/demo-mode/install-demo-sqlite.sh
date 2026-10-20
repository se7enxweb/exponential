#!/usr/bin/env bash
# Exponential 6: rebuild a public demo from the current code (SQLite), in demo mode
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
#
# The generic shape of the script a public demo runs from cron every few hours
# (crontab-demo in this folder): build a fresh installation next to the live
# one, install it in demo mode with the published administrator password,
# check that the password went in as given, give the new site demo mode for
# its web requests, swap it in, warm it. A failed build is kept for reading and
# the live site stays untouched. Explained in README.md here and in
# doc/eng-us/help/hosting/servers/run-a-public-demo-on-litespeed-and-plesk.md.
#
# Replace the paths, the URL and the repository; run it as the demo's own
# system user, never as root. It holds no secret: the demo password is public
# by design. Guide: doc/eng-us/bc/6.0/demo-mode.md.

set -euo pipefail

SITE_ROOT=/var/www/demo.example.com            # the virtual host's directory
DOCROOT_NAME=httpdocs                          # the document root inside it, swapped on success
BUILDS="$SITE_ROOT/builds"                     # where builds are made and failed ones kept
REPO=https://github.com/se7enxweb/exponential.git
BRANCH=main
URL=https://demo.example.com
DEMO_PASSWORD=publishing                       # the published password; publish works too
PHP=${PHP:-php}
COMPOSER=${COMPOSER:-composer}

# demo mode for every step: the installers accept the published password, and
# every console command of the build runs as the site will
export EXP_DEMO_MODE=1

stamp=$(date +%Y%m%d-%H%M%S)
build="$BUILDS/build-$stamp"
mkdir -p "$BUILDS"

fail() {
    echo "FAIL: $*" >&2
    mv "$build" "$BUILDS/failed-$stamp" 2>/dev/null || true
    exit 1
}

git clone --quiet --depth 1 --branch "$BRANCH" "$REPO" "$build" || fail "git clone"
cd "$build"
"$COMPOSER" install --quiet --no-dev --no-interaction || fail "composer install"

# the installation: SQLite, the published administrator password as given
"$PHP" bin/php/install.php --db=sqlite --url="$URL" --password="$DEMO_PASSWORD" \
    --email=demo@example.invalid > "$BUILDS/install-$stamp.txt" 2>&1 || fail "exp:install (see $BUILDS/install-$stamp.txt)"

# exp:install replaces a password it may not install by a generated one and
# records it in var/log/initial-admin-password. On a demo that means demo mode
# was not on during the installation: the published sign-in would not work.
if [ -e var/log/initial-admin-password ]; then
    fail "the installer generated an administrator password: EXP_DEMO_MODE=1 did not reach it"
fi

# demo mode for the web requests: LiteSpeed (lsphp) and Apache read it from .htaccess
cat >> .htaccess <<'EOF'

# public demo: demo mode (doc/eng-us/bc/6.0/demo-mode.md)
<IfModule mod_env.c>
    SetEnv EXP_DEMO_MODE 1
</IfModule>
EOF

# swap: the new build becomes the document root, the old one is removed after
cd "$SITE_ROOT"
if [ -d "$DOCROOT_NAME" ]; then
    mv "$DOCROOT_NAME" "$BUILDS/old-$stamp"
fi
mv "$build" "$DOCROOT_NAME"
rm -rf "$BUILDS/old-$stamp"

# warm the new site with the kernel's preloader
cd "$SITE_ROOT/$DOCROOT_NAME"
"$PHP" bin/php/preload.php --siteaccess site > /dev/null 2>&1 || echo "WARN: the preloader did not finish" >&2

echo "PASS: demo rebuilt ($stamp)"
