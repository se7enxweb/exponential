---
title: Configuration managed by the deployment
audience: government, admin, developer, hosting
category: public-sector
order: 230
summary: For public bodies with change management - how the ManagedByDeployment switch makes the administration unable to change settings, templates, extensions and packages in production, so every change goes through version control and review.
keywords: ManagedByDeployment, configuration management, change management, version control, deployment, locked views, INI editor, template editor, setup wizard, packages, separation of duties, production, public sector
type: help
---
# Configuration managed by the deployment

This page is for administrators, developers and hosting teams of public bodies whose change process requires that
production changes go through version control, review and a deployment. Without the switch, the administration's INI
editor, toolbar and menu editors and template editor write files into the release directory, where version control does
not see them. With it, they cannot ([Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)).

## Contents

1. [The switch](#the-switch)
2. [What it locks](#what-it-locks)
3. [What stays open](#what-stays-open)
4. [Introducing it](#introducing-it)
5. [Limits](#limits)
6. [Related pages](#related-pages)

## The switch

Off by default, per siteaccess, since 6.0.15:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php (from version control)
[ConfigurationSettings]
ManagedByDeployment=enabled
```

## What it locks

In a web request, with the switch on:

- the settings editor, the toolbar and menu editors, the template editor, the setup wizard, extension activation, the
  code generators (RAD wizards) and package upload, install, uninstall, create and export answer like a disabled view,
  whatever the roles say; the list lives in code, so a settings file cannot weaken it;
- menus and links to those views are hidden;
- every INI write from a web request is refused (`eZINI::save()` returns false), including writers from extensions;
- the setup wizard does not start, whatever `CheckValidity` says.

## What stays open

- Read-only views: settings view, template and package lists, cache management, system information, logs.
- Command-line scripts and the deployment itself, including `./console exp:ini`.
- Keys the code generates on first use (audit keys, the mail preference secret).

## Introducing it

1. Put every setting the administration used to write into version control
   (`settings/siteaccess/...`, extension settings); secrets stay in `settings/override` outside version control.
2. Turn the switch on for the production administration siteaccess in a versioned file.
3. Deploy, then check: Setup > Ini settings shows values but no edit; `/setup/rad` answers as disabled.
4. Tell administrators that settings changes are now change requests.

## Limits

- Installing a package from the command line still writes to the database.
- File permissions on the release directory remain the stronger measure (code read-only for the web server's user);
  the switch is an application-level guard.

## Related pages

- [Configuration managed by the deployment](../../../features/6.0/managed-by-deployment.md)
- [Deploying](../../../guides/deploying.md)
- [File permissions and ownership](file-permissions-and-ownership.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
