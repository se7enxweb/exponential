---
title: exphelp.ini [DocLanguages]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [DocLanguages] in exphelp.ini: Default, Directories, Map, Names, Legacy"
keywords: Einstellung, exphelp.ini, DocLanguages, INI, setting, Default, Directories, Map, Names, Legacy
type: generated
generated: exp:help:examples
---
# exphelp.ini [DocLanguages]

Der Block `[DocLanguages]` von `settings/exphelp.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Default

> The languages of the documentation (expHelpLanguage; doc/eng-us/bc/6.0/translated-documentation.md). Each language  
> has a directory below doc/ named by its Exponential locale code in lower case: doc/eng-us/ (eng-US) is the  
> original, a translation lives in doc/<locale>/ (doc/ger-de/ for ger-DE) with the same relative paths, or with  
> translated file names listed in doc/<locale>/.paths.json ("translated path": "path below doc/eng-us/"). A page id  
> is its path below doc/eng-us/ in every language, so a help address shows the same page in each language. Files  
> directly below doc/ in no language directory (doc/changelogs/) are not translated.  
> The reader's language is the locale of the interface (site.ini \[RegionalSettings\] Locale, set by the  
> administration's language switch), lower cased: ger-DE reads doc/ger-de/. A locale without a directory of its own  
> uses an alias of Map\[\], else the first directory of its language (ger-AT -> ger-de), else Default; a page the  
> reader's language does not have yet is shown in Default, with a note.  
> The original language, and the language of a locale that has no directory  

Standard: `eng-us`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DocLanguages]
Default=eng-us
*/ ?>
```

## Directories

> The language directories the help reads (the default first); a directory that does not exist yet is fine  

Standard: eine Liste mit 2: `eng-us`, `ger-de`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DocLanguages]
# ein leeres "Directories[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Directories[]
Directories[]=eng-us
Directories[]=ger-de
*/ ?>
```

## Map

> Optional aliases, locale => directory, for a locale that should read another locale's directory  

Standard: ein Hash mit 3: `eng-GB=eng-us`, `ger-AT=ger-de`, `ger-CH=ger-de`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DocLanguages]
# ein leeres "Map[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Map[]
Map[eng-GB]=eng-us
Map[ger-AT]=ger-de
Map[ger-CH]=ger-de
*/ ?>
```

## Names

> Directory => the language's name in English (translated in kernel/exphelp for the "not translated yet" note)  

Standard: ein Hash mit 2: `eng-us=English`, `ger-de=German`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DocLanguages]
# ein leeres "Names[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Names[]
Names[eng-us]=English
Names[ger-de]=German
*/ ?>
```

## Legacy

> The directories of 6.0.14 and before => today's: an old address (exphelp/viewer/en/us/<id>, exphelp/viewer/doc/en/us/<id>,  
> exphelp/viewer/de/<translated path>, /(lang)/en-us) still finds its page. No file is read from an old directory.  

Standard: ein Hash mit 2: `en/us=eng-us`, `de=ger-de`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DocLanguages]
# ein leeres "Legacy[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Legacy[]
Legacy[en/us]=eng-us
Legacy[de]=ger-de
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
