---
title: menu.ini [Leftmenu_audit]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Leftmenu_audit] in menu.ini: Name, Links, LinkNames, PolicyList_dashboard, PolicyList_console, PolicyList_recent, PolicyList_charts, PolicyList_alerts ..."
keywords: Einstellung, menu.ini, Leftmenu_audit, INI, setting, Name, Links, LinkNames, PolicyList_dashboard, PolicyList_console, PolicyList_recent, PolicyList_charts, PolicyList_alerts, PolicyList_export, PolicyList_archives, PolicyList_settings, LinkIcons, LinkDescriptions
type: generated
generated: exp:help:examples
---
# menu.ini [Leftmenu_audit]

Der Block `[Leftmenu_audit]` von `settings/menu.ini`, mit 13 Einstellung(en).

> Seit 4.3 standardmäßig ausgeblendet  
> Links\[my\_shopping\_basket\]=shop/basket  
> PolicyList\_my\_shopping\_basket\[\]=shop/buy  
> Seit 4.3 standardmäßig ausgeblendet  
> Links\[my\_wish\_list\]=shop/wishlist  
> PolicyList\_my\_wish\_list\[\]=shop/buy  
> Das linke Menü des Reiters Audit (design:audit/menu.tpl). Archive und Einstellungen brauchen audit/manage.  

Bearbeiten Sie `settings/menu.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/menu.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/menu.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/menu.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `audit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
Name=audit
*/ ?>
```

## Links

Standard: ein Hash mit 8: `dashboard=audit/dashboard`, `console=audit/console`, `recent=audit/recent`, `charts=audit/charts`, `alerts=audit/alerts`, `export=audit/export`, `archives=audit/archives`, `settings=audit/settings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "Links[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "LinkNames[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
LinkNames[]
*/ ?>
```

## PolicyList_dashboard

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_dashboard[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_dashboard[]
PolicyList_dashboard[]=audit/read
*/ ?>
```

## PolicyList_console

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_console[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_console[]
PolicyList_console[]=audit/read
*/ ?>
```

## PolicyList_recent

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_recent[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_recent[]
PolicyList_recent[]=audit/read
*/ ?>
```

## PolicyList_charts

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_charts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_charts[]
PolicyList_charts[]=audit/read
*/ ?>
```

## PolicyList_alerts

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_alerts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_alerts[]
PolicyList_alerts[]=audit/read
*/ ?>
```

## PolicyList_export

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_export[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_export[]
PolicyList_export[]=audit/read
*/ ?>
```

## PolicyList_archives

Standard: eine Liste mit 1: `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_archives[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_archives[]
PolicyList_archives[]=audit/manage
*/ ?>
```

## PolicyList_settings

Standard: eine Liste mit 1: `audit/manage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "PolicyList_settings[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList_settings[]
PolicyList_settings[]=audit/manage
*/ ?>
```

## LinkIcons

> Die Kacheln dieser Links, wo die Setup-Übersicht dieses Menü zeigt (\[Leftmenu\_setup\] GroupSections\[audit\]);  
> übersetzt in design/admin/parts/audit/menu  
> Der Link KI-Ereignisse, den die Erweiterung expai hier anhängt, für das Mega-Menü des Reiters Dashboard in admin4  
> (\[Leftmenu\_my\_mega\] fügt dieses Menü seiner Gruppe "activity" hinzu)  

Standard: ein Hash mit 9: `dashboard=speedometer`, `console=terminal`, `recent=list-ul`, `charts=graph-up-arrow`, `alerts=exclamation-triangle`, `export=download`, `archives=archive`, `settings=journal-check`, `expai_events=lightning`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "LinkIcons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: ein Hash mit 9: `dashboard=Health, activity, security and alerts at a glance.`, `console=The timeline of events, with filters and search.`, `recent=The latest 100 events of all channels.`, `charts=Events, refusals and logins per day.`, `alerts=The alerts that fired and the rules in use.`, `export=The console's filter as CSV or JSON.`, `archives=Live files, archives and their verification.`, `settings=The effective audit settings, sinks and index.`, `expai_events=The events of Exponential Intelligence in the audit console.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/menu.ini.append.php
[Leftmenu_audit]
# ein leeres "LinkDescriptions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

## Siehe auch

- [Alle Blöcke von menu.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/menu.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
