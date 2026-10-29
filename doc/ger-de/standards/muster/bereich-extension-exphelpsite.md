---
title: Muster in extension/exphelpsite
audience: developer
category: standards
order: 413
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/exphelpsite mit den Entwurfsmustern.
keywords: extension/exphelpsite, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/exphelpsite

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/exphelpsite` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (1)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `exphelpsiteInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/exphelpsite/ezinfo.php:11` | Die Extension exphelpsite: die Node-Templates der als Inhalt gepflegten Hilfe für die öffentlichen Seiten einer Site (doc/eng-us/features/6.0/help-content.md, „Help… |
