---
title: dashboard.ini [DashboardBlock_cronjobs]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardBlock_cronjobs] in dashboard.ini: Priority, PolicyList, ViewList"
keywords: Einstellung, dashboard.ini, DashboardBlock_cronjobs, INI, setting, Priority, PolicyList, ViewList
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardBlock_cronjobs]

Der Block `[DashboardBlock_cronjobs]` von `settings/dashboard.ini`, mit 3 Einstellung(en).

> When each cronjob part last ran, with a warning when none has run yet or one is late  
> (doc/eng-us/bc/6.0/installer-crontab.md).  

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Priority

Standard: `25`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_cronjobs]
Priority=25
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `setup/managecronjobs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_cronjobs]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=setup/managecronjobs
*/ ?>
```

## ViewList

Standard: eine Liste mit 1: `setup/cronjobs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_cronjobs]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=setup/cronjobs
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
