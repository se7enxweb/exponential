---
title: Installing a study site in one command
audience: student
category: learning
order: 11
summary: A learner's walk through the documented quick start - get the code with Composer, install on SQLite with exp:install, serve the site with the php engine, sign in, change the password - with the expected output of each step and what to do when a step fails.
keywords: install, exp:install, one command, composer create-project, sqlite, velocity, php engine, localhost, study site, quick start, random password, beginner
type: help
---
# Installing a study site in one command

This page takes you through the quick start of [Installing Exponential 6.0](../../INSTALL.md#1-what-you-get-and-the-quick-start)
and [Getting started: install Exponential](../../guides/getting-started.md), with notes for learners. It needs PHP and
Composer and no database server. Allow fifteen minutes; the install itself takes about a minute on SQLite, according
to the getting-started guide.

## Contents

1. [Check your PHP](#check-your-php)
2. [Get the files](#get-the-files)
3. [Install](#install)
4. [Serve the site](#serve-the-site)
5. [Sign in and change the password](#sign-in-and-change-the-password)
6. [If a step fails](#if-a-step-fails)

## Check your PHP

```bash
php -v
```

Expected: a line beginning `PHP 8.`. Exponential 6.0 runs on PHP 7.4 to 8.5
([Requirements](../../INSTALL.md#2-requirements)). Set `memory_limit` (at least `64M`) and `date.timezone` in
`php.ini` before you go on.

## Get the files

For a site you only want to try and study, the Composer project is the quickest path:

```bash
composer create-project se7enxweb/exponential exponential
cd exponential
php bin/php/console --version
```

Expected: the first line reads `console (Exponential) 6.0.15stable` (or the version you installed). Write it in your
[study journal](keeping-a-study-journal.md).

The console is `bin/php/console`. Many installations add a shortcut in the root (`ln -s bin/php/console console`),
so that `./console` and `php bin/php/console` are the same program ([Installing Exponential 6.0](../../INSTALL.md)).
This folder writes `php bin/php/console`, which works everywhere. For the developer path, get the files as Git
clones instead: [Installing from Git clones for study](installing-from-git-clones-for-study.md).

## Install

```bash
php bin/php/console exp:install --apply --random-password --url=http://localhost:8087
```

What this does, as the [getting-started guide](../../guides/getting-started.md#31-the-one-command) describes it:

- installs the multisite site package on SQLite, in `eng-US`;
- creates the siteaccesses, with the public site and the administration told apart by the first part of the address;
- generates a 24-character administrator password, shows it **once** at the end, and writes it to
  `var/log/initial-admin-password`;
- refuses to run over an existing installation unless you add `--force`.

At the end you see a box with the site address, the admin login address, the username `admin` and the password.
Copy the password into a password manager now. Do not put it in your journal or in a commit.

If you work as `root` (on a training virtual machine, for example), scripts that need it ask for
`--allow-root-user`.

## Serve the site

```bash
php bin/php/console exp:velocity start --engine=php
php bin/php/console exp:velocity status --engine=php
```

The `php` engine is PHP's own server, for development; it listens on port 8087 by default, which is why the install
used `--url=http://localhost:8087` ([Getting started](../../guides/getting-started.md#4-see-the-site)).

Open `http://localhost:8087/site/` in a browser. You see the front page of your study site. Stop the server with
`php bin/php/console exp:velocity stop --engine=php` when you are done for the day.

## Sign in and change the password

1. Open `http://localhost:8087/admin/user/login`.
2. Sign in as `admin` with the printed password.
3. Use **Change password** in the user menu and choose your own.
4. Delete `var/log/initial-admin-password` afterwards, as the guide asks
   ([Getting started](../../guides/getting-started.md#5-first-login)).

You are now on the dashboard of the administration. Continue with
[Your first hour in the administration](your-first-hour-in-the-administration.md).

## If a step fails

| What you see | What to do | Source |
|---|---|---|
| `exp:install` says an installation exists | Add `--force` only if you mean to replace it | [Getting started](../../guides/getting-started.md#8-when-something-goes-wrong) |
| A white page or a 500 error | Read `var/log/error.log`; check `php -v` and that `vendor/` exists | same |
| The address does not answer | Is the server running (`exp:velocity status --engine=php`)? Does the port match `--url`? | same |
| You lost the password | Look in `var/log/initial-admin-password`, or set a new one with `php bin/php/resetuserpassword.php --allow-root-user -u admin -g` | same |
| A change does not show | Clear the content cache: `php bin/php/ezcache.php --clear-tag=content --allow-root-user` | [Getting started](../../guides/getting-started.md#6-first-content) |
| A PHP extension is missing | `php bin/php/console exp:velocity ext check` lists what is missing | [Requirements](../../INSTALL.md#2-requirements) |

Still stuck? [Reading logs and errors](reading-logs-and-errors.md) and
[Asking for help and study groups](asking-for-help-and-study-groups.md).

## Related pages

- [Your first site: three ways](your-first-site-three-ways.md)
- [Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md)
- [Install in one command](../../features/6.0/install-in-one-command.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
