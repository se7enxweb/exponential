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

> Welcher Eingabe-Handler verwendet wird, ezsimplified ist der Standard  

Standard: `eZSimplifiedXMLInput`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
HandlerClass=eZSimplifiedXMLInput
*/ ?>
```

## AliasClasses

> Erlaubt, einen Handler durch einen anderen zu ersetzen  

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
> Leerzeichen in der Eingabe abschneiden oder nicht  
> 'true' - Leerzeichen am Zeilenanfang werden abgeschnitten.  
> 'false'- alle Leerzeichen bleiben an ihrem Platz  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
TrimSpaces=true
*/ ?>
```

## AllowMultipleSpaces

> 'false' - Folgen von Leerzeichen werden in ein einzelnes Leerzeichen umgewandelt.  
> 'true'  - alle Leerzeichen werden im XML gespeichert.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
AllowMultipleSpaces=false
*/ ?>
```

## AllowNumericEntities

> Erlaubt, fremde Zeichen (aus anderen Zeichensätzen) als numerische  
> Entitäten zu speichern.  

Standard: `false`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/ezxml.ini.append.php
[InputSettings]
AllowNumericEntities=false
*/ ?>
```

## SupplementaryCharacters

> Zeichen über U+FFFF (Emoji, mathematische Buchstaben wie U+1D538) in  
> Rich Text: 'enabled' behält sie, 'disabled' ersetzt sie wie in XML  
> nicht erlaubte Zeichen (beim Speichern durch ein Leerzeichen). 'auto' behält sie, außer die  
> Datenbank ist MySQL oder MariaDB mit einem anderen Verbindungszeichensatz als utf8mb4  
> (\[DatabaseSettings\] Charset=utf-8 verbindet als utf8, das sie nicht speichern kann:  
> Sie würden zu "????", oder das Speichern schlüge im strikten SQL-Modus fehl).  

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
