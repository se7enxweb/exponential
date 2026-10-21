---
title: "Ein Kundenzitat"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1104
summary: Der Quote-Block mit Quote text, Author, Source und Alignment oder dasselbe in einem Template.
keywords: Zitat, Kundenstimme, Quote-Block, Autor, Quelle, Ausrichtung, quote, testimonial, Quote block, author, source, alignment
type: help
---
# Ein Kundenzitat

Ein lobender Satz, mit dem Namen der Person, die ihn gesagt hat.

*Lebenszyklus: Bauen, Beispiel 5 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Quote** (Gruppe *Components*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Quote text**, **Author**, **Source** und **Alignment** (Standard `left`) aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

Das Standard-Template schreibt `<div class="slb slb-quote quote-<alignment>">`.

## Mit Templates

1. Kopieren Sie `extension/explayouts/design/standard/templates/explayouts/block/quote.tpl` nach `extension/mysite/design/media/templates/explayouts/block/quote.tpl` und ändern Sie Ihre Kopie; jeder Block dieses Typs ändert sich in jedem Layout ([Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)). Zum Beispiel, um ein `<cite>` um die Quelle zu setzen.

## Welchen Weg Sie wählen

Der Block für das Zitat selbst; der Override, wenn jedes Zitat der Site anders aussehen soll.

## Prüfen

- `quote-left` (oder Ihre Ausrichtung) im Markup.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../zuordnung-block-zu-template.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
