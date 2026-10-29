---
title: Automating provisioning
audience: hosting
category: hosting
order: 70
summary: A provisioning pipeline for new Exponential customer sites, step by step: system user and isolation, database, code from Git or Composer, exp:install with secrets from the environment, serving the site, cron, password handover, backups and monitoring, with checks after each step and a way to remove a site again.
keywords: automation, provisioning, pipeline, script, system user, PHP-FPM pool, Velocity, database, Git, Composer, exp:install, EXP_INSTALL_DB_PASSWORD, crontab, initial-admin-password, backup, monitoring, idempotent, deprovision
type: help
---
# Automating provisioning

This page is for system administrators and developers who automate the creation of Exponential customer sites. It
answers: which steps a pipeline needs, in which order, what each step checks, and how a site is removed again. Your
own tooling (panel hooks, configuration management, shell scripts) does the work; this page gives the Exponential side
of each step.

## Contents

1. [The pipeline](#the-pipeline)
2. [Step by step](#step-by-step)
3. [Making it safe to run again](#making-it-safe-to-run-again)
4. [Removing a site](#removing-a-site)
5. [A record per site](#a-record-per-site)

## The pipeline

| # | Step | Checks |
|---|---|---|
| 1 | System user and directory | the user exists, the directory belongs to it |
| 2 | PHP: FPM pool or Velocity instance | the pool or instance runs as that user |
| 3 | Database | empty database, user with create rights (not for SQLite 3) |
| 4 | Code | the tested release is checked out, `vendor/` complete |
| 5 | Install | `exp:install --dry-run`, then `exp:install`; exit status 0 |
| 6 | Serve | the site answers over HTTPS |
| 7 | Cron | the site user's crontab runs `runcronjobs.php` |
| 8 | Handover | password delivered, `var/log/initial-admin-password` removed |
| 9 | Backups and monitoring | the site is registered in both |

## Step by step

### 1. System user and directory

One system user per site ([Per-customer users and pools](../security/per-customer-users-and-pools.md)). Everything
the installer and the site write must belong to that user: "Run it as the user the web server or Velocity runs as, or
fix the ownership afterwards" ([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).

### 2. PHP

- PHP-FPM: a pool for the site, running as its user ([PHP-FPM tuning](../performance/php-fpm-tuning.md)).
- Velocity: workers run as `[ServerSettings] User` and `Group` of the site's `velocity.ini`; give each site its own
  ports ([Velocity workers and pools](../servers/velocity-workers-and-pools.md)).

### 3. Database

For SQLite 3, nothing but a writable `var/storage/sqlite3/`. For a server engine, create a database and a user for
this site alone ([Databases and users in panels](../panels/databases-and-users.md)); never reuse `root` or `postgres`.

### 4. Code

A checkout of the release tag you tested, then the dependencies from the lock file:

```bash
git clone --branch v6.0.<n> https://github.com/se7enxweb/exponential.git <installation>
cd <installation> && composer install --no-dev
```

Pin one release for all new sites and move the pin only after a test ([Composer and Git on servers](../basics/composer-and-git-on-servers.md)).

### 5. Install

Pass the database password through the environment and test first:

```bash
export EXP_INSTALL_DB_PASSWORD='<from your secret store>'
php bin/php/console exp:install --dry-run --db=pgsql --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact> --package=sevenx_multisite_clean
php bin/php/console exp:install --apply --db=pgsql --db-host=<db host> --db-name=<db> --db-user=<db user> \
  --url=https://www.example.com --email=<contact> --package=sevenx_multisite_clean
```

On exit status 1, keep `var/log/setup.log` and stop the pipeline; the site stays in maintenance mode
([exp:install for hosts](exp-install.md#exit-status)).

### 6. Serve

Configure the web server for the site, or set the Velocity engine and start it
([Choosing a web server](../servers/choosing-a-web-server.md)). Under Apache or LiteSpeed, put `.htaccess` into place,
and `config.php` with the values this server needs, from the console. Run as root, every command needs
`--allow-root-user`, and `--owner` gives a new file to the site's user:

```bash
php bin/php/console exp:setup:htaccess --apply --allow-root-user --owner=<site user>:<site group>
php bin/php/console exp:setup:config --apply --allow-root-user --owner=<site user>:<site group>
php bin/php/console exp:config:set EZP_DIR_MODE_MAX 0750 --apply --allow-root-user
php bin/php/console exp:config:set EZP_FILE_MODE_MAX 0640 --apply --allow-root-user
php bin/php/console exp:config:list --only-set --allow-root-user
```

Each command ends with `PASS:` or `FAIL:` and exits 0, 1 or 2; a second run says `nothing to do`
([Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md#11-in-scripts-containers-and-ci)).
Then check from outside:

```bash
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/
curl -s -o /dev/null -w '%{http_code}\n' https://www.example.com/admin/user/login
```

Both should answer `200` ([10.11](../../../install/10-after-installing.md#1011-monitoring-and-health-checks)).

### 7. Cron

Install the crontab of [10.3](../../../install/10-after-installing.md#a-crontab) in the site user's crontab, with the
same PHP binary the web uses ([Cron jobs](../operations/cron-jobs.md)).

### 8. Handover

If the password was generated, read it from `var/log/initial-admin-password`, deliver it through a secure channel and
delete the file. Tell the customer to change it and create personal accounts
([First sign-in and default passwords](../security/first-sign-in-and-default-passwords.md)).

### 9. Backups and monitoring

Register the database and `var/storage`, `settings/override`, `settings/siteaccess` in the backup system, and the
site's address in monitoring ([What to back up](../operations/backups-what-to-back-up.md),
[Health checks and uptime](../operations/health-checks-and-uptime.md)).

## Making it safe to run again

- **Detect an existing installation** the way `exp:install` does: `settings/override/site.ini.append.php` holds a
  `[DatabaseSettings]` block. Skip the install step when it does; `exp:install` itself refuses without `--force`.
- **Never pass `--force` from a pipeline** unless the run is meant to destroy and rebuild that site.
- **Keep `--db-action=remove` away from databases with data.** A pipeline should always create a new database for a
  new site.
- **Keep secrets out of logs.** Do not echo the environment; the saved configuration in `var/log/` masks passwords,
  and the `exp:config:*` commands print `********` for every password or key unless given `--show-secrets`. Pass a
  secret as `--value="$VARIABLE"`, not typed on the command line.
- **`.htaccess` and `config.php` are safe to repeat.** `exp:setup:htaccess` and `exp:setup:config` never replace a
  file that differs from its template without `--force`, and `exp:config:set` changes nothing when the value is
  already there. In a drift check, `exp:setup:htaccess` without `--apply` exits 1 when `.htaccess` has changed, and
  `exp:config:list --only-set --format=json` gives a document to compare.

## Removing a site

The reverse order: maintenance mode on, a final backup, crontab removed, web server or Velocity instance removed,
database and user dropped, files removed, backups kept for the agreed retention. The full list is the
[Customer offboarding checklist](../checklists/customer-offboarding.md).

## A record per site

Write what the pipeline did into a record per site: domain, system user, PHP version, engine and ports, database
engine and name, release tag, site package, install date, backup and monitoring registration. Support staff need it
on the first call.

## Related pages

- [One-click installers](one-click-installers.md)
- [Containers: Docker and Podman](../models/containers-docker-and-podman.md)
- [New server checklist](../checklists/new-server.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
