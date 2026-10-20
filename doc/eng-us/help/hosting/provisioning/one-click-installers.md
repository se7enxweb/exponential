---
title: One-click installers for Exponential
audience: hosting
category: hosting
order: 20
summary: How a host offers Exponential as a one-click install: the one installer with three front ends (setup wizard, kickstarter, exp:install), which one fits a panel, a script or a customer, what the one click must do before and after, and the safety rules for passwords and databases.
keywords: one-click installer, auto installer, app installer, exp:install, kickstarter, setup wizard, provisioning, panel, CreateSites, password, database action, site package, unattended
featured: true
type: help
---
# One-click installers for Exponential

This page is for hosting companies that want customers to get an Exponential site with one click in their panel or
order form. It answers: what Exponential already provides for unattended installs, which front end to build on, and
what your one click must do around it.

## Contents

1. [One installer, three front ends](#one-installer-three-front-ends)
2. [Which front end for which offer](#which-front-end-for-which-offer)
3. [What the one click must do](#what-the-one-click-must-do)
4. [Safety rules](#safety-rules)
5. [What the customer receives](#what-the-customer-receives)

## One installer, three front ends

Every install method runs the same sequence of step classes in `kernel/setup/steps/`, from `Welcome` to `Final`;
`CreateSites` is the step that writes the database, the packages and the settings. The front ends differ only in
where the answers come from ([4.1](../../../install/04-choosing-an-install-method.md#41-one-installer-three-front-ends)):

| | Setup wizard | Kickstarter | `exp:install` |
|---|---|---|---|
| Answers from | browser forms | `kickstart.ini` | command-line options, each with a default |
| Needs a web server during the install | yes | no | no |
| Dry run | no | `run --dry-run` | `--dry-run`, `--print` |
| Guard against overwriting | starts only while `CheckValidity=true` | `--force` required | refuses an existing installation without `--force` |
| Log | `var/log/setup.log` | `setup.log`, `kickstart.log` | `setup.log`, `var/log/exp-install-<date>.ini` |
| Administrator password | typed in the form | from the file; generated when empty or well known | generated unless an acceptable `--password` is given |

Source: [4.2](../../../install/04-choosing-an-install-method.md#42-the-three-methods-at-a-glance).

## Which front end for which offer

| Offer | Front end | Why |
|---|---|---|
| A panel button that creates the site | `exp:install` | "Prefer `exp:install` in automation. It needs no file, validates its options before anything is written, and returns a non-zero exit status on every failure" ([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)) |
| Templates you maintain ("agency starter site") | the kickstarter | the reviewed `kickstart.ini` is the specification of the install |
| Customers on shared hosting without a shell | the setup wizard | needs only a browser; can be pre-filled by a `kickstart.ini` |
| Container images | `exp:install` in the entry point, run once | see [Containers: Docker and Podman](../models/containers-docker-and-podman.md) |

## What the one click must do

Exponential installs the site. Your installer does everything around it:

1. **Prepare the account**: a system user, a home directory, a PHP-FPM pool or a Velocity instance for the site
   ([Per-customer users and pools](../security/per-customer-users-and-pools.md)).
2. **Prepare the database**: nothing for SQLite 3 (the default; a writable `var/storage/sqlite3/` is enough); for a
   server engine, an empty database and a user that may create tables
   ([Databases and users in panels](../panels/databases-and-users.md)).
3. **Put the code in place**: a Git checkout of a release tag, or `composer create-project`/`composer install` from a
   lock file you tested ([Composer and Git on servers](../basics/composer-and-git-on-servers.md)).
4. **Run the installer as the site's user**, never as root:

   ```bash
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --dry-run --db=pgsql --db-user=<db user> --url=https://www.example.com
   sudo -u <site user> env EXP_INSTALL_DB_PASSWORD='<from your secret store>' \
     php bin/php/console exp:install --db=pgsql --db-user=<db user> --url=https://www.example.com \
     --email=<customer contact>
   ```

5. **Serve the site**: point the web server at it, or start Velocity ([Choosing a web server](../servers/choosing-a-web-server.md)).
6. **Install the crontab** for `runcronjobs.php` ([Cron jobs](../operations/cron-jobs.md)).
7. **Hand over the password**: read `var/log/initial-admin-password` once, give it to the customer through a secure
   channel, and delete the file; or set the password from your secret store with `--password=`.
8. **Register the site** in backups and monitoring.

Each step is described in [Automating provisioning](automating-provisioning.md).

## Safety rules

- **Never point `--db-action=remove` at a database that holds data.** It is the default of `exp:install` and "empties
  the database first" ([7.3](../../../install/07-console-install.md#73-options)). A one-click installer should create
  a new, empty database each time.
- **Keep secrets off the command line.** Use `EXP_INSTALL_DB_PASSWORD` rather than `--db-password`; an option "is
  visible to every user of the machine in the process list (`ps`) while the installation runs".
- **Run as the site's user.** "Files created by `root` cannot be changed by the web server later".
- **Check the exit status.** `0` installed, `1` anything failed; on failure maintenance mode stays on, and
  `var/log/setup.log` says why ([7.7](../../../install/07-console-install.md#77-exit-status)).
- **Do not leave install files behind.** A `kickstart.ini` holds passwords in clear text; remove it after the
  install, as the wizard would read it on a later run.

## What the customer receives

- The site address and the administration address (by URL `/admin`, by host, or by port, depending on `--access`).
- The administrator login `admin` and its password, once; advice to change it and create personal accounts
  ([First sign-in and default passwords](../security/first-sign-in-and-default-passwords.md)).
- The site package you chose, with or without demo content ([Site packages](site-packages.md)).
- A link to the help: the help dashboard in their administration.

## Related pages

- [exp:install](exp-install.md)
- [The kickstarter](kickstarter.md)
- [Licensing for hosts](../business/licensing-for-hosts.md) (images and installers you distribute)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
