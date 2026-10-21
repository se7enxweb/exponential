---
title: Barrierefreie Informationen zu Veranstaltungen
audience: government, website-creator, editor, designer
category: public-sector
order: 80
summary: Was jede öffentliche Veranstaltungsseite als Text sagen muss, damit alle teilnehmen können - Datum und Uhrzeit, Ort und Anfahrt, Barrierefreiheit des Veranstaltungsorts, angebotene Unterstützung, wie man fragt - mit Praktiken für Templates und Redakteure und den zugrunde liegenden WCAG-Kriterien.
keywords: barrierefreie Veranstaltungen, Informationen zu Veranstaltungen, Barrierefreiheit des Veranstaltungsorts, stufenloser Zugang, Induktionsschleife, Gebärdensprache, Untertitel, Datumsformat, Plakate, Alternativtext, einfache Sprache, accessible events, event information, venue accessibility, step-free access, hearing loop, sign language, captions, date format, time element, posters, alternative text, plain language
type: help
---
# Barrierefreie Informationen zu Veranstaltungen

Diese Seite richtet sich an Site-Ersteller, Designer und Redakteure, die öffentliche Veranstaltungen veröffentlichen.
Eine Veranstaltung steht nur dann allen offen, wenn alle herausfinden können, was, wann und wo stattfindet und ob sie
teilnehmen können. Viele Informationen zu Veranstaltungen kommen noch als Plakatbild; diese Seite legt fest, was als
Text auf der Seite stehen muss. Der Stand der Barrierefreiheit von Exponential selbst steht in
[Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md): Die öffentlichen Templates müssen Sie selbst
testen.

## Inhalt

1. [Was jede Veranstaltungsseite sagt](#was-jede-veranstaltungsseite-sagt)
2. [Veranstaltungsort und Unterstützung](#veranstaltungsort-und-unterstützung)
3. [Datum und Uhrzeit](#datum-und-uhrzeit)
4. [Plakate, Bilder und Videos](#plakate-bilder-und-videos)
5. [Checkliste für Redakteure](#checkliste-für-redakteure)

## Was jede Veranstaltungsseite sagt

| Angabe | Wo in der Klasse |
|---|---|
| Was es ist, in einem oder zwei einfachen Sätzen | der Anfang von **Text** |
| Datum, Beginn und Ende | **From Time**, **To Time** |
| Ort, mit Adresse | eine Beziehung zu einem Ort oder Text |
| Kosten und ob eine Buchung nötig ist | Cost, Booking |
| Für wen sie ist | Audience |
| Barrierefreiheit des Veranstaltungsorts und der Veranstaltung | Accessibility at the event |
| Wie man eine Frage stellt oder Unterstützung anfordert | ein Ansprechpartner |

## Veranstaltungsort und Unterstützung

| Thema | Schreiben Sie |
|---|---|
| Hineinkommen | stufenloser Eingang oder nicht, wo; Aufzüge; Entfernungen |
| Hören | Induktionsschleife, Untertitel, Gebärdensprachdolmetschen (angeboten oder auf Anfrage, bis wann) |
| Sehen | Material in Großdruck, Blindenführhunde willkommen |
| Andere Bedürfnisse | Ruheraum, Sitzplätze, barrierefreie Toiletten |
| Anfahrt | öffentlicher Verkehr, barrierefreie Parkplätze |

Sagen Sie, was es gibt, nicht nur „barrierefrei“; Menschen brauchen Details, um zu entscheiden.

## Datum und Uhrzeit

| Praxis | Warum |
|---|---|
| Schreiben Sie Daten im Text vollständig: „Dienstag, 14. April 2026, 18:00 bis 20:00 Uhr“ | eindeutig für alle und für jeden Screenreader |
| Zeichnen Sie sie im Template mit `<time datetime="...">` aus | Maschinen lesen den genauen Wert |
| Nennen Sie die Zeitzone, wenn Besucher anderswo sein können | Online-Veranstaltungen |
| Verlassen Sie sich nicht allein auf ein Kalenderraster | Raster sind mit assistiven Technologien schwer zu benutzen ([Kalenderansichten](kalenderansichten.md#barrierefreiheit-von-kalendern)) |

## Plakate, Bilder und Videos

| Element | Praxis |
|---|---|
| Ein Plakatbild | nie die einzige Informationsquelle; dieselben Fakten als Text; der Alternativtext fasst den Zweck des Plakats zusammen |
| Fotos | Alternativtext erforderlich ([Inhaltsmodell und Bearbeitung, 9](../../../../anleitungen/inhaltsmodell-und-bearbeitung.md#9-pflicht-alternativtext-gekürzte-textzeilen-und-von-php-erzeugte-namen)) |
| Videoeinladungen oder Aufzeichnungen | Untertitel und ein Transkript |
| Programme als PDF | das Programm auch auf der Seite |

Einschlägige Kriterien der [WCAG 2.2](https://www.w3.org/TR/WCAG22/): 1.1.1 Nicht-Text-Inhalt, 1.2.2 Untertitel
(aufgezeichnet), 1.3.1 Info und Beziehungen, 2.4.4 Linkzweck, 3.1.5 Leseniveau (AAA, als Orientierung nützlich).

## Checkliste für Redakteure

- [ ] Datum, Uhrzeit und Ort stehen im Text, nicht nur auf dem Plakat.
- [ ] Der Abschnitt zur Barrierefreiheit sagt, was verfügbar ist und wie man Unterstützung anfordert.
- [ ] Jedes Bild hat einen Alternativtext; Videos haben Untertitel.
- [ ] Links sagen, wohin sie führen („Einen Platz für <Veranstaltung> buchen“).
- [ ] Einfache Sprache: kurze Sätze, keine unerklärten Abkürzungen.

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Barrierefreie Formulare](../formulare/barrierefreie-formulare.md)
- [Barrierefreiheit für Site-Ersteller](../../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
