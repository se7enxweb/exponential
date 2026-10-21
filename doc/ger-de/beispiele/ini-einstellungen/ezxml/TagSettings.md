---
title: ezxml.ini [TagSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TagSettings] in ezxml.ini: TagPresets"
keywords: Einstellung, ezxml.ini, TagSettings, INI, setting, TagPresets
type: generated
generated: exp:help:examples
---
# ezxml.ini [TagSettings]

Der Block `[TagSettings]` von `settings/ezxml.ini`, mit 1 Einstellung(en).

> AliasClasses\[eZXHTMLXMLOutput\]=eZPlainXMLOutput  

Bearbeiten Sie `settings/ezxml.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezxml.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezxml.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezxml.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TagPresets

> Legt Vorlagen erlaubter Tags fest, derzeit nur vom Online Editor 5.0 verwendet,  
> um verschiedene Arten von Editoren zu haben (d. h. es begrenzt nur die sichtbaren Schaltflächen,  
> nicht, was Sie kopieren/einfügen oder im Code-Editor von Hand eingeben können)  
> Die Einstellung ist in class/edit wählbar, wenn TagPreset Werte hat.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[TagSettings]
# ein leeres "TagPresets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TagPresets[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezxml.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ezxml.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
