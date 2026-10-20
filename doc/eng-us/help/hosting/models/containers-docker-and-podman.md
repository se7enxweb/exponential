---
title: Containers: Docker and Podman
audience: hosting, admin
category: hosting
order: 60
summary: Running Exponential in Docker or Podman containers: what the sources say (tier 2, Velocity's published images), what goes on a persistent volume, the one-writer rule for SQLite, an entry point that installs once and keeps Velocity in the foreground, and secrets.
keywords: containers, Docker, Podman, image, volume, persistent volume, entry point, exp:install, Velocity, ghcr.io, SQLite, WAL, one writer, PostgreSQL, EXP_INSTALL_DB_PASSWORD, secrets
type: help
---
# Containers: Docker and Podman

This page is for hosting companies and platform teams that run Exponential sites in Linux containers with Docker or
Podman. It answers what the project's sources say about containers, what must live on a persistent volume, how many
containers may write a SQLite database, how an entry point installs a site once and keeps the server running, and
how to keep secrets out of images.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [Images](#images)
3. [Volumes: what must persist](#volumes-what-must-persist)
4. [The database and the one-writer rule](#the-database-and-the-one-writer-rule)
5. [An entry point that installs once](#an-entry-point-that-installs-once)
6. [Secrets](#secrets)
7. [Operations inside containers](#operations-inside-containers)
8. [Checklist](#checklist)

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

Two routes:

| Route | What you get | What to watch |
|---|---|---|
| Your own image from a PHP 8.1+ base | the PHP extensions you choose, Composer, the installation built with `composer install` | install every required extension: `dom`, `xsl`, `intl`, `mbstring`, `iconv`, `zlib`, one database extension, `gd` or ImageMagick, and for Velocity `sockets`, `pcntl`, `openssl` |
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

In the official order: 1. SQLite 3 (the default) · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL.
Microsoft SQL Server is known to be possible with additional work at this time.

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
    php bin/php/console exp:install \
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

- [ ] Image with PHP 8.1+ and every required extension; `exp:velocity ext check` passes in the pipeline.
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
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
