---
title: dashboard.ini [DashboardBlock_migrations]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardBlock_migrations] in dashboard.ini: Priority, PolicyList, ViewList"
keywords: Einstellung, dashboard.ini, DashboardBlock_migrations, INI, setting, Priority, PolicyList, ViewList
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardBlock_migrations]

Der Block `[DashboardBlock_migrations]` von `settings/dashboard.ini`, mit 3 Einstellung(en).

> Die Migrationen: wie viele ausstehen oder fehlgeschlagen sind, die verwaisten, der laufende Lauf und der letzte Lauf, mit Links zu  
> Einrichtung > Migrationen (migration/status) und zur Seite des Laufs (migration/log/<run id>).  

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Priority

Standard: `27`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_migrations]
Priority=27
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `migration/read`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_migrations]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=migration/read
*/ ?>
```

## ViewList

Standard: eine Liste mit 1: `migration/status`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_migrations]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=migration/status
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
