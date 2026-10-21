---
title: Container images
audience: bundler, hosting
category: bundling
order: 280
summary: How to bundle Exponential as a Docker or Podman image - the project ships no Dockerfile, so this page gives generic guidance - choosing a base, what goes in the image and what on volumes, the documented entry point that installs once and keeps Velocity running, secrets, the SQLite one-writer rule, and an image checklist.
keywords: container, Docker, Podman, image, Dockerfile, volume, entry point, exp:install, Velocity, ghcr.io, SQLite, PostgreSQL, secrets, EXP_INSTALL_DB_PASSWORD, bundle
type: help
---
# Container images

This page is for bundlers who publish Exponential as a container image. The Exponential repository contains **no
Dockerfile or compose file**; the guidance below is generic and built on what the documentation states. Velocity, the
web server that is part of Exponential, does publish images you can build on.

## Contents

1. [What the sources say](#what-the-sources-say)
2. [Choosing a base](#choosing-a-base)
3. [Image or volume](#image-or-volume)
4. [An entry point](#an-entry-point)
5. [Secrets and the database](#secrets-and-the-database)
6. [Image checklist](#image-checklist)

## What the sources say

- "Docker and Podman (Linux containers) are tier 2, known to work" ([Operating system support](../os-support.md#tier-2-known-to-work)).
- "Any image with PHP 8 and the extensions above runs Exponential ... Keep `var/` (storage, logs, the SQLite database)
  and `settings/override` on a persistent volume. With SQLite, run **one** container that writes"
  ([Containers, cloud and hosting panels](../os-support.md#containers-cloud-and-hosting-panels)).
- `exp:install` is the method for "CI pipeline, container image, automated test environment"
  ([4.5](../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation)).

## Choosing a base

| Route | Notes |
|---|---|
| Your own image from a PHP 8.0+ base | install every required PHP extension: `dom`, `xsl`, `intl`, `mbstring`, `iconv`, `zlib`, one database extension, `gd` or ImageMagick, and for Velocity `sockets`, `pcntl`, `openssl` |
| Velocity's image `ghcr.io/se7enxweb/exponential-velocity` | tagged `<release>-php<version>-<variant>`; "Pin a fixed tag in production"; the `standard` variant is "what the documentation assumes" |

Source: [Containers: Docker and Podman](../hosting/models/containers-docker-and-podman.md#images). Whichever base,
run `php bin/php/console exp:velocity ext check` inside the built image in your pipeline.

A generic build, in words rather than a Dockerfile:

1. Start from the pinned base.
2. Copy your bundle's `composer.json` and `composer.lock`; run `composer install --no-dev --no-interaction` as the
   user the site will run as.
3. Add your site package files and import them, or plan to import at first start ([Offline bundles](offline-bundles.md)).
4. Regenerate autoloads ([Caches and autoloads after building](caches-and-autoloads-after-building.md)).
5. Set ownership of the writable directories ([File permissions](file-permissions-and-var-ownership.md)).
6. Add the entry point below; do not run the installer during the build.

## Image or volume

| Path | Where |
|---|---|
| `kernel/`, `lib/`, `vendor/`, `extension/`, `design/` | in the image |
| `var/` | persistent volume |
| `settings/override` | persistent volume |
| `settings/siteaccess` | persistent volume |

"Never bake `settings/override` into an image: it holds the database credentials"
([Containers](../hosting/models/containers-docker-and-podman.md#secrets)). Decide the user ID once for the image and
the volume, and keep it.

## An entry point

The documented minimal entry point "installs once, then starts the server and stays in the foreground while it runs"
([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)):

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

- Pass `EXP_INSTALL_DB_PASSWORD` in the container's environment.
- "If the entry point runs as `root`, add `--allow-root-user` to the two `exp:velocity` commands."
- Change `--db` to the database of your bundle (`sqlite` is the default, `pgsql` the recommended server).
- Without `--password`, a 24-character password is generated and written to `var/log/initial-admin-password`; tell
  the recipient how to read it once.

## Secrets and the database

- **SQLite:** one writing container per database file; "SQLite's WAL needs shared memory on one host".
- **Several replicas:** a database server ("PostgreSQL first"), shared storage for `var/storage`, and sessions every
  container can read ([Kubernetes](../hosting/models/kubernetes.md#sessions)).
- **Cron** still has to run: a scheduler container sharing the volumes, as the same user
  ([Containers](../hosting/models/containers-docker-and-podman.md#operations-inside-containers)).

## Image checklist

- [ ] Base image pinned by tag or digest; Composer from the lock file; `--no-dev`.
- [ ] `exp:velocity ext check` passes in the image.
- [ ] No `settings/override`, `settings/siteaccess`, `kickstart.ini`, logs or caches from the build in the image.
- [ ] Volumes documented: `var/`, `settings/override`, `settings/siteaccess`.
- [ ] The entry point installs once and keeps the main process alive.
- [ ] The source of every GPL component is offered ([GPL obligations](gpl-obligations-when-distributing.md)).
- [ ] Labels or notes name the Exponential version and your bundle version.

## Related pages

- [Containers: Docker and Podman](../hosting/models/containers-docker-and-podman.md)
- [First boot and install once](first-boot-and-install-once.md)
- [Velocity in a bundle](velocity-in-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
