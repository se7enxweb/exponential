---
title: Was eine öffentliche Bibliothek veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die öffentliche Bibliotheken typischerweise veröffentlichen - Zweigstellen und Öffnungszeiten, Veranstaltungen, Dienste, digitale Angebote, Leseempfehlungen, Nachrichten - und was jede Art von Exponential braucht.
keywords: Website einer Bibliothek, Zweigstellen, Öffnungszeiten, Veranstaltungen, Vorlesestunde, Lesekreis, Bibliotheksausweis, digitale Angebote, E-Books, Empfehlungen, Umfang, library website, branches, opening hours, events, story time, reading group, library card, digital resources, e-books, recommendations, scope
type: help
---
# Was eine öffentliche Bibliothek veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller einer öffentlichen Bibliothek oder eines
Bibliotheksverbunds, die den Umfang einer Website festlegen. Sie listet auf, was Bibliotheken typischerweise
veröffentlichen (allgemeine Praxis im öffentlichen Sektor), und was jede Art von Inhalt braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Was die Website nicht tun sollte](#was-die-website-nicht-tun-sollte)
4. [Zu klärende Fragen](#zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Rhythmus |
|---|---|---|
| Zweigstellen: Anschrift, Öffnungszeiten, Schließungen an Feiertagen, Ausstattung, Barrierefreiheit | alle | ändert sich einige Male im Jahr; Schließungen kurzfristig |
| Veranstaltungen: Vorlesestunden, Lesekreise, Autorenlesungen, Kurse, Ausstellungen | Familien, Lesende, Lernende | wöchentlich; viele kleine Einträge |
| Dienste: Anmeldung, Ausleihregeln, Gebühren, Drucken, Raumbuchung, Medienbringdienst | Mitglieder | stabil |
| Digitale Angebote: E-Books, Datenbanken, Streaming, mit Anleitung zur Anmeldung | Mitglieder | Links zu Anbietern |
| Empfehlungen: Leselisten, Tipps des Teams, Themen | Lesende | monatlich |
| Lernen: Hilfe am Computer, Sprachcafés, Hausaufgabenhilfe | Lernende | je Halbjahr |
| Nachrichten: neue Dienste, Bauarbeiten | alle | gelegentlich |
| Über die Bibliothek: Trägerschaft, Pläne, Jahresbericht, Ehrenamt, Stellen | Beteiligte | jährlich |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Ein Veranstaltungskalender mit Monatsansicht und Programmliste | die Klassen Event und Event calendar; Kalender- und Programmansicht im Design ezwebin | [Veranstaltungen und Kataloge](veranstaltungen-und-verlinkte-kataloge.md) |
| Beschäftigte der Zweigstellen tragen ihre eigenen Veranstaltungen ein | eine Rolle, eingeschränkt auf den Teilbaum oder die Sektion der Zweigstelle | [Struktur, Sektionen und Rollen](struktur-sektionen-und-rollen.md) |
| Öffnungszeiten an einer Stelle | ein Objekt Zweigstelle je Zweigstelle, aus anderen Seiten verknüpft | [Inhaltsmodell](inhaltsmodell.md) |
| Kurzfristige Schließungen | ein Hinweis auf der Seite der Zweigstelle und der Startseite | [Der Layout-Editor](../../../layout-editor.md) |
| Veranstaltungen nach Thema oder Zielgruppe | Tags | [Verschlagworten mit eztags](../../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md) |
| Anmeldung zu Veranstaltungen | der Informationssammler oder das Veranstaltungssystem, das Sie verwenden | [Formulare mit dem Informationssammler](../../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md) |
| Ein Newsletter mit Veranstaltungen | cjw_newsletter | [Newsletter mit CJW Newsletter](../../../../../eng-us/help/creators/website-creators/newsletters-with-cjw-newsletter.md) |
| Mehrere Sprachen | Übersetzungen je Objekt | [Mehrsprachige Websites](../../../../../eng-us/help/creators/website-creators/multilingual-sites.md) |

## Was die Website nicht tun sollte

| Nicht | Stattdessen |
|---|---|
| Den Katalog als Inhaltsobjekte nachbauen | auf die Suche des Katalogs verlinken; höchstens ein Suchfeld einbetten, das die Anfrage dorthin schickt |
| Ausleih- oder Leserdaten im CMS speichern | das Bibliotheksmanagementsystem enthält sie |
| In einem Formular der Website nach der Nummer des Bibliotheksausweises fragen | die Menschen zur eigenen Anmeldung des Katalogs schicken |
| Mit Analysediensten Dritter verfolgen, was Besucher lesen | datenschutzfreundliche Statistik verwenden ([Statistik und Analyse](../../../../../eng-us/help/creators/website-creators/statistics-and-analytics.md)) |

## Zu klärende Fragen

- [ ] Welches System enthält den Katalog, und welche Suchadresse akzeptiert er?
- [ ] Bleibt der Veranstaltungskalender auf der Website, oder speist ihn ein Veranstaltungssystem?
- [ ] Wer veröffentlicht an einem Sonntag eine unerwartete Schließung?
- [ ] Welche Sprachen sprechen die Gemeinschaften der Bibliothek?

## Verwandte Seiten

- [Öffentliche Bibliotheken](README.md)
- [Inhaltsmodell für öffentliche Bibliotheken](inhaltsmodell.md)
- [Ihre Website planen](../../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
