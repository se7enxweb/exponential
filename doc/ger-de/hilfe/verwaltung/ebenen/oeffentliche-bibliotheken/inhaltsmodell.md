---
title: Inhaltsmodell für öffentliche Bibliotheken
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Die mitgelieferten Klassen Event und Event calendar und ihre Attribute, weitere Klassen, die zur Website einer Bibliothek passen, und Rezepte für die Klassen Zweigstelle, Empfehlung und Digitales Angebot mit echten Datentypen.
keywords: Inhaltsmodell, Bibliothek, Klasse Event, Veranstaltungskalender, Klasse Zweigstelle, Öffnungszeiten, Empfehlung, Leseliste, digitales Angebot, content model, library, event class, event calendar, branch class, opening hours, recommendation, reading list, digital resource, ezdatetime, ezkeyword, eztags
type: help
---
# Inhaltsmodell für öffentliche Bibliotheken

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Inhaltsklassen der Website einer
Bibliothek entwerfen. Die Klassen für Veranstaltungen werden mit Exponential ausgeliefert; Zweigstellen und Empfehlungen
fügen Sie im Klasseneditor hinzu
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)). Die
Klassendefinitionen stammen aus dem Paket `sevenx_classes`
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)) und der Klassenansicht des
Demonstrationsservers.

## Inhalt

1. [Die Klassen für Veranstaltungen](#die-klassen-für-veranstaltungen)
2. [Weitere mitgelieferte Klassen, die passen](#weitere-mitgelieferte-klassen-die-passen)
3. [Hinzuzufügende Klasse: Zweigstelle](#hinzuzufügende-klasse-zweigstelle)
4. [Hinzuzufügende Klasse: Empfehlung](#hinzuzufügende-klasse-empfehlung)
5. [Hinzuzufügende Klasse: Digitales Angebot](#hinzuzufügende-klasse-digitales-angebot)
6. [Die Klasse Event erweitern](#die-klasse-event-erweitern)

## Die Klassen für Veranstaltungen

**Event** (`event`), kein Container:

| Attribut | Bezeichner | Datentyp | Pflicht |
|---|---|---|---|
| Full title | `title` | Textzeile (`ezstring`) | nein |
| Short title | `short_title` | Textzeile | ja |
| Text | `text` | XML-Block (`ezxmltext`) | nein |
| Category | `category` | Schlüsselwörter (`ezkeyword`) | nein |
| From Time | `from_time` | Datum und Zeit (`ezdatetime`) | ja |
| To Time | `to_time` | Datum und Zeit | nein |
| Meta Data | `metadata` | Metadaten (`xrowmetadata`) | nein |

**Event calendar** (`event_calendar`) ist ein Container mit einer Auswahl (`ezselection`), die seine Ansicht bestimmt;
das Design ezwebin zeigt die Auswahlmöglichkeiten als Kalender (`event_view_calendar.tpl`) oder als Programmliste
(`event_view_program.tpl`). In den Rollen des Standardpakets darf die Redaktion `event` unterhalb von `event_calendar`
anlegen ([Rollen und Richtlinien von sevenx_multisite](../../../seitenpakete/sevenx-multisite/rollen-und-richtlinien-von-sevenx-multisite.md)).

## Weitere mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Ordner der Zweigstellen, Dienste, Themen |
| Article | `article` | Nachrichten, Erklärungen zu Diensten |
| Gallery, Image | `gallery`, `image` | Fotos von Veranstaltungen und Gebäuden |
| Link | `link` | der Katalog, Anbieter digitaler Angebote |
| Feedback form | `feedback_form` | Kontakt, Vorschläge |
| Poll | `poll` | schnelle Fragen an die Lesenden |

## Hinzuzufügende Klasse: Zweigstelle

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Name | `name` | Textzeile | |
| Anschrift | `address` | Textabschnitt (`eztext`) | |
| Öffnungszeiten | `opening_hours` | XML-Block | eine Tabelle je Wochentag |
| Ausnahmen | `closures` | XML-Block | Schließungen an Feiertagen, kurzfristige Änderungen |
| Ausstattung | `facilities` | Tags (`eztags`) | Computer, Drucken, Lernräume, Kinderbereich |
| Barrierefreiheit | `access` | XML-Block | Eingang, Toiletten, Induktionsschleife, Parken |
| Telefon | `phone` | Textzeile | |
| E-Mail | `email` | E-Mail (`ezemail`) | ein Postfach der Zweigstelle |
| Bild | `image` | Bild (`ezimage`) | mit Alternativtext |

Veranstaltungen sind über ein der Klasse Event hinzugefügtes Attribut `ezobjectrelation` mit ihrer Zweigstelle
verknüpft (siehe unten), sodass die Seite einer Zweigstelle ihre Veranstaltungen auflisten kann.

## Hinzuzufügende Klasse: Empfehlung

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | das Thema der Liste |
| Einleitung | `intro` | XML-Block | |
| Titel der Medien | `items` | XML-Block oder Matrix (`ezmatrix`) | Titel, Autorin oder Autor und ein Link auf den Datensatz im Katalog |
| Zielgruppe | `audience` | Tags | Kinder, Jugendliche, Erwachsene, leichte Lektüre |
| Sprache | — | die Übersetzungen des Objekts | |

Eine Matrix ergibt eine ordentliche Tabelle; Rich Text gibt mehr Freiheit. In beiden Fällen verlinkt jeder Eintrag auf
den Datensatz im Katalog, statt ihn zu kopieren.

## Hinzuzufügende Klasse: Digitales Angebot

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name | `name` | Textzeile |
| Was es bietet | `description` | XML-Block |
| Wie man sich anmeldet | `access` | XML-Block |
| Link | `url` | URL (`ezurl`) |
| Datenschutzerklärung des Anbieters | `privacy_url` | URL |

## Die Klasse Event erweitern

Der mitgelieferten Klasse Event Attribute hinzuzufügen ist sicher (vorhandene Veranstaltungen erhalten leere Werte,
[Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)).
Nützliche Ergänzungen für Bibliotheken:

| Attribut | Datentyp | Warum |
|---|---|---|
| Zweigstelle | Objektbeziehung (`ezobjectrelation`) | der Ort |
| Zielgruppe | Tags oder Auswahl | Kinder, Familien, Erwachsene |
| Buchung | Auswahl | freier Eintritt, Anmeldung erforderlich, ausgebucht, abgesagt |
| Link zur Buchung | URL | wenn Buchungen anderswo laufen |

Ändern Sie die Templates so, dass sie die neuen Felder zeigen; die mitgelieferten Templates von ezwebin kennen sie nicht
([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)).

## Verwandte Seiten

- [Öffentliche Bibliotheken](README.md)
- [Veranstaltungen und Kataloge, verlinkt statt nachgebaut](veranstaltungen-und-verlinkte-kataloge.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
