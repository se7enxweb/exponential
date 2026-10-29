---
title: The setup wizard on shared hosting
audience: hosting
category: hosting
order: 50
summary: How customers without shell access install Exponential in the browser with the setup wizard, and what the host prepares: code upload, writable directories, the .htaccess file, HTTPS, the database, the maintenance hold during the run, and a kickstart.ini that pre-fills pages.
keywords: setup wizard, shared hosting, browser install, no shell, FTP, CheckValidity, .htaccess_root, .htaccess, system check, directory permissions, maintenance hold, kickstart.ini, pre-fill, HTTPS
type: help
---
# The setup wizard on shared hosting

This page is for hosts whose customers install Exponential themselves in a browser, typically on shared hosting
without shell access, and for the support staff who help them. It answers: what the host must prepare, what the
customer sees, and how the wizard protects the site while it runs. The page-by-page reference is
[Chapter 5](../../../install/05-setup-wizard.md).

## Contents

1. [When the wizard is the right choice](#when-the-wizard-is-the-right-choice)
2. [What the host prepares](#what-the-host-prepares)
3. [What the customer does](#what-the-customer-does)
4. [While it runs](#while-it-runs)
5. [After the wizard](#after-the-wizard)
6. [Pre-filling pages with kickstart.ini](#pre-filling-pages-with-kickstartini)

## When the wizard is the right choice

The installation book recommends it for "shared hosting without shell access ... it needs only a browser and FTP
access" ([4.5](../../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation)). It is also "the
only method that shows each decision on a page with an explanation". When you can run commands for the customer,
[exp:install](exp-install.md) is faster and scriptable.

## What the host prepares

| Item | Why |
|---|---|
| PHP 8.0 or later (PHP 7.4 usable without support) with the required extensions; Apache with `mod_rewrite` and PHP-FPM is "the classic setup; shared hosting" | [Requirements at a glance](../basics/requirements-at-a-glance.md) |
| The code, with `vendor/` (the dependencies), in the customer's web space | the wizard cannot run Composer; upload a complete release |
| `settings/`, `var/` and `design/` writable by the user PHP runs as | the System check tests `[directory_permissions] CheckList` ([Writable directories and permissions](../basics/writable-directories-and-permissions.md)) |
| `.htaccess` in place: a copy of the shipped `.htaccess_root` | the rewrite rules that keep internal files from being served |
| HTTPS on the domain before the wizard is opened | "The database password and the administrator password travel in those hidden fields with every page" ([5.1](../../../install/05-setup-wizard.md#how-the-wizard-remembers-your-answers)) |
| A database and its user, or nothing for SQLite 3 | SQLite needs only a writable `var/storage/sqlite3/` |

> **Tip**
> If `.htaccess` is missing, the wizard's **Site security** page asks for it with "cp .htaccess_root .htaccess" and
> "If you do not have shell access, you will have to copy the file using an FTP client or ask your hosting provider
> to do this for you" ([5.3.13](../../../install/05-setup-wizard.md#5313-site-security)). Put it there in advance and
> the page is skipped.

The host's provisioning can do that with the console, before the customer opens the wizard. As root, the copy needs
`--allow-root-user` and should be given to the account's user:

```bash
cd /path/to/the/installation
php bin/php/console exp:setup:htaccess --apply --allow-root-user --owner=<account-user>:<account-group>
php bin/php/console exp:setup:config --apply --allow-root-user --owner=<account-user>:<account-group>   # optional
```

Without `--owner`, a new file gets the owner of the template it is copied from, never root. Details:
[Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md).

## What the customer does

1. Opens the site's address. While `[SiteAccessSettings] CheckValidity=true` (the shipped value), "whatever path you
   open, the wizard's first page appears" ([5.1](../../../install/05-setup-wizard.md#51-how-the-wizard-starts)).
2. Walks the pages: welcome, system check, mail, database, languages, site package, siteaccesses, site details,
   administrator, and the installation itself ([5.3](../../../install/05-setup-wizard.md#53-the-pages-step-by-step)).
3. Chooses an administrator password. The wizard "refuses a short or well-known one"
   ([The default administrator password](../../default-administrator-password.md)).
4. Reaches the Finished page with the addresses of the new site.

If the System check fails, it prints the cause and the fix, for permissions the exact `chmod` and `chown` commands;
on shared hosting those are often something only the host can run.

## While it runs

The wizard holds the site for the browser that started it ([5.2](../../../install/05-setup-wizard.md#52-the-browser-hold-and-maintenance-mode)):

- The first posted step writes `var/maintenance.json` with a 15-minute lease, renewed by every further step.
- Every other visitor gets HTTP 503 "The site is being set up".
- The Finished page ends the hold; an abandoned wizard "does not keep the site offline: once the lease has run out,
  the marker no longer counts".

## After the wizard

- `CreateSites` writes `CheckValidity=false` to `settings/override/site.ini.append.php`; "from then on the site is
  served normally and the wizard can no longer be reached".
- On an installed site, `setup/init` only shows "This site is already installed" and writes nothing
  ([5.2](../../../install/05-setup-wizard.md#the-wizard-on-an-installed-site)).
- Install the crontab for the customer if your panel does not let them ([Cron and shell access](../panels/cron-and-shell-access.md)).
- Go through the [go-live checklist](../checklists/new-customer-site-go-live.md).

## Pre-filling pages with kickstart.ini

The wizard reads `kickstart.ini` in the installation root: "When the wizard finds a section for a step, it pre-fills
that page, or skips it when the section says `Continue=true`"
([4.1](../../../install/04-choosing-an-install-method.md#41-one-installer-three-front-ends)). A host can use this to
fill in the database page with the database it created, so the customer never sees the database password. Remove the
file after the install; it holds passwords in clear text.

**A public demo through the wizard.** The setup wizard reads `EXP_DEMO_MODE=1` from the web server's PHP (for example `SetEnv` in `.htaccess`); its **Site administrator** page then shows a **Demo mode** note and accepts the published password, also `publish`. [Demo mode for hosts](../security/demo-mode-for-hosts.md).

## Related pages

- [One-click installers](one-click-installers.md)
- [Shared hosting](../models/shared-hosting.md)
- [Apache with PHP-FPM](../servers/apache-with-php-fpm.md)
- [Setting up .htaccess and config.php from the console](../../../guides/setup-files-from-the-console.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
