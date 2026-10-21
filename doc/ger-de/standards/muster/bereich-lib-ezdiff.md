---
title: Muster in lib/ezdiff
audience: developer
category: standards
order: 530
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von lib/ezdiff mit den Entwurfsmustern.
keywords: lib/ezdiff, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in lib/ezdiff

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `lib/ezdiff` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (10)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `eZDiff` | class | – | `lib/ezdiff/classes/ezdiff.php:20` | – |
| `eZDiffContainerObject` | class extends eZDiffContent | – | `lib/ezdiff/classes/ezdiffcontainerobject.php:18` | – |
| `eZDiffContainerObjectEngine` | class extends eZDiffEngine | [Strategie / Handler](strategie-handler.md) | `lib/ezdiff/classes/ezdiffcontainerobjectengine.php:21` | – |
| `eZDiffContent` | class | – | `lib/ezdiff/classes/ezdiffcontent.php:21` | – |
| `eZDiffEngine` | class | [Strategie / Handler](strategie-handler.md) | `lib/ezdiff/classes/ezdiffengine.php:21` | – |
| `eZDiffMatrix` | class | – | `lib/ezdiff/classes/ezdiffmatrix.php:21` | – |
| `eZDiffTextEngine` | class extends eZDiffEngine | [Strategie / Handler](strategie-handler.md) | `lib/ezdiff/classes/ezdifftextengine.php:21` | – |
| `eZDiffXMLTextEngine` | class extends eZDiffEngine | [Strategie / Handler](strategie-handler.md) | `lib/ezdiff/classes/ezdiffxmltextengine.php:18` | – |
| `eZTextDiff` | class extends eZDiffContent | – | `lib/ezdiff/classes/eztextdiff.php:21` | – |
| `eZXMLTextDiff` | class extends eZDiffContent | – | `lib/ezdiff/classes/ezxmltextdiff.php:18` | – |
