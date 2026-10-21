---
title: Muster in extension/expsite_installer
audience: developer
category: standards
order: 430
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/expsite_installer mit den Entwurfsmustern.
keywords: extension/expsite_installer, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/expsite_installer

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/expsite_installer` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (2)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expSiteInstaller` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/expsite_installer/classes/expsiteinstaller.php:6` | Super-Installer für stundenweise arbeitende Site-Entwickler: ein Befehl, der alles einrichtet. |
| `expsite_installerInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/expsite_installer/ezinfo.php:12` | – |
