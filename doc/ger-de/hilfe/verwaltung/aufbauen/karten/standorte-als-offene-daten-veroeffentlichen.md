---
title: Standorte als offene Daten veröffentlichen
audience: government, admin, editor
category: public-sector
order: 70
summary: Wie Sie die in Exponential als Inhalte gepflegten Servicestellen oder Einrichtungen als offenen Datensatz von Standorten veröffentlichen (CSV mit Koordinaten), da kein GeoJSON und kein Geo-Webdienst mitgeliefert wird, und was vor dem Veröffentlichen zu prüfen ist.
keywords: offene Daten, Standorte, Koordinaten, CSV, Geodaten, Servicestellen, Einrichtungen, Export, Datensatz, open data, locations, coordinates, geodata, service points, facilities, xrowextract, export, GeoJSON, dataset
type: help
---
# Standorte als offene Daten veröffentlichen

Diese Seite richtet sich an Administratoren und Datenteams. Werden Servicestellen oder Einrichtungen als Inhalte mit
einem Standortfeld gepflegt, ist ihre Liste ein wertvoller Datensatz. Exponential liefert keine Ausgabe als GeoJSON,
KML oder Geo-Webdienst mit (siehe „Was es für Karten gibt“); eine aus den Inhalten erzeugte CSV-Datei mit Koordinaten
deckt die meisten Weiterverwendungen ab.

## Inhalt

1. [Was der Datensatz enthält](#was-der-datensatz-enthält)
2. [Die Datei erzeugen](#die-datei-erzeugen)
3. [Sie veröffentlichen](#sie-veröffentlichen)
4. [Prüfungen](#prüfungen)

## Was der Datensatz enthält

Eine Zeile je Standort: eine stabile Kennung (die Objekt-ID oder Ihr eigener Code), Name, Adresse, Breitengrad,
Längengrad (Dezimalgrad, WGS 84, wie Google Maps es verwendet), Öffnungszeiten, Hinweise zur Barrierefreiheit, ein Link
zur Seite.

## Die Datei erzeugen

- Mit `xrowextract`: **Export > One class of content export**, die Klasse (zum Beispiel `service_point`), der Ordner,
  CSV; prüfen Sie in der Vorschau die Spalten und wie das Standortfeld erscheint, und speichern Sie die Einstellungen
  als Vorlage für die nächste Aktualisierung ([xrowextract](../../../../../eng-us/features/6.0/extensions/xrowextract.md)).
- Mit dem Befehl des Kernels: `./console exp:ezcsvexport --storage-dir=var/export/csv <node id>` schreibt eine
  CSV-Datei je Klasse; prüfen Sie die Standortspalten vor dem Veröffentlichen
  ([exp:ezcsvexport](../../../../../eng-us/examples/commands/ezcsvexport.md)).
- Ein Entwickler kann auch eine Template-Ansicht schreiben, die die Koordinaten als GeoJSON ausgibt; das ist Ihr
  eigener Code.

## Sie veröffentlichen

Veröffentlichen Sie die Datei als Distribution eines Datensatzes in Ihrem Katalog, mit Lizenz, Aktualisierungsrhythmus
und dem Datum des Exports (siehe den [Bereich offene Daten](../../offene-daten/README.md), „Datensatzdateien
veröffentlichen“).

## Prüfungen

- [ ] Nur öffentliche Standorte; keine Adressen von Einzelpersonen (zum Beispiel Leistungen von zu Hause aus).
- [ ] Koordinaten verwenden einen Punkt als Dezimaltrennzeichen und durchgehend dieselbe Genauigkeit.
- [ ] Jede Zeile hat eine stabile Kennung, damit Weiterverwender Änderungen verfolgen können.
- [ ] Die Datei wird aktualisiert, wenn sich Standorte ändern.

## Verwandte Seiten

- [Was es für Karten gibt](was-es-fuer-karten-gibt.md)
- [Inhalte als CSV, JSON oder XML exportieren](../../offene-daten/inhalte-als-csv-json-xml-exportieren.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
