---
title: Muster in extension/str_replace
audience: developer
category: standards
order: 479
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/str_replace mit den Entwurfsmustern.
keywords: extension/str_replace, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/str_replace

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/str_replace` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `MyStrReplaceOperator` | class | [Template-Operator (Interpreter-Plugin)](template-operator.md) | `extension/str_replace/autoloads/str_replace_controloperator.php:3` | – |
| `str_replaceInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/str_replace/ezinfo.php:2` | – |

## Template-Operatoren (1)

| Operatoren | Klasse | Registriert in |
| --- | --- | --- |
| ezstr_replace | `MyStrReplaceOperator` | `extension/str_replace/autoloads/eztemplateautoload.php:7` |
