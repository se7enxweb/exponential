---
title: Monatliche Checkliste für Non-Profit-Organisationen
audience: non-profit, businesses, admin, editor
category: non-profits
order: 30
summary: Die monatliche Routine für die Website einer Non-Profit-Organisation mit Exponential in etwa einer Stunde - Sicherungen und Updates, Cron und Logs, der Spendenlink und die Spenden, Formulare und Spam, Newsletter, Konten ausgeschiedener Personen, Aktualität der Inhalte, Zahlen für Geldgeber und eine kurze Notiz über das Erledigte.
keywords: monatliche Checkliste, Routine, Sicherungen, Updates, Cron, Logs, Spendenlink, Spenden, Formulare, Spam, Newsletter, Konten, Aktualität der Inhalte, Zahlen, Non-Profit, Betrieb, monthly checklist, backups, donate link, donations, forms, accounts, content freshness, figures, operations
type: help
---
# Monatliche Checkliste für Non-Profit-Organisationen

Gehen Sie diese Liste in der ersten Woche jedes Monats durch. Für eine kleine Website dauert sie etwa eine Stunde,
sobald Sie damit vertraut sind (eine Schätzung, kein gemessener Wert). Tragen Sie neben jedem Abschnitt einen Namen ein;
mehrere Personen können sich die Arbeit teilen.

## Inhalt

1. [Technischer Zustand](#technischer-zustand)
2. [Geld](#geld)
3. [Formulare und Personen](#formulare-und-personen)
4. [Kommunikation](#kommunikation)
5. [Inhalte](#inhalte)
6. [Zahlen](#zahlen)
7. [Notiz des Monats](#notiz-des-monats)

## Technischer Zustand

- [ ] Die letzte Sicherung existiert und ist aktuell ([Routine für Sicherungen und Updates](../betrieb/routine-fuer-sicherungen-und-updates.md)).
- [ ] Neue Releases von Exponential oder Erweiterungen geprüft; Updates geplant oder eingespielt ([Wie Updates funktionieren](../../kunden/gesund-halten/wie-updates-funktionieren.md)).
- [ ] Cron ist gelaufen: Geplante Seiten sind erschienen, der Newsletter wurde versendet, das Audit-Protokoll rotiert ([Cronjobs](../../../anleitungen/cronjobs.md)).
- [ ] Logs auf wiederholte Fehler durchgesehen ([Log-Betrachter](../../../funktionen/6.0/log-betrachter.md)).
- [ ] HTTPS-Zertifikat noch länger als einen Monat gültig.

## Geld

- [ ] Der Spendenlink führt dorthin, wohin er soll (abgemeldet öffnen).
- [ ] Spenden auf der Plattform oder bezahlte Shop-Bestellungen stimmen mit der Abrechnung des Anbieters überein ([Vertrauen der Spender und Dank](../spenden/vertrauen-der-spender-und-dank.md)).
- [ ] Bestellungen, die im Status „Zahlung fehlgeschlagen“ hängen, angesehen, falls Sie den Shop verwenden.

## Formulare und Personen

- [ ] Bewerbungen von Ehrenamtlichen und Kontaktanfragen beantwortet ([Bewerbungen von Ehrenamtlichen bearbeiten](../ehrenamtliche/bewerbungen-von-ehrenamtlichen-bearbeiten.md)).
- [ ] Spam gesammelt entfernt.
- [ ] Einsendungen, die älter als Ihre Aufbewahrungsfrist sind, entfernt.
- [ ] In diesem Monat erstellte CSV-Exporte gelöscht.
- [ ] Konten ausgeschiedener Personen deaktiviert ([Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md)).

## Kommunikation

- [ ] Newsletter-Ausgabe versendet; Bounces angesehen ([Unterstützer-Newsletter](../kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md)).
- [ ] Anstehende Veranstaltungen online; vergangene Veranstaltungen aufgeräumt ([Veranstaltungen und Kalender](../kommunikation/veranstaltungen-und-kalender-fuer-non-profit-organisationen.md)).
- [ ] Kampagnen-Layouts, die enden sollten, sind beendet ([Kampagnen und Spendenaufrufe](../kommunikation/kampagnen-und-spendenaufrufe.md)).

## Inhalte

- [ ] Startseite aktuell: nichts älter, als Sie akzeptieren würden.
- [ ] Eine wichtige Seite mit der Tastatur getestet ([Grundlagen der Barrierefreiheit](../barrierefreiheit/barrierefreiheit-grundlagen-fuer-non-profit-organisationen.md)).
- [ ] Defekte Links geprüft ([Link- und Inhaltsprüfungen](../../ersteller/website-ersteller/link-und-inhaltspruefungen.md)).
- [ ] Übersetzungen der wichtigsten Seiten auf dem gleichen Stand ([Mehrsprachige Non-Profit-Websites](../kommunikation/mehrsprachige-non-profit-websites.md)).

## Zahlen

Halten Sie diese Zahlen für Vorstand und Geldgeber fest ([Wirkung mit Website-Daten messen](../foerdermittel/wirkung-mit-website-daten-messen.md)):

| Zahl | Dieser Monat |
|---|---|
| Bewerbungen von Ehrenamtlichen | |
| Kontakt- oder Hilfeanfragen | |
| Bestätigte Newsletter-Abonnenten | |
| Spenden (Anzahl, laut Anbieter) | |
| Seitenaufrufe der wichtigsten Seiten | |

## Notiz des Monats

```text
Month ....................... ________
Done by ..................... ________
What was done ............... ______________________
Problems and follow-up ...... ______________________
```

Vergleichen Sie mit der allgemeinen [Monatlichen Checkliste für den Betrieb](../../unternehmen/vorlagen/monatliche-betriebscheckliste.md)
und der [Monatlichen Checkliste](../../kunden/nutzen/checklisten/monatlich.md) für Kunden.

## Verwandte Seiten

- [Checklisten für Non-Profit-Organisationen](README.md)
- [Jährliche Checkliste](jaehrliche-checkliste.md)
- [Wöchentliche Routine des Eigentümers](../../kunden/alltag/woechentliche-routine-des-betreibers.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
