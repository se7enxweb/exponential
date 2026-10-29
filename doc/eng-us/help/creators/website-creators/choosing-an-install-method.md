---
title: Choosing an install method for a new site
audience: website-creator, admin
category: website-creation
order: 60
summary: Which of the three install methods (setup wizard, kickstarter, console install) and which site package to choose for a new website, with the commands and the decisions each one asks for.
keywords: install, installation, setup wizard, kickstarter, exp:install, console install, site package, sevenx_multisite, clean, demo content, new site, website creator
type: help
---
# Choosing an install method for a new site

This page is for website creators who are about to install Exponential for a new site. It answers which of the three
install methods suits you, which site package to choose, and which options matter for a website. The installation
book has the full detail: [Choosing an install method](../../../install/04-choosing-an-install-method.md).

## Contents

1. [One installer, three front ends](#one-installer-three-front-ends)
2. [Which method for which situation](#which-method-for-which-situation)
3. [Choosing the site package](#choosing-the-site-package)
4. [The options that matter for a website](#the-options-that-matter-for-a-website)
5. [After the install](#after-the-install)

## One installer, three front ends

All three methods run the same installation steps and end in the same step, `CreateSites`, so they write the same
settings and the same database. They differ in how they ask their questions
([4.2 The three methods at a glance](../../../install/04-choosing-an-install-method.md#42-the-three-methods-at-a-glance)):

| Method | You answer | Needs a web server during the install | Best for |
|---|---|---|---|
| Setup wizard | forms in the browser, one page per step | yes | a first install, evaluating, hosts without shell access |
| Kickstarter (`exp:kickstarter`) | a reviewed `kickstart.ini` file | no | repeatable installs, staging copies, offline package sets |
| Console install (`exp:install`) | command-line options, each with a default | no | the fastest install; scripts, CI, demos |

## Which method for which situation

| Your situation | Choose |
|---|---|
| You want to see Exponential running in minutes on your own machine | `exp:install` with its defaults (SQLite, `sevenx_multisite`) |
| You will hand the site to a customer and want every decision explained on a page | the setup wizard ([chapter 5](../../../install/05-setup-wizard.md)) |
| You build the same site for staging and production, or for several customers | the kickstarter with one `kickstart.ini` per environment ([chapter 6](../../../install/06-kickstarter.md)) |
| Your hosting has no shell access | the setup wizard |
| You test in CI | `exp:install --dry-run`, then `exp:install` ([chapter 7](../../../install/07-console-install.md)) |

## Choosing the site package

A site package is the starting content and settings: classes, a design, demo content, roles. The package index of
this version offers eleven; the ones you are most likely to choose
([4.3 Site packages](../../../install/04-choosing-an-install-method.md#43-site-packages-what-is-installed)):

| Package | What you get | Choose it when |
|---|---|---|
| `sevenx_multisite` | the default multisite installation with demo content | you want to explore, or build on the demo |
| `sevenx_multisite_clean` | the same, without demo content | you build a real site and fill it yourself |
| `sevenx_site`, `sevenx_site_clean` | the simpler website interface | a small site |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (and `_clean`), `plain_site` | the older packages | you continue a site built on them |

> **Tip**
> Install the full package once on your laptop to learn, and the `_clean` one for the real site.

## The options that matter for a website

With `exp:install` every option has a default; for a real site set at least these
([7.3 Options](../../../install/07-console-install.md#73-options)):

| Option | Why it matters |
|---|---|
| `--url=https://www.example.com` | every siteaccess's address, mail links and feeds are built from it |
| `--access=url\|host\|port` | how visitors reach each siteaccess; `host` is best for production and HTTPS ([Going live](going-live-hosts-https.md)) |
| `--host`, `--admin-host` | the public and administration host names, with `--access=host` |
| `--language`, `--languages` | the primary language and the others ([Building a multilingual site](multilingual-sites.md)) |
| `--title` | the site name, used in titles and mail |
| `--db=...` with `--db-user` | the database; a database user for Exponential alone ([Database support](../../database-support.md)) |
| `--email` | a real address: it becomes `[MailSettings] AdminEmail` |
| `--package` | the site package, see above |

An example for a production-style install by host names, run as the web server's user:

```bash
EXP_INSTALL_DB_PASSWORD='<database password>' \
sudo -u www-data php bin/php/console exp:install --apply \
  --db=pgsql --db-user=exponential --db-name=exponential \
  --package=sevenx_multisite_clean --language=eng-US --languages=ger-DE \
  --title="Example" --url=https://www.example.com \
  --access=host --host=www.example.com --admin-host=admin.example.com \
  --email=webmaster@example.com --dry-run
```

Remove `--dry-run` when the check passes. The password goes in `EXP_INSTALL_DB_PASSWORD` rather than an option, so it
does not show in the process list or the shell history.

> **Warning**
> `--db-action=remove` is the default and empties the database first. Never point an install at a database that holds
> content you want to keep.

## After the install

1. Note the administrator password the install shows once, sign in and change it
   ([The default administrator password](../../default-administrator-password.md)).
2. Serve the site ([Hosting requirements for a website](hosting-for-a-website.md)).
3. Work through the first-week checklist of [After installing](../../../install/10-after-installing.md).

## Related pages

- [Your first site, step by step](your-first-site-step-by-step.md)
- [Hosting requirements for a website](hosting-for-a-website.md)
- [Site packages and the themes that ship](site-packages-and-shipped-themes.md)
- [Installation book](../../../install/README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
