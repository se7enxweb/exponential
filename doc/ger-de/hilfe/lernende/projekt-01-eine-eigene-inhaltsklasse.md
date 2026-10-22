---
title: "Projekt 1: eine eigene Inhaltsklasse"
audience: student
category: learning
order: 25
summary: Lernprojekt 1 - eine eigene Inhaltsklasse (eine Vereinsveranstaltung) mit dem Klasseneditor entwerfen und anlegen, Datentypen wählen, Pflichtfelder und ein Muster für den Objektnamen festlegen, Objekte davon anlegen und veröffentlichen und eine kurze Entwurfsnotiz mit Screenshots abgeben, bewertet mit einem Bewertungsraster.
keywords: lernprojekt, inhaltsklasse, klasseneditor, attribut, datentyp, Textzeile, Textabschnitt, XML Block, Bild, Datum, muster für den objektnamen, erforderlich, lernende, study project, content class, class editor, attribute, datatype, Text line, Text block, XML block, Image, Date, object name pattern, required, learner
type: help
---
# Projekt 1: eine eigene Inhaltsklasse

In diesem Projekt entwerfen Sie eine Inhaltsklasse für eine Art von Inhalt, die es auf Ihrer Lernwebsite noch nicht
gibt, legen sie im Klasseneditor an und veröffentlichen einige Objekte davon. Das ist der erste Schritt jeder Website,
die Sie jemals bauen werden.

## Inhalt

1. [Auf einen Blick](#auf-einen-blick)
2. [Ziel](#ziel)
3. [Bevor Sie beginnen](#bevor-sie-beginnen)
4. [Aufgaben](#aufgaben)
5. [Abgabe](#abgabe)
6. [Bewertungsraster](#bewertungsraster)
7. [Zusatzziele](#zusatzziele)

## Auf einen Blick

| | |
|---|---|
| Stufe | Einsteiger |
| Zeit | Etwa 2 Stunden |
| Pfade | Administrator, Entwickler |
| Voraussetzungen | Eine Lernwebsite und die Administratoranmeldung dafür; [Das Inhaltsmodell verstehen](das-inhaltsmodell-verstehen.md) |

## Ziel

Eine Klasse **Club event** mit mindestens fünf Attributen, drei veröffentlichte Veranstaltungen in einem Ordner Ihrer
Website und eine kurze Entwurfsnotiz, die Ihre Entscheidungen erklärt.

## Bevor Sie beginnen

Lesen Sie [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md), Abschnitte
1 bis 3, und [Klassengruppen](../../anleitungen/klassengruppen.md). Planen Sie zuerst auf Papier:

| Attribut | Bezeichner | Datentyp (Name im Klasseneditor) | Erforderlich |
|---|---|---|---|
| Titel | `title` | Textzeile | ja |
| Datum | `event_date` | Datum | ja |
| Zusammenfassung | `summary` | Textabschnitt | nein |
| Beschreibung | `description` | XML Block (Rich Text) | nein |
| Plakat | `poster` | Bild | nein |
| Plätze | `seats` | Ganzzahl | nein |

Die Namen in der dritten Spalte sind die Datentypnamen, die der Kernel ihnen gibt (zum Beispiel „Textzeile“ für
`ezstring` und „XML Block“ für `ezxmltext`, in `kernel/classes/datatypes/`). Bezeichner werden kleingeschrieben, mit
Unterstrichen.

## Aufgaben

1. **Öffnen Sie die Klassengruppen.** Öffnen Sie in der Administrationsoberfläche `/class/grouplist`. Erwartet: die
   Klassengruppen, darunter **Inhalt**.
2. **Legen Sie die Klasse an.** Klicken Sie auf **Inhalt**, dann auf **Neue Klasse**. Name `Club event`, Bezeichner
   `club_event`, Muster für den Objektnamen `<title>`.
3. **Fügen Sie die Attribute hinzu.** Wählen Sie für jede Zeile Ihres Plans den Datentyp unter **Attribut
   hinzufügen**, klicken Sie auf **Hinzufügen** und tragen Sie Name und Bezeichner ein; setzen Sie das Häkchen bei
   **Erforderlich**, wo geplant.
4. **Speichern Sie.** Klicken Sie auf **OK**. Erwartet: Die Klasse erscheint in der Liste der Gruppe Inhalt, und ihre
   Seite (**Setup > Klassen** oder `/class/view/<id>`) zeigt Ihre Attribute
   ([Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md#2-eine-klasse-anlegen)).
5. **Legen Sie einen Ordner** „Veranstaltungen“ in der Inhaltsstruktur an.
6. **Legen Sie darin drei Veranstaltungen an**: Wählen Sie den Ordner, wählen Sie im Menü zum Anlegen **Club event**,
   **Hier erstellen**, füllen Sie die Felder aus und **Zur Veröffentlichung schicken**. Lassen Sie einmal absichtlich
   den Titel leer und notieren Sie die Meldung, die Sie erhalten.
7. **Sehen Sie sie sich auf der Website an.** Öffnen Sie den Ordner im öffentlichen Siteaccess. Wenn die neuen
   Elemente in einer Liste nicht erscheinen, leeren Sie einmal den Inhalts-Cache ([Caches verstehen](caches-verstehen.md)).
8. **Bearbeiten Sie eine Veranstaltung zweimal** und sehen Sie sich ihre Versionen an
   ([Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md#3-ein-objekt-erstellen-und-bearbeiten)).

## Abgabe

- Eine Entwurfsnotiz (eine halbe Seite): warum es jedes Attribut gibt, warum jeder Datentyp gewählt wurde und welche
  Felder Pflichtfelder sind und warum.
- Screenshots: die Klassenseite mit allen Attributen; der Ordner mit drei Veranstaltungen; die Meldung aus Aufgabe 6.
- Der Bezeichner der Klasse und die Objektnummern Ihrer Veranstaltungen.
- Das Release, mit dem Sie gearbeitet haben.

## Bewertungsraster

Bewerten Sie die vier gemeinsamen Kriterien aus [Die eigene Arbeit bewerten](die-eigene-arbeit-bewerten.md) und diese
Prüfungen (8):

1. Die Klasse hat mindestens fünf Attribute.
2. Jeder Bezeichner ist kleingeschrieben, ohne Leerzeichen.
3. Das Muster für den Objektnamen macht den Titel der Veranstaltung zu ihrem Namen.
4. Titel und Datum sind Pflichtfelder, und die Notiz sagt, warum.
5. Mindestens vier verschiedene Datentypen werden verwendet, jeder mit einer Begründung.
6. Drei Veranstaltungen sind in einem Ordner veröffentlicht.
7. Eine Veranstaltung hat mindestens drei Versionen.
8. Die Notiz nennt die verwendeten Seiten.

## Zusatzziele

- Fügen Sie ein Attribut **Objekt Verknüpfungsliste** hinzu, das auf eine Klasse „Veranstaltungsort“ zeigt, die Sie
  ebenfalls anlegen.
- Übersetzen Sie den Klassennamen für eine zweite Sprache, wenn Ihre Website eine hat: Klassen haben auf ihrer Seite
  unter **Setup > Klassen** ein Fenster **Übersetzungen** ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen)).
- Machen Sie ein Attribut nicht durchsuchbar und erklären Sie die Wirkung nach [Projekt 6](projekt-06-eine-suche-die-das-richtige-findet.md).
- Lesen Sie [Inhaltsklassen für Website-Bauer](../erweiterungsentwicklung/teil-2-builder/inhaltsklassen-fuer-builder.md)
  und notieren Sie eine Sache, die Sie jetzt anders entwerfen würden.

## Verwandte Seiten

- [Lernprojekte: Überblick](lernprojekte-ueberblick.md)
- [Projekt 2: eine kleine Website-Struktur](projekt-02-eine-kleine-website-struktur.md)
- [Das Inhaltsmodell verstehen](das-inhaltsmodell-verstehen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
