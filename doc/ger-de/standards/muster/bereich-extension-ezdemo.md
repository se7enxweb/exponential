---
title: Muster in extension/ezdemo
audience: developer
category: standards
order: 441
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezdemo mit den Entwurfsmustern.
keywords: extension/ezdemo, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezdemo

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezdemo` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZArchive` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezdemo/autoloads/ezarchive.php:28` | – |
| `ezdemoInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezdemo/ezinfo.php:12` | – |
| `eZKeywordList` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezdemo/autoloads/ezkeywordlist.php:28` | – |
| `eZPageData` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezdemo/autoloads/ezpagedata.php:40` | – |
| `eZTagCloud` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/ezdemo/autoloads/eztagcloud.php:28` | – |

## Template-Operatoren (4)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezkeywordlist | `eZKeywordList` | `extension/ezdemo/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezdemo/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezdemo/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezdemo/autoloads/eztemplateautoload.php:39` |
