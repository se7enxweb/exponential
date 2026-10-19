---
title: menu.ini [Leftmenu_audit]
audience: developer, admin
category: examples
summary: "The settings of [Leftmenu_audit] in menu.ini: Name, Links, LinkNames, PolicyList_dashboard, PolicyList_console, PolicyList_recent, PolicyList_charts, PolicyList_alerts ..."
keywords: menu.ini, Leftmenu_audit, INI, setting, Name, Links, LinkNames, PolicyList_dashboard, PolicyList_console, PolicyList_recent, PolicyList_charts, PolicyList_alerts, PolicyList_export, PolicyList_archives, PolicyList_settings, LinkIcons, LinkDescriptions
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_audit]

The block `[Leftmenu_audit]` of `settings/menu.ini`, with 13 setting(s).

> Hidden by default as of 4.3  
> Links\[my\_shopping\_basket\]=shop/basket  
> PolicyList\_my\_shopping\_basket\[\]=shop/buy  
> Hidden by default as of 4.3  
> Links\[my\_wish\_list\]=shop/wishlist  
> PolicyList\_my\_wish\_list\[\]=shop/buy  
> The left menu of the audit tab (design:audit/menu.tpl). Archives and Settings need audit/manage.  

Never edit `settings/menu.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/menu.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in an extension (read when the extension is active).

## Name

Default: `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
Name=audit
*/ ?>
```

## Links

Default: a hash of 8: `dashboard=audit/dashboard`, `console=audit/console`, `recent=audit/recent`, `charts=audit/charts`, `alerts=audit/alerts`, `export=audit/export`, `archives=audit/archives`, `settings=audit/settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "Links[]" first replaces the shipped list; without it the items are added
Links[]
Links[dashboard]=audit/dashboard
Links[console]=audit/console
Links[recent]=audit/recent
Links[charts]=audit/charts
Links[alerts]=audit/alerts
Links[export]=audit/export
Links[archives]=audit/archives
Links[settings]=audit/settings
*/ ?>
```

## LinkNames

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "LinkNames[]" first replaces the shipped list; without it the items are added
LinkNames[]
*/ ?>
```

## PolicyList_dashboard

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_dashboard[]" first replaces the shipped list; without it the items are added
PolicyList_dashboard[]
PolicyList_dashboard[]=audit/read
*/ ?>
```

## PolicyList_console

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_console[]" first replaces the shipped list; without it the items are added
PolicyList_console[]
PolicyList_console[]=audit/read
*/ ?>
```

## PolicyList_recent

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_recent[]" first replaces the shipped list; without it the items are added
PolicyList_recent[]
PolicyList_recent[]=audit/read
*/ ?>
```

## PolicyList_charts

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_charts[]" first replaces the shipped list; without it the items are added
PolicyList_charts[]
PolicyList_charts[]=audit/read
*/ ?>
```

## PolicyList_alerts

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_alerts[]" first replaces the shipped list; without it the items are added
PolicyList_alerts[]
PolicyList_alerts[]=audit/read
*/ ?>
```

## PolicyList_export

Default: a list of 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_export[]" first replaces the shipped list; without it the items are added
PolicyList_export[]
PolicyList_export[]=audit/read
*/ ?>
```

## PolicyList_archives

Default: a list of 1: `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_archives[]" first replaces the shipped list; without it the items are added
PolicyList_archives[]
PolicyList_archives[]=audit/manage
*/ ?>
```

## PolicyList_settings

Default: a list of 1: `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "PolicyList_settings[]" first replaces the shipped list; without it the items are added
PolicyList_settings[]
PolicyList_settings[]=audit/manage
*/ ?>
```

## LinkIcons

> The tiles of these links where the Setup overview shows this menu (\[Leftmenu\_setup\] GroupSections\[audit\]);  
> translated in design/admin/parts/audit/menu  
> The AI events link the extension expai appends here, for admin4's mega menu of the Dashboard tab  
> (\[Leftmenu\_my\_mega\] adds this menu to its group "activity")  

Default: a hash of 9: `dashboard=speedometer`, `console=terminal`, `recent=list-ul`, `charts=graph-up-arrow`, `alerts=exclamation-triangle`, `export=download`, `archives=archive`, `settings=journal-check`, `expai_events=lightning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "LinkIcons[]" first replaces the shipped list; without it the items are added
LinkIcons[]
LinkIcons[dashboard]=speedometer
LinkIcons[console]=terminal
LinkIcons[recent]=list-ul
LinkIcons[charts]=graph-up-arrow
LinkIcons[alerts]=exclamation-triangle
LinkIcons[export]=download
LinkIcons[archives]=archive
LinkIcons[settings]=journal-check
LinkIcons[expai_events]=lightning
*/ ?>
```

## LinkDescriptions

Default: a hash of 9: `dashboard=Health, activity, security and alerts at a glance.`, `console=The timeline of events, with filters and search.`, `recent=The latest 100 events of all channels.`, `charts=Events, refusals and logins per day.`, `alerts=The alerts that fired and the rules in use.`, `export=The console's filter as CSV or JSON.`, `archives=Live files, archives and their verification.`, `settings=The effective audit settings, sinks and index.`, `expai_events=The events of Exponential Intelligence in the audit console.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# an empty "LinkDescriptions[]" first replaces the shipped list; without it the items are added
LinkDescriptions[]
LinkDescriptions[dashboard]=Health, activity, security and alerts at a glance.
LinkDescriptions[console]=The timeline of events, with filters and search.
LinkDescriptions[recent]=The latest 100 events of all channels.
LinkDescriptions[charts]=Events, refusals and logins per day.
LinkDescriptions[alerts]=The alerts that fired and the rules in use.
LinkDescriptions[export]=The console's filter as CSV or JSON.
LinkDescriptions[archives]=Live files, archives and their verification.
LinkDescriptions[settings]=The effective audit settings, sinks and index.
LinkDescriptions[expai_events]=The events of Exponential Intelligence in the audit console.
*/ ?>
```

## Related

- [All blocks of menu.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/menu.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
