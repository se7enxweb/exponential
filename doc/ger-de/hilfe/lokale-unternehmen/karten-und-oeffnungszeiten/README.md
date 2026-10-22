---
title: "Karten und Öffnungszeiten: wo und wann Sie geöffnet haben"
audience: local-business, businesses, customer
category: local-business
order: 30
summary: Die Seiten dazu, wie Sie Standort und Öffnungszeiten eines lokalen Unternehmens auf einer Exponential-Website zeigen - der Datentyp ezgmaplocation und der Karten-Block der Layouts, API-Schlüssel und Datenschutz, Öffnungszeiten ohne besonderen Datentyp abbilden, Feiertags- und Saisonöffnungszeiten und mehrere Standorte.
keywords: Karte, Standort, ezgmaplocation, Karten-Block, Google Maps, OpenStreetMap, API-Schlüssel, Öffnungszeiten, Feiertagszeiten, Saisonzeiten, mehrere Standorte, Filialen, map, location, map block, API key, opening hours, holiday hours, seasonal hours, several locations, branches
type: help
---
# Karten und Öffnungszeiten: wo und wann Sie geöffnet haben

Diese Seitengruppe ist für lokale Unternehmen, die wollen, dass Besucher auf einen Blick sehen, **wo** sie sind und
**wann** sie geöffnet haben. Beides sieht einfach aus, und beides hat einen Haken: Karten kommen von einem externen
Anbieter, und Exponential hat keinen Feldtyp für Öffnungszeiten.

## Inhalt

1. [Die Seiten dieser Gruppe](#die-seiten-dieser-gruppe)
2. [Was geprüft wurde](#was-geprüft-wurde)
3. [Die Kurzfassung](#die-kurzfassung)

## Die Seiten dieser Gruppe

| Seite | In einer Zeile |
|---|---|
| [Ihren Standort auf einer Karte zeigen](ihren-standort-auf-einer-karte-zeigen.md) | der Datentyp `ezgmaplocation`, der Karten-Block der Layouts, API-Schlüssel und Datenschutz |
| [Öffnungszeiten ohne besonderen Datentyp](oeffnungszeiten-ohne-besonderen-datentyp.md) | drei Wege, Öffnungszeiten mit dem Mitgelieferten abzubilden |
| [Feiertags- und Saisonöffnungszeiten](feiertags-und-saisonoeffnungszeiten.md) | Ausnahmen von Hand bearbeiten, Hinweise zeitlich planen, Plattformen im Einklang halten |
| [Mehrere Standorte](mehrere-standorte.md) | Filialen, Marktstände und mobile Dienste |

## Was geprüft wurde

Am 20. Oktober 2026, im Repository und in den Erweiterungen der Standarddistribution:

| Frage | Ergebnis | Quelle |
|---|---|---|
| Gibt es einen Datentyp für Karten? | ja, `ezgmaplocation` speichert Breitengrad, Längengrad und die Adresse | `extension/ezgmaplocation/README`, [ezgmaplocation](../../../funktionen/6.0/erweiterungen/ezgmaplocation.md) |
| Braucht er einen API-Schlüssel? | ja, einen API-Schlüssel für die Google Maps JavaScript API für die Karte; ohne ihn werden die Koordinaten ohne Karte angezeigt | `ezgmaplocation.ini [GMapSettings] ApiKey` |
| Gibt es einen Karten-Block für Landingpages? | ja, den Block **Map** des Layout-Editors; Google mit Schlüssel, sonst OpenStreetMap | `extension/explayouts/classes/explayoutsmapblockhandler.php` |
| Gibt es einen Datentyp für Öffnungszeiten? | nein; die Datentypen des Kernels stehen in `kernel/classes/datatypes/`, und keine der Erweiterungen fügt einen hinzu | [Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md) |
| Werden Feiertagszeiten automatisch angezeigt? | nein; sie werden von Hand bearbeitet, wahlweise mit zeitgesteuertem Veröffentlichen | [Zeitgesteuertes Veröffentlichen](../../kunden/alltag/zeitgesteuertes-veroeffentlichen.md) |

## Die Kurzfassung

- Geben Sie Ihre Adresse zuerst als **Text** an. Eine Karte ist eine Zugabe; die Textadresse ist das, was Menschen in
  ihr Smartphone kopieren.
- Wählen Sie eine Karte mit oder ohne Google-Schlüssel ([Ihren Standort auf einer Karte zeigen](ihren-standort-auf-einer-karte-zeigen.md)).
- Halten Sie die Öffnungszeiten in **einem** Objekt, und zeigen Sie es überall
  ([Öffnungszeiten ohne besonderen Datentyp](oeffnungszeiten-ohne-besonderen-datentyp.md)).
- Planen Sie die Feiertagszeiten für das Jahr im Januar ([Feiertags- und Saisonöffnungszeiten](feiertags-und-saisonoeffnungszeiten.md)).

## Verwandte Seiten

- [Lokale Unternehmen: hier beginnen](../lokale-unternehmen-hier-beginnen.md)
- [Inhaltsvorlage für Öffnungszeiten](../vorlagen/inhaltsvorlage-fuer-oeffnungszeiten.md)
- [Unternehmenseinträge außerhalb von Exponential](../gefunden-werden/unternehmenseintraege-ausserhalb-von-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
