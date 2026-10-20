---
title: transform.ini [search]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [search] in transform.ini: Files, Extensions, Commands"
keywords: Einstellung, transform.ini, search, INI, setting, Files, Extensions, Commands
type: generated
generated: exp:help:examples
---
# transform.ini [search]

Der Block `[search]` von `settings/transform.ini`, mit 3 Einstellung(en).

> \[<group>\]  
> Rules\[\]=<command>(<option>\[,<option>\])  
> or  
> Rules\[\]=<command>  
>
> Commands:  
> normalize  
> - Normalizes multiple characters with similar meaning to one type  
> - Looks for rule named <parameter>\_normalize for each parameter  
> - If no parameters are used it will find all normalize rules  
>
> search\_normalize  
> - Runs normalization rules required for search engine  
> - Looks for rule named <parameter>\_search\_normalize for each parameter  
> - If no parameters are used it will find all search rules  
>
> transform  
> - Transforms one type of character into another  
> - Looks for rule named <parameter1>\_to\_<parameter2>  
> - If no parameters are used it will find all transformation rules  
>
> decompose  
> - Turns special characters into strings representing the character  
> - Looks for rule named <parameter>\_decompose for each parameter  
> - If no parameters are used it will find all decompose rules  
>
> lowercase  
> - Turns characters into their lowercase variant if possible  
> - Looks for rule named <parameter>\_lowercase for each parameter  
> - If no parameters are used it will find all lowercase rules  
>
> uppercase  
> - Turns characters into their uppercase variant if possible  
> - Looks for rule named <parameter>\_uppercase for each parameter  
> - If no parameters are used it will find all uppercase rules  
>
> transliterate  
> - Converts from one type of charset to another by finding similar  
>   characters or strings  
> - Looks for rule named <parameter1>\_transliterate\_<parameter2>  
> - If no parameters are used it will find all transliteration rules  
>
> diacritical  
> - Removes diacriticals from characters  
> - Looks for rule named <parameter>\_diacritical for each parameter  
> - If no parameters are used it will find all diacritical rules  
>
> rule  
> - Runs custom transformation rule  
> - Looks for rule named <parameter> for each parameter  
> - If no parameters are used it will do nothing  
>
> Special commands:  
> url\_cleanup  
> - Performs cleanups to make the text presentable in a URL  
>
> identifier\_cleanup  
> - Performs cleanups to make the text usable as an identifier  
>
> search\_cleanup:  
> - Performs cleanup to make the text parsable by the search engine  
> - Looks for rule named <parameter>\_search\_cleanup for each parameter  
> Rules used by the search engine, these rules ensures that  
> the index or search text is normalized in such way  
> to make it easier for the engine to work with it.  

Bearbeiten Sie `settings/transform.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/transform.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/transform.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/transform.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Files

> Extra transformation files for search  

Standard: eine Liste mit 1: `search.tr`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[search]
# ein leeres "Files[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Files[]
Files[]=search.tr
*/ ?>
```

## Extensions

> Extensions that have transformation files when search is used  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[search]
# ein leeres "Extensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Extensions[]
*/ ?>
```

## Commands

> The commands to use for search  
> Commands\[\]=transform  

Standard: eine Liste mit 8: `decompose`, `normalize`, `diacritical`, `search_normalize`, `lowercase`, `transform(inverted,normal)`, `transform(math,ascii)`, `search_cleanup`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/transform.ini.append.php
[search]
# ein leeres "Commands[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Commands[]
Commands[]=decompose
Commands[]=normalize
Commands[]=diacritical
Commands[]=search_normalize
Commands[]=lowercase
Commands[]=transform(inverted,normal)
Commands[]=transform(math,ascii)
Commands[]=search_cleanup
*/ ?>
```

## Siehe auch

- [Alle Blöcke von transform.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/transform.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
