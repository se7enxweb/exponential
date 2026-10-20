---
title: "Die Caches vor dem Livegang einmal leeren"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1211
summary: Nach den letzten Änderungen an Templates und Einstellungen einmal Template & Inhalt leeren, dann die Hauptseiten vorwärmen.
keywords: Livegang, Cache leeren, Vorwärmen, Template & Inhalt, launch, cache clear, warm-up, Template & content, exp:cache
type: help
---
# Die Caches vor dem Livegang einmal leeren

Die Seiten zeigen am Tag des Livegangs den endgültigen Stand und antworten schnell.

*Lebenszyklus: Livegang, Beispiel 12 von 33. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Auf der Kommandozeile](#auf-der-kommandozeile)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Administration: **Cache leeren > Template & Inhalt > Leeren**.

## Auf der Kommandozeile

1. `./console exp:cache clear --tag=template,content` (endet mit `PASS`), dann die Hauptseiten einmal abrufen.

## Welchen Weg Sie wählen

Vermeiden Sie **Alle Caches**, wenn es nicht nötig ist; das leert auch, was nicht geleert werden muss.

## Prüfen

- Eine zweite Anfrage an die Startseite antwortet mit `X-Exp-Cache: HIT`.

## Verwandte Seiten

- [Caches nach einer Änderung](../../caches-nach-einer-aenderung.md)
- [Beispiele zum Livegang, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
