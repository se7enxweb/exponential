---
title: dashboard.ini [DashboardBlock_drafts]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardBlock_drafts] in dashboard.ini: Priority, NumberOfItems, PolicyList, ViewList"
keywords: Einstellung, dashboard.ini, DashboardBlock_drafts, INI, setting, Priority, NumberOfItems, PolicyList, ViewList
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardBlock_drafts]

Der Block `[DashboardBlock_drafts]` von `settings/dashboard.ini`, mit 4 Einstellung(en).

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Priority

Standard: `30`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_drafts]
Priority=30
*/ ?>
```

## NumberOfItems

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_drafts]
NumberOfItems=10
*/ ?>
```

## PolicyList

Standard: eine Liste mit 1: `content/edit`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_drafts]
# ein leeres "PolicyList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PolicyList[]
PolicyList[]=content/edit
*/ ?>
```

## ViewList

Standard: eine Liste mit 1: `content/draft`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardBlock_drafts]
# ein leeres "ViewList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ViewList[]
ViewList[]=content/draft
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
