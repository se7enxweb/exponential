---
title: Configuration managed by deployment
audience: government, admin, developer, hosting
category: public-sector
order: 60
summary: How the ManagedByDeployment switch locks the settings, template and package screens of a live siteaccess so that every configuration change passes version control and your change board, what stays open, and how to switch it on.
keywords: ManagedByDeployment, configuration management, change control, version control, locked settings, INI editor, template editor, packages, deployment, audit, immutable configuration
type: help
---
# Configuration managed by deployment

This page is for administrators and change managers who want every configuration change on a public website to go
through version control and a change board, not through a browser on the live server. Exponential has one switch for
this, `[ConfigurationSettings] ManagedByDeployment`, described on
[Configuration managed by the deployment](../../../../features/6.0/managed-by-deployment.md).

## Contents
1. [Why public bodies want it](#why-public-bodies-want-it)
2. [What the switch locks](#what-the-switch-locks)
3. [What stays open](#what-stays-open)
4. [Switching it on](#switching-it-on)
5. [Working with it](#working-with-it)
6. [Limits](#limits)
7. [Related pages](#related-pages)

## Why public bodies want it

Without the switch, the INI editor, the toolbar and menu editors and the template editor write `*.ini.append.php` and
`.tpl` files into the release directory, where version control does not see them and the next deployment drops them.
A change made that way has no ticket, no review and no history beyond the audit trail. With the switch on, the live
site can only run what was deployed, which is what a change advisory board approves.

## What the switch locks

In a web request, with `ManagedByDeployment=enabled` for the siteaccess:

- **Views** answer like a view switched off (by default a 404), whatever the roles say: `settings/edit`, the settings
  toolbar, the toolbar and menu editors, the template editor, the setup wizard, extension activation
  (`setup/extensions`), the code generators (`setup/rad` and its wizards) and package upload, install, uninstall,
  create and export. The list is in the class `expManagedConfiguration`, so a settings file cannot weaken it.
- **Menus and links** to those views are hidden.
- **Writers** refuse: `eZINI::save()` returns false and `expIniEditor::save()` throws a refusal, which also stops
  writers the list does not name (extension modules, the debug bar settings, audit and mail sender settings pages).
- The **setup wizard** does not start.
- **Packages**: compare still works, import and removal are not offered.

PHP-FPM, Apache, FrankenPHP, the built-in server and Velocity's workers all refuse the same way.

## What stays open

- Read-only views: `settings/view` (**Setup > Ini settings**), the template and package lists, **Setup > Cache
  management**, **Setup > System information**, the logs.
- Command-line scripts and the deployment itself, including `./console exp:ini` and `bin/php/*.php`.
- Key material the code generates on first use (audit keys, the mail preference secret): runtime state, still
  written.

## Switching it on

The switch is per siteaccess and is never written from the administration:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php
[ConfigurationSettings]
ManagedByDeployment=enabled
```

Put it in version control with the rest of the settings, deploy, and clear the INI cache
(`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`). Check: **Setup > Ini settings** shows the settings
but offers no edit; `/settings/edit` answers like a disabled view.

## Working with it

| Change | Path |
|---|---|
| A setting | edit the file in the repository, review, approve, deploy; `./console exp:ini` on a test copy to try values |
| A template | the same; the template editor stays available on development and test siteaccesses where the switch is off |
| An extension | `composer.json` and settings in the repository; activation by deployment |
| Content, roles, workflows | unchanged: they are data, edited in the administration and recorded in the audit trail |

The setting can differ per environment with `EXP_ENV` variant files, so test and development stay editable
([Environments: development, test and production](../hosting/environments-dev-test-prod.md)).

## Limits

- Installing a package from the command line still writes to the database, and packages can carry content.
- File permissions on the release directory remain the stronger measure; the switch is an application-level guard.
- Roles, workflows, sections and states are configuration in the database: they are not locked. Govern them with
  roles (who may change them) and the audit trail.

## Related pages
- [Change management and advisory boards](change-management-and-advisory-boards.md)
- [Staging and approval](staging-and-approval.md)
- [Configuration under version control](../hosting/configuration-under-version-control.md)
- [Lock configuration with ManagedByDeployment](../../tasks/operations/lock-configuration-with-managed-by-deployment.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
