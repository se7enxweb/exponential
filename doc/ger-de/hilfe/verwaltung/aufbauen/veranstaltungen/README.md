---
title: Veranstaltungen
audience: government, website-creator, project-manager, editor
category: public-sector
order: 10
summary: Index der Anleitungen zum Veröffentlichen öffentlicher Veranstaltungen auf einer Exponential-Site mit dem, was wirklich mitgeliefert wird - die Klassen Event, Event calendar und Multicalendar und die Kalender- und Programmansichten von ezwebin - und wie man Veranstaltungen erweitert, auflistet, Anmeldungen annimmt, sie ändert und archiviert.
keywords: Veranstaltungen, Veranstaltungskalender, Kalenderansicht, Programmansicht, kommende Veranstaltungen, Anmeldung, Absage, barrierefreie Veranstaltungen, Archiv, events, event calendar, multicalendar, calendar view, program view, ezwebin, upcoming events, registration, cancellation, accessible events, archive
type: help
---
# Veranstaltungen

Dieser Ordner richtet sich an Site-Ersteller, Projektleiter und Redakteure, die öffentliche Veranstaltungen
veröffentlichen: öffentliche Ratssitzungen, Informationsabende, Beteiligungstermine, Tage der offenen Tür,
Veranstaltungen für die Gemeinschaft. Er beginnt mit dem, was Exponential wirklich mitliefert (drei Inhaltsklassen und
zwei Kalenderansichten im Design ezwebin), und zeigt, wie man bei Bedarf darauf aufbaut. Nichts hier setzt ein
Buchungssystem für Veranstaltungen voraus: Exponential enthält keines.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Was mitgeliefert wird und was nicht](#was-mitgeliefert-wird-und-was-nicht)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Was mitgeliefert wird](was-fuer-veranstaltungen-mitgeliefert-wird.md) | Event, Event calendar und Multicalendar, ihre Attribute, die Kalender- und Programmansichten und woher sie kommen |
| [Eine Klasse für Veranstaltungen modellieren](eine-klasse-fuer-veranstaltungen-modellieren.md) | Attribute, die für öffentliche Veranstaltungen hinzukommen (Ort, Barrierefreiheit, Zielgruppe, Buchungsstatus), und wie man sie sicher hinzufügt |
| [Kalenderansichten](kalenderansichten.md) | Das Monatsraster und die Programmliste von ezwebin, ihre Adressen, Caching, und was bei einem anderen Design zu tun ist |
| [Kommende Veranstaltungen auflisten](kommende-veranstaltungen-auflisten.md) | Template-Fetches für Listen „nächste Veranstaltungen“, nach Ort oder Schlagwort, mit Ablauf des Caches |
| [Anmeldungen zu Veranstaltungen](anmeldungen-zu-veranstaltungen.md) | Ein Anmeldeformular mit dem Information Collector, seine Grenzen, und wann ein Buchungssystem verlinkt werden sollte |
| [Absagen und Änderungen](absagen-und-aenderungen.md) | Zeit oder Ort ändern, absagen, Menschen informieren, und die in den Versionen bewahrte Historie |
| [Barrierefreie Informationen zu Veranstaltungen](barrierefreie-informationen-zu-veranstaltungen.md) | Was jede Veranstaltungsseite als Text sagen muss, Barrierefreiheit des Veranstaltungsorts, Daten für Screenreader |
| [Vergangene Veranstaltungen und das Archiv](vergangene-veranstaltungen-und-das-archiv.md) | Vergangene Veranstaltungen verstecken oder behalten, Archive nach Jahr, Anmeldedaten entfernen |

## Lesereihenfolge

1. **Was mitgeliefert wird**, dann **Kalenderansichten**.
2. **Eine Klasse für Veranstaltungen modellieren**, wenn die mitgelieferten Felder nicht genügen.
3. **Kommende Veranstaltungen auflisten** für Start- und Themenseiten.
4. **Anmeldungen zu Veranstaltungen**, **Absagen und Änderungen**, **Barrierefreie Informationen zu Veranstaltungen**.
5. **Vergangene Veranstaltungen und das Archiv**.

Nach oben: [Eine Site für den öffentlichen Sektor aufbauen](../README.md), der Index aller Anleitungen zum Aufbauen,
und [Behörden: hier beginnen](../../verwaltungen-hier-beginnen.md), der Anfang des Bereichs für Behörden. Bibliotheken haben
eine eigene Seite zu Veranstaltungen:
[Veranstaltungen und Kataloge verlinken statt bauen](../../../../../eng-us/help/government/levels/public-libraries/events-and-linked-catalogs.md).

## Was mitgeliefert wird und was nicht

| Mitgeliefert | Nicht mitgeliefert |
|---|---|
| Die Klassen Event (`event`), Event calendar (`event_calendar`) und Multicalendar (`multicalendar`) in `sevenx_classes` | ein Buchungssystem mit Plätzen, Wartelisten und Zahlungen |
| `event_view_calendar.tpl` und `event_view_program.tpl` von ezwebin | Kalender-Templates im Standarddesign `simple` |
| Attribute `ezdatetime` und Fetches mit Attributfiltern | Export von Veranstaltungen als iCalendar (`.ics`) |
| RSS-Exporte eines Kalenderordners | wiederkehrende Veranstaltungen (jeder Termin ist ein eigenes Objekt) |

## Verwandte Seiten

- [Nachrichten und Warnmeldungen](../../../../../eng-us/help/government/building/news-and-alerts/README.md)
- [Formulare aufbauen](../formulare/README.md)
- [sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
