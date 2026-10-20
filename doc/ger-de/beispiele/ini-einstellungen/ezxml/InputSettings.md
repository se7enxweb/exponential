---
title: ezxml.ini [InputSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [InputSettings] in ezxml.ini: HandlerClass, AliasClasses, TrimSpaces, AllowMultipleSpaces, AllowNumericEntities, SupplementaryCharacters"
keywords: Einstellung, ezxml.ini, InputSettings, INI, setting, HandlerClass, AliasClasses, TrimSpaces, AllowMultipleSpaces, AllowNumericEntities, SupplementaryCharacters
type: generated
generated: exp:help:examples
---
# ezxml.ini [InputSettings]

Der Block `[InputSettings]` von `settings/ezxml.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/ezxml.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/ezxml.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/ezxml.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/ezxml.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## HandlerClass

> Which input handler to use, ezsimplified is the default  

Standard: `eZSimplifiedXMLInput`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
HandlerClass=eZSimplifiedXMLInput
*/ ?>
```

## AliasClasses

> Allows for overriding a handler with another  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
# ein leeres "AliasClasses[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AliasClasses[]
*/ ?>
```

## TrimSpaces

> AliasClasses\[eZSimplifiedXMLInput\]=myXMLInputHandler  
> Trim whitespaces in the input or not  
> 'true' - spaces in the beginning of the line are trimmed.  
> 'false'- all spaces stay in their place  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
TrimSpaces=true
*/ ?>
```

## AllowMultipleSpaces

> 'false' - spaces sequences are converted to a single space character.  
> 'true'  - all spaces are stored in XML.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
AllowMultipleSpaces=false
*/ ?>
```

## AllowNumericEntities

> Allow foreign characters (from another charsets) to be stored as numeric  
> entities.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
AllowNumericEntities=false
*/ ?>
```

## SupplementaryCharacters

> Characters above U+FFFF (emoji, mathematical letters such as U+1D538) in  
> rich text: 'enabled' keeps them, 'disabled' replaces them as XML characters  
> that are not allowed (a space on saving). 'auto' keeps them unless the  
> database is MySQL or MariaDB on a connection charset other than utf8mb4  
> (\[DatabaseSettings\] Charset=utf-8 connects as utf8, which cannot store them:  
> they would become "????", or the save would fail in strict SQL mode).  

Standard: `auto`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
SupplementaryCharacters=auto
*/ ?>
```

## Siehe auch

- [Alle Blöcke von ezxml.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/ezxml.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
