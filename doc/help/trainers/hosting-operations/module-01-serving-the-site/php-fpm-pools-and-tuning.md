---
title: "Lesson 1.3: PHP-FPM pools and tuning"
audience: trainer, hosting, admin
category: training
order: 13
summary: Learners set the PHP values Exponential needs in a PHP-FPM pool, line up the timeouts, check APCu, and find and reload the master that really serves the site. 45 minutes.
keywords: hosting and operations, PHP-FPM, pool, memory_limit, max_execution_time, request_terminate_timeout, APCu, ApcuReserve, reload, PhpFpmService, PhpFpmReload, sizing
type: help
---
# Lesson 1.3: PHP-FPM pools and tuning

Under Apache or nginx the PHP-FPM pool is where a host isolates and tunes each site. This lesson covers the PHP
values Exponential needs, the timeouts that must agree with each other, APCu, and the most common operational
mistake: reloading a PHP-FPM master that does not serve the site.

## At a glance

| | |
|---|---|
| Course | [Hosting and operations](../README.md) |
| Module | [Module 1: Serving the site](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | System administrators and hosting support staff |
| Prerequisites | [Lesson 1.2](apache-and-nginx-with-php-fpm.md); root on the learner's own virtual machine |

## Learning objectives

After this lesson, learners can:

- set `memory_limit`, `max_execution_time`, the upload limits and `date.timezone` for one pool;
- explain why `request_terminate_timeout` is the real wall-clock limit under PHP-FPM;
- check APCu and explain why `ApcuReserve` matters when sites share a master;
- find the master that serves a site and preview a deploy's reload with `--dry-run`.

## Trainer's notes

### How to present it

Source: [PHP-FPM tuning](../../../hosting/performance/php-fpm-tuning.md) and
[Chapter 8, PHP-FPM](../../../../install/08-serving-the-site.md#852-php-fpm).

1. **One pool per site** (3 minutes). A pool runs PHP as the site's user; it is the unit of isolation and tuning.
   Panels such as Plesk create one pool per domain.
2. **PHP values** (5 minutes). Show the table of the tuning page: `memory_limit` at least 256M in production,
   `max_execution_time` at least 30 (60 in chapter 2's production example), `date.timezone`, `file_uploads=On`,
   upload limits for the customer's largest upload, `allow_url_fopen=On`, `variables_order` containing `E`, empty
   `open_basedir`, OPcache on. The web PHP and the command-line PHP read different files.
3. **Timeouts** (4 minutes). `max_execution_time` is the script's own timer; `request_terminate_timeout` is enforced
   by the master whatever the script does. On SQLite keep `SQLiteTransactionWait` (25 s by default) below the request
   timeout. Long jobs belong on the command line.
4. **APCu** (3 minutes). APCu is one memory for every site of the pool; when it fills up it empties itself
   completely. `[QueryCacheSettings] ApcuReserve=25` keeps a quarter free; keep it above 0.
5. **Reloading** (5 minutes). A reload is not confined to one site: the master waits for every child of every pool.
   `exp:velocity deploy` reloads only when it must (`[DeploySettings] PhpFpmReload=auto`), and
   `PhpFpmService=auto` finds the right master. Show `deploy --no-velocity --dry-run`.

### Common questions

**How many children should a pool have?** The sources give no number. They give a method: measure with
`exp:benchmark --cold` at rising concurrency and pick the point where p90 starts to climb (Module 7).

**I changed `php.ini` and nothing happened.** Most often the change went into the file the other PHP reads (CLI
versus FPM), or the master serving the site was not reloaded.

**Why do sessions matter here?** The role-aware HTTP cache needs sessions in files every server of the site can read;
moved elsewhere, it stores nothing for signed-in visitors.

**When must I force a reload?** After a `php.ini` or pool change, a new PHP extension, or PHP files removed or renamed
in place (`deploy --fpm-reload`).

### Pitfalls

- **Several PHP versions.** The master serving the site is often not the one called `php-fpm`. Find it by its pool
  file, as in step 6.
- **Learners without root.** The reload step needs root; run as anyone else it is skipped with a note. Make sure
  learners use their own virtual machine.
- **Editing the wrong `php.ini`.** Have learners print `php --ini` and compare with the pool's `php_admin_value`
  lines.

## Hands-on exercise

Use your own Linux virtual machine from lesson 1.2.

1. Open your site's pool file (for example below `/etc/php-fpm.d/` or `/etc/php/<version>/fpm/pool.d/`).
2. Note the pool's `user`, `listen` and any `php_admin_value` lines.
3. Add or check these lines in the pool:

   ```ini
   php_admin_value[memory_limit] = 256M
   php_admin_value[max_execution_time] = 60
   php_admin_value[date.timezone] = Europe/Berlin
   php_admin_value[upload_max_filesize] = 64M
   php_admin_value[post_max_size] = 64M
   ```

4. Look for `request_terminate_timeout` in the pool and write down its value, or that it is not set.
5. Reload that pool's PHP-FPM service with `systemctl reload <service>`.
6. In the installation root, preview a deploy and read which master it found and whether it would reload or wait:

   ```bash
   php bin/php/console exp:velocity deploy --no-velocity --dry-run --allow-root-user
   ```

7. Sign in to the administration of your training site and open **Setup**, then **System information**. In the section
   **PHP extensions and settings**, check `memory_limit`; under **OPcache and APCu**, check that APCu is listed.
8. Run `php -m | grep -i apcu` and compare the command-line PHP with what the web page showed.
9. Write down one difference between the CLI and the pool settings, if any.

## Expected result

- After the reload, **System information** shows `memory_limit` 256M for the web PHP.
- The `--dry-run` output lists each deploy step as `DRY` and names the PHP-FPM service it found, or explains why it
  would skip the reload.
- The learner can say whether the CLI PHP and the pool differ, and why that matters.
- The trainer checks by asking each learner which service name the dry run found.

## Quiz

1. Which limit is the real wall-clock limit for a request under PHP-FPM?
   A. `max_execution_time` B. `request_terminate_timeout` C. `memory_limit` D. `post_max_size`
2. What happens when a shared APCu fills up?
3. Why can a PHP-FPM reload slow down other customers' sites?
4. What does `[DeploySettings] PhpFpmService=auto` do?
5. Where do long-running imports and cache warming belong, and why?

<details>
<summary>Answers</summary>

1. **B.** The master enforces it whatever the script does ([PHP-FPM tuning](../../../hosting/performance/php-fpm-tuning.md#timeouts-that-must-agree)).
2. It empties itself completely, and every other cache and counter in it is lost; `ApcuReserve` keeps space free.
3. The master asks every child of every pool to finish its request and starts no new child until the last one has.
4. It finds the master that serves the site by its pool file (Plesk, distribution and Remi naming).
5. On the command line, where the PHP CLI has no execution limit by default; web timeouts stay sized for pages.

</details>

## Further reading

- [PHP-FPM tuning](../../../hosting/performance/php-fpm-tuning.md): every value and the sizing method.
- [PHP settings for Exponential](../../../hosting/basics/php-settings-for-exponential.md): the php.ini reference.
- [PHP selection and FPM pools](../../../hosting/panels/php-selection-and-fpm-pools.md): pools in hosting panels.
- [Per-customer users and pools](../../../hosting/security/per-customer-users-and-pools.md): isolation between
  customers.

## Related pages

- [Module 1: Serving the site](README.md)
- [Hosting and operations: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
