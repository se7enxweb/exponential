---
title: Was ein Rettungsdienst veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Feuerwehren und Rettungsdienste, Katastrophenschutz und Stellen des Krisenmanagements typischerweise veröffentlichen - Informationen zu Einsätzen und Warnungen, Vorsorge, Vorbeugung, Dienstleistungen, Nachwuchsgewinnung, Berichte - und was jede Art von Exponential braucht.
keywords: Website eines Rettungsdienstes, Feuerwehr, Einsätze, Warnungen, Vorsorge, Vorbeugung, Evakuierung, Notunterkünfte, Nachwuchsgewinnung, Ehrenamtliche, Berichte, Umfang, emergency services website, fire service, incidents, warnings, preparedness, prevention, evacuation, shelters, recruitment, volunteers, reports, scope
type: help
---
# Was ein Rettungsdienst veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller einer Feuerwehr, eines Rettungsdienstes, einer
Katastrophenschutzbehörde oder einer Stelle des Krisenmanagements, die den Umfang einer Website festlegen. Sie listet
auf, was solche Stellen typischerweise veröffentlichen (allgemeine Praxis im öffentlichen Sektor), und was jede Art von
Inhalt braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Was die Website nicht ist](#was-die-website-nicht-ist)
4. [Zu klärende Fragen](#zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Rhythmus |
|---|---|---|
| Informationen zu Einsätzen: was geschieht, wo, was zu tun ist, Straßensperrungen, Notunterkünfte | die Öffentlichkeit, Medien | Minuten bis Stunden während Einsätzen |
| Warnungen: Unwetter, Hochwasser, Waldbrandgefahrenstufen | die Öffentlichkeit | wie herausgegeben |
| Vorsorge: Notfallausrüstung, Pläne für Haushalte und Unternehmen, Evakuierungswege | die Öffentlichkeit | stabil, jährlich überprüft |
| Vorbeugung: Brandschutz zu Hause, Rauchmelder, Sicherheitsprüfungen | die Öffentlichkeit, Unternehmen | stabil |
| Dienstleistungen: Begehungen, Genehmigungen, Sicherheitsbesuche | Unternehmen, Einwohnerinnen und Einwohner | stabil |
| Nachwuchsgewinnung: Berufe, Ehrenamtliche, Jugendfeuerwehr | Bewerberinnen und Bewerber | Kampagnen |
| Berichte: Jahresberichte, Einsatzstatistiken, Pläne | Aufsicht, Medien | jährlich |
| Nachrichten und Presse | Medien | häufig |
| Über uns, Wachen, Kontakt (Nummern außerhalb des Notrufs) | alle | gelegentlich |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Eine Einsatzmeldung in einer Minute veröffentlichen, von jedem Gerät | eine kurze Klasse Einsatzmeldung; eine Bereitschaftsrolle ohne Freigabe | [Inhaltsmodell](inhaltsmodell.md), [Struktur](struktur-sektionen-und-rollen.md) |
| Unter Last erreichbar bleiben | Ansichts-Cache, statischer Cache, Antwort-Cache von Velocity | [Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md) |
| Alte Meldungen beenden | `hide.php` mit einer Endzeit | [Cronjobs](../../../../anleitungen/cronjobs.md) |
| Feeds für Medien und Partner | RSS-Export der Einsatzmeldungen | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Meldungen an Folgende | Benachrichtigungen | [Benachrichtigungen: der Leitfaden für Benutzer](../../../../funktionen/6.0/benachrichtigungen.md) |
| Orte | zuerst Text; ein Datentyp für Kartenorte existiert (`ezgmaplocation`, Google Maps, ein Dritter) | [ezgmaplocation](../../../../funktionen/6.0/erweiterungen/ezgmaplocation.md) |
| Mehrere Sprachen | Übersetzungen je Objekt | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |

## Was die Website nicht ist

| Nicht | Verwenden Sie stattdessen |
|---|---|
| Eine Notrufnummer oder ein Weg, einen Notfall zu melden | den Notruf; sagen Sie das auf jeder Seite |
| Ein Warnsystem für die Bevölkerung | nationale Warnsysteme, Rundfunk, Cell Broadcast, wo verfügbar |
| Ein Live-Kartensystem | ein GIS oder einen Kartendienst; verlinken oder einbetten mit einer Textalternative |
| Der einzige Kanal | soziale Medien, Radio, Partner; verlinken Sie sie |

## Zu klärende Fragen

- [ ] Wer veröffentlicht nachts Einsatzinformationen, und wie schnell müssen sie erscheinen?
- [ ] Welchen Verkehr hat der größte vergangene Einsatz gebracht?
- [ ] Wo werden Einsatzinformationen sonst noch veröffentlicht, und welche Quelle ist maßgeblich?
- [ ] Was geschieht mit der Website, wenn der Haupt-Hostingstandort vom Einsatz betroffen ist?

## Verwandte Seiten

- [Rettungsdienste](README.md)
- [Inhaltsmodell für Rettungsdienste](inhaltsmodell.md)
- [Ihre Website planen](../../../ersteller/website-ersteller/ihre-website-planen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
