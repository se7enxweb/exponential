---
title: "Für Geschwindigkeit planen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1033
summary: Seiten aus dem Cache antworteten auf der Testkopie in 22 bis 102 ms, eine Seite nach dem vollständigen Leeren der Templates in etwa 3 s; planen Sie Livegänge und das Leeren von Caches mit diesem Wissen.
keywords: Leistung, Geschwindigkeit, Seiten-Cache, Template-Kompilierung, Cache leeren, Livegang, performance, speed, page cache, template compile, cache clear, launch
type: help
---
# Für Geschwindigkeit planen

Keine überraschend langsamen ersten Besuche nach einem Deployment.

*Lebenszyklus: Planen, Beispiel 34 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Nach Änderungen an Templates](#nach-änderungen-an-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Das Veröffentlichen eines Layouts hat die betroffenen Seiten auf der Testkopie ohne vollständiges Leeren aktualisiert (`X-Exp-Cache: MISS (purged)`).

## Nach Änderungen an Templates

1. Nach einem vollständigen Leeren von **Template & Inhalt** kompilieren die nächsten Anfragen die Templates neu (2,9 bis 3,4 s für die erste Seite auf der Testkopie).
2. Wärmen Sie nach dem Leeren die Hauptseiten vor (rufen Sie sie einmal ab).

## Welchen Weg Sie wählen

Ziehen Sie gezielte Aktionen (Veröffentlichen, Inhalte leeren) **Alle Caches** vor.

## Prüfen

- Die Hauptseiten antworten nach dem Vorwärmen aus dem Seiten-Cache (`X-Exp-Cache: HIT`).

## Verwandte Seiten

- [Gemessene Schritte und Zeiten](../../gemessene-schritte-und-zeiten.md)
- [Caches nach einer Änderung](../../../../../eng-us/help/build-two-ways/caches-after-a-change.md)
- [Beispiele zur Planung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
