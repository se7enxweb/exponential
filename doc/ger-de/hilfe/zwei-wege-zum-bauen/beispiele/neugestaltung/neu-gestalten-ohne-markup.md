---
title: "Blöcke neu gestalten, ohne das Markup anzufassen"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1407
summary: Die meisten Neugestaltungen brauchen nur CSS für die Wrapper-Klassen ngl-<type>, ngl-vt-<view type>, bg-color-*, whitespace-*.
keywords: CSS, neu gestalten, Wrapper-Klassen, ngl-block, Neugestaltung, Stylesheet, restyle, wrapper classes, redesign, stylesheet
type: help
---
# Blöcke neu gestalten, ohne das Markup anzufassen

Neue Farben, Abstände und Schrift, keine Änderungen an Templates.

*Lebenszyklus: Neugestaltung, Beispiel 8 von 34. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Redakteure verwenden weiterhin dieselben Optionen; auch die Klassen, die sie setzen (CSS class), lassen sich gestalten.

## Mit Templates

1. Nehmen Sie Ihr Stylesheet in die Liste des Media-Designs auf, indem Sie `pagelayout/head/style.tpl` überschreiben ([Ihre Blöcke gestalten](../../bloecke-gestalten.md)), und gestalten Sie die Klassen, die der Wrapper bereits hat.

## Welchen Weg Sie wählen

Zuerst CSS; überschreiben Sie Templates nur dort, wo sich die Struktur ändern muss.

## Prüfen

- Das gepackte CSS enthält Ihre Regeln.

## Verwandte Seiten

- [Ihre Blöcke gestalten](../../bloecke-gestalten.md)
- [Beispiele zur Neugestaltung, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
