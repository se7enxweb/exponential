---
title: Was es für Karten gibt
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Eine ehrliche Bestandsaufnahme der Kartenfunktionen für öffentliche Sites mit Exponential - ein Datentyp für Kartenstandorte (ezgmaplocation, Google Maps), ein Entfernungsfilter und sonst nichts; kein GIS, keine selbst gehosteten Kartenkacheln, kein Export von Geodaten, mit den Alternativen.
keywords: Karten, Karte, Standort, GIS, OpenStreetMap, Geodaten, Koordinaten, Servicestellen, Bestandsaufnahme, maps, map, location, ezgmaplocation, Google Maps, geodata, coordinates, service points, inventory
type: help
---
# Was es für Karten gibt

Diese Seite richtet sich an Führungskräfte und Site-Ersteller, die Kartenfunktionen planen, zum Beispiel eine Karte
der Servicestellen. Sie listet, was wirklich verfügbar ist, geprüft in den installierten Erweiterungen, damit Sie den
Rest planen können.

## Inhalt

1. [Was es gibt](#was-es-gibt)
2. [Was es nicht gibt](#was-es-nicht-gibt)
3. [Einen Ansatz wählen](#einen-ansatz-wählen)

## Was es gibt

| Funktion | Wo | Hinweis |
|---|---|---|
| Ein Standortfeld an Inhalten (Breitengrad, Längengrad, Adresse) | die Erweiterung `ezgmaplocation`, Datentyp „GMap Location“ | mit Exponential installiert, inaktiv bis zur Aktivierung |
| Eine Karte im Bearbeitungsformular, um den Punkt über die Adresse zu finden | dieselbe, mit einem API-Schlüssel für Google Maps | Google ist ein Drittanbieter |
| Eine Karte auf der Seite | dieselbe, mit einem API-Schlüssel für Google Maps | ohne Schlüssel werden Koordinaten und Adresse ohne Karte gezeigt |
| Inhalte nach Entfernung zu einem Punkt abrufen, nach Nähe sortiert | erweiterter Attributfilter `ezgmlLocationFilter` | in Templates |

Dokumentation: [ezgmaplocation](../../../../../eng-us/features/6.0/extensions/ezgmaplocation.md) und das README der Erweiterung
in `extension/ezgmaplocation/`.

## Was es nicht gibt

Geprüft: Nichts davon wird mit Exponential oder den Erweiterungen geliefert, die es installiert.

- Keine GIS-Funktionen (Ebenen, Polygone, Flächen, Routenplanung).
- Keine Einbindung von OpenStreetMap oder selbst gehosteten Kartenkacheln.
- Kein Export von Standorten als GeoJSON, KML oder Geo-Webdienst.
- Keine Karte vieler Objekte auf einmal in den mitgelieferten Templates (die Ansicht zeigt einen Standort je Objekt).
- Keine Geokodierung ohne Google (die Schaltfläche „Find address“ verwendet Google Maps).

## Einen Ansatz wählen

| Bedarf | Ansatz |
|---|---|
| Zeigen, wo ein Büro ist | eine Adresse im Text, ein Link zu einem Kartendienst, optional das Standortfeld |
| Eine Liste von Servicestellen in der Nähe des Besuchers | das Standortfeld und der Entfernungsfilter in einem Template |
| Eine Karte aller Servicestellen | Ihr eigenes Template und eine Kartenbibliothek Ihrer Wahl, gespeist mit den Koordinaten der Standortfelder |
| Offene Geodaten | exportieren Sie die Koordinaten (siehe „Standorte als offene Daten veröffentlichen“) |
| Echtes GIS | ein GIS-Produkt neben der Site; verlinken oder einbetten |

## Verwandte Seiten

- [Kartenstandorte mit ezgmaplocation](kartenstandorte-mit-ezgmaplocation.md)
- [Karten und Datenschutz](karten-und-datenschutz.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
