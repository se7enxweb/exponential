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

> Aus einer anderen Installation importierte Inhalte können deren IDs behalten, wo ein Feld  
> oder ein Layout Inhalte per ID nennt (die interne ID eines erweiterten Links, der Inhaltsparameter  
> einer Layout-Komponente). Eine Website, deren Templates solche IDs als Remote-IDs oder mit einem Versatz auflösen,  
> listet hier dieselben Muster in der Reihenfolge, in der sie sie versucht, damit beide Prüfungen sie so auflösen,  
> wie die Website es tut. %id ist die gespeicherte ID, %id+N oder %id-N die um N verschobene ID. Die Demo der Media Site  
> löst sie so auf:  
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
