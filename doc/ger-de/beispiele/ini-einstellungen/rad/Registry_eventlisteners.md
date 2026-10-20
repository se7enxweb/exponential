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
> Title=<what the entries are>                 auf setup/rad und setup/radsurvey angezeigt (erforderlich)  
> Description=<one sentence>                    angezeigt, wenn der Zeiger auf setup/rad über dem Register ruht  
> Doc=<path to the documentation>  
> TranslationContext=<context>                  von Title und Description; Standard kernel/setup/rad  
>
> Aus einer INI-Datei gelesene Einträge:  
>   Ini=<file>.ini                              die Kopie des Kernels, des Overrides und jeder Erweiterung wird gelesen  
>   Section=<name> | <prefix>\* | /<pattern>/    ein Block, eine Familie von Blöcken (der Rest des Namens oder die erste  
>   Sections\[\]=...                                Gruppe des Musters ist der Name des Eintrags) oder mehrere davon  
>   Entries\[<variable>\]=<check>                 jeder Wert von <variable> in diesen Blöcken ist ein Eintrag  
>   Skip=<variable>                             ein Block, in dem dies true ist, ist kein Punkt (ein eingebauter)  
> Einträge, die eine Klasse liefert (ein Register, das im Code statt in den Einstellungen lebt):  
>   List=<class>::<CONSTANT> | <class>::<method>   name => class, name => array( 'class' => ... ) oder eine Liste von Namen  
>   Implements=<class or interface>             was jede Klasse erweitern oder implementieren muss  
> InTotal=enabled | disabled                    disabled: aufgeführt und geprüft, nicht zur Summe gezählt (eine Liste, die  
>                                               nur auf Punkte verweist, die anderswo gezählt werden)  
>
> Eine Prüfung ist eine von:  
>   <class or interface>    der Wert ist eine Klasse, die sie erweitert oder implementiert  
>   class                   der Wert ist eine beliebige Klasse (eine leere Prüfung bedeutet dasselbe)  
>   callable                <class>::<method>  
>   listener                <event>@<class>::<method> oder <event>@<function>, wie \[Event\] Listeners\[\]  
>   template                ein Template, das ein Design hat (design:<path> oder <path>)  
>   section:<prefix>        dieselbe INI-Datei hat einen Block \[<prefix><value>\]  
>   in:<class>::<NAME>      der Wert ist ein Schlüssel oder Wert dieser Klassenkonstante oder öffentlichen statischen Methode  
>   one-of:<a>,<b>,...      der Wert ist eines dieser Wörter  
>   name                    jeder Wert: der Eintrag wird gezählt, nichts geprüft  
> Ein Eintrag, der seine Prüfung nicht besteht, wird als defekt angezeigt.  

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
