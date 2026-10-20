---
title: image.ini [AliasSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AliasSettings] in image.ini: AliasList"
keywords: Einstellung, image.ini, AliasSettings, INI, setting, AliasList
type: generated
generated: exp:help:examples
---
# image.ini [AliasSettings]

Der Block `[AliasSettings]` von `settings/image.ini`, mit 1 Einstellung(en).

> -------- Image alias settings --------  

Bearbeiten Sie `settings/image.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/image.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/image.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/image.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AliasList

> Defines a list of aliases that are available to  
> the template engine and other clients.  
> The alias must be defined as a separate INI block.  

Standard: eine Liste mit 6: `reference`, `small`, `tiny`, `medium`, `large`, `rss`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/image.ini.append.php
[AliasSettings]
# ein leeres "AliasList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasList[]
AliasList[]=reference
AliasList[]=small
AliasList[]=tiny
AliasList[]=medium
AliasList[]=large
AliasList[]=rss
*/ ?>
```

## Siehe auch

- [Alle Blöcke von image.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/image.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
