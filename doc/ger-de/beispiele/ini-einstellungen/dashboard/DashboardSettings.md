---
title: dashboard.ini [DashboardSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardSettings] in dashboard.ini: DashboardBlocks"
keywords: Einstellung, dashboard.ini, DashboardSettings, INI, setting, DashboardBlocks
type: generated
generated: exp:help:examples
---
# dashboard.ini [DashboardSettings]

Der Block `[DashboardSettings]` von `settings/dashboard.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/dashboard.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/dashboard.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/dashboard.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/dashboard.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DashboardBlocks

> DashboardBlocks\[\]=example  

Standard: eine Liste mit 6: `pending_list`, `drafts`, `latest_content`, `all_latest_content`, `audit`, `cronjobs`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/dashboard.ini.append.php
[DashboardSettings]
# ein leeres "DashboardBlocks[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DashboardBlocks[]
DashboardBlocks[]=pending_list
DashboardBlocks[]=drafts
DashboardBlocks[]=latest_content
DashboardBlocks[]=all_latest_content
DashboardBlocks[]=audit
DashboardBlocks[]=cronjobs
*/ ?>
```

## Siehe auch

- [Alle Blöcke von dashboard.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/dashboard.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
