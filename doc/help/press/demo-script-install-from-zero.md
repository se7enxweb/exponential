---
title: "Demo script: install from zero"
audience: press
category: press-media
order: 160
summary: A ten-minute video script from an empty folder to a running Exponential site - clone, Composer, a dry run, the one-command install on SQLite, serving the site, the first sign-in and the password change - with the commands from the installation chapters and the moments you must cut or blur.
keywords: demo script, install, installation, from zero, exp:install, SQLite, Composer, git clone, dry run, first sign-in, password, Velocity, video, creator
type: help
---
# Demo script: install from zero

This page is for creators who film an installation of Exponential from nothing. The commands come from
[Develop with Git](../develop-with-git.md#a-new-site), [Installing Exponential in one command](../../features/6.0/install-in-one-command.md)
and [The console install](../../install/07-console-install.md). `exp:install` is part of the 6.0.15 line, which is in
development; on the release 6.0.14 you would use the setup wizard or the kickstarter instead
([Choosing an install method](../../install/04-choosing-an-install-method.md)). Say on camera which one you show.

The script runs about ten minutes of edited video. Downloads take longer: the documentation names more than 27,000
commits and about 370 MB for the kernel's clone, and a few minutes for the extensions; cut the waiting.

## Contents

1. [Before you record](#before-you-record)
2. [The script](#the-script)
3. [Moments to cut or blur](#moments-to-cut-or-blur)
4. [If it fails on camera](#if-it-fails-on-camera)
5. [Description block](#description-block)

## Before you record

```text
[ ] A demo machine or virtual machine you may wipe; PHP, Git 2.28+ and Composer 2 installed
[ ] Requirements checked once (PHP version and extensions), so the take does not fail on them
[ ] Terminal: large font, plain prompt without a real host or user name, empty history
[ ] A web server or Velocity ready to serve the folder you install into
[ ] You ran the whole script once, off camera, and timed it
```

Requirements: [Requirements](../../install/02-requirements.md). The full creator setup is on
[Setting up a demo installation](setting-up-a-demo-installation.md).

## The script

| Step | Command or click | Say |
|---|---|---|
| 1 | Title card | Disclosure. "From an empty folder to a running Exponential site, on <version>." |
| 2 | `git clone https://github.com/se7enxweb/exponential.git exponential` | "The kernel, with its whole history." |
| 3 | `cd exponential` and `git switch -c my-demo` | "My own branch on top of main, the development line." (For the release: `git switch -c my-demo v6.0.14`.) |
| 4 | `composer install` | "Composer fetches the extensions and libraries. Exponential needs this step before it can run." |
| 5 | `php bin/php/console exp:install --dry-run` | "A dry run checks the configuration and the packages and installs nothing." |
| 6 | `php bin/php/console exp:install` | "Without options: the multisite package, on SQLite, in American English, with no database server." |
| 7 | Show the first line of output only | "It says what it is about to do." Then cut to the end |
| 8 | Show the summary with the password lines blurred | "Site address, administration login, user name, database, package and siteaccesses. The generated password is printed once and written to a file only the owner can read." |
| 9 | Serve the site: your web server, or `php bin/php/console exp:velocity start` | "The installer does not start a web server; I serve the folder." |
| 10 | Browser: the admin login address from the summary | "The administration sign-in." Pause the recording while you type the password |
| 11 | User menu, **Change password** | "First thing: change the generated password, then delete `var/log/initial-admin-password`." |
| 12 | The dashboard, then the site | "And here is the site." |

The database choices, in the documented order of preference: SQLite (the default), PostgreSQL, MongoDB, MariaDB,
Oracle, MySQL ([Installing Exponential in one command](../../features/6.0/install-in-one-command.md#choose-a-database)).
If you show another database, give the password with the environment variable `EXP_INSTALL_DB_PASSWORD`, which keeps
it out of the process list and shell history, and never type it on camera.

## Moments to cut or blur

| Moment | Why |
|---|---|
| The `Password:` lines and the "Copy and paste" block of the summary | They print the administrator password |
| Any `--password=` or `--db-password=` you typed | Passwords in the command line and history |
| `cat var/log/initial-admin-password` | Never run it on camera |
| The sign-in form while you type | Pause the recording |
| Error output with full paths of your machine | Read it first; blur what you do not want to show |

Even after you blur it, treat a password that was on your screen during a recording as burned: change it
([Keeping secrets off screen](keeping-secrets-off-screen.md)).

## If it fails on camera

| Symptom | Cause, and what to do |
|---|---|
| "This directory already holds an installation ..." | An earlier take: add `--force` on this demo machine only |
| "Unknown option or missing value" | Every value needs `=`: `--db=mysql`, not `--db mysql` |
| The run stops while fetching packages | No network access to the package server |
| The site shows the maintenance page after a failed run | Fix the cause and run again, or `php bin/php/maintenance.php off` |
| Permission errors after installing as root | Give the files to the web server's user |

Full table: [What can go wrong](../../install/07-console-install.md#711-what-can-go-wrong). Logs: `var/log/setup.log`.

## Description block

```text
Installs Exponential <version> from scratch on SQLite with one command, on my own test machine.
<disclosure line>

Commands shown:
  git clone https://github.com/se7enxweb/exponential.git exponential
  composer install
  php bin/php/console exp:install --dry-run
  php bin/php/console exp:install
Install in one command: <link to "Installing Exponential in one command">
Change the generated password after the first sign-in: <link to "The default administrator password">
```

## Related pages

- [Setting up a demo installation](setting-up-a-demo-installation.md)
- [The default administrator password](../default-administrator-password.md)
- [Demo script: Velocity](demo-script-velocity.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
