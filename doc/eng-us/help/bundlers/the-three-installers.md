---
title: The three installers, from a bundler's view
audience: bundler, hosting, developer
category: partnership
order: 190
summary: The setup wizard, the kickstarter and exp:install run the same install steps; what differs is where the answers come from. Which one a bundle should call, what each writes, how each guards against overwriting a site, and how each handles the administrator password.
keywords: installer, setup wizard, kickstarter, exp:install, install.php, kickstarter.php, CreateSites, unattended, bundle, dry run, force, maintenance mode, exit status
type: help
---
# The three installers, from a bundler's view

This page is for bundlers deciding how their bundle installs the site. Exponential has one installation sequence
with three front ends. The reference is
[Chapter 4: Choosing an install method](../../install/04-choosing-an-install-method.md).

## Contents

1. [One sequence, three front ends](#one-sequence-three-front-ends)
2. [Side by side](#side-by-side)
3. [Which one your bundle calls](#which-one-your-bundle-calls)
4. [What every installer writes](#what-every-installer-writes)
5. [Maintenance mode during the install](#maintenance-mode-during-the-install)

## One sequence, three front ends

"Every install method runs the same sequence of step classes in `kernel/setup/steps/`, from `Welcome` to `Final`;
`CreateSites` is the step that writes the database, the packages and the settings. The front ends differ only in where
the answers come from" ([One-click installers](../hosting/provisioning/one-click-installers.md#one-installer-three-front-ends)).

| Front end | Started by | Answers from |
|---|---|---|
| Setup wizard | opening the site in a browser while `[SiteAccessSettings] CheckValidity=true` | browser forms (pre-filled by `kickstart.ini` if present) |
| Kickstarter | `php bin/php/console exp:kickstarter run` or `php bin/php/kickstarter.php run` | `kickstart.ini` in the installation root |
| Console install | `php bin/php/console exp:install` or `php bin/php/install.php` | command-line options, each with a default |

## Side by side

| | Setup wizard | Kickstarter | `exp:install` |
|---|---|---|---|
| Needs a web server during the install | yes | no | no |
| Dry run | no | `run --dry-run` | `--dry-run`, `--print` |
| Guard against overwriting | starts only while `CheckValidity=true` | `--force` required | refuses an existing installation without `--force` |
| Log | `var/log/setup.log` | `setup.log`, `kickstart.log` | `setup.log`, `var/log/exp-install-<date>.ini` |
| Administrator password | typed in the form | from the file; generated when empty or well known | generated unless an acceptable `--password` is given |
| Exit status for scripts | not applicable | `0` or `1` ([6.2.3](../../install/06-kickstarter.md#623-exit-status)) | `0` or `1` ([7.7](../../install/07-console-install.md#77-exit-status)) |

Source: [4.2](../../install/04-choosing-an-install-method.md#42-the-three-methods-at-a-glance) and the chapters linked.

## Which one your bundle calls

| Bundle | Front end | Why |
|---|---|---|
| Container image, CI, automated environments | `exp:install` | "It needs no file, validates its options before anything is written, and returns a non-zero exit status on every failure" ([4.6](../../install/04-choosing-an-install-method.md#46-unattended-installs-ci-and-containers)) |
| Panel installer | `exp:install` | options map to the panel's form fields |
| Agency starter kit, several reviewed environments | the kickstarter | "the reviewed `kickstart.ini` is the specification of the install" |
| Server image whose customer finishes the install | the setup wizard, pre-filled by `kickstart.ini` | the customer chooses the site details in a browser |
| Shared hosting without shell | the setup wizard | "it needs only a browser and FTP access" ([4.5](../../install/04-choosing-an-install-method.md#45-which-method-for-which-situation)) |

Details: [Unattended install with exp:install](unattended-install-with-exp-install.md),
[The kickstarter for bundles](the-kickstarter-for-bundles.md).

## What every installer writes

All three end in `CreateSites`, "so they write the same things"
([4.4](../../install/04-choosing-an-install-method.md#44-what-an-installation-writes)):

| Where | What |
|---|---|
| `settings/override/site.ini.append.php` | siteaccess matching, site list, mail settings, `ActiveExtensions` from the site package |
| `settings/siteaccess/site/`, `admin/`, `editor/` (and `adminui/` when `exp_adminui` is present) | per-siteaccess settings, **including the database password** |
| the database | schema, base data, site package content |
| `var/storage/packages/`, `var/storage/sqlite3/` | imported packages, the SQLite file |
| `var/log/setup.log`, `var/log/initial-admin-password` | the run's log; a generated password (mode `0600`) |

"The siteaccess **directory names are always `site`, `admin` and `editor`**"; what you choose is the match value
(URL path, port or host). A bundle's documentation should say which addresses it configured.

> **Warning**
> Because the installers write secrets into `settings/override/` and `settings/siteaccess/`, a bundle must never ship
> those directories filled from its build machine. Install at the recipient's first start
> ([First boot and install once](first-boot-and-install-once.md)).

## Maintenance mode during the install

"Every method switches **maintenance mode** on while it works": `var/maintenance.json` exists and the front
controllers answer HTTP 503. After a failed step it stays on; `php bin/php/maintenance.php off` ends it. "A dry run does
not switch it on" ([4.4](../../install/04-choosing-an-install-method.md#maintenance-mode-during-the-install)). A
bundle's health check should treat 503 during first boot as "installing", not as "broken".

## Related pages

- [One-click installers for Exponential](../hosting/provisioning/one-click-installers.md)
- [Chapter 5: The setup wizard](../../install/05-setup-wizard.md)
- [Install in one command](../../features/6.0/install-in-one-command.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
