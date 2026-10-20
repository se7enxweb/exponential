---
title: "Ein externes Video"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1116
summary: Der Block External video (Video URL or embed src, Width, Height, Autoplay) oder ein Video-Inhaltsobjekt.
keywords: Video, YouTube, Block External video, Einbettung, automatisches Abspielen, NG Video, video, External video block, embed, autoplay
type: help
---
# Ein externes Video

Ein Video eines Videodienstes auf einer Landingpage.

*Lebenszyklus: Bauen, Beispiel 17 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **External video** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Video URL or embed src**, **Width (px or %)** (Standard 100%), **Height (px)** (Standard 360) und **Autoplay** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Die Site hat einen Videobereich (Video, Knoten 164) mit Inhalten vom Typ *NG Video*; um diese zu zeigen, verwenden Sie stattdessen einen List-Block mit einer Sammlung.

## Mit Templates

1. Zeigen Sie ein Video-Inhaltsobjekt mit seiner eigenen Ansicht, damit die Video-Einwilligung der Site greift; das Theme lädt dafür `exp-youtube-consent.css`.

## Welchen Weg Sie wählen

Ein einzelnes Video: der Block. Videos, die Inhalte sind (mit Titeln, Beschreibungen, Suche): NG-Video-Objekte.

## Prüfen

- Das Video startet erst, wenn der Besucher es anfordert, es sei denn, Autoplay ist gesetzt.

## Verwandte Seiten

- [Manuelle und dynamische Sammlungen](../../../../../eng-us/help/build-two-ways/manual-and-dynamic-collections.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
