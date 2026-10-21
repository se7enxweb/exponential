---
title: Formularantworten als CSV exportieren
audience: government, editor
category: public-sector
order: 510
summary: Für Redakteure, die Formulare bearbeiten - die Einsendungen eines Formulars unter Setup > Gesammelte Informationen als CSV-Datei herunterladen, sie richtig öffnen und die Datei als die personenbezogenen Daten behandeln, die sie sind.
keywords: CSV exportieren, Formularantworten, Gesammelte Informationen, infocollector, Einsendungen, Tabellenkalkulation, UTF-8, audit data.export.csv, personenbezogene Daten, Antworten auf Beteiligungsverfahren, export CSV, form responses, collected information, submissions, spreadsheet, personal data, consultation responses
type: help
---
# Formularantworten als CSV exportieren

Diese Seite ist für Redakteure, die bearbeiten, was Besucher einsenden: Antworten auf Beteiligungsverfahren, Meldungen,
Anmeldungen zu Veranstaltungen. Die Einsendungen jedes Formulars lassen sich als CSV-Datei für eine
Tabellenkalkulation herunterladen ([Gesammelte Informationen, 2](../../../../anleitungen/gesammelte-informationen.md#2-sammlungen-lesen-und-exportieren)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Ihre Rolle hat `infocollector/read` (alles unter **Setup > Gesammelte Informationen** braucht es).
- Sie haben einen Ort für die Datei, an den personenbezogene Daten gehören (keinen privaten Laptop und kein
  öffentlich freigegebenes Laufwerk).

## Schritte

1. **Setup > Gesammelte Informationen** (`/infocollector/overview`).
2. Finden Sie das Formular: **Objekt finden** mit einem Teil seines Namens, oder sortieren Sie die Liste.
3. Klicken Sie auf seiner Karte auf **CSV exportieren**. Die Datei `<name>-collected.csv` wird heruntergeladen.
4. Öffnen Sie sie in Ihrer Tabellenkalkulation. Sie ist in UTF-8 mit Byte Order Mark gespeichert, sodass Umlaute und
   Akzente richtig erscheinen.

Die Spalten: die ID der Sammlung, die Zeiten des Sendens und der Änderung (UTC), die ID des Benutzers, der gesendet
hat (der anonyme Benutzer bei Besuchern), dann eine Spalte je Formularfeld in der Reihenfolge der Klasse. Werte, die
eine Tabellenkalkulation als Formel lesen würde, beginnen mit einem Apostroph. Höchstens 50 000 Sammlungen, die
neuesten zuerst.

## Das Ergebnis prüfen

- [ ] Die Zahl der Zeilen entspricht der Zahl der Sammlungen auf der Karte.
- [ ] Die Zeiten sind in UTC; rechnen Sie sie um, wenn Sie Ortszeiten berichten.
- [ ] Der Export ist im Audit als `data.export.csv` mit der Zahl der Zeilen aufgezeichnet (niemals mit den Werten).

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| Die CSV-Datei herummailen | bewahren Sie sie an einem Ort auf; teilen Sie den Zugang, nicht Kopien |
| Exporte aufbewahren, wenn die Arbeit erledigt ist | löschen Sie sie nach demselben Plan wie die Sammlungen ([Nicht mehr benötigte Formularantworten entfernen](nicht-mehr-benoetigte-formularantworten-entfernen.md)) |
| Eine Spalte ist leer oder fehlt | das Feld wurde erst nach diesen Antworten zum Sammelfeld gemacht oder ist kein Sammelfeld mehr |
| Formeln von Besuchern | der Apostroph schützt Sie; entfernen Sie ihn nicht, bevor Sie geprüft haben |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Formulareinsendungen lesen und exportieren](../../../ersteller/website-ersteller/formulareinsendungen.md)
- [Aufbewahrung gesammelter Informationen](../../aufbauen/formulare/aufbewahrung-gesammelter-informationen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
