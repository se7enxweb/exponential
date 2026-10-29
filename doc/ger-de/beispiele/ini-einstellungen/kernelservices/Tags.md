---
title: kernelservices.ini [Tags]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Tags] in kernelservices.ini: TagList"
keywords: Einstellung, kernelservices.ini, Tags, INI, setting, TagList
type: generated
generated: exp:help:examples
---
# kernelservices.ini [Tags]

Der Block `[Tags]` von `settings/kernelservices.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/kernelservices.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/kernelservices.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/kernelservices.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/kernelservices.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TagList

> Die Tags, die Services tragen dürfen. exp:services:check warnt vor einem Tag, der  
> hier nicht aufgeführt ist (eine Erweiterung fügt eigene mit TagList\[\]=<tag> hinzu).  

Standard: eine Liste mit 9: `kernel.core`, `kernel.settings`, `template.operator`, `datatype`, `cronjob.part`, `syscard.provider`, `help.source`, `event.listener`, `transfer.tool`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/kernelservices.ini.append.php
[Tags]
# ein leeres "TagList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TagList[]
TagList[]=kernel.core
TagList[]=kernel.settings
TagList[]=template.operator
TagList[]=datatype
TagList[]=cronjob.part
TagList[]=syscard.provider
TagList[]=help.source
TagList[]=event.listener
TagList[]=transfer.tool
*/ ?>
```

## Siehe auch

- [Alle Blöcke von kernelservices.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/kernelservices.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
