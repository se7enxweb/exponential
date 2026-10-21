---
title: Einer Servicestelle einen Kartenstandort hinzufügen
audience: government, admin, editor
category: public-sector
order: 360
summary: Aufgabe für Administratoren und Redakteure - der Klasse der Servicestelle ein Feld GMap Location (ezgmaplocation) geben und den Standort eines Büros setzen; Google Maps ist ein Drittanbieter und braucht einen API-Schlüssel, ohne den Koordinaten ohne Karte erscheinen.
keywords: Kartenstandort, Servicestelle, Google Maps, API-Schlüssel, Koordinaten, Adresse suchen, map location, service point, ezgmaplocation, GMap Location, ApiKey, coordinates, Find address
type: help
---
# Einer Servicestelle einen Kartenstandort hinzufügen

Diese Aufgabe richtet sich an Administratoren (Einrichtung) und Redakteure (Standorte). Die einzige mitgelieferte
Kartenfunktion ist `ezgmaplocation`, die Google Maps verwendet, einen Drittanbieter; lesen Sie zuerst
[Karten und Datenschutz](../../aufbauen/karten/karten-und-datenschutz.md). Hintergrund:
[Kartenstandorte mit ezgmaplocation](../../aufbauen/karten/kartenstandorte-mit-ezgmaplocation.md).

## Schritte für Administratoren

1. Aktivieren Sie `ezgmaplocation`; `php bin/php/ezpgenerateautoloads.php -e`.
2. Legen Sie die Tabelle aus `extension/ezgmaplocation/sql/<engine>/` an.
3. Entscheiden Sie über den Schlüssel: Setzen Sie `[GMapSettings] ApiKey` in
   `settings/override/ezgmaplocation.ini.append.php` für Karten, oder lassen Sie ihn leer für Koordinaten ohne jede
   Anfrage an Google. Mit einem Schlüssel und einer Content Security Policy fügen Sie die Google-Hosts hinzu, die das
   README der Erweiterung nennt.
4. **Setup > Klassen**, die Klasse der Servicestelle, **Bearbeiten**, **Attribut hinzufügen**: **GMap Location**,
   Bezeichner `location`, **OK**.

## Schritte für Redakteure

1. Bearbeiten Sie die Servicestelle; geben Sie im Standortfeld die Adresse ein und drücken Sie **Find address**.
2. Ziehen Sie bei Bedarf die Markierung oder klicken Sie auf die genaue Stelle; die Felder **Latitude** und
   **Longitude** folgen.
3. **Zur Veröffentlichung schicken**.

## Prüfen

Die Seite zeigt die Karte (mit Schlüssel) oder die Koordinaten und die Adresse (ohne). Eine Liste in der Nähe, die mit
dem Entfernungsfilter gebaut ist, findet die Servicestelle.

## Probleme

| Problem | Lösung |
|---|---|
| Keine Karte im Bearbeitungsformular | kein Schlüssel gesetzt, oder die CSP blockiert das Skript von Google |
| Alte Adressen zeigen `&uuml;`-Codes | führen Sie das `decodeaddressentities.php` der Erweiterung nach einem Upgrade von 6.0.7 oder älter einmal aus |

## Verwandte Seiten

- [Was es für Karten gibt](../../aufbauen/karten/was-es-fuer-karten-gibt.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
