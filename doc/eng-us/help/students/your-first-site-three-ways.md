---
title: "Your first site: three ways"
audience: student
category: learning
order: 9
summary: The three ways a learner gets a first Exponential site - the public demo, the one-command install on your own computer, and the browser setup wizard - plus Git clones for future developers, with what each needs, what you can and cannot do with it, and which one to choose for each learning path.
keywords: first site, install, demo, exp:install, setup wizard, kickstarter, git clone, sqlite, study installation, choose, compare, beginner
type: help
---
# Your first site: three ways

Every learning path in this folder starts with a site you can use. This page compares the ways to get one, so you
can pick the right one in five minutes. All of them are documented in
[Getting started: install Exponential](../../guides/getting-started.md) and
[Installing Exponential 6.0](../../INSTALL.md).

## Contents

1. [The options at a glance](#the-options-at-a-glance)
2. [Which one for your path](#which-one-for-your-path)
3. [What every installation gives you](#what-every-installation-gives-you)
4. [What you need on your computer](#what-you-need-on-your-computer)
5. [Before you begin](#before-you-begin)

## The options at a glance

| Way | Needs | Good for | Limits | Page |
|---|---|---|---|---|
| **The public demo** | A browser | A first look at the site and the administration | Shared with everyone, reset regularly, administrator passwords cannot be changed; no terminal | [Trying the public demo](trying-the-public-demo.md) |
| **One command** (`exp:install`) | PHP 8.0 or newer (PHP 7.4 usable without support), Composer | Every learning path; fast to rebuild | Runs on your computer, so you set it up yourself | [Installing a study site in one command](installing-a-study-site-in-one-command.md) |
| **The setup wizard** | PHP, Composer, a browser | Learners who prefer clicking to commands | Slower to repeat than one command | [Installing with the setup wizard](installing-with-the-setup-wizard.md) |
| **Git clones** | The above, plus Git | The developer path, and contributing | A larger download (the kernel has its full history) | [Installing from Git clones for study](installing-from-git-clones-for-study.md) |

A fourth installer, the **kickstarter**, reads its answers from a file `kickstart.ini`. It is made for repeating the
same installation on several servers ([Getting started](../../guides/getting-started.md#33-from-a-file-repeatable-installs));
learners rarely need it.

## Which one for your path

| Path | First choice | Why |
|---|---|---|
| Editor | The demo for day one, then one command | The demo shows you around; your own site lets you work without others changing it |
| Administrator | One command | You will reinstall, change settings and read logs |
| Developer | Git clones | You will read history, change code and send fixes back |
| Designer | One command, or Git clones | You will change templates and create a design extension |

## What every installation gives you

From [Installing Exponential 6.0](../../INSTALL.md#1-what-you-get-and-the-quick-start):

- the Exponential kernel and the extensions listed in `composer.json`;
- a **site package** with its content and design (the default is `sevenx_multisite`;
  see [Site packages for learners](site-packages-for-learners.md));
- three **siteaccesses**, always called `site` (visitors), `admin` (administrators) and `editor` (editors: the
  administration for content only).

With `exp:install` on its defaults, the database is **SQLite** in a file, so you need no database server
([Getting started](../../guides/getting-started.md#1-what-you-need)).

## What you need on your computer

| Need | Detail | Source |
|---|---|---|
| PHP | 8.0 to 8.5; check with `php -v` | [Requirements](../../INSTALL.md#2-requirements) |
| PHP settings | `memory_limit` at least `64M`, `date.timezone` set | [Requirements](../../INSTALL.md#2-requirements) |
| Composer | version 2 | [Getting started](../../guides/getting-started.md#1-what-you-need) |
| Git | 2.28 or later, only for Git clones | [Develop with Git](../develop-with-git.md#what-you-need) |
| A database server | None to start: SQLite is the default | [Database support](../database-support.md) |
| A web server | None to start: Exponential can start PHP's own server for you | [Getting started](../../guides/getting-started.md#4-see-the-site) |

On a running installation, `php bin/php/console exp:velocity ext check` lists the PHP extensions the installation
wants and marks what is missing ([Requirements](../../INSTALL.md#2-requirements)).

## Before you begin

- **Use your own machine or a training machine** you may change. Do not install on a server of your school or
  employer without permission.
- **Keep the administrator password** the installer prints. It is shown once.
- **Use invented data.** Test accounts get addresses at `example.invalid`, so no exercise sends mail to a real
  person (the same rule trainers keep, [Trainers: start here](../trainers/trainers-start-here.md#the-rules-every-trainer-keeps)).
- **Write down your release** (`php bin/php/console --version`) in your [study journal](keeping-a-study-journal.md).

## Related pages

- [Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md)
- [Your first hour in the administration](your-first-hour-in-the-administration.md)
- [Installing Exponential 6.0](../../INSTALL.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
