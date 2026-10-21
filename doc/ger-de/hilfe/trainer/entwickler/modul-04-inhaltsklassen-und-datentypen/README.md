---
title: "Modul 4: Inhaltsklassen und Datentypen"
audience: trainer, developer
category: training
order: 40
summary: Das vierte Modul des Kurses Entwickler. Die Teilnehmenden lesen das Inhaltsmodell über Bezeichner, ändern mit einem Datentyp-Override das Aussehen eines Feldes, lesen und schreiben Inhalte aus PHP und erzeugen mit dem RAD-Assistenten einen eigenen Datentyp. 3 Stunden 40 Minuten.
keywords: entwickler, modul 4, inhaltsklasse, attribut, bezeichner, datentyp, attribute_view_gui, override, eZContentFunctions, createAndPublishObject, datentyp-assistent, RAD, developers course, module 4, content class, attribute, identifier, datatype, Datatype wizard
type: help
---
# Modul 4: Inhaltsklassen und Datentypen

Templates zeigen Inhalte; in diesem Modul geht es um die Inhalte selbst, von der Seite eines Entwicklers aus. Die
Teilnehmenden legen eine kleine Klasse an, finden ihre Bezeichner aus PHP heraus, ändern mit einem Override die Ansicht
eines Feldes, schreiben Inhalte aus einem Skript und schließen mit einem eigenen Datentyp ab, den der RAD-Assistent
schreibt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Dauer | 3 Stunden 40 Minuten (mit einer Pause von 10 Minuten) |
| Lektionen | 4 |
| Niveau | Fortgeschrittene bis Experten |

## Lektionen

| Lektion | Dauer | Was die Teilnehmenden tun |
|---|---|---|
| [Lektion 4.1: Klassen, Attribute und Bezeichner](klassen-attribute-und-bezeichner.md) | 50 Minuten | Die Klasse **Note** anlegen, ihre Attribute aus PHP auflisten |
| [Lektion 4.2: Datentypen und ihre Templates](datentypen-und-ihre-templates.md) | 45 Minuten | Datentyp-Templates lesen, den Titel von Notizen überschreiben |
| [Lektion 4.3: Inhalte aus PHP lesen und schreiben](inhalte-aus-php.md) | 55 Minuten | Eine Notiz mit `eZContentFunctions` anlegen und aktualisieren |
| [Lektion 4.4: Ein eigener Datentyp](ein-eigener-datentyp.md) | 60 Minuten | Mit dem **Datentyp-Assistent** einen Datentyp erzeugen, ihn einer Klasse hinzufügen |

## Dieses Modul vorbereiten

- Alle Teilnehmenden brauchen ein Administratorkonto auf ihrer eigenen Schulungsinstallation: Das Modul legt eine
  Klasse an, schreibt Inhalte und erzeugt eine Erweiterung.
- Auf einer gemeinsamen Installation verwenden alle Teilnehmenden ihre Initialen in Klassen- und Erweiterungsnamen
  (`note_ab`, `mysite_rating_ab`) und ihren eigenen Kursordner.
- Lektion 4.4 schreibt Dateien unterhalb von `extension/`: Der Benutzer des Webservers muss dort auf der
  Schulungsinstallation schreiben dürfen (niemals auf einer Live-Website).
- Proben Sie den Assistenten einmal auf Ihrer eigenen Installation und behalten Sie die Liste der Dateien, die er
  geschrieben hat, für die Diskussion.

## Zeitplan

| Teil | Minuten |
|---|---|
| Lektion 4.1 | 50 |
| Lektion 4.2 | 45 |
| Pause | 10 |
| Lektion 4.3 | 55 |
| Lektion 4.4 | 60 |
| **Gesamt** | **220** |

## Verwandte Seiten

- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
