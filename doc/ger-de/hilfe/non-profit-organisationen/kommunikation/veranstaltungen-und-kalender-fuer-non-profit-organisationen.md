---
title: Veranstaltungen und Kalender für Non-Profit-Organisationen
audience: non-profit, businesses, editor, website-creator
category: non-profits
order: 30
summary: Was eine Non-Profit-Organisation mit Veranstaltungen in Exponential tun kann - die Klassen event, event_calendar und multicalendar aus den ezwebin-Paketen, Kalender- und Programmansichten, öffentliche Veranstaltungen und solche nur für Ehrenamtliche, Anmeldung mit einem Formular des Informationssammlers, Karten mit ezgmaplocation, Ankündigungen, kostenpflichtige Tickets über den Shop - und die klare Liste dessen, was nicht eingebaut ist (Ticketing mit Platzbegrenzung, iCal-Export, wiederkehrende Veranstaltungen, Erinnerungen).
keywords: Veranstaltungen, Veranstaltungskalender, Veranstaltungsklasse, Kalenderansicht, Programmansicht, Anmeldeformular, Benefizveranstaltung, Veranstaltungen für Ehrenamtliche, Karte, Tickets, wiederkehrende Veranstaltungen, events, event calendar, multicalendar, ezwebin, event class, from_time, to_time, calendar view, program view, sign-up form, fundraising event, volunteers-only events, ezgmaplocation, map, tickets, iCal, recurring events
type: help
---
# Veranstaltungen und Kalender für Non-Profit-Organisationen

Diese Seite richtet sich an Organisatoren von Benefizveranstaltungen, Aktionstagen für Ehrenamtliche, Workshops und
Gemeindetreffen. Die Klassen wurden im Repository geprüft: `ezwebin` liefert Installationspakete für `event`,
`event_calendar` und `multicalendar` mit (`extension/ezwebin/packages/`). Die vollständige Beschreibung ist
[Veranstaltungen und Kalender](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md) im Bereich für Unternehmen; diese
Seite wendet sie auf eine Non-Profit-Organisation an.

## Inhalt

1. [Die Veranstaltungsklassen](#die-veranstaltungsklassen)
2. [Kalender oder Programm](#kalender-oder-programm)
3. [Öffentliche Veranstaltungen und solche nur für Ehrenamtliche](#öffentliche-veranstaltungen-und-solche-nur-für-ehrenamtliche)
4. [Anmeldung](#anmeldung)
5. [Ort und Karten](#ort-und-karten)
6. [Kostenpflichtige Benefizveranstaltungen](#kostenpflichtige-benefizveranstaltungen)
7. [Veranstaltungen ankündigen](#veranstaltungen-ankündigen)
8. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
9. [Checkliste](#checkliste)

## Die Veranstaltungsklassen

| Klasse | Was sie ist |
|---|---|
| `event` | Eine Veranstaltung: vollständiger und kurzer Titel, Text, Kategorie, Beginn (Pflichtfeld), Ende, Metadaten |
| `event_calendar` | Ein Ordner für Veranstaltungen mit einer Auswahl **View** |
| `multicalendar` | Zeigt mehrere Kalender in einem an |

Quelle: [Veranstaltungen und Kalender, Die Veranstaltungsklassen](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md#die-veranstaltungsklassen).

## Kalender oder Programm

Die Vollansicht des Veranstaltungskalenders zeigt je nach seinem Feld **View** entweder einen Monatskalender oder eine
Programmliste. Ein Kalender passt zu vielen kleinen Veranstaltungen (Aktionstage für Ehrenamtliche), ein Programm zu
wenigen großen (eine jährliche Gala, eine Konferenz). Die Kalenderansicht wird 400 Sekunden lang gecacht, daher kann es
einige Minuten dauern, bis eine neue Veranstaltung erscheint (dieselbe Quelle).

## Öffentliche Veranstaltungen und solche nur für Ehrenamtliche

- Öffentliche Veranstaltungen kommen in einen öffentlichen Kalender.
- Veranstaltungen nur für Ehrenamtliche kommen in einen Kalender im Bereich der Ehrenamtlichen, der nur für die Rolle
  der Ehrenamtlichen sichtbar ist ([Ein Mitgliederbereich für Ehrenamtliche](../ehrenamtliche/mitgliederbereich-fuer-ehrenamtliche.md)).
- Ein `multicalendar` kann angemeldeten Ehrenamtlichen beides zeigen.

## Anmeldung

Die Veranstaltungsklasse hat keine Anmeldefunktion. Setzen Sie unter oder neben die Veranstaltung ein
Anmeldeformular-Objekt, das mit dem Informationssammler gebaut ist ([Anmeldeformular für Ehrenamtliche, Schritt für Schritt](../ehrenamtliche/anmeldeformular-fuer-ehrenamtliche-schritt-fuer-schritt.md)
zeigt das Vorgehen). Fragen Sie nur ab, was die Veranstaltung braucht; löschen Sie die Einsendungen nach der
Veranstaltung ([Bewerbungen von Ehrenamtlichen bearbeiten](../ehrenamtliche/bewerbungen-von-ehrenamtlichen-bearbeiten.md#ein-aufbewahrungsplan)).

## Ort und Karten

Der Datentyp `ezgmaplocation` speichert Breiten- und Längengrad; der Redakteur findet den Punkt, indem er unter einer
Google-Karte eine Adresse eingibt. Er braucht einen API-Schlüssel für Google Maps; bei leerem Schlüssel wird kein
Google-Skript geladen, und die Koordinaten werden ohne Karte angezeigt ([ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md)).
Er ist kein Attribut der mitgelieferten Klasse `event`; ein Website-Ersteller fügt ihn hinzu, oder Sie schreiben die
Adresse als Text. Eine Karte lädt das Skript eines Drittanbieters: Erwähnen Sie das in der Datenschutzerklärung
([Karten und Einbettungen](../../verwaltung/datenschutz/karten-und-einbettungen.md)).

## Kostenpflichtige Benefizveranstaltungen

Tickets mit Preis lassen sich im eingebauten Shop als Produkt pro Veranstaltung verkaufen
([Spendenartikel im Shop, Schritt für Schritt](../spenden/spendenartikel-im-shop-schritt-fuer-schritt.md) zeigt dieselben
Schritte) oder über einen externen Ticketdienst, der von der Veranstaltungsseite aus verlinkt ist.

## Veranstaltungen ankündigen

| Kanal | Wie |
|---|---|
| Startseite | Ein Layout-Block, der kommende Veranstaltungen auflistet ([Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md)) |
| Newsletter | Eine Ausgabe lädt ein ([Unterstützer-Newsletter](unterstuetzer-newsletter-mit-cjw-newsletter.md)) |
| Feed | Ein RSS-Feed des Kalenders ([Offene Daten, RSS und REST](../transparenz/offene-daten-rss-und-rest.md)) |
| Soziale Medien | Teilen Sie die Veranstaltungsseite; ihre Metadaten liefern die Vorschau |

## Was nicht eingebaut ist

Im Repository gesucht (siehe auch [Veranstaltungen und Kalender, Was nicht eingebaut ist](../../unternehmen/mitglieder/veranstaltungen-und-kalender.md#was-nicht-eingebaut-ist)):

| Nicht eingebaut | Planen Sie stattdessen |
|---|---|
| Anmeldung mit Platzbegrenzung, Wartelisten, Tickets | Eine Erweiterung oder einen externen Ticketdienst |
| Kalenderdateien (iCal) für die Kalender-Apps der Besucher | Eine Erweiterung; ein solcher Export wurde im Code nicht gefunden |
| Wiederkehrende Veranstaltungen | Ein Veranstaltungsobjekt pro Termin |
| Automatische Erinnerungen | Eine Newsletter-Ausgabe oder eine E-Mail aus dem Büro |

## Checkliste

- [ ] Veranstaltungskalender installiert und seine Ansicht gewählt.
- [ ] Veranstaltungsseiten mit Datum, Uhrzeit, Ort und Angaben zur Barrierefreiheit (stufenloser Zugang, Untertitel,
  Kontakt).
- [ ] Anmeldeformular, falls nötig, mit Löschdatum.
- [ ] Karte nur, wenn die Datenschutzerklärung sie abdeckt.
- [ ] Auf der Startseite, im Newsletter und im Feed angekündigt.
- [ ] Nach der Veranstaltung: Ergebnisse oder Fotos (mit Erlaubnis) veröffentlicht; alte Anmeldungen gelöscht.

## Verwandte Seiten

- [Kommunikation für Non-Profit-Organisationen](README.md)
- [Veranstaltungen und Nachrichten für Gruppen](../../unternehmen/vereine/veranstaltungen-und-neuigkeiten-fuer-gruppen.md)
- [ezwebin](../../../funktionen/6.0/erweiterungen/ezwebin.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
