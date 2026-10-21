---
title: Den Standort einer Servicestelle hinzufügen
audience: government, editor
category: public-sector
order: 40
summary: Für Redakteure - wie Sie der Seite einer Servicestelle, eines Büros oder einer Einrichtung mit dem Feld GMap Location ihren Standort geben, ihn prüfen und die Adresse so schreiben, dass sie mit und ohne Karte nützlich ist.
keywords: Servicestelle, Bürostandort, Adresse, Breitengrad, Längengrad, Redakteur, Karte, service point, office location, address, GMap Location, Find address, Restore, latitude, longitude, editor, map
type: help
---
# Den Standort einer Servicestelle hinzufügen

Diese Seite richtet sich an Redakteure, die Seiten von Büros, Servicestellen oder Einrichtungen pflegen, deren Klasse
ein Standortfeld hat (siehe „Kartenstandorte mit ezgmaplocation“). Die Schritte stammen aus dem README der Erweiterung.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Den Standort setzen](#den-standort-setzen)
3. [Ohne Karte](#ohne-karte)
4. [Die Adresse für Menschen schreiben](#die-adresse-für-menschen-schreiben)
5. [Prüfen](#prüfen)

## Bevor Sie beginnen

Die Klasse der Seite hat ein Feld vom Typ **GMap Location**. Ob eine Karte erscheint, hängt davon ab, ob der
Administrator einen API-Schlüssel gesetzt hat; mit Schlüssel wird im Bearbeitungsformular die Karte von Google geladen.

## Den Standort setzen

1. Öffnen Sie die Seite und drücken Sie **Bearbeiten**.
2. Geben Sie im Standortfeld die Adresse unter der Karte ein.
3. Drücken Sie **Find address**; die Markierung springt zur Adresse, und die Felder **Latitude** und **Longitude**
   werden gefüllt.
4. Stimmt der Punkt nicht genau (zum Beispiel der Eingang), ziehen Sie die Markierung oder klicken Sie auf die richtige
   Stelle der Karte; die Koordinaten folgen. **Restore** kehrt zu den Werten zurück, mit denen Sie begonnen haben.
5. **Zur Veröffentlichung schicken**.

Die Adresse wird so gespeichert, wie Sie sie eingegeben haben. Vermeiden Sie **My current location**, sofern Sie nicht
vor Ort sind: Es fragt Ihren Browser nach Ihrer eigenen Position.

## Ohne Karte

Ist kein API-Schlüssel gesetzt, zeigt das Feld die Koordinaten ohne Karte. Sie können Breiten- und Längengrad in
Dezimalgrad aus einer anderen Quelle eingeben (zum Beispiel aus dem Adressregister Ihrer Behörde), und die Adresse.

## Die Adresse für Menschen schreiben

Das Standortfeld dient der Karte und der Entfernungssuche; Menschen brauchen mehr. Geben Sie im Text der Seite an:

- die vollständige Postanschrift und den Eingang;
- Öffnungszeiten, Barrierefreiheit des Gebäudes, öffentlichen Verkehr;
- wie man die Stelle erreicht.

## Prüfen

Öffnen Sie die Seite abgemeldet. Mit Schlüssel zeigt die Karte den Punkt; ohne erscheinen Koordinaten und Adresse.
Prüfen Sie in einer Liste nahegelegener Servicestellen, dass die Seite für einen Ort in der Nähe erscheint.

## Verwandte Seiten

- [Kartenstandorte mit ezgmaplocation](kartenstandorte-mit-ezgmaplocation.md)
- [Inhalte bearbeiten](../../../inhalte-bearbeiten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
