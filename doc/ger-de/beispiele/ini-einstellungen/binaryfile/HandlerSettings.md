---
title: binaryfile.ini [HandlerSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [HandlerSettings] in binaryfile.ini: MetaDataExtractor, Repositories, ExtensionRepositories"
keywords: Einstellung, binaryfile.ini, HandlerSettings, INI, setting, MetaDataExtractor, Repositories, ExtensionRepositories
type: generated
generated: exp:help:examples
---
# binaryfile.ini [HandlerSettings]

Der Block `[HandlerSettings]` von `settings/binaryfile.ini`, mit 3 Einstellung(en).

> Here you can add handlers for new datatypes.  

Bearbeiten Sie `settings/binaryfile.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/binaryfile.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/binaryfile.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/binaryfile.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MetaDataExtractor

Standard: ein Hash mit 3: `text/plain=ezplaintext`, `application/pdf=ezpdf`, `application/msword=ezword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/binaryfile.ini.append.php
[HandlerSettings]
# ein leeres "MetaDataExtractor[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
MetaDataExtractor[]
MetaDataExtractor[text/plain]=ezplaintext
MetaDataExtractor[application/pdf]=ezpdf
MetaDataExtractor[application/msword]=ezword
*/ ?>
```

## Repositories

> A list of directories to search for metadata extractor handlers  

Standard: eine Liste mit 1: `kernel/classes/datatypes/ezbinaryfile`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/binaryfile.ini.append.php
[HandlerSettings]
# ein leeres "Repositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Repositories[]
Repositories[]=kernel/classes/datatypes/ezbinaryfile
*/ ?>
```

## ExtensionRepositories

> A list of extensions which have metadata extractor handlers  
> It's common to create a settings/binaryfile.ini.append.php file  
> in your extension and add the extension name to automatically  
> get handlers from the extension when it's turned on.  
> Handlers need to be placed in the extension subdir "plugins".  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/binaryfile.ini.append.php
[HandlerSettings]
# ein leeres "ExtensionRepositories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionRepositories[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von binaryfile.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/binaryfile.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
