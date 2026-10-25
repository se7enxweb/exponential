---
title: Velocity in a bundle
audience: bundler, hosting
category: bundling
order: 270
summary: How a bundle serves Exponential with Velocity, the web and application server that is part of Exponential since 6.0.15 - where it comes from (Composer, deb and rpm, Docker images, binaries), the engine setting, users and root, starting at boot, deploying a change, and what to check.
keywords: Velocity, exponential-velocity, qbix, Engine, velocity.ini, ServerSettings, User, Group, AllowRootWorkers, systemd, exp:velocity start, status, deploy, ext check, deb, rpm, ghcr.io, bundle
type: help
---
# Velocity in a bundle

This page is for bundlers who serve the bundled site with Exponential Velocity. "As of 6.0.15 the root `composer.json`
**requires** `se7enxweb/exponential-velocity ~0.0.4.55`", so every installation built with Composer already has it
([Velocity engines](../../bc/6.0/velocity-engines.md#velocity-is-part-of-exponential-as-of-6015)).

## Contents

1. [Where Velocity comes from](#where-velocity-comes-from)
2. [Settings a bundle sets](#settings-a-bundle-sets)
3. [Users and root](#users-and-root)
4. [Starting it](#starting-it)
5. [Deploying a change](#deploying-a-change)
6. [Checks](#checks)

## Where Velocity comes from

| Form | For | Source |
|---|---|---|
| Composer package `se7enxweb/exponential-velocity` | every Exponential installation | `composer.json` |
| deb and rpm packages | a system service on Debian 12 and 13, Ubuntu 22.04 and 24.04, Enterprise Linux 9 and 10 | [Velocity packages](../../features/6.0/velocity-packages-and-binaries.md#operating-system-packages) |
| Docker images on `ghcr.io/se7enxweb/exponential-velocity` | containers, amd64 and arm64 | [Velocity packages](../../features/6.0/velocity-packages-and-binaries.md#docker) |
| static binaries, the phar, a source kit | single machines, laptops, appliances, unusual platforms | [Velocity packages](../../features/6.0/velocity-packages-and-binaries.md#pick-a-form) |

"An Exponential installation already has the engine through Composer and needs none of this"; the other forms are
for Velocity outside an installation. "All files of a release carry checksums in `SHA256SUMS`."

## Settings a bundle sets

Settings live in `settings/velocity.ini`; a bundle overrides them in `settings/override/velocity.ini.append.php`
(start from `doc/eng-us/examples/web-servers/velocity.ini.append.php.example`) or with
`exp:velocity config set <Block> <Setting> <value>`, which writes the same file
([Velocity example](../../examples/web-servers/velocity.md)).

```ini
[ServerSettings]
Engine=qbix
```

"`Engine` is the **default** engine ... It ships as `php` ... any site, from development to production, sets `qbix`."
The shipped port of `qbix` is `8088` on `127.0.0.1`, "reachable only locally or through a reverse proxy"; a bundle
that answers on 80 and 443 changes the addresses as the example shows.

## Users and root

- `[ServerSettings] User` and `Group` name the user the workers run as; "Velocity started as root is fine: only its
  parent process stays root, and root workers are refused unless `AllowRootWorkers=enabled` (shipped `disabled`)"
  ([Writable directories and permissions](../hosting/basics/writable-directories-and-permissions.md#setup-1-one-user)).
- `exp:velocity` itself refuses `root` "unless `--allow-root-user` is given"
  ([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
- When Velocity and Apache or nginx with PHP-FPM serve the same installation, "they share `var/`, the caches and the
  sessions: give them the same user, or this group setup".

## Starting it

- **On a server:** a systemd unit that runs `php bin/php/console exp:velocity start --allow-root-user`. The example
  `doc/eng-us/examples/web-servers/exponential-velocity.service` is "Not shipped as a unit"; copy it, replace the
  paths and the database unit, and "Do not combine it with the unit of the operating-system package for the same
  ports".
- **In a container:** `exp:velocity start` "detaches the server and returns as soon as it answers", so the entry point
  loops on `exp:velocity status` ([Container images](container-images.md#an-entry-point)).

## Deploying a change

"A changed PHP class reaches Velocity's workers only after a restart; `deploy` does it in the right order":

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user
php bin/php/console exp:velocity deploy --allow-root-user
php bin/php/console exp:velocity deploy --kernel --allow-root-user   # a kernel class was added or renamed
```

Source: [Velocity engines](../../bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy). In containers
"the usual equivalent is a new image and a new container".

## Checks

```bash
php bin/php/console exp:velocity status
php bin/php/console exp:velocity ext check
```

`status` "reports the engine package and its version"; `ext check` lists the PHP extensions the installation wants and
what is missing. Velocity needs `ext-sockets` (`pcntl` and `openssl` recommended).

## Related pages

- [Web server and engine choices](web-server-and-engine-choices.md)
- [Velocity for hosts](../hosting/servers/velocity-for-hosts.md), [Velocity will not start](../hosting/runbooks/velocity-will-not-start.md)
- [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
