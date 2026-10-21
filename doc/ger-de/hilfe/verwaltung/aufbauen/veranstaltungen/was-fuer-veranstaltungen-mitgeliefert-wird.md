---
title: Was für Veranstaltungen mitgeliefert wird
audience: government, website-creator, project-manager
category: public-sector
order: 20
summary: Die Funktionen für Veranstaltungen, die wirklich mit Exponential geliefert werden - die Klassen Event, Event calendar und Multicalendar aus sevenx_classes mit ihren Attributen, die Kalender- und Programmansicht des Designs ezwebin, Richtlinien in den Standardrollen und ein Beispiel für eine Erweiterung zum Import von Veranstaltungen.
keywords: Klasse Event, Klasse Event calendar, Multicalendar, Kalenderansicht, Programmansicht, Veranstaltungen importieren, event class, event calendar class, multicalendar, sevenx_classes, ezwebin, event_view_calendar, event_view_program, from_time, to_time, ezdatetime, ezkeyword, cjw_tmv
type: help
---
# Was für Veranstaltungen mitgeliefert wird

Diese Seite richtet sich an Site-Ersteller und Projektleiter, die vor der Planung genau wissen wollen, was Exponential
für Veranstaltungen bietet. Jeder Punkt stammt aus den Klassenpaketen, den Templates und den Standardrollen dieser
Version.

## Inhalt

1. [Die Klassen](#die-klassen)
2. [Die Ansichten](#die-ansichten)
3. [Berechtigungen im Standardpaket](#berechtigungen-im-standardpaket)
4. [Veranstaltungen aus einem anderen System importieren](#veranstaltungen-aus-einem-anderen-system-importieren)
5. [Lücken](#lücken)

## Die Klassen

Aus [sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md) und der Klassenansicht des
Demonstrationsservers:

**Event** (`event`), kein Container:

| Attribut | Bezeichner | Datentyp | Erforderlich | Übersetzbar |
|---|---|---|---|---|
| Full title | `title` | Textzeile | nein | ja |
| Short title | `short_title` | Textzeile | ja | ja |
| Text | `text` | XML Block | nein | ja |
| Category | `category` | Schlüsselwörter (`ezkeyword`) | nein | ja |
| From Time | `from_time` | Datum und Zeit | ja | nein |
| To Time | `to_time` | Datum und Zeit | nein | nein |
| Meta Data | `metadata` | Metadaten (`xrowmetadata`) | nein | ja |

Das Muster für den Objektnamen ist `<short_title|title>`: Der kurze Titel benennt die Veranstaltung in Listen.

**Event calendar** (`event_calendar`), ein Container: Full Title (erforderlich), Short Title, **View** (Auswahlliste,
erforderlich: die Kalender- oder die Programmansicht), Meta Data.

**Multicalendar** (`multicalendar`): eine Liste von Objektbeziehungen `calendars` zu mehreren Event calendars; seine
vollständige Ansicht listet die Veranstaltungen aller, zum Beispiel ein siteweiter Kalender aus den Kalendern der
Abteilungen.

## Die Ansichten

| Template | In | Zeigt |
|---|---|---|
| `full/event_calendar.tpl` | Override-Templates von ezwebin | bindet die unter **View** gewählte Ansicht ein |
| `full/event_view_calendar.tpl` | Templates von ezwebin | ein Monatsraster mit vorherigem und nächstem Monat; Adressen enthalten `(month)/<n>/(year)/<yyyy>` |
| `full/event_view_program.tpl` | Templates von ezwebin | eine Liste von Veranstaltungen, 15 je Seite, mit Seiten für neuere und ältere |
| `full/event.tpl` | Override-Templates von ezwebin | eine Veranstaltung |
| `full/multicalendar.tpl` | Override-Templates von ezwebin | die Veranstaltungen der verknüpften Kalender |

Das Standarddesign `simple` neuer Installationen (`sevenx_themes_simple`) hat keine Templates für Veranstaltungen; dort
erscheinen Veranstaltungen mit der allgemeinen vollständigen Ansicht, sofern Sie keine Templates hinzufügen
([Kalenderansichten](kalenderansichten.md)).

## Berechtigungen im Standardpaket

Die Rolle Editor des Installers erlaubt das Anlegen von `event_calendar` und `multicalendar` unter Ordnern und
Frontpages und von `event` unter `event_calendar`
([Rollen und Richtlinien von sevenx_multisite](../../../seitenpakete/sevenx-multisite/rollen-und-richtlinien-von-sevenx-multisite.md)).
Besucher lesen Veranstaltungen in der Sektion Standard wie alle Inhalte.

## Veranstaltungen aus einem anderen System importieren

`cjw_tmv` ist ein Beispiel für eine Erweiterung, die Veranstaltungen aus einer externen Veranstaltungsdatenbank per
Cronjob als Inhaltsobjekte importiert (sie bedient eine regionale Tourismusdatenbank und hat eigene Klassen). Sie zeigt
das Muster für einen Import: Remote-IDs je Veranstaltung, Aktualisierung geänderter Veranstaltungen, Entfernen beendeter
Termine. Ein Import aus Ihrem eigenen Veranstaltungssystem ist eine Entwicklungsaufgabe nach demselben Muster
([REST API v1 und v2](../../../../anleitungen/rest-api-v1-und-v2.md)).

## Lücken

| Fehlt | Abhilfe |
|---|---|
| Wiederkehrende Veranstaltungen | ein Event-Objekt je Termin; oder eine Veranstaltung mit den Terminen im Text |
| Orte als strukturierte Daten | fügen Sie eine Beziehung zu einem Ort oder Textattribute hinzu ([Eine Klasse für Veranstaltungen modellieren](eine-klasse-fuer-veranstaltungen-modellieren.md)) |
| Buchungen und Kapazität | ein Formular mit dem Information Collector oder ein Buchungssystem ([Anmeldungen zu Veranstaltungen](anmeldungen-zu-veranstaltungen.md)) |
| iCalendar-Export | nicht mitgeliefert; ein Template, das `.ics` erzeugt, ist eine Entwicklungsaufgabe |

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Kalenderansichten](kalenderansichten.md)
- [ezwebin](../../../../funktionen/6.0/erweiterungen/ezwebin.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
