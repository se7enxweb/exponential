---
title: Installing with the setup wizard
audience: student
category: learning
order: 12
summary: A learner's guide to the browser setup wizard - how it starts, the fifteen pages and what a study site should enter on each, what the wizard writes, what it teaches you about an installation, and how it differs from the one-command install.
keywords: setup wizard, browser installer, install, system check, database, site package, siteaccess, administrator, CheckValidity, study site, beginner
type: help
---
# Installing with the setup wizard

The setup wizard installs Exponential in the browser, page by page. It is slower than one command, but it shows you
every decision an installation makes: the system check, the database, the languages, the site package and the
siteaccesses. That makes it a good teacher. This page follows
[Installing Exponential 6.0, section 5](../../INSTALL.md#5-install-with-the-setup-wizard) and adds notes for a study
site.

## Contents

1. [How the wizard starts](#how-the-wizard-starts)
2. [The pages and what to enter](#the-pages-and-what-to-enter)
3. [What you learn from the wizard](#what-you-learn-from-the-wizard)
4. [Wizard or one command](#wizard-or-one-command)
5. [If something goes wrong](#if-something-goes-wrong)

## How the wizard starts

1. Get the files: `composer create-project se7enxweb/exponential exponential` and `cd exponential`
   ([Installing a study site in one command](installing-a-study-site-in-one-command.md#get-the-files)).
2. Serve the installation, for example with `php bin/php/console exp:velocity start --engine=php`.
3. Open the site address in a browser (`http://localhost:8087/` with the `php` engine's default port).

`settings/site.ini` ships with `[SiteAccessSettings] CheckValidity=true`; while it is `true`, every request goes to
the setup wizard. The last step writes `CheckValidity=false` into `settings/override/site.ini.append.php`, and from
then on the site is served ([Installing Exponential 6.0](../../INSTALL.md#how-it-starts)).

While the wizard runs, only your browser gets through; other visitors see a "The site is being set up" page.

## The pages and what to enter

The table summarizes the steps of the installation guide, with a suggestion for a study site.

| # | Page | For a study site |
|---|---|---|
| 1 | Welcome | Continue |
| 2 | System check | Fix every critical problem it lists; each comes with the fix. Do not ignore checks on a study site you will keep |
| 3 | System finetuning | Optional checks; read them, then **Next** |
| 4 | Outgoing Email | Choose a setting that sends no real mail from exercises (see the note below) |
| 5 | Choose database system | Keep SQLite, which is listed first and preselected |
| 6 | Database initialization | For SQLite only a file name; keep the default |
| 7 | Language support | `eng-US`, and a second language if you plan [project 5](project-05-a-multilingual-site.md) |
| 8 | Site package | Keep the preselected one; see [Site packages for learners](site-packages-for-learners.md) |
| 9 | Package language options | Shown only when needed; keep the preselection |
| 10 | Site access configuration | **URL path** is the easiest on one computer |
| 11 | Site details | A title such as "My study site"; the match values of `site`, `admin` and `editor` |
| 12 | Site administrator | A password of at least 10 characters; an e-mail address at `example.invalid` |
| 13 | Site security | Read the advice |
| 14 | Registration | Nothing to enter; the wizard sends no registration e-mail |
| 15 | Finished | Note the addresses of the public, admin and editor sites |

The wizard logs its progress to `var/log/setup.log` ([Installing Exponential 6.0](../../INSTALL.md#the-steps)).

> **Note**
> No study exercise should send mail to a real person. Use addresses at `example.invalid` for every test account,
> as trainers do ([Trainers: start here](../trainers/trainers-start-here.md#the-rules-every-trainer-keeps)). If you
> are unsure how mail is handled on your computer, ask before you test anything that sends mail.

Unlike `exp:install`, the wizard writes no `var/log/initial-admin-password` file, because you type the password
yourself ([Getting started](../../guides/getting-started.md#8-when-something-goes-wrong)). Keep it in a password
manager.

## What you learn from the wizard

| Page | Concept to note in your journal | Read more |
|---|---|---|
| System check | Which PHP extensions Exponential needs | [Requirements](../../INSTALL.md#2-requirements) |
| Database | SQLite needs no server; other databases are choices | [Database support](../database-support.md) |
| Languages | Content languages are chosen at install and can be added later | [Content languages](../../guides/content-languages.md) |
| Site package | A site is built from packages, not from code alone | [Site packages: start here](../site-packages/README.md) |
| Site access | One installation is reached in several ways, each with its own settings | [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md) |

## Wizard or one command

| | Setup wizard | `exp:install` |
|---|---|---|
| How | Browser, fifteen pages | One command |
| Good for | Seeing every decision | Fast, repeatable rebuilds |
| Administrator password | You type it | Generated and shown once, or given with `--password` |
| Repeating the same install | Slow | One line |

Many learners use the wizard once, to understand, and `exp:install` afterwards, to rebuild quickly
([Resetting and rebuilding your study site](resetting-and-rebuilding-your-study-site.md)).

## If something goes wrong

- The system check names each problem and its fix; for file permissions it shows the exact commands.
- After the install, errors are in `var/log/error.log`; the wizard's own log is `var/log/setup.log`
  ([Reading logs and errors](reading-logs-and-errors.md)).
- The full chapter: [The setup wizard](../../install/05-setup-wizard.md); troubleshooting:
  [Troubleshooting](../../install/12-troubleshooting.md).

## Related pages

- [Your first site: three ways](your-first-site-three-ways.md)
- [Setup wizard and editor siteaccess](../../features/6.0/setup-wizard-and-editor-siteaccess.md)
- [Your first hour in the administration](your-first-hour-in-the-administration.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
