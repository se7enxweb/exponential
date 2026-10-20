---
title: dashboard.ini [DashboardBlock_audit]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardBlock_audit] in dashboard.ini: Priority, NumberOfItems, PolicyList, ViewList"
keywords: Einstellung, dashboard.ini, DashboardBlock_audit, INI, setting, Priority, NumberOfItems, PolicyList, ViewList
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardBlock_audit]

Der Block `[DashboardBlock_audit]` von `settings/dashboard.ini`, mit 4 Einstellung(en).

> The latest security events (notice or worse, access and system channels), the alerts of the last  
> day and, for audit/manage, the "audit is off" and "chain broken" warnings (doc/eng-us/bc/6.0/audit.md).  

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Priority

Standard: `20`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_audit]
Priority=20
*/ ?>
```

## NumberOfItems

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_audit]
NumberOfItems=10
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `audit/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_audit]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=audit/read
*/ ?>
```

## ViewList

Standard: eine Liste mit 1: `audit/console`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_audit]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=audit/console
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
