---
title: Muster in extension/expimageeditor
audience: developer
category: standards
order: 414
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expimageeditor mit den Entwurfsmustern.
keywords: extension/expimageeditor, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expimageeditor

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expimageeditor` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expImageEditorException` | class extends RuntimeException | [Ausnahmehierarchie](ausnahme.md) | `extension/expimageeditor/classes/expimageeditorexception.php:15` | Eine Anfrage, die der Bildeditor ablehnt: die Meldung (englisch, bei der Anzeige übersetzt) und der HTTP-Status der Antwort. |
| `expImageEditorFunctionCollection` | class | [Fetch-Funktion (Query Object, Fassade)](fetch-funktion.md) | `extension/expimageeditor/classes/expimageeditorfunctioncollection.php:15` | Die Fetch-Funktionen des Moduls expimageeditor. |
| `expimageeditorInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expimageeditor/ezinfo.php:9` | – |
| `expImageEditorStore` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expimageeditor/classes/expimageeditorstore.php:31` | Was die Speichern-Ansicht des Bild-Editors auf dem Server tut: Der Editor arbeitet im Browser und sendet das fertige Bild; diese Klasse prüft, wer sendet… |
| `Exponential\View\Extension\Expimageeditor\Expimageeditor\Save` | class extends ModuleView | [Modulansicht (Page Controller)](modulansicht.md) | `extension/expimageeditor/classes/runnable/views/expimageeditor/save.php:20` | expimageeditor/save//: nimmt das bearbeitete Bild entgegen (POST, Feld „image“, mit dem Formular-Token im Header X-CSRF-Token oder im Feld ezxform_token), speichert… |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/expimageeditor/save` | `save.php` | edit | `extension/expimageeditor/modules/expimageeditor/module.php:15` |

## Richtlinienfunktionen (1)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `expimageeditor/edit` | – | `extension/expimageeditor/modules/expimageeditor/module.php:20` |

## Fetch-Funktionen (1)

| Fetch | Implementiert von | Definiert in |
| --- | --- | --- |
| `fetch( 'expimageeditor', 'aspect_ratios' )` | `expImageEditorFunctionCollection::fetchAspectRatios()` | `extension/expimageeditor/modules/expimageeditor/function_definition.php:13` |
