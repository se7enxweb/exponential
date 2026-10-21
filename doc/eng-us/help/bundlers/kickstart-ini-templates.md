---
title: kickstart.ini templates for bundles
audience: bundler, hosting, developer
category: bundling
order: 220
summary: How to keep the installation of a bundle as a reviewed kickstart.ini template with placeholders, render it at install time with mode 0600, run a dry run and the install, and remove it afterwards - with a complete SQLite template and a PostgreSQL variant.
keywords: kickstart.ini, template, placeholder, envsubst, umask 077, 0600, dry run, run --force, SQLite, PostgreSQL, DatabaseAction, Site_package, bundle, starter kit
type: help
---
# kickstart.ini templates for bundles

This page is for bundlers who want the install of their bundle written down as a file that can be reviewed, diffed and
versioned. The pattern comes from [6.11 In CI and containers](../../install/06-kickstarter.md#611-in-ci-and-containers).

## Contents

1. [The pattern](#the-pattern)
2. [A SQLite template](#a-sqlite-template)
3. [A PostgreSQL variant](#a-postgresql-variant)
4. [Rendering and running it](#rendering-and-running-it)
5. [Reviewing a template](#reviewing-a-template)

## The pattern

1. "Keep a template, e.g. `deploy/kickstart.ini.template`, under version control with placeholders for secrets."
2. "Render it at deploy time into the installation root with mode `0600`."
3. "Run a dry run, then the install, and fail the job on a non-zero status."
4. Remove the rendered file.

The placeholders below are in `${NAME}` form for `envsubst`; any template tool works.

## A SQLite template

Keys from [6.4.2 Sections and keys](../../install/06-kickstarter.md#642-sections-and-keys). Check each against the
`kickstart.ini-dist` of the version you ship.

```ini
[database_choice]
Continue=true
Type=sqlite3

[database_init]
Continue=true
Database=${SITE_DB_FILE}

[language_options]
Continue=true
Primary=eng-US

[site_types]
Continue=true
Site_package=${SITE_PACKAGE}

[site_access]
Continue=true
Access=url

[site_details]
Continue=true
Title=${SITE_TITLE}
URL=${SITE_URL}
Access=site
AdminAccess=admin
EditorAccess=editor
Database=${SITE_DB_FILE}
DatabaseAction=remove

[site_admin]
Continue=true
FirstName=Site
LastName=Administrator
Email=${ADMIN_EMAIL}
Password=

[security]
Continue=true

[registration]
Continue=true
Send=false
```

- For SQLite, `Database` is "a file name in `var/storage/sqlite3/`"; give the same value in `[database_init]` and
  `[site_details]`.
- `Password=` stays empty: the installer generates one and writes it to `var/log/initial-admin-password`.
- `DatabaseAction=remove` is right for a first install into a new file and must never meet a database that holds data.

## A PostgreSQL variant

Change the database blocks; keep the rest:

```ini
[database_choice]
Continue=true
Type=pgsql

[database_init]
Continue=true
Server=${DB_HOST}
Port=5432
Database=${DB_NAME}
User=${DB_USER}
Password=${DB_PASSWORD}
```

and `Database=${DB_NAME}` in `[site_details]`. "None of the installers creates a database server's database or user.
Create them first" ([Database support](../database-support.md#installer-support-per-path)). Annotated full examples
for SQLite, MySQL or MariaDB and PostgreSQL are in [6.6](../../install/06-kickstarter.md#66-annotated-example-files).

## Rendering and running it

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /srv/exponential
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
rm -f kickstart.ini
```

This is the script of [6.11](../../install/06-kickstarter.md#611-in-ci-and-containers). Points to remember from that
section: "`run` needs no terminal"; "The run takes a few minutes; give the job enough time"; "In a container, run the
install once ... not at every start".

If you edit a rendered `kickstart.ini` by hand and the browser wizard is to read it, delete
`var/cache/ini/kickstart-*.php` first: "`ini` and `run` delete [them] themselves ... The browser wizard does not."

## Reviewing a template

- [ ] Every placeholder is filled from your secret store or the recipient's input, never from a value in the
      template.
- [ ] `DatabaseAction` is written explicitly.
- [ ] `[site_admin] Password` is empty, or filled from a secret store with a long random value.
- [ ] `[registration] Send=false`.
- [ ] `URL` and `Email` belong to the recipient.
- [ ] The rendered file is created with mode `0600` and removed after the run.
- [ ] A dry run of the template runs in your CI for every bundle release.

## Related pages

- [The kickstarter for bundles](the-kickstarter-for-bundles.md)
- [The kickstarter for hosts](../hosting/provisioning/kickstarter.md#templates-with-placeholders)
- [Agency starter kits](agency-starter-kits.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
