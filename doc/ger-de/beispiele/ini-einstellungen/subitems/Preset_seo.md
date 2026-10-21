---
title: subitems.ini [Preset_seo]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Preset_seo] in subitems.ini: Name, Columns"
keywords: Einstellung, subitems.ini, Preset_seo, INI, setting, Name, Columns
type: generated
generated: exp:help:examples
---
# subitems.ini [Preset_seo]

Der Block `[Preset_seo]` von `settings/subitems.ini`, mit 2 Einstellung(en).

> Vorlagen: ein benannter Satz sichtbarer Spalten, den alle in den Tabellenoptionen wählen können. Benutzer speichern ihre  
> eigenen Vorlagen neben diesen (in ihren Einstellungen); eine Benutzervorlage kann nicht die ID einer INI-Vorlage annehmen.  
> \[Preset\_<id>\]   id: a-z, 0-9, \_ und -  
> Name=           der angezeigte Name (übersetzt im Kontext design/admin/node/view/full)  
> Columns\[\]       die sichtbaren Spalten, in Reihenfolge  

Bearbeiten Sie `settings/subitems.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/subitems.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/subitems.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/subitems.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `SEO`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Preset_seo]
Name=SEO
*/ ?>
```

## Columns

Standard: eine Liste mit 5: `name`, `urlalias`, `pagetitle`, `systemurl`, `visibility`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/subitems.ini.append.php
[Preset_seo]
# ein leeres "Columns[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Columns[]
Columns[]=name
Columns[]=urlalias
Columns[]=pagetitle
Columns[]=systemurl
Columns[]=visibility
*/ ?>
```

## Siehe auch

- [Alle Blöcke von subitems.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/subitems.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
