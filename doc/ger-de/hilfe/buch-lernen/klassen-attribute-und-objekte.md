---
title: "Klassen, Attribute und Objekte"
audience: user, editor, developer, builder
category: books
order: 220
summary: Wie eine Inhaltsklasse über ihre Attribute eine Art von Inhalt definiert, wie daraus Objekte angelegt werden und wie Versionen, Übersetzungen und die Versionsgrenze funktionieren.
keywords: Inhaltsklasse, Klassenattribut, Datentyp, Inhaltsobjekt, Version, Versionshistorie, Übersetzung, Buch Learning, content class, class attribute, datatype, content object, version, version history, translation, DefaultVersionHistoryLimit, content.ini, Learning book
type: help
---
# Klassen, Attribute und Objekte

## Klasse, Attribut, Objekt

- Eine **Inhaltsklasse** legt fest, wie eine Art von Inhalt aufgebaut ist: ein Artikel, ein Ordner, eine Person.
- Ein **Klassenattribut** ist ein Feld der Klasse, mit einem bestimmten **Datentyp** (ein kurzer Text, Rich Text, ein
  Datum, ein Bild). Ein Datentyp ist die unterste Ebene der Typisierung, doch einige sind mächtige zusammengesetzte Typen
  ([Rundgang durch die Datentypen](rundgang-durch-die-datentypen.md)).
- Ein **Inhaltsobjekt** ist eine Instanz einer Klasse. Seine Inhaltselemente sind die Attribute, die die Klasse nennt.
  Daneben hat es allgemeine Eigenschaften: Objekt-ID, Name, Erstellungsdatum, Besitzer und mehr. Der Name wird durch das
  **Muster für den Objektnamen** der Klasse aus einem oder mehreren Attributen gebildet.

Exponential liefert eine große Zahl von Datentypen mit, und Erweiterungen fügen weitere hinzu (Tags, einen Kartenort,
Sternebewertungen).

## Versionen

Ein Objekt steht immer unter **Versionsverwaltung**. Das Bearbeiten erzeugt einen Entwurf; das Veröffentlichen macht ihn
zur aktuellen Version und die vorherige zu einer archivierten Version. Die Historie wird innerhalb einer Grenze
aufbewahrt, die sich in `content.ini` ändern lässt:

```ini
[VersionManagement]
DefaultVersionHistoryLimit=45
VersionHistoryClass[1]=75
```

`DefaultVersionHistoryLimit` ist die Zahl der Versionen, die für jede Klasse aufbewahrt werden;
`VersionHistoryClass[<class id>]` setzt sie für eine einzelne Klasse (hier behält Klasse 1 deren 75). Zwei Dinge aus dem
Buch gelten weiterhin:

- Eine Grenze unter **2** wird nicht beachtet: Das Bearbeiten braucht einen Entwurf neben der veröffentlichten Version,
  daher behält der Kernel mindestens 2.
- Ältere Versionen werden nicht bearbeitet; um zurückzugehen, kopieren Sie die ältere Version in einen neuen Entwurf und
  veröffentlichen ihn. Für eine Website mit Qualitätsregeln für Dokumente ist das die Dokumentenhistorie.

Liegengebliebene Entwürfe (ein Redakteur, der ein Fenster verlassen hat) werden durch Einstellungen im selben Abschnitt
(`DraftsDuration` und verwandte) und durch einen Cronjob aufgeräumt; siehe [Entwürfe und ausstehende Einträge](../../anleitungen/entwuerfe-und-ausstehende-objekte.md).
Das Buch merkte an, dass man zum Vergleichen von Versionen ein Diff-Werkzeug brauchte. Heute zeigt die Versionsansicht
die Versionen nebeneinander ([Inhaltshistorie](../../anleitungen/inhaltsverlauf.md)).

Die Versionsverwaltung gilt für **jedes** Attribut, Binärdateien eingeschlossen.

## Übersetzungen

Ein Objekt kann in andere Sprachen übersetzt werden. Ein Attribut, das nicht übersetzt werden soll (etwa ein Foto), kann
auf Ebene der Klasse entsprechend markiert werden. Siehe [Inhaltssprachen](../../anleitungen/inhaltssprachen.md) und
[Inhalte übersetzen](../handbuch/wie-uebersetze-ich-inhalte.md).

## Wie es weitergeht

[Inhaltsmodell und Bearbeitung](../../anleitungen/inhaltsmodell-und-bearbeitung.md) ist die Referenz dazu in der Administration.
Weiter: [Inhalt und Darstellung](inhalt-und-darstellung.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
