---
title: Feiertags- und Saisonöffnungszeiten
audience: local-business, businesses, customer, editor
category: local-business
order: 33
summary: Schließungen an Feiertagen, Sommeröffnungszeiten und andere Ausnahmen auf der Website eines lokalen Unternehmens - von Hand bearbeitet, mit zeitgesteuertem Veröffentlichen und Verstecken zu einem Datum vorbereitet, als Hinweis angezeigt und im Einklang mit externen Einträgen gehalten - mit einem Jahresplaner.
keywords: Feiertagszeiten, Saisonzeiten, geschlossen, Feiertag, Sommeröffnungszeiten, Hinweis, zeitgesteuertes Veröffentlichen, Bis Datum warten, zu einem Datum verstecken, Layout-Regel, lokales Unternehmen, holiday hours, seasonal hours, public holiday, summer hours, notice, scheduled publishing, wait until date, layout rule, local business
type: help
---
# Feiertags- und Saisonöffnungszeiten

Diese Seite ist für die Person, die die Öffnungszeiten eines lokalen Unternehmens aktuell hält. Exponential hat keinen
Feiertagskalender: Ausnahmen werden **von Hand bearbeitet**. Mit etwas Planung lässt sich der größte Teil der Arbeit im
Januar erledigen und am richtigen Tag automatisch veröffentlichen.

## Inhalt

1. [Drei Arten von Ausnahmen](#drei-arten-von-ausnahmen)
2. [Von Hand bearbeiten](#von-hand-bearbeiten)
3. [Mit Daten vorausplanen](#mit-daten-vorausplanen)
4. [Ein Hinweis, der auffällt](#ein-hinweis-der-auffällt)
5. [Vergessen Sie die Plattformen nicht](#vergessen-sie-die-plattformen-nicht)
6. [Ein Jahresplaner](#ein-jahresplaner)

## Drei Arten von Ausnahmen

| Art | Beispiel | Am besten behandelt als |
|---|---|---|
| Ein einzelner Schließtag | ein Feiertag | ein Hinweis, dazu die Plattformen |
| Ein Zeitraum mit anderen Zeiten | Sommeröffnungszeiten, längere Öffnung im Dezember | die regulären Zeiten ändern und danach zurückändern |
| Eine ungeplante Schließung | Krankheit, ein Rohrbruch | ein Hinweis, sofort veröffentlicht |

## Von Hand bearbeiten

Für einen Hinweis im Objekt mit den Öffnungszeiten (siehe
[Öffnungszeiten ohne besonderen Datentyp](oeffnungszeiten-ohne-besonderen-datentyp.md)):

1. Melden Sie sich an, und öffnen Sie das Objekt mit den Öffnungszeiten zum Bearbeiten.
2. Schreiben Sie den Hinweis: „Am Montag, 25. Dezember geschlossen. Ab Dienstag wieder ab 8:00 Uhr geöffnet.“
   Verwenden Sie das vollständige Datum, nicht „nächsten Montag“.
3. Veröffentlichen Sie. Prüfen Sie die Fußzeile und die Kontaktseite.
4. Bearbeiten Sie das Objekt nach dem Datum erneut und entfernen Sie den Hinweis, oder lassen Sie das Template ihn
   ausblenden, wenn die Klasse ein Datum „gültig bis“ hat.

Jede Bearbeitung ist eine neue **Version**; der vorherige Text lässt sich wiederherstellen
([Entwürfe, Versionen und Rückgängigmachen](../../kunden/alltag/entwuerfe-versionen-und-rueckgaengig.md)).

## Mit Daten vorausplanen

Exponential kann zu einem Datum handeln, wenn Ihr Administrator das eingerichtet hat
([Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md)):

| Bedarf | Werkzeug | Eingerichtet von |
|---|---|---|
| Eine Seite mit einem Feiertagshinweis erscheint zu einem Datum | das Workflow-Ereignis *Bis Datum warten* beim Veröffentlichen | Administrator |
| Ein Hinweis verschwindet nach dem Datum | der Cronjob `hide.php` mit einem Datumsfeld in der Klasse | Administrator |
| Eine saisonale Startseite (Sommerkarte, Angebote im Dezember) für einen Zeitraum | eine Layout-Regel mit einer Zeitbedingung | Layout-Editor ([Layout-Regeln](../../ersteller/website-ersteller/layout-regeln.md)) |

All das hängt davon ab, dass auf dem Server Cronjobs laufen. Prüfen Sie am Tag selbst, ob es funktioniert hat; wie,
steht in [Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md).

Die regulären Öffnungszeiten in einer Matrix oder einem Textfeld zu einem Datum zu ändern, ist **nicht**
automatisiert: Öffnen Sie das Objekt am Tag selbst und bearbeiten Sie es, oder bereiten Sie einen Entwurf vor und
veröffentlichen Sie ihn dann.

## Ein Hinweis, der auffällt

Ein Feiertagshinweis sollte gesehen werden, bevor Besucher die regulären Öffnungszeiten lesen:

- oben auf der Startseite (ein Banner, ein Hinweisblock im Layout-Editor oder das Hinweisfeld in der Kopfzeile);
- neben den regulären Öffnungszeiten auf der Kontaktseite;
- in einem Newsletter, falls Sie einen versenden ([Newsletter für lokale Unternehmen](../marketing/newsletter-fuer-lokale-unternehmen.md)).

Schreiben Sie den Hinweis in jeder Sprache Ihrer Website ([Mehrsprachig für Urlaubsregionen](../marketing/mehrsprachig-fuer-urlaubsregionen.md)).

## Vergessen Sie die Plattformen nicht

Karten- und Suchplattformen zeigen ihre eigene Kopie Ihrer Öffnungszeiten. Tragen Sie auch dort Sonderöffnungszeiten
ein, am besten am selben Tag, an dem Sie den Hinweis auf der Website vorbereiten ([Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)).

## Ein Jahresplaner

Füllen Sie ihn jeden Januar aus, und legen Sie ihn neben die Kasse:

| Datum oder Zeitraum | Was sich ändert | Hinweistext fertig | Website erledigt | Plattformen erledigt | Hinweis entfernt |
|---|---|---|---|---|---|
| | | [ ] | [ ] | [ ] | [ ] |
| | | [ ] | [ ] | [ ] | [ ] |
| | | [ ] | [ ] | [ ] | [ ] |
| | | [ ] | [ ] | [ ] | [ ] |

## Verwandte Seiten

- [Saisonale Änderungen](../im-betrieb/saisonale-aenderungen.md)
- [Inhaltsvorlage für Öffnungszeiten](../vorlagen/inhaltsvorlage-fuer-oeffnungszeiten.md)
- [Inhaltskalender](../../kunden/alltag/redaktionskalender.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
