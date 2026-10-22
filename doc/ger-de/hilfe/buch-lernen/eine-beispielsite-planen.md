---
title: "Eine Beispielsite planen: das Vereinsportal"
audience: builder, creators, developer
category: books
order: 320
summary: Das ausgearbeitete Planungsbeispiel des Buchs, ein Portal für einen Verein für Naturfotografie und Astronomie, als Rezept für die Planung jeder Website: Anforderungen, Klassen, der Baum, mit den Klassen als Attributlisten ausgeschrieben.
keywords: Beispielsite, Planung, Anforderungen, Klassen, Taxonomie, Struktur, Dokumente, Bilder, Forum, Kalender, Vereinsportal, Buch Learning, example site, planning, requirements, classes, taxonomy, structure, documents, images, forum, calendar, club portal, Learning book
type: help
---
# Eine Beispielsite planen: das Vereinsportal

Das Buch beendet sein Kapitel über Inhalte mit einer Planungsübung, und es ist eine gute. Das Projekt: ein Portal für
einen Verein für Naturfotografie und Astronomie. Verwenden Sie es als Vorlage für Ihre eigene Planung, in der
Reihenfolge, der das Buch folgt: **Anforderungen, Klassen, Struktur, Sektionen, Gruppen, Rollen.**

## 1. Anforderungen

Ein **öffentlicher Teil**: Dokumente, Bilder der Mitglieder in Galerien, Diskussionsforen, ein Kalender der
Aktivitäten, Logbücher, Nachrichten mit einem Abonnementdienst und einige persönliche Funktionen für registrierte
Benutzer. Ein **privater Teil** für den Vorstand des Vereins: Satzungsdokumente, Sitzungsprotokolle, Aufgaben und
private Diskussionen.

Der Punkt des Buchs: Mit einem flexiblen Modell gibt es oft mehr als einen Weg. Verringern Sie die Zahl der Klassen.
Fragen Sie bei jeder: Ist es dieselbe Sache mit einem Merkmal oder eine andere Sache?

## 2. Die Klassen

| Klasse | Attribute (Datentyp) |
|---|---|
| Dokument | Titel (Textzeile); Einleitung (Rich Text); Haupttext (Rich Text); Anhang (Datei); Schlüsselwörter (Schlüsselwörter, ein automatisches „verwandte Dokumente“); Verwandte Einträge (Objektbeziehungsliste); Kommentare erlauben (Kontrollkästchen); Als Nachricht auf der Startseite zeigen (Kontrollkästchen) |
| Logbucheintrag | Betreff (Textzeile); Haupttext (Rich Text); Verwandte Einträge, die anderswo veröffentlicht sind (Objektbeziehungsliste). Das Logbuch selbst ist ein Ordner. |
| Bild | Titel; Beschreibung (Rich Text); Bild; Bildunterschrift (Text); Verwendete Ausrüstung (Rich Text); Datum und Uhrzeit; Ort (Rich Text); Schlüsselwörter |
| Forumsbeitrag | Betreff; Nachricht (reiner Text); Typ (Auswahl: Frage, Anmerkung, wichtige Neuigkeit ...). Das Forum ist ein Ordner. |
| Kalendereintrag | Betreff; Beschreibung; Ort; Datum und Uhrzeit für Beginn und Ende; Schlüsselwörter; Kategorie (Auswahl); Verwandte Einträge |
| Platzhalterobjekt | Name (Textzeile). Dient als Aufhänger für besondere Seiten wie die Startseite, mit einem eigenen Template. |
| Link | Die Standard-Linkklasse: Titel, Beschreibung, Link. |

Die Datentypen stehen im [Rundgang durch die Datentypen](rundgang-durch-die-datentypen.md); wie Sie eine Klasse anlegen, steht in
[Eine Inhaltsklasse anlegen](eine-inhaltsklasse-anlegen.md). Kommentare werden mit Templates hinzugefügt
([Häufige Aufgaben mit Templates](haeufige-aufgaben-mit-templates.md)). Heute könnten Sie auch die mitgelieferten Klassen des
installierten Site-Pakets verwenden (article, folder, image, file, link, comment) und nur ergänzen, was fehlt.

## 3. Der Baum

```
Home (dummy object: aggregates news and other items)
  News (with an archive navigator)
  Documents: Technical documents, Meeting reports, Miscellaneous
  Calendar of events
  Links: Astronomy related, General photography
  Galleries: Astronomy, Nature
  Forums: Announcements, Astronomy related, Nature related
  My site (personal)
  Restricted part: Forum, Statutory documents
```

Die Struktur ist mit Absicht schlicht. Das visuelle Erlebnis kommt aus den Templates
([Inhalt und Darstellung](inhalt-und-darstellung.md)). Prüfen Sie Ihren Plan, indem Sie den Baum zeichnen und einen
Besucher hindurchführen.

## 4. Sektionen, Gruppen, Rollen

Weiter: [Benutzer, Gruppen und Rollen im Beispiel](benutzer-gruppen-und-rollen-im-beispiel.md).

## Quelle und Lizenz

Bearbeitet nach Kapitel 2, „Content Management with eZ publish“, von *Learning eZ publish: Building Content Management Solutions* von Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding und Ben Pirt (zusätzliches Material von Bruce Morrison), zweite Auflage, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x und Graham
Brookins. Lizenziert unter der GNU Free Documentation License, Version 1.3 oder einer späteren Version, ohne
unveränderliche Abschnitte, ohne vordere und ohne hintere Umschlagtexte ([die Lizenz](../../../eng-us/LICENSE-GFDL-1.3.txt)).
Änderungen: umgestellt und gekürzt; Einstellungen, Beschriftungen, Datentypen, Tags und URLs gegen Exponential 6 geprüft und korrigiert; Links auf die aktuellen Anleitungen ergänzt.
