---
title: dashboard.ini [DashboardBlock_staticpublish]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardBlock_staticpublish] in dashboard.ini: Priority, PolicyList, ViewList"
keywords: Einstellung, dashboard.ini, DashboardBlock_staticpublish, INI, setting, Priority, PolicyList, ViewList
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardBlock_staticpublish]

Der Block `[DashboardBlock_staticpublish]` von `settings/dashboard.ini`, mit 3 Einstellung(en).

> Static publishing: the targets with their stale pages and last publish, or (without a target) a one-line  
> invitation to the wizard and the tutorial that each user can dismiss, with links to Setup > Static publishing.  

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Priority

Standard: `29`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_staticpublish]
Priority=29
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `staticpublish/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_staticpublish]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=staticpublish/read
*/ ?>
```

## ViewList

Standard: eine Liste mit 1: `staticpublish/dashboard`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_staticpublish]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=staticpublish/dashboard
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
