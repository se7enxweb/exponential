---
title: Deploying with Velocity
audience: admin, developer
category: examples
order: 130
summary: Put a PHP, INI, template or style sheet change live with exp:velocity deploy — what each step does, which option each kind of change needs, a dry run first, and how to check that the new code answers on every server.
keywords: deploy, exp:velocity deploy, Velocity, PHP-FPM, reload, restart, dry-run, kernel, packer, no-velocity, no-fpm, autoload, engine.phar, cache clear, PhpFpmService, DeploySettings, response cache
type: example
---
# Deploying with Velocity

A running site keeps code in memory: PHP-FPM workers have their OPcache, Velocity's persistent workers have the
classes, the settings and the compiled templates they loaded when they started. A change reaches visitors only when
those are renewed **and** the caches that hold output made by the old code are cleared afterwards. `exp:velocity
deploy` does both, in the right order, with one command. You deploy the example extension of the cookbook.

Prerequisites: a shell in the installation root, as root (the reload of PHP-FPM and the restart of a Velocity that
listens on ports below 1024 need it); Velocity installed or not (without it the Velocity steps are skipped).

## 1. Look first

```bash
php bin/php/console exp:velocity deploy --dry-run --allow-root-user
```

Every step is printed with what it would do (`DRY`), nothing is changed. Check the PHP-FPM service it found: on a
machine with several PHP versions the one serving the site is often not the one called `php-fpm`.

## 2. Which deploy for which change

| You changed | Run |
|---|---|
| A PHP class, operator, datatype, event or view of an extension | `exp:velocity deploy` |
| A **new** or renamed class of the kernel (`kernel/`, `lib/`) | `exp:velocity deploy --kernel` |
| An ezjscore server function, or the settings of the packer | `exp:velocity deploy --packer` |
| A style sheet or script of a design | `exp:velocity deploy --packer` (the packed files and the template blocks that link them) |
| An INI file | `exp:velocity deploy` (it clears the INI caches and restarts Velocity) |
| A template only | no deploy: `php bin/php/ezcache.php --clear-tag=template --allow-root-user` (a new template file also `--clear-id=template-override`), then `./console exp:velocity cache clear` |
| Content | nothing: publishing expires what it touches |

```bash
php bin/php/console exp:velocity deploy --allow-root-user
```

## 3. What the steps are

| # | Step | By hand |
|---|---|---|
| 1 | extension autoloads | `php bin/php/ezpgenerateautoloads.php -e` |
| 2 | kernel autoloads, only with `--kernel` (restricted to `kernel/` and `lib/`) | `php bin/php/ezpgenerateautoloads.php -k` |
| 3 | INI caches | `php bin/php/ezcache.php --clear-tag=ini` |
| 4 | template, template-override, translation and design_base caches | `--clear-id=<id>`, one per call |
| 5 | engine archive, when Velocity runs from one (rebuilt only when something in it changed) | `php bin/php/console exp:phar build` |
| 6 | reload PHP-FPM | `systemctl reload <[DeploySettings] PhpFpmService>` |
| 7 | restart Velocity, when it runs | `exp:velocity restart` |
| 8 | content, exphttpcache, (`--packer`: ezjscore-packer,) template-block caches | `--clear-id=<id>` |
| 9 | Velocity's response cache | `exp:velocity cache clear` |

Each step prints `PASS`, `FAIL` or `SKIP` with its time; the first failure stops the run with exit status 1 and lists
the steps not run. The caches of rendered output (steps 8 and 9) come **after** the reload and the restart: cleared
earlier, a page requested in between is rendered by the old code and cached again.

Options: `--kernel`, `--packer`, `--no-autoload`, `--no-fpm`, `--no-velocity`, `--rebuild-phar`, `--engine=<name>`,
`--dry-run`, `--json`.

## 4. Settings

`settings/velocity.ini [DeploySettings]`, changed in `settings/override/velocity.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[DeploySettings]
# auto: find the pool of this installation; a unit name (or several, comma-separated); disabled when Velocity
# serves the site alone
PhpFpmService=auto

*/ ?>
```

`auto` looks for the pool configuration named after the installation's hosting domain or naming its directory in
`/opt/plesk/php/*/etc/php-fpm.d`, `/etc/php-fpm.d`, `/etc/php/*/fpm/pool.d` and `/etc/opt/remi/php*/php-fpm.d`. The
reload is graceful (`systemctl reload`): no request is dropped. Run as a user other than root, the step is skipped with
a note naming the service.

## 5. Check that the new code answers

```bash
php bin/php/console exp:velocity status --allow-root-user       # the server and its workers, started a moment ago
php bin/php/console exp:phar check --allow-root-user            # the engine archive is current (when one is used)
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/   # 200 from the public site
```

Then look at the change itself on every server that answers the site: the HTTPS port of Apache or nginx (PHP-FPM) and
the port of Velocity, if both run. A change visible on one and not the other means one of them still runs the old code:
the reload found another PHP-FPM, or Velocity was not restarted.

Checked for this page (2026-10-18): the steps, their order and the options are those of
`doc/en/us/install/08-serving-the-site.md` (section 8.3.14) and `settings/velocity.ini [DeploySettings]`. The commands were
not run for this page.

## Common mistakes

| Symptom | Cause |
|---|---|
| The change works on Velocity's port, not on the web server's | the PHP-FPM reloaded is not the one of the site; set `PhpFpmService` to the unit |
| `Class '...' not found` after adding a kernel class | deployed without `--kernel` |
| A new style sheet is not used | deployed without `--packer`; then a hard reload in the browser |
| The page still shows the old text for a few seconds | Velocity's response cache: step 9 ran before your request, or clear it again |
| `exp:velocity restart` alone and the change looks lost | the content cache was not cleared after the restart; use `deploy` |

## Derived from

- `doc/en/us/install/08-serving-the-site.md`, sections 8.3.14 and 8.3.15
- `settings/velocity.ini` `[DeploySettings]`; `kernel/classes/expvelocitydeploy.php`
- `doc/en/us/bc/6.0/velocity-engines.md`

## Related pages

- [Cookbook](README.md)
- [Serving the site: Velocity, Apache, nginx and FrankenPHP](../../install/08-serving-the-site.md)
- [Velocity engines](../../bc/6.0/velocity-engines.md), [Engine archive (phar)](../../bc/6.0/phar.md),
  [Deploying](../../guides/deploying.md)
- [Caching](caching.md); the web server and Velocity configurations in [Examples](../README.md)
- [Commands](../commands/README.md): `exp:velocity` and the other console commands
- The coding rules and the pattern catalogue: [the standards](../../standards/README.md)
- [Free documentation](../../help/free-documentation.md): this page is free to copy, translate and improve.
