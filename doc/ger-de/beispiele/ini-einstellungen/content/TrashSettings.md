---
title: content.ini [TrashSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TrashSettings] in content.ini: KeepItemsForDays"
keywords: Einstellung, content.ini, TrashSettings, INI, setting, KeepItemsForDays
type: generated
generated: exp:help:examples
---
# content.ini [TrashSettings]

Der Block `[TrashSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

> ResolverClasses\[\]=myNameResolver  

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## KeepItemsForDays

> How many days an item stays in the trash before the cronjob trashpurge  
> (cronjobs/trashpurge.php) purges it. Empty or 0: the cronjob purges the whole  
> trash, as before. A value that is not a whole number of days up to 36500 purges  
> nothing and reports an error. bin/php/trashpurge.php is not affected; it takes  
> --trashed-days=<days>. Items trashed before 6.0 have no trash time (trashed = 0)  
> and are purged on the first run whatever the value; see the guide to keep them.  
> Guide: doc/eng-us/bc/6.0/trash.md  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[TrashSettings]
KeepItemsForDays=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
