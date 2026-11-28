---
title: Containers: Docker and Podman
audience: hosting, admin
category: hosting
order: 60
summary: Running Exponential in Docker or Podman containers: the official images (ghcr.io/se7enxweb/exponential, Apache with PHP-FPM or Velocity, amd64 and arm64), what the sources say (tier 2, Velocity's published images), what goes on a persistent volume, the one-writer rule for SQLite, an entry point that installs once and keeps Velocity in the foreground, and secrets.
keywords: containers, Docker, Podman, official image, ghcr.io/se7enxweb/exponential, compose, edge, tags, image, volume, persistent volume, entry point, exp:install, Velocity, ghcr.io, SQLite, WAL, one writer, PostgreSQL, EXP_INSTALL_DB_PASSWORD, secrets
type: help
---
# Containers: Docker and Podman

This page is for hosting companies and platform teams that run Exponential sites in Linux containers with Docker or
Podman. It describes the official images and their tags, and answers what the project's sources say about containers, what must live on a persistent volume, how many
containers may write a SQLite database, how an entry point installs a site once and keeps the server running, and
how to keep secrets out of images.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../panels/plesk.md), [cPanel](../panels/cpanel.md), [DirectAdmin](../panels/directadmin.md), [ISPConfig](../panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [Official images](#official-images)
2. [What the sources say](#what-the-sources-say)
3. [Images](#images)
4. [Volumes: what must persist](#volumes-what-must-persist)
5. [The database and the one-writer rule](#the-database-and-the-one-writer-rule)
6. [An entry point that installs once](#an-entry-point-that-installs-once)
7. [Secrets](#secrets)
8. [Operations inside containers](#operations-inside-containers)
9. [Checklist](#checklist)

## Official images

Exponential publishes its own images on the GitHub Container Registry, `ghcr.io/se7enxweb/exponential`, for amd64 and
arm64. They are built from [`docker/`](../../../../../docker/Dockerfile) in the kernel repository on the official PHP
images (Debian), with every PHP extension Exponential and Velocity use, and the installation made with
`composer install`. Each image:

- runs as `www-data` (uid 33), never as root; the code belongs to root and cannot be changed by the server: `var/`,
  `settings/override`, `settings/siteaccess` and `autoload/` belong to `www-data`, and the installation root,
  `design/`, `extension/` and `settings/` let it add files of its own (sticky bit), as the installer needs;
- listens on port **8080** (publish it as you like: `-p 80:8080`);
- installs a site on its first start with `exp:install`, from the environment, and keeps nothing secret inside;
- answers a health check (Apache: a static file and the PHP-FPM ping; Velocity: `/Q/health`).

### Tags

| Tag | What it is | Moves? |
|---|---|---|
| `<version>-php<php>-apache` | a release on PHP `<php>`, served by Apache with PHP-FPM | fixed |
| `<version>-php<php>-velocity` | a release on PHP `<php>`, served by Velocity (6.0.15 and later) | fixed |
| `<version>`, `<version>-velocity` | a release on the default PHP (8.5) | fixed |
| `latest`, `latest-velocity` | the newest release on the default PHP | with each release |
| `edge-php<php>-apache`, `edge-php<php>-velocity` | the main branch, rebuilt nightly | always |
| `edge`, `main`, `edge-velocity` | the main branch on the default PHP | always |

`<version>` is the release without its `v` (`6.0.14`). `<php>` is 8.0 to 8.5 for 6.0.15 and later, and 8.4 or 8.5 for
6.0.14: its `composer.json` requires PHPUnit 13.0.0, which needs PHP 8.4.1, so `composer install` of that release
does not resolve on older PHP. PHP 8.5 is the default because it is the newest; pick an older one only when an
extension of yours needs it. Every tag is multi-architecture. The PHP 8.0 images are built on Debian 11, the only base
the official PHP 8.0 image has; Debian 11 has reached its end of life and its packages come from archive.debian.org, so
neither PHP 8.0 nor the system gets security updates. Use them to test compatibility, not to serve a public site.

**A fixed tag never changes.** Images are pushed by digest and tagged only after every build of the run passed its
tests; the workflow stops before it would overwrite a fixed tag that exists. An image found wrong after its release
is not replaced under the same tag: the next build carries the fix, and its notes say what was wrong. Pin a fixed tag
(or a digest) in production; use `edge` to try what is coming.

### Start a site

A single container on SQLite, served by Apache with PHP-FPM:

```bash
docker run -d --name exponential -p 8080:8080 \
  -v exp-var:/var/www/exponential/var \
  -v exp-override:/var/www/exponential/settings/override \
  -v exp-siteaccess:/var/www/exponential/settings/siteaccess \
  -e EXP_SITE_URL=localhost:8080 -e EXP_ADMIN_EMAIL=you@example.com \
  ghcr.io/se7enxweb/exponential:edge
docker logs -f exponential                                  # the installation, then the server
docker exec exponential cat var/log/initial-admin-password  # the administrator's password
```

The site answers on `http://localhost:8080/`, the administration on `http://localhost:8080/admin` (user `admin`).
Podman takes the same command (`podman run ...`). For Velocity, use the `-velocity` tag of the same release.

With a database server, the kernel repository has compose files: [`docker/compose.mariadb.yml`](../../../../../docker/compose.mariadb.yml)
and [`docker/compose.postgresql.yml`](../../../../../docker/compose.postgresql.yml) (the site, a cron container and the
database, the password as a secret file), and [`docker/compose.sqlite.yml`](../../../../../docker/compose.sqlite.yml)
for the single container:

```bash
printf '%s' "$(openssl rand -hex 24)" > docker/db_password.txt
docker compose -f docker/compose.postgresql.yml up -d
```

Without compose, the variables alone point the container at a database server you already run. MariaDB or MySQL
(`EXP_DB=mysql`), with the password in a file mounted read-only (readable by uid 33, the container's `www-data`):

```bash
docker run -d --name exponential -p 8080:8080 \
  -v exp-var:/var/www/exponential/var \
  -v exp-override:/var/www/exponential/settings/override \
  -v exp-siteaccess:/var/www/exponential/settings/siteaccess \
  -v "$PWD/db_password.txt:/run/secrets/db_password:ro" \
  -e EXP_DB=mysql -e EXP_DB_HOST=db.example.com -e EXP_DB_NAME=exponential -e EXP_DB_USER=exponential \
  -e EXP_DB_PASSWORD_FILE=/run/secrets/db_password \
  -e EXP_SITE_URL=www.example.com -e EXP_ADMIN_EMAIL=you@example.com \
  ghcr.io/se7enxweb/exponential:edge
```

For PostgreSQL, set `EXP_DB=pgsql` (and `EXP_DB_PORT` when it is not 5432). The database and its user must exist;
the installer creates the tables in it.

### Variables

| Variable | Default | Meaning |
|---|---|---|
| `EXP_SERVER` | `apache` (`velocity` in `-velocity` images) | the server: Apache with PHP-FPM, or Velocity |
| `EXP_INSTALL` | `auto` | `auto` installs on the first start when `settings/override/site.ini.append.php` has no `[DatabaseSettings]`; `never` does not |
| `EXP_DB` | `sqlite` | `sqlite`, `mysql` (MariaDB and MySQL), `pgsql`, and the other values of `exp:install --db` |
| `EXP_DB_HOST`, `EXP_DB_PORT`, `EXP_DB_NAME`, `EXP_DB_USER` | as `exp:install` | the database server |
| `EXP_DB_PASSWORD_FILE` | | a file holding the database password (a Docker or Podman secret); `EXP_DB_PASSWORD` works too, but shows in `docker inspect` |
| `EXP_DB_ACTION` | `remove` for SQLite, `ignore` for a server | what `exp:install` does with an existing database; a server database is only emptied when you set `remove` |
| `EXP_DB_WAIT` | `90` | seconds to keep retrying while the database server starts |
| `EXP_SITE_URL`, `EXP_SITE_TITLE`, `EXP_LANGUAGE`, `EXP_TIMEZONE` | | the site's address, title, primary language and time zone |
| `EXP_ADMIN_EMAIL`, `EXP_ADMIN_PASSWORD_FILE` | | the administrator; without a password one is generated and written to `var/log/initial-admin-password` |
| `EXP_INSTALL_ARGS` | | further `exp:install` options, for example `--package=... --languages=ger-DE` |
| `EXP_VELOCITY_WORKERS` | `4` | Velocity's worker count, written to `settings/override/velocity.ini.append.php` on the first start |
| `EXP_CRON_INTERVAL` | `300` | seconds between two runs of `runcronjobs.php` in a container started with the command `cron` |

The command decides what the container does: `serve` (the default), `install` (install and stop), `cron` (run the
cron jobs on a schedule, for a second container on the same volumes) or any command, for example
`docker exec exponential bin/php/console exp:velocity ext check`.

Mount your own PHP settings into `/usr/local/etc/php/conf.d/` (a name after `zz-exponential.ini`, such as
`zzz-site.ini`). The code in the image is read-only; a new image is the way to change it.

### Releases before 6.0.15

The 6.0.14 images serve the release as it was published: it has no `exp:install` and no Velocity, so there is no
`-velocity` tag, and a new container shows the setup wizard in the browser, which writes the same volumes.

### Building and testing your own

From a clone of the kernel repository:

```bash
docker build -f docker/Dockerfile -t exponential:local .                       # Apache with PHP-FPM, PHP 8.5
docker build -f docker/Dockerfile --target velocity -t exponential:velocity .  # Velocity
docker build -f docker/Dockerfile --build-arg PHP_VERSION=8.3 -t exponential:php8.3 .
docker/test-image.sh exponential:local sqlite mysql pgsql                       # install, front page, login page
```

`PHP_VERSION` is 8.0 to 8.5 and `DEBIAN` the base release (`bookworm`; `bullseye` for 8.0). The build never reads
`settings/override` or `vendor/` from your tree, so a working copy with credentials can be built safely. The workflow
`.github/workflows/docker.yml` builds the same way on native amd64 and arm64 runners, runs `docker/test-image.sh`, and
publishes.

## What the sources say

- **Docker and Podman (Linux containers) are tier 2**, known to work: "Velocity publishes images on
  `ghcr.io/se7enxweb/exponential-velocity` for amd64 and arm64, one per PHP version and variant"
  ([Tier 2](../../os-support.md#tier-2-known-to-work)).
- "Any image with PHP 8 and the extensions above runs Exponential ... Keep `var/` (storage, logs, the SQLite
  database) and `settings/override` on a persistent volume. With SQLite, run **one** container that writes"
  ([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels)).
- `exp:install` is the method for "CI pipeline, container image, automated test environment": options or environment
  variables, exit status ([4.6 Unattended installs](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).

## Images

Three routes:

| Route | What you get | What to watch |
|---|---|---|
| The official image, `ghcr.io/se7enxweb/exponential` ([above](#official-images)) | a tested installation, Apache with PHP-FPM or Velocity, installed on its first start | pin a fixed tag; add your extensions in an image built `FROM` it |
| Your own image from a PHP 8.0+ base | the PHP extensions you choose, Composer, the installation built with `composer install` | install every required extension: `dom`, `xsl`, `intl`, `mbstring`, `iconv`, `zlib`, one database extension, `gd` or ImageMagick, and for Velocity `sockets`, `pcntl`, `openssl` |
| Velocity's published image as a base | the server and a PHP with a defined extension set | check the variant against what Exponential needs |

Velocity's images are tagged by release, PHP version and variant
([Velocity packages, Docker images and binaries](../../../features/6.0/velocity-packages-and-binaries.md#docker)):

```text
ghcr.io/se7enxweb/exponential-velocity:<release>-php<version>-<variant>   fixed
ghcr.io/se7enxweb/exponential-velocity:php<version>-<variant>             moves with each release
ghcr.io/se7enxweb/exponential-velocity:latest                             newest PHP, standard variant
```

`<version>` is 8.2, 8.3, 8.4 or 8.5; `<variant>` is `mini`, `lite`, `standard` or `full`. "Pin a fixed tag in
production." The `standard` variant is "the default; what the documentation assumes".

> **Tip**
> Whatever the base, run `php bin/php/console exp:velocity ext check` inside the built image as part of your image
> pipeline. It lists every extension the installation wants, marks what is missing and prints the install command
> ([Finding what is missing](../../../install/02-requirements.md#finding-what-is-missing)).

## Volumes: what must persist

| Path | Holds | Volume |
|---|---|---|
| `var/` | uploads and image variations (`var/storage`), caches, logs, autoload arrays, the SQLite database (`var/storage/sqlite3/`) | persistent |
| `settings/override` | the installation's settings, including the database connection | persistent |
| `settings/siteaccess` | the siteaccess settings the installer writes | persistent (the backup list names it) |
| `kernel/`, `lib/`, `vendor/`, `extension/`, `design/` | the code | in the image |

The backup set is the same as everywhere: the database and `var/storage`, `settings/override` and
`settings/siteaccess` ([Backup and restore](../../database-support.md#backup-and-restore)).

> **Warning**
> Files on the volume must belong to the user the container's PHP runs as. "Files created by `root` may later be
> unwritable for the web server" ([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
> Decide the user ID once for the image and the volume, and keep it.

## The database and the one-writer rule

In the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL ·
7. Microsoft SQL Server and Azure SQL (more databases from 6.0.15: [Choosing a
database](../../choosing-a-database.md)).

- **1. SQLite 3** works in a container when **one** container writes the database: "SQLite's WAL needs shared memory
  on one host, so several replicas cannot share one database file over a network volume."
- **Several containers serving one site** need a database server ("PostgreSQL first") and shared storage for
  `var/storage` ([Containers, cloud and hosting panels](../../os-support.md#containers-cloud-and-hosting-panels)).
  They also need sessions every container can read; see [Kubernetes](kubernetes.md#sessions).
- The database server usually runs in its own container or as a managed service of your platform, with its own
  volume and its own backups ([Database backups per engine](../operations/database-backups-per-engine.md)).

## An entry point that installs once

Chapter 4 gives "a minimal container entry point" that installs once, then starts Velocity and stays in the
foreground while it runs:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
if ! grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null; then
    php bin/php/console exp:install --apply \
        --db=mysql --db-host="${DB_HOST}" --db-name="${DB_NAME}" --db-user="${DB_USER}" \
        --url="${SITE_URL}" --email="${ADMIN_EMAIL}" --password="${ADMIN_PASSWORD}"
fi
php bin/php/console exp:velocity start
# exp:velocity start runs the server in the background and returns; status exits 0 while it runs
while php bin/php/console exp:velocity status >/dev/null 2>&1; do sleep 30; done
echo "Velocity stopped" >&2
exit 1
```

What the chapter explains about it:

- The `grep` "is the same test `exp:install` itself uses to decide that a directory already holds an installation".
- "`exp:velocity start` detaches the server and returns as soon as it answers", so the loop keeps the container's
  main process alive and ends it when the server stops, "so the container runtime can restart it".
- "If the entry point runs as `root`, add `--allow-root-user` to the two `exp:velocity` commands."
- The example uses `--db=mysql`; the other values are `sqlite` (the default), `pgsql`, `mongodb`, `mariadb` and
  `oracle` ([Database support, At a glance](../../database-support.md#at-a-glance)).

> **Warning**
> `exp:install` uses the database action `remove` by default. That "is what a fresh container wants, and exactly what
> must never point at a database that holds data you need" ([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
> Keep the "already installed" test in front of it.

## Secrets

- Pass the database password in `EXP_INSTALL_DB_PASSWORD`; `exp:install` reads it "when `--db-password` is not
  given; that keeps it out of the process list and the shell history".
- Without `--password`, `exp:install` generates a 24-character administrator password and writes it to
  `var/log/initial-admin-password` (mode `0600`); read it from there and rotate it, or pass one from your secret store
  ([First sign-in and default passwords](../security/first-sign-in-and-default-passwords.md)).
- Never bake `settings/override` into an image: it holds the database credentials
  ([13.3 Secrets in settings/override](../../../install/13-security-hardening.md#133-secrets-in-settingsoverride)).
- `config.php` can be made in the image (`exp:setup:config --apply --allow-root-user --owner=www-data:www-data` in the
  build step), but a password in it, such as `CLUSTER_STORAGE_PASS`, is set at start from the environment:
  `php bin/php/console exp:config:set CLUSTER_STORAGE_PASS --value="$CLUSTER_DB_PASS" --apply --allow-root-user`. The
  output shows it as `********`. In CI, `exp:config:list --only-set --format=json` shows what an image or a running
  container sets ([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md#11-in-scripts-containers-and-ci)).

## Operations inside containers

- **Cron.** `runcronjobs.php` still has to run on a schedule: from a scheduler container that shares the volumes and
  the database, or from your platform's scheduler, as the same user as the web process
  ([Cron jobs](../operations/cron-jobs.md)).
- **Logs.** Exponential writes its logs under `var/log` (and per site under `var/<site>/log`); keep them on the volume
  or collect them from there ([Request logs](../operations/request-logs.md)).
- **Code changes** reach Velocity's workers only after a restart; `exp:velocity deploy` runs the steps in order
  ([8.3.14](../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)). In containers the
  usual equivalent is a new image and a new container.

## Checklist

- [ ] The official image, or an image with PHP 8.0+ and every required extension; `exp:velocity ext check` passes in the pipeline.
- [ ] Fixed image tags in production.
- [ ] `var/`, `settings/override`, `settings/siteaccess` on persistent volumes, owned by the container's PHP user.
- [ ] One writing container for SQLite, or a database server for more.
- [ ] Entry point with the "already installed" test; secrets from the environment, not the image.
- [ ] Cron, logs and backups defined.

**A public demo in a container.** Give the demo service `EXP_DEMO_MODE=1` (`-e`, `--env-file` or `env_file:`; [`docker-demo.env`](../../../examples/demo-mode/docker-demo.env)), never a base image other sites use. With PHP-FPM inside, keep `clear_env = no` or set `env[EXP_DEMO_MODE] = 1` in the pool. [Demo mode for hosts](../security/demo-mode-for-hosts.md).

## Related pages

- [Kubernetes](kubernetes.md), [Platform as a service](platform-as-a-service.md)
- [exp:install](../provisioning/exp-install.md), [Automating provisioning](../provisioning/automating-provisioning.md)
- [Installing in one command](../../../features/6.0/install-in-one-command.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
