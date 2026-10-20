---
title: rad.ini [Registry_eventlisteners]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [Registry_eventlisteners] in rad.ini: Title, Description, Doc, Ini, Sections, Entries"
keywords: Einstellung, rad.ini, Registry_eventlisteners, INI, setting, Title, Description, Doc, Ini, Sections, Entries
type: generated
generated: exp:help:examples
---
# rad.ini [Registry_eventlisteners]

Der Block `[Registry_eventlisteners]` von `settings/rad.ini`, mit 6 Einstellung(en).

> \[Registry\_<key>\]  
> Title=<what the entries are>                 shown on setup/rad and setup/radsurvey (required)  
> Description=<one sentence>                    shown when the pointer rests on the registry on setup/rad  
> Doc=<path to the documentation>  
> TranslationContext=<context>                  of Title and Description; default kernel/setup/rad  
>
> Entries read from an ini file:  
>   Ini=<file>.ini                              the kernel's, the override's and every extension's copy are read  
>   Section=<name> | <prefix>\* | /<pattern>/    one block, a family of blocks (the rest of the name, or the first  
>   Sections\[\]=...                                group of the pattern, is the entry's name), or several of them  
>   Entries\[<variable>\]=<check>                 every value of <variable> in those blocks is one entry  
>   Skip=<variable>                             a block in which this is true is no point (a built-in one)  
> Entries a class gives (a registry that lives in code rather than in settings):  
>   List=<class>::<CONSTANT> | <class>::<method>   name => class, name => array( 'class' => ... ), or a list of names  
>   Implements=<class or interface>             what each class must extend or implement  
> InTotal=enabled | disabled                    disabled: listed and checked, not added to the total (a list that  
>                                               only refers to points counted somewhere else)  
>
> A check is one of:  
>   <class or interface>    the value is a class that extends or implements it  
>   class                   the value is any class (an empty check means the same)  
>   callable                <class>::<method>  
>   listener                <event>@<class>::<method> or <event>@<function>, as \[Event\] Listeners\[\]  
>   template                a template some design has (design:<path> or <path>)  
>   section:<prefix>        the same ini file has a block \[<prefix><value>\]  
>   in:<class>::<NAME>      the value is a key or a value of that class constant or public static method  
>   one-of:<a>,<b>,...      the value is one of these words  
>   name                    any value: the entry is counted, nothing is checked  
> An entry that fails its check is shown as broken.  

Bearbeiten Sie `settings/rad.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/rad.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/rad.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/rad.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Title

Standard: `Event listeners`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_eventlisteners]
Title=Event listeners
*/ ?>
```

## Description

Standard: `The listeners attached to kernel events and filters: site.ini [Event] Listeners[] and [RunnableSettings] Listeners[], each <event>@<callback>.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_eventlisteners]
Description=The listeners attached to kernel events and filters: site.ini [Event] Listeners[] and [RunnableSettings] Listeners[], each <event>@<callback>.
*/ ?>
```

## Doc

Standard: `doc/eng-us/bc/6.0/rad-extension-points.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_eventlisteners]
Doc=doc/eng-us/bc/6.0/rad-extension-points.md
*/ ?>
```

## Ini

Standard: `site.ini`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_eventlisteners]
Ini=site.ini
*/ ?>
```

## Sections

Standard: eine Liste mit 2: `Event`, `RunnableSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_eventlisteners]
# ein leeres "Sections[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Sections[]
Sections[]=Event
Sections[]=RunnableSettings
*/ ?>
```

## Entries

Standard: ein Hash mit 1: `Listeners=listener`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/rad.ini.append.php
[Registry_eventlisteners]
# ein leeres "Entries[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Entries[]
Entries[Listeners]=listener
*/ ?>
```

## Siehe auch

- [Alle Blöcke von rad.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/rad.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
