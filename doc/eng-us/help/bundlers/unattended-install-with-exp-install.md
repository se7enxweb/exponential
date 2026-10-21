---
title: Unattended install with exp:install
audience: bundler, hosting, developer
category: partnership
order: 200
summary: How a bundle installs Exponential with one command - exp:install and its options for database, site, administrator and control, secrets in EXP_INSTALL_DB_PASSWORD, the database action, the generated password file, exit status, logs, and safe patterns for images and installers.
keywords: exp:install, install.php, unattended, --db, --db-action, --package, --url, --access, --host, --admin-host, --email, --password, --random-password, --force, --dry-run, --print, EXP_INSTALL_DB_PASSWORD, initial-admin-password, exit status
type: help
---
# Unattended install with exp:install

This page is for bundlers who install the site from a script: an image's first boot, a panel installer, a CI job.
`exp:install` (also `php bin/php/install.php`) "writes a `kickstart.ini` from its options and runs the kickstarter"
([The default administrator password](../default-administrator-password.md#the-console-install-expinstall)). The full
reference is [Chapter 7: The console install](../../install/07-console-install.md).

## Contents

1. [The shortest install](#the-shortest-install)
2. [The options](#the-options)
3. [Secrets](#secrets)
4. [The database action](#the-database-action)
5. [Exit status and logs](#exit-status-and-logs)
6. [Patterns for bundles](#patterns-for-bundles)

## The shortest install

```bash
php bin/php/console exp:install
```

"That installs the multisite package on **SQLite**, in `eng-US`, with the siteaccesses `site` and `admin` reached by
URL, and prints a summary with the site and administration login addresses"
([Install in one command](../../features/6.0/install-in-one-command.md)).

## The options

From `php bin/php/install.php --help` (6.0.15):

| Group | Options |
|---|---|
| Database | `--db` (`sqlite` default, `mysql`/`mariadb`, `pgsql`, `mongodb`, `oracle`), `--db-host`, `--db-port`, `--db-name`, `--db-user`, `--db-password`, `--db-socket`, `--db-action` (`remove` default, `ignore`, `skip`) |
| Site | `--package` (default `sevenx_multisite`), `--language` (default `eng-US`), `--languages`, `--title`, `--url`, `--access` (`url`, `host`, `port`), `--site-access`, `--admin-access`, `--host`, `--admin-host`, `--port`, `--admin-port`, `--editor-access`, `--editor-host`, `--editor-port` |
| Administrator (login `admin`) | `--email` (default `nospam@exponential.earth`), `--password`, `--random-password`, `--first-name`, `--last-name` |
| Run | `--force`, `--dry-run`, `--print`, `--help` |

A few rules from the help text:

- `--host` or `--admin-host` imply `--access=host`; "--access=host needs both --host and --admin-host", and they
  must differ.
- `--password` "is kept when it has at least 10 characters (site.ini MinPasswordLength) and is not a well-known one";
  otherwise a generated one replaces it and the summary says so.
- `--random-password` generates 24 characters, "shown once at the end and written to var/log/initial-admin-password".
- Always set `--url` for a real site; the default is `http://localhost`.
- Always set `--email` to an address of the recipient; the default is a placeholder.

## Secrets

- **Database password:** "Give the database password with `--db-password` or, better, with the environment variable
  `EXP_INSTALL_DB_PASSWORD`, which keeps it out of the process list and shell history."
- **Administrator password:** let it be generated and hand it over from `var/log/initial-admin-password`, or pass one
  from your secret store. "Never write it into a script or an image"
  ([The default administrator password](../default-administrator-password.md#the-console-install-expinstall)).
- `--print` shows the configuration "passwords masked"; `var/log/exp-install-<date>.ini` keeps it masked too.

## The database action

`--db-action=remove` is the default and "empties the database first". It is "what a fresh container wants, and
exactly what must never point at a database that holds data you need"
([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).

| Value | Use in a bundle |
|---|---|
| `remove` | the first install into a new, empty database or a new SQLite file |
| `ignore` | adding into an empty database; a second run adds a second copy of the data |
| `skip` | rewriting the settings against an existing, complete database (`--db-action=skip --force`) |

## Exit status and logs

| Status | Meaning |
|---|---|
| `0` | installed; or `--dry-run` passed; or `--print`; or `--help` |
| `1` | an option failed its check, the directory already holds an installation without `--force`, or the run failed |

On failure, maintenance mode stays on and `var/log/setup.log` says why
([7.7 Exit status](../../install/07-console-install.md#77-exit-status)). Keep `var/log/setup.log` and
`var/log/kickstart.log` from every failed bundle install.

## Patterns for bundles

A dry run, then the install, failing on any error:

```bash
set -euo pipefail
export EXP_INSTALL_DB_PASSWORD="$(cat /run/secrets/db_password)"
php bin/php/console exp:install --dry-run --db=pgsql --db-host=db --db-name=site --db-user=site \
    --url=https://www.example.com --email=webmaster@example.com
php bin/php/console exp:install --db=pgsql --db-host=db --db-name=site --db-user=site \
    --url=https://www.example.com --email=webmaster@example.com
```

Separate hosts for the site and the administration:

```bash
php bin/php/console exp:install --access=host --host=www.example.com --admin-host=admin.example.com
```

Run it as the site's user, never as root: "Files created by `root` cannot be changed by the web server later"
([One-click installers](../hosting/provisioning/one-click-installers.md#safety-rules)). Install once, then never at
every start ([First boot and install once](first-boot-and-install-once.md)).

## Related pages

- [exp:install for hosts](../hosting/provisioning/exp-install.md)
- [The three installers](the-three-installers.md)
- [Default credentials and first sign-in](default-credentials-and-first-sign-in.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
