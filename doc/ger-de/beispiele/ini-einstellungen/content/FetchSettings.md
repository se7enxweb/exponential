---
title: content.ini [FetchSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [FetchSettings] in content.ini: DefaultClassFilterType"
keywords: Einstellung, content.ini, FetchSettings, INI, setting, DefaultClassFilterType
type: generated
generated: exp:help:examples
---
# content.ini [FetchSettings]

Der Block `[FetchSettings]` von `settings/content.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultClassFilterType

> Der Typ des Klassenfilters, den die Fetch-Funktionen content/list, content/tree,  
> content/list\_count, content/tree\_count und content/calendar verwenden, wenn ein Fetch  
> class\_filter\_array ohne class\_filter\_type angibt.  
> Leer (der Standard): Das Array des Klassenfilters wird dann wie bisher ignoriert.  
> include: nur die Klassen im Array; exclude: jede Klasse außer diesen.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[FetchSettings]
DefaultClassFilterType=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
