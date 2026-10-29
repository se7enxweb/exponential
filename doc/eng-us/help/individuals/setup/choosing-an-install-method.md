---
title: Choosing an install method for one person
audience: individual, website-creator
category: personal-sites
order: 1631
summary: Which of Exponential's three install methods suits an individual - the setup wizard for shared hosting without a shell, exp:install for a quick start on your own computer or a small server, the kickstarter for repeatable installs - with the options that matter for a personal site and the mistakes to avoid.
keywords: install, install method, setup wizard, exp:install, console install, kickstarter, kickstart.ini, SQLite, dry run, shared hosting, FTP, no shell, first install, individual
type: help
---
# Choosing an install method for one person

This page is for individuals who are about to install Exponential. There are three methods; all three run the same
installation steps and produce the same result. They differ in how they ask their questions
([Choosing an install method](../../creators/website-creators/choosing-an-install-method.md),
[Chapter 4 of the installation book](../../../install/04-choosing-an-install-method.md)).

## Contents

1. [The three methods](#the-three-methods)
2. [Which one for you](#which-one-for-you)
3. [The quick start on your own computer](#the-quick-start-on-your-own-computer)
4. [The setup wizard on shared hosting](#the-setup-wizard-on-shared-hosting)
5. [Options that matter for a real site](#options-that-matter-for-a-real-site)
6. [Mistakes to avoid](#mistakes-to-avoid)

## The three methods

| Method | You answer | Needs | Best for |
|---|---|---|---|
| **Setup wizard** | Forms in the browser, one page per step | A web server during the install; a browser and FTP | A first install; hosts without shell access |
| **Console install** (`exp:install`) | Command-line options, each with a default | A shell | The fastest install; your own computer; a small server |
| **Kickstarter** (`exp:kickstarter`) | A reviewed `kickstart.ini` file | A shell | Repeating the same install (a test copy and the real site) |

## Which one for you

| Your situation | Choose |
|---|---|
| You want to see Exponential running tonight on your laptop | `exp:install` with its defaults |
| Your host offers only FTP and a control panel | The setup wizard |
| You have a small virtual server with a shell | `exp:install`, with the options below |
| You will rebuild the site several times while you learn | The kickstarter, so every rebuild is the same |

## The quick start on your own computer

With PHP and Composer installed and the code fetched ([Getting the code](../../../install/03-getting-the-code.md)):

```bash
php bin/php/console exp:install --dry-run
php bin/php/console exp:install --apply
```

The defaults are SQLite and the default site package `sevenx_multisite`, with demo content to explore
([Console install](../../../install/07-console-install.md)). Then serve it
([Serving the site](../../../install/08-serving-the-site.md)) and sign in
([Default administrator password](../../default-administrator-password.md)).

## The setup wizard on shared hosting

1. Upload the code, including `vendor/`, by FTP. Without a shell you cannot run Composer on the host, so prepare
   `vendor/` on your own computer and upload it with the rest
   ([Shared hosting, Installing without a shell](../../hosting/models/shared-hosting.md#installing-without-a-shell)).
2. Open the site's address in the browser; the wizard starts.
3. Follow its pages: system checks, database (SQLite needs nothing prepared), site package, languages, site details.
4. At the end, sign in and change the administrator password
   ([Change the admin password](../../change-the-admin-password.md)).

Since 6.0.15 the wizard works with a single database you own and does not need privileges a shared host does not give
([A clean installation that fits shared hosting](../../../features/6.0/clean-install-defaults.md)). The full chapter:
[The setup wizard](../../../install/05-setup-wizard.md).

## Options that matter for a real site

For `exp:install` on a server, set at least ([7.3 Options](../../../install/07-console-install.md#73-options)):

| Option | Why |
|---|---|
| `--package=sevenx_multisite_clean` | No demo content in the real site |
| `--url=https://www.example.com` | Links, mails and feeds are built from it |
| `--access=host` with `--host`, `--admin-host` | Separate addresses for the site and the administration; best with HTTPS |
| `--title` | The site name |
| `--email` | A real address; it becomes the administrator mail |
| `--language` | Your site's main language |

## Mistakes to avoid

- **Reinstalling a site that holds real content.** An install replaces the database. Back up first, and never point
  an install at your live database ([Backups for one person](../running-it/backups-for-one-person.md)).
- **Keeping the default administrator password.** Change it in the first minute.
- **Building the real site on the full package with demo content.** Deleting demo content later is tedious; use the
  `_clean` package for the real site.
- **Running the web server and the install as different users** on a server, so files are not writable later
  ([Writable directories and permissions](../../hosting/basics/writable-directories-and-permissions.md)).

## Related pages

- [Setup for individuals](README.md)
- [Choosing a site package and theme](choosing-a-site-package-and-theme.md)
- [Choosing an install method (website creators)](../../creators/website-creators/choosing-an-install-method.md)
- [After installing](../../../install/10-after-installing.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
