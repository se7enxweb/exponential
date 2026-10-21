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

> Die Sprachen der Dokumentation (expHelpLanguage; doc/eng-us/bc/6.0/translated-documentation.md). Jede Sprache  
> hat ein Verzeichnis unter doc/, benannt nach ihrem Locale-Code von Exponential in Kleinbuchstaben: doc/eng-us/ (eng-US) ist das  
> Original, eine Übersetzung liegt in doc/<locale>/ (doc/ger-de/ für ger-DE) mit denselben relativen Pfaden oder mit  
> übersetzten Dateinamen, aufgeführt in doc/<locale>/.paths.json ("übersetzter Pfad": "Pfad unter doc/eng-us/"). Eine Seiten-ID  
> ist in jeder Sprache ihr Pfad unter doc/eng-us/, eine Hilfeadresse zeigt also in jeder Sprache dieselbe Seite. Dateien  
> direkt unter doc/ in keinem Sprachverzeichnis (doc/changelogs/) werden nicht übersetzt.  
> Die Sprache des Lesers ist die Locale der Oberfläche (site.ini \[RegionalSettings\] Locale, gesetzt über die  
> Sprachumschaltung der Administration), in Kleinbuchstaben: ger-DE liest doc/ger-de/. Eine Locale ohne eigenes Verzeichnis  
> verwendet einen Alias aus Map\[\], sonst das erste Verzeichnis ihrer Sprache (ger-AT -> ger-de), sonst Default; eine Seite, die die  
> Sprache des Lesers noch nicht hat, wird in Default angezeigt, mit einem Hinweis.  
> Die Originalsprache und die Sprache einer Locale, die kein Verzeichnis hat  

Standard: `eng-us`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[DocLanguages]
Default=eng-us
*/ ?>
```

## Directories

> Die Sprachverzeichnisse, die die Hilfe liest (das Standardverzeichnis zuerst); ein Verzeichnis, das es noch nicht gibt, ist in Ordnung  

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

> Optionale Aliase, Locale => Verzeichnis, für eine Locale, die das Verzeichnis einer anderen Locale lesen soll  

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

> Verzeichnis => der englische Name der Sprache (übersetzt in kernel/exphelp für den Hinweis „noch nicht übersetzt“)  

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

> Die Verzeichnisse von 6.0.14 und früher => die heutigen: Eine alte Adresse (exphelp/viewer/en/us/<id>, exphelp/viewer/doc/en/us/<id>,  
> exphelp/viewer/de/<translated path>, /(lang)/en-us) findet ihre Seite weiterhin. Aus einem alten Verzeichnis wird keine Datei gelesen.  

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
