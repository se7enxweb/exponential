---
title: content.ini [AlphabeticalFilterSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [AlphabeticalFilterSettings] in content.ini: ContentFilterList, AlphabetList, EnableUnusedLetters"
keywords: Einstellung, content.ini, AlphabeticalFilterSettings, INI, setting, ContentFilterList, AlphabetList, EnableUnusedLetters
type: generated
generated: exp:help:examples
---
# content.ini [AlphabeticalFilterSettings]

Der Block `[AlphabeticalFilterSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ContentFilterList

> List of alphabets that will be used in alphabetical navigation.  
> Alphabets will be fetched from AlphabetList\[<name of alphabet>\]  
> For example:  
>     ContentFilterList\[\]=<name of alphabet>  
> If array is empty means the alphabetical navigation will be disabled.  
> Note: This is currently implemented in new admin interface, as it was in the old one,  
> as a full refresh of the page. It might be enhanced by Ajax calls in the future.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[AlphabeticalFilterSettings]
# ein leeres "ContentFilterList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ContentFilterList[]
*/ ?>
```

## AlphabetList

> ContentFilterList\[\]=eng-GB  
> ContentFilterList\[\]=ger-DE  
> Alphabet range in unicode (range of english alphabet is between 97 and 122).  
> You can use range (e.g. 97-122) and/or sequence of letters (e.g. 248,249).  

Standard: ein Hash mit 4: `eng-GB=97-122`, `ger-DE=97,228,98-111,246,112-115,223,116,117,252,118-122`, `rus-RU=1072-1103`, `nor-NO=97-122,230,248,229`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[AlphabeticalFilterSettings]
# ein leeres "AlphabetList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AlphabetList[]
AlphabetList[eng-GB]=97-122
AlphabetList[ger-DE]=97,228,98-111,246,112-115,223,116,117,252,118-122
AlphabetList[rus-RU]=1072-1103
AlphabetList[nor-NO]=97-122,230,248,229
*/ ?>
```

## EnableUnusedLetters

> If true unused letters in alphabetical navigation (when browsing or fetching subitems)  
> will be disabled.  
> For example:  
> if there are no objects with 'a' first letter in the object name 'a' letter will be disabled in alphabetical navigation.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[AlphabeticalFilterSettings]
EnableUnusedLetters=true
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
