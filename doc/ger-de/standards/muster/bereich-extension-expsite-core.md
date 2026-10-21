---
title: Muster in extension/expsite_core
audience: developer
category: standards
order: 428
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expsite_core mit den Entwurfsmustern.
keywords: extension/expsite_core, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expsite_core

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expsite_core` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (4)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expSiteBundleMailHelper` | class | – | `extension/expsite_core/classes/expsitebundlemailhelper.php:2` | – |
| `expSiteBundlePathHelper` | class | – | `extension/expsite_core/classes/expsitebundlepathhelper.php:2` | – |
| `expSiteBundleRedirectHelper` | class | – | `extension/expsite_core/classes/expsitebundleredirecthelper.php:2` | – |
| `expsite_coreInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expsite_core/ezinfo.php:12` | – |
