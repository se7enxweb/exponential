---
title: Muster in extension/expsite_data_media
audience: developer
category: standards
order: 429
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expsite_data_media mit den Entwurfsmustern.
keywords: extension/expsite_data_media, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expsite_data_media

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#how-the-catalogue-is-built).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expsite_data_media` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expSiteDataMediaInstaller` | class extends expLayoutsSiteInstaller | – | `extension/expsite_data_media/classes/expsitedatamediainstaller.php:3` | – |
| `expsite_data_mediaInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expsite_data_media/ezinfo.php:12` | – |
