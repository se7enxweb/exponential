---
title: "Einer einzelnen Seite ein eigenes Layout geben"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1202
summary: Eine Zuordnung vom Typ Ort (oder Pfad) mit hoher Priorität schlägt für eine Seite die Klassen-Zuordnung.
keywords: einzelne Seite, Ziel Ort, Ziel Pfad, übersteuernde Zuordnung, Priorität, single page, Location target, Path target, override mapping, priority
type: help
---
# Einer einzelnen Seite ein eigenes Layout geben

Die Seite Contact bekommt ein Layout mit einer Karte, andere Seiten ihrer Klasse nicht.

*Lebenszyklus: Livegang, Beispiel 3 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. **Layouts > Layout-Zuordnungen > Neue Zuordnung**; **Layout verknüpfen**: *das Layout für die Seite*; **Priorität**: eine Priorität über den allgemeinen Zuordnungen.
2. Ziel: **Ort** mit der Seite oder **Pfad** mit ihrer URL ohne den führenden Schrägstrich (`contact`), **Ziel hinzufügen**.
3. **Zuordnung hinzufügen**.

Der Durchgang mit der Kampagne verwendete ein Ziel Pfad `spring-2027` mit Priorität 2500 ([Eine Kampagnenseite bauen, von Anfang bis Ende](../../../../../eng-us/help/build-two-ways/build-a-campaign-page.md)).

## Mit Templates

1. `override.ini` mit `Match[node]=<id>` oder `Match[remote_id]=<remote id>` (die Tour-Seite des Themes verwendet `Match[remote_id]=showcase-s1-tour`).

## Welchen Weg Sie wählen

Pfad-Ziele sind leicht zu lesen; Ort-Ziele überstehen eine Änderung der URL.

## Prüfen

- Nur diese Seite hat das Layout.

## Verwandte Seiten

- [Welches Layout bekommt eine Seite?](../../../../../eng-us/help/build-two-ways/layout-mappings.md)
- [Eine Kampagnenseite bauen, von Anfang bis Ende](../../../../../eng-us/help/build-two-ways/build-a-campaign-page.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
