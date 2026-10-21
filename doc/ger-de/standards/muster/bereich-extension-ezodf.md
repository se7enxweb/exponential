---
title: Muster in extension/ezodf
audience: developer
category: standards
order: 446
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezodf mit den Entwurfsmustern.
keywords: extension/ezodf, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezodf

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezodf` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (8)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `Exponential\View\Extension\Ezodf\Ezodf\Export` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezodf/classes/runnable/views/ezodf/export.php:71` | Der Code von extension/ezodf/modules/ezodf/export.php, in eine Klasse verschoben (#207 Stufe 1). |
| `Exponential\View\Extension\Ezodf\Ezodf\Import` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/ezodf/classes/runnable/views/ezodf/import.php:44` | Der Code von extension/ezodf/modules/ezodf/import.php, in eine Klasse verschoben (#207 Stufe 1). |
| `ezodfInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezodf/ezinfo.php:12` | – |
| `eZOOConverter` | class | [Strategie / Handler](strategie-handler.md) | `extension/ezodf/classes/ezooconverter.php:41` | – |
| `eZOOGenerator` | class | [Builder](builder.md) | `extension/ezodf/classes/ezoogenerator.php:41` | – |
| `eZOOImport` | class | – | `extension/ezodf/classes/ezooimport.php:41` | – |
| `eZOpenofficeUploadHandler` | class extends eZContentUploadHandler | [Strategie / Handler](strategie-handler.md) | `extension/ezodf/uploadhandlers/ezopenofficeuploadhandler.php:41` | – |
| `eZRESTODFHandler` | class extends eZRESTBaseHandler | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md), [Strategie / Handler](strategie-handler.md) | `extension/ezodf/classes/ezrestodfhandler.php:46` | – |

## Modulansichten (2)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/ezodf/import` | `import.php` | import | `extension/ezodf/modules/ezodf/module.php:34` |
| `/ezodf/export` | `export.php` | export | `extension/ezodf/modules/ezodf/module.php:42` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `ezodf/import` | – | `extension/ezodf/modules/ezodf/module.php:52` |
| `ezodf/export` | – | `extension/ezodf/modules/ezodf/module.php:53` |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[RESTSettings] HandlerList[]` | `eZRESTODFHandler` | `extension/ezodf/settings/ezrest.ini.append.php:4` |
