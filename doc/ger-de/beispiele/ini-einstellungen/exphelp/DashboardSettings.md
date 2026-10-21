---
title: exphelp.ini [DashboardSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DashboardSettings] in exphelp.ini: RecentLimit, AudiencePageLimit, AudienceExcludeTypes"
keywords: Einstellung, exphelp.ini, DashboardSettings, INI, setting, RecentLimit, AudiencePageLimit, AudienceExcludeTypes
type: generated
generated: exp:help:examples
---
# exphelp.ini [DashboardSettings]

Der Block `[DashboardSettings]` von `settings/exphelp.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RecentLimit

> Wie viele neue Änderungen unter den Wurzeln das Dashboard auflistet  

Standard: `8`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DashboardSettings]
RecentLimit=8
*/ ?>
```

## AudiencePageLimit

> Wie viele Seiten jede Zielgruppenkarte auflistet  

Standard: `4`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DashboardSettings]
AudiencePageLimit=4
*/ ?>
```

## AudienceExcludeTypes

> Seitentypen, die die Zielgruppenkarten nie auflisten (sie werden über die Suche und die Kategorien gefunden)  

Standard: eine Liste mit 6: `changelog`, `history`, `bc`, `specification`, `generated`, `catalogue`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DashboardSettings]
# ein leeres "AudienceExcludeTypes[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AudienceExcludeTypes[]
AudienceExcludeTypes[]=changelog
AudienceExcludeTypes[]=history
AudienceExcludeTypes[]=bc
AudienceExcludeTypes[]=specification
AudienceExcludeTypes[]=generated
AudienceExcludeTypes[]=catalogue
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
