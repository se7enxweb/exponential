---
title: The kickstarter for hosts
audience: hosting, admin
category: hosting
order: 40
summary: How a host uses the kickstarter for repeatable Exponential installs from a reviewed kickstart.ini: the ini and run commands, DatabaseAction and why it can destroy data, dry runs, templates with placeholders, logs, and cleaning up the file afterwards.
keywords: kickstarter, kickstart.ini, exp:kickstarter, kickstarter.php, ini, run, --force, --dry-run, --list-steps, DatabaseAction, remove, ignore, skip, template, envsubst, staging, repeatable install, kickstart.log
type: help
---
# The kickstarter for hosts

This page is for system administrators who install the same kind of Exponential site again and again: a host's
standard templates, staging copies, training sites. It answers: how the kickstarter works, how to keep its
configuration as a reviewed template, and how not to destroy a database with it. The reference is
[Chapter 6](../../../install/06-kickstarter.md).

## Contents

1. [What it is](#what-it-is)
2. [The commands](#the-commands)
3. [DatabaseAction](#databaseaction)
4. [Templates with placeholders](#templates-with-placeholders)
5. [Logs](#logs)
6. [Clean up](#clean-up)

## What it is

"The kickstarter (`bin/php/kickstarter.php`, console name `exp:kickstarter`) runs the step classes in one PHP process
on the command line and reads every answer from `kickstart.ini`" ([4.2](../../../install/04-choosing-an-install-method.md#the-kickstarter)).
It is the method "to use when the same installation has to be made more than once, when the answers should be reviewed
as a file before they are applied, or when the site package comes from a local package repository".

## The commands

```bash
php bin/php/console exp:kickstarter ini --yes          # write kickstart.ini from the built-in defaults
php bin/php/console exp:kickstarter run --list-steps   # show the steps, change nothing
php bin/php/console exp:kickstarter run --dry-run      # check, stop after SiteDetails
php bin/php/console exp:kickstarter run --force        # install
```

- `ini` writes the file: interactively, with `--yes` (built-in defaults) or `--defaults` (the values of
  `kickstart.ini-dist`).
- `run` installs from it. `--force` "is required whenever the range of steps includes `CreateSites`".
- A call without a command "shows the help and exits with status 0, so a script that forgot the subcommand does not
  fail; check that your scripts name `run`" ([6.2](../../../install/06-kickstarter.md#62-the-commands)).

## DatabaseAction

> **Warning**
> "`DatabaseAction=remove` destroys data. It empties the database named in `[site_details] Database` and installs
> into it ... Everything in that database is lost, and nothing asks you again: `--force` is the only confirmation."
> ([6.5](../../../install/06-kickstarter.md#65-databaseaction-read-this-before-you-run))

| Value | Database | Use it for |
|---|---|---|
| `remove` | tables dropped (SQLite: file emptied), then installed | a clean install, a reinstall |
| `ignore` | added to whatever is there, nothing dropped | a new, empty database |
| `skip` | nothing inserted | regenerating `settings/siteaccess/` and `settings/override/` against a complete database |

"A value that is missing or misspelled behaves like `ignore`": write it explicitly. Back up any database a run might
touch into a directory outside the installation root first.

## Templates with placeholders

The pattern from [6.11](../../../install/06-kickstarter.md#611-in-ci-and-containers), for a host's standard sites:

1. Keep a template such as `deploy/kickstart.ini.template` under version control, with placeholders for the customer
   values and secrets.
2. Render it at provisioning time into the installation root with mode `0600`.
3. Dry run, then install, and fail on a non-zero status:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /var/www/vhosts/<domain>/<installation>
umask 077
envsubst < deploy/kickstart.ini.template > kickstart.ini
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --force
rm -f kickstart.ini
```

`exp:kickstarter ini --from-installed` fills `Database` from an existing installation's settings, which helps when you
build a template from a site you already run ([6.2.1](../../../install/06-kickstarter.md#621-ini-write-kickstartini)).

> **Tip**
> The browser wizard reads `kickstart.ini` too and pre-fills or skips pages from it
> ([5.6](../../../install/05-setup-wizard.md#56-how-kickstartini-pre-fills-and-skips-pages)). A host can pre-fill the
> database page for a customer who then finishes the install in the browser
> ([The setup wizard on shared hosting](setup-wizard-on-shared-hosting.md)).

## Logs

| File | Holds |
|---|---|
| `var/log/setup.log` | every step of the run |
| `var/log/kickstart.log` (`.1` to `.9` keep earlier runs) | each kickstarter run, passwords masked; `EXP_KICKSTART_LOG=0` turns it off |

"`var/log/kickstart.log` and `var/log/setup.log` are the artefacts to keep from a failed job."

## Clean up

- Remove `kickstart.ini` after the install: it "holds passwords in clear text" and the wizard would read it on a later
  run ([4.6](../../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)).
- Deliver and remove `var/log/initial-admin-password` if a password was generated.

## Related pages

- [exp:install for hosts](exp-install.md)
- [Automating provisioning](automating-provisioning.md)
- [One-click installers](one-click-installers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
