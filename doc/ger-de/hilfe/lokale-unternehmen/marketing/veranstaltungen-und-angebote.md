---
title: Veranstaltungen und Angebote
audience: local-business, businesses, customer, editor
category: local-business
order: 61
summary: Veranstaltungen, Tagesangebote und saisonale Angebote auf der Website eines lokalen Unternehmens ankündigen - die Klassen für Veranstaltung und Veranstaltungskalender aus ezwebin, Angebote als Artikel, die zu einem Datum erscheinen und verschwinden, eine saisonale Landingpage mit einer Layout-Regel, Blöcke auf der Startseite und was nicht eingebaut ist (Tickets, iCal, wiederkehrende Veranstaltungen).
keywords: Veranstaltungen, Angebote, Aktionen, Tagesangebot, Veranstaltungskalender, ezwebin, zeitgesteuertes Veröffentlichen, Bis Datum warten, zu einem Datum verstecken, Layout-Regel, Startseite, lokales Unternehmen, events, specials, offers, daily special, event calendar, scheduled publishing, wait until date, layout rule, local business
type: help
---
# Veranstaltungen und Angebote

Diese Seite ist für die Person, die ankündigt, was bei einem lokalen Unternehmen passiert: ein Verkostungsabend, ein
Workshop, das Mittagsangebot, ein Sommerschlussverkauf. Exponential hat Veranstaltungsklassen und datumsabhängiges
Veröffentlichen; diese Seite zeigt, wie ein kleines Unternehmen sie nutzt.

## Inhalt

1. [Veranstaltungen](#veranstaltungen)
2. [Angebote und Aktionen](#angebote-und-aktionen)
3. [Zu einem Datum, automatisch](#zu-einem-datum-automatisch)
4. [Dafür sorgen, dass sie gesehen werden](#dafür-sorgen-dass-sie-gesehen-werden)
5. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
6. [Checkliste](#checkliste)

## Veranstaltungen

Die Seitenpakete von `ezwebin` liefern die Klassen `event`, `event_calendar` und `multicalendar` mit
([Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md#die-veranstaltungsklassen)):

| Klasse | Was sie ist | Wichtige Felder |
|---|---|---|
| Event | eine Veranstaltung | Titel, Kurztitel, Text, Kategorie, Beginn, Ende |
| Event calendar | ein Ordner mit Veranstaltungen, angezeigt als Monatskalender oder Programmliste | Titel, Ansicht (Kalender oder Programm) |
| Multicalendar | eine Seite, die mehrere Kalender zusammenführt | Kalender |

Für ein lokales Unternehmen: ein Veranstaltungskalender namens „Was los ist“, mit den Veranstaltungen darin. Wählen
Sie die Ansicht **Programm**, wenn Sie wenige Veranstaltungen pro Monat haben; die Liste ist auf einem Smartphone
leichter zu lesen als ein Kalenderraster.

Eine Veranstaltung hinzufügen:

1. Öffnen Sie den Veranstaltungskalender, und legen Sie ein **Event** an.
2. Kurztitel („Weinprobe“), vollständiger Titel („Herbstliche Weinprobe mit einem Weingut aus der Region“), der Text
   mit Preis und Anmeldung.
3. Beginn und Ende mit der Datumsauswahl.
4. Veröffentlichen Sie. Die Kalenderansicht aktualisiert sich innerhalb weniger Minuten (sie wird zwischengespeichert).

Anmeldungen: ein Formular des Informationssammlers auf der Veranstaltungsseite oder ein Link zu einem Ticketdienst
([Angebots- und Anfrageformulare](../kontakt-und-formulare/angebots-und-anfrageformulare.md)).

## Angebote und Aktionen

Angebote sind kurzlebig, halten Sie sie also einfach:

- Ein Ordner „Angebote“ mit einem **Artikel** pro Angebot: Titel, kurzer Text, Foto, Preis, „gültig bis“.
- Oder eine einzige Seite „Diese Woche“, die Sie jede Woche bearbeiten. Ältere Versionen bleiben im Versionsverlauf.
- Für das Tagesgericht eines Restaurants ist ein Artikel, den Sie jeden Morgen bearbeiten, die geringste Arbeit.

## Zu einem Datum, automatisch

Wenn Ihr Administrator sie eingerichtet hat ([Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md)):

| Gewünscht | Werkzeug |
|---|---|
| Das Weihnachtsangebot erscheint am 1. Dezember | das Workflow-Ereignis *Bis Datum warten*: jetzt vorbereiten und veröffentlichen, es geht zum Datum online |
| Das Angebot verschwindet nach dem 24. Dezember | der Cronjob `hide.php` mit einem Datumsfeld in der Klasse |
| Eine saisonale Startseite für einen Zeitraum | eine Layout-Regel mit einer Zeitbedingung ([Layout-Regeln](../../ersteller/website-ersteller/layout-regeln.md)) |

Prüfen Sie am Tag selbst, ob es geklappt hat; alle drei hängen davon ab, dass auf dem Server Cronjobs laufen.

## Dafür sorgen, dass sie gesehen werden

- Ein Block „Was los ist“ oder „Diese Woche“ auf der Startseite, gebaut im Layout-Editor oder mit `ezflow`
  ([Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md)).
- Eine Zeile im nächsten Newsletter ([Newsletter für lokale Unternehmen](newsletter-fuer-lokale-unternehmen.md)).
- Ein RSS-Feed des Kalenders für Menschen und Websites, die Feeds folgen
  ([RSS-Feeds für Ihre Website](../../ersteller/website-ersteller/rss-feeds-fuer-ihre-website.md)).
- Ein gutes Vorschaubild, damit die Veranstaltung beim Teilen in Chats richtig aussieht
  ([Titel und Beschreibungen mit xrowmetadata](../gefunden-werden/titel-und-beschreibungen-mit-xrowmetadata.md#das-vorschaubild-zum-teilen)).
- Die Veranstaltung in Ihrem Unternehmenseintrag, falls die Plattform das unterstützt (außerhalb von Exponential).

## Was nicht eingebaut ist

Aus [Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md#was-nicht-eingebaut-ist):

| Nicht eingebaut | Stattdessen |
|---|---|
| Tickets mit Platzbegrenzung und Wartelisten | ein Ticketdienst, von der Veranstaltung aus verlinkt |
| Bezahlte Tickets | ein Produkt pro Veranstaltung im Shop oder ein Ticketdienst |
| iCal-Dateien für die Kalender der Besucher | eine Erweiterung; keine gefunden |
| Wiederkehrende Veranstaltungen | ein Veranstaltungsobjekt pro Termin |
| Automatische Erinnerungen | eine Newsletter-Ausgabe |

## Checkliste

- [ ] Ein Veranstaltungskalender, Ansicht Programm, wenn es wenige Veranstaltungen gibt.
- [ ] Jede Veranstaltung nennt Datum, Uhrzeit, Preis und wie man sich anmeldet.
- [ ] Angebote haben ein klares Enddatum und werden danach versteckt.
- [ ] Datumsabhängiges Veröffentlichen einmal mit einem Testangebot geprüft.
- [ ] Die Startseite zeigt, was los ist.

## Verwandte Seiten

- [Saisonale Änderungen](../im-betrieb/saisonale-aenderungen.md)
- [Vorlage für einen Inhaltskalender](../../kunden/alltag/vorlage-redaktionskalender.md)
- [Restaurantbestellungen und was nicht eingebaut ist](../einfacher-shop/restaurantbestellungen-und-was-nicht-eingebaut-ist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
