#!/usr/bin/env bash
# Exponential 6: the rebuild script of the public demo demo.exponential.earth (SQLite, demo mode, daily)
#
# Free documentation: GFDL 1.3 or later, see doc/eng-us/README-LICENSE.md.
# A real variant of install-demo-sqlite.sh in this folder: one Plesk vhost with several names, the admin
# siteaccess matched by its own host (edit.demo.exponential.earth), the other siteaccesses by host and URI, the
# old names redirected, and a check that the settings name only the demo's hosts.
#
# Rebuilds the Exponential demo at demo.exponential.earth from the newest sources on GitHub, once a day:
#   1. clones se7enxweb/exponential (main) into a fresh build folder next to the site,
#   2. composer install,
#   3. installs it on SQLite with exp:install (administrator: admin / publish, demo mode),
#   4. sets the siteaccess matching (edit.demo.exponential.earth -> admin by host; demo.exponential.earth/<name> -> each
#      other siteaccess by host and URI; demo.exponential.earth -> site) and checks it and every SiteURL,
#   5. swaps it in: the live public_html is moved to /tmp, the new build is moved into its place (instant, same
#      filesystem), and the old copy in /tmp is removed.
# The site keeps serving the old copy until the swap; a failed build is left aside and the live site is untouched.
#
# Host: the Plesk domain demo.ezpublish.one (system user ezpautodemo, PHP 8.5 lsphp, docroot public_html) with the
# aliases demo.exponential.earth, edit.demo.exponential.earth and edit.demo.ezpublish.one. Exponential matches the
# siteaccess with MatchOrder=host_uri;host;uri: demo.exponential.earth/<name> is each siteaccess the installer made
# except admin and site (bold, bold_ger, editor, adminui ...; SiteURL demo.exponential.earth/<name>), then by host
# edit.demo.exponential.earth is the admin siteaccess (its SiteURL, no /admin) and demo.exponential.earth the site, so
# demo.exponential.earth/admin is a 404: the admin answers only on its own host. The old names are
# redirected (301) by rules this script puts at the top of .htaccess: demo.ezpublish.one/<path> to
# demo.exponential.earth/<path>, edit.demo.ezpublish.one/<path> to edit.demo.exponential.earth/<path>.
#
# Schedule: daily at 04:20 (crontab of ezpautodemo; latest.demo runs at :15 every 2 hours, the CJW demo at :45).
# One run at a time. Log: /var/www/vhosts/demo.ezpublish.one/var-demo-install/install.log (last 20 runs kept).
# Demo mode (EXP_DEMO_MODE=1): every step below runs with it, so exp:install accepts the well-known demo password and
# administrator passwords cannot be changed or reset; the site gets it through SetEnv in its .htaccess (LiteSpeed),
# so the admin shows the demo banner. See doc/eng-us/bc/6.0/demo-mode.md.
set -euo pipefail
export EXP_DEMO_MODE=1

SITE=/var/www/vhosts/demo.ezpublish.one
LIVE=$SITE/public_html
WORK=$SITE/var-demo-install
PHP=/opt/plesk/php/8.5/bin/php
COMPOSER=/usr/local/psa/var/modules/composer/composer.phar
REPO=https://github.com/se7enxweb/exponential.git
BRANCH=main
HOST=demo.exponential.earth
ADMIN_HOST=edit.demo.exponential.earth
URL=https://$HOST
ADMIN_PASSWORD=publish
STAMP=$(date +%Y%m%d-%H%M%S)
BUILD=$SITE/build-$STAMP
OLD=/tmp/demo-exponential-earth-old-$STAMP

mkdir -p "$WORK"
# PHP session and WSDL cache dirs (set in the domain's vhost.conf and php.ini): inside open_basedir, outside public_html
mkdir -p -m 0700 "$SITE/tmp" "$SITE/tmp/php-sessions" "$SITE/tmp/wsdl-cache"
LOG=$WORK/install.log
exec >>"$LOG" 2>&1
echo "=== $(date '+%F %T') start build $STAMP"

# one run at a time
exec 9>"$WORK/install.lock"
if ! flock -n 9; then
    echo "another run is still working; skipped"
    exit 0
fi

fail() {
    echo "FAIL: $*"
    if [ -d "$BUILD" ]; then
        mv -- "$BUILD" "$WORK/failed-$STAMP" 2>/dev/null || true
        # keep only the newest failed build for inspection
        ls -1dt "$WORK"/failed-* 2>/dev/null | tail -n +2 | xargs -r rm -rf --
    fi
    exit 1
}
trap 'fail "line $LINENO"' ERR

export COMPOSER_HOME=$WORK/composer-home COMPOSER_ALLOW_SUPERUSER=1 COMPOSER_NO_INTERACTION=1
export HOME=$WORK

echo "clone $REPO ($BRANCH)"
git clone --quiet --depth 1 --branch "$BRANCH" "$REPO" "$BUILD"
echo "commit $(git -C "$BUILD" rev-parse --short HEAD)"

cd "$BUILD"
# config.php of the demo: the shipped template with the owner's two changes (time zone, INI cache without file
# date checks), written before anything runs so the install already uses them
echo "config.php"
cp config.php-RECOMMENDED config.php
sed -i "s#^//date_default_timezone_set( 'Europe/Paris' );#date_default_timezone_set( 'America/Los_Angeles' );#" config.php
sed -i "s#^//define( 'EZP_INI_FILEMTIME_CHECK', false );#define( 'EZP_INI_FILEMTIME_CHECK', false );#" config.php
grep -q "^date_default_timezone_set( 'America/Los_Angeles' );" config.php || fail "config.php: time zone line not found in the template"
grep -q "^define( 'EZP_INI_FILEMTIME_CHECK', false );" config.php || fail "config.php: EZP_INI_FILEMTIME_CHECK line not found in the template"

echo "composer install"
"$PHP" "$COMPOSER" install --no-interaction --no-progress --prefer-dist --optimize-autoloader

# Composer for the admin's Updates and packages pages (ezupdate): the site's PHP runs with open_basedir
# (the vhost and /tmp only), so ezupdate cannot see the server's Composer under /usr/local and says
# "Composer was not found". A copy of the same composer.phar this build used, in var/ezupdate/ (the first
# place ezupdate looks), makes the pages work from the first visit. Rebuilt every run, so it never goes stale.
echo "composer for ezupdate"
mkdir -p var/ezupdate
cp -- "$COMPOSER" var/ezupdate/composer.phar
chmod 755 var/ezupdate/composer.phar
"$PHP" var/ezupdate/composer.phar --version --no-interaction >/dev/null || fail "var/ezupdate/composer.phar does not run"

echo "install on SQLite"
"$PHP" bin/php/console exp:install --db=sqlite --url="$URL" --password="$ADMIN_PASSWORD" --title="Exponential demo" --force

# In demo mode the installer takes the demo password as given. If it generated one instead (it then writes
# var/log/initial-admin-password), demo mode did not reach it: stop, keep the live site, and do not reset the
# password afterwards (demo mode refuses administrator password resets, and the build should not go live without it).
if [ -f var/log/initial-admin-password ]; then
    rm -f -- var/log/initial-admin-password
    fail "the installer did not take the demo password (is EXP_DEMO_MODE=1 supported by this release?)"
fi

# Siteaccess matching, set on every build (the installer writes MatchOrder=uri;host and an empty host map):
#   MatchOrder=host_uri;host;uri
#   HostUriMatchMapItems[]=demo.exponential.earth;<name>;<name>;strict  for every other siteaccess the installer made
#   HostMatchMapItems[]=edit.demo.exponential.earth;admin and =demo.exponential.earth;site, DefaultAccess=site,
# the admin's SiteURL on its own host, the site's on the host root, the others' on demo.exponential.earth/<name>.
echo "siteaccess matching"
SA=settings/override/site.ini.append.php
grep -q '^HostMatchMapItems\[\]$' "$SA" || fail "$SA: no HostMatchMapItems[] line to fill"
grep -q '^MatchOrder=' "$SA" || fail "$SA: no MatchOrder line"
# the URI siteaccesses: AvailableSiteAccessList without admin and site, longest name first (bold_ger before bold)
URI_SAS=$(sed -n 's/^AvailableSiteAccessList\[\]=//p' "$SA" | grep -vxE 'admin|site' | awk '{ print length, $0 }' | sort -rn | cut -d' ' -f2-)
[ -n "$URI_SAS" ] || fail "$SA: no siteaccesses besides admin and site in AvailableSiteAccessList"
HOSTURI=$(for n in $URI_SAS; do printf '\\nHostUriMatchMapItems[]=%s;%s;%s;strict' "$HOST" "$n" "$n"; done)
sed -i -e 's#^MatchOrder=.*#MatchOrder=host_uri;host;uri#' \
       -e '/^HostMatchMapItems\[\]=/d' -e '/^HostUriMatchMapItems\[\]/d' \
       -e "s#^HostMatchMapItems\[\]\$#HostMatchMapItems[]\nHostMatchMapItems[]=$ADMIN_HOST;admin\nHostMatchMapItems[]=$HOST;site\nHostUriMatchMapItems[]$HOSTURI#" \
       -e 's#^DefaultAccess=.*#DefaultAccess=site#' "$SA"
sed -i "s#^SiteURL=.*#SiteURL=$ADMIN_HOST#" settings/siteaccess/admin/site.ini.append.php
# the site is the host's root now (the installer wrote demo.exponential.earth/site): the preloader and the links use it
sed -i "s#^SiteURL=.*#SiteURL=$HOST#" settings/siteaccess/site/site.ini.append.php
for n in $URI_SAS; do
    f=settings/siteaccess/$n/site.ini.append.php
    [ -f "$f" ] || fail "no $f"
    sed -i "s#^SiteURL=.*#SiteURL=$HOST/$n#" "$f"
done

# Host names in the settings: every SiteURL must be on demo.exponential.earth (the admin's on edit.demo.exponential.earth),
# and nothing may name the build folder or another demo's host.
echo "check host names in the settings"
bad=$(grep -rhE '^SiteURL=' settings/override settings/siteaccess | grep -vE "^SiteURL=($HOST|$ADMIN_HOST)(/|$)" || true)
[ -z "$bad" ] || fail "SiteURL not on $HOST: $bad"
if grep -rlE "build-$STAMP|latest\.demo\.exponential\.earth|site\.cjw\.demo|demo\.ezpublish\.one" settings/override settings/siteaccess >/dev/null; then
    fail "the settings name another host or the build folder"
fi
grep -qx "SiteURL=$ADMIN_HOST" settings/siteaccess/admin/site.ini.append.php || fail "admin SiteURL is not $ADMIN_HOST"
grep -qx "SiteURL=$HOST" settings/siteaccess/site/site.ini.append.php || fail "site SiteURL is not $HOST"
grep -qx 'MatchOrder=host_uri;host;uri' "$SA" || fail "MatchOrder is not host_uri;host;uri"
for n in $URI_SAS; do
    grep -qx "HostUriMatchMapItems\[\]=$HOST;$n;$n;strict" "$SA" || fail "no host_uri map for $HOST/$n"
    grep -qx "SiteURL=$HOST/$n" "settings/siteaccess/$n/site.ini.append.php" || fail "$n SiteURL is not $HOST/$n"
done
grep -qx "HostMatchMapItems\[\]=$ADMIN_HOST;admin" "$SA" || fail "no host map for $ADMIN_HOST"
grep -qx "HostMatchMapItems\[\]=$HOST;site" "$SA" || fail "no host map for $HOST"
grep -qx 'DefaultAccess=site' "$SA" || fail "DefaultAccess is not site"

# web server rules of the release (Apache + PHP handler)
if [ -f .htaccess_root ] && [ ! -f .htaccess ]; then
    cp .htaccess_root .htaccess
fi
# the old names of this vhost go to the new ones (Let's Encrypt challenges excepted); edit.demo.exponential.earth
# is not redirected: Exponential matches it to the admin siteaccess
{
    printf '# public demo: old names to demo.exponential.earth / edit.demo.exponential.earth (written by bin/install-demo-sqlite.sh)\n'
    printf 'RewriteEngine On\n'
    printf 'RewriteCond %%{REQUEST_URI} !^/\\.well-known/\n'
    printf 'RewriteCond %%{HTTP_HOST} ^edit\\.demo\\.ezpublish\\.one$ [NC]\n'
    printf 'RewriteRule (.*) https://%s/$1 [R=301,L]\n' "$ADMIN_HOST"
    printf 'RewriteCond %%{REQUEST_URI} !^/\\.well-known/\n'
    printf 'RewriteCond %%{HTTP_HOST} ^demo\\.ezpublish\\.one$ [NC]\n'
    printf 'RewriteRule (.*) https://%s/$1 [R=301,L]\n\n' "$HOST"
    cat .htaccess
} > .htaccess.new
mv -- .htaccess.new .htaccess
# demo mode for the running site: LiteSpeed (and Apache with mod_env) pass SetEnv to PHP
if ! grep -q '^SetEnv EXP_DEMO_MODE 1' .htaccess 2>/dev/null; then
    printf '\n# public demo: Exponential demo mode (doc/eng-us/bc/6.0/demo-mode.md)\nSetEnv EXP_DEMO_MODE 1\n' >> .htaccess
fi

# same rights as the docroot Plesk made: owner the site user, group read for the web server
chmod 750 "$BUILD"

echo "swap"
if [ -d "$LIVE" ]; then
    mv -- "$LIVE" "$OLD"
fi
mv -- "$BUILD" "$LIVE"
trap - ERR

# caches may hold the build folder's path: clear them in the live place (quick)
cd "$LIVE"
"$PHP" bin/php/ezcache.php --clear-all --purge >/dev/null 2>&1 || "$PHP" bin/php/ezcache.php --clear-all >/dev/null 2>&1 || true

# the old copy is only rubbish now: a file in it the site user cannot remove (e.g. one written by root) must not
# stop the steps that follow, the site is already swapped in
if [ -d "$OLD" ]; then
    rm -rf -- "$OLD" 2>/dev/null || echo "warning: could not remove all of $OLD (files of another owner); left in place"
fi
# old copies left by earlier runs: try again (only our own, older than this one)
for left in /tmp/demo-exponential-earth-old-*; do
    [ -d "$left" ] && [ "$left" != "$OLD" ] && { rm -rf -- "$left" 2>/dev/null || true; }
done

# sample data for the collaboration inbox (approval workflow, editors, articles waiting/approved/denied with
# comment threads); marked "Sample:", removable with --remove
echo "collaboration sample data"
"$PHP" bin/php/collaborationsampledata.php 2>&1 | tail -n 2 || echo "collaboration sample data: failed (the site still works)"

# warm-up with the preloader: a fresh install has no image aliases or view caches yet (they are made on the
# first render), so the site is preloaded once now instead of by the first visitors
echo "preload"
"$PHP" bin/php/preload.php --siteaccess=site --no-colors 2>&1 | tail -n 3 || echo "preload: failed (the site still works, only not warmed)"
date +%s > "$WORK/last-success"
echo "=== $(date '+%F %T') done: $URL serves build $STAMP"

# keep the log short: last 20 runs
if [ "$(grep -c '^=== .* start build' "$LOG")" -gt 20 ]; then
    tail -n 4000 "$LOG" > "$LOG.tmp" && mv -- "$LOG.tmp" "$LOG"
fi
