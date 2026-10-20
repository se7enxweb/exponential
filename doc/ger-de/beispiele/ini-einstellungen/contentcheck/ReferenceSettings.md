---
title: contentcheck.ini [ReferenceSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ReferenceSettings] in contentcheck.ini: ImportedIdRemotePatterns, ImportedIdObjectPatterns"
keywords: Einstellung, contentcheck.ini, ReferenceSettings, INI, setting, ImportedIdRemotePatterns, ImportedIdObjectPatterns
type: generated
generated: exp:help:examples
---
# contentcheck.ini [ReferenceSettings]

Der Block `[ReferenceSettings]` von `settings/contentcheck.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/contentcheck.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/contentcheck.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/contentcheck.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/contentcheck.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ImportedIdRemotePatterns

> Content imported from another installation may keep that installation's ids where a field  
> or a layout names content by id (an enhanced link's internal id, a layout component's  
> content parameter). A site whose templates resolve such ids as remote ids or with an offset  
> lists the same patterns here, in the order it tries them, so both checks resolve them the  
> way the site does. %id is the stored id, %id+N or %id-N the id moved by N. The media site  
> demo resolves them like this:  
> ImportedIdRemotePatterns\[\]=media-o-%id+776  
> ImportedIdRemotePatterns\[\]=media-o-%id  
> ImportedIdObjectPatterns\[\]=%id+776  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ReferenceSettings]
# ein leeres "ImportedIdRemotePatterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImportedIdRemotePatterns[]
*/ ?>
```

## ImportedIdObjectPatterns

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/contentcheck.ini.append.php
[ReferenceSettings]
# ein leeres "ImportedIdObjectPatterns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ImportedIdObjectPatterns[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von contentcheck.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/contentcheck.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
