---
title: Muster in extension/exp_enhanced_link
audience: developer
category: standards
order: 437
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/exp_enhanced_link mit den Entwurfsmustern.
keywords: extension/exp_enhanced_link, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/exp_enhanced_link

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/exp_enhanced_link` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (3)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `exp_enhanced_linkInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/exp_enhanced_link/ezinfo.php:12` | – |
| `ngEnhancedLinkType` | class extends eZDataType | [Datentyp (Strategie für Inhaltsfelder)](datentyp.md) | `extension/exp_enhanced_link/datatypes/ngenhancedlink/ngenhancedlinktype.php:5` | – |
| `ngEnhancedLinkValue` | class | – | `extension/exp_enhanced_link/classes/ngenhancedlinkvalue.php:3` | – |
