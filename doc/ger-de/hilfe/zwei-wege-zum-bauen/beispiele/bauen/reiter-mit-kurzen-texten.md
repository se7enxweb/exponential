---
title: "Reiter mit kurzen Texten"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1114
summary: Der Tabs-Block (Tabs „Title|Content“ pro Zeile, Active tab index).
keywords: Reiter, Tabs-Block, Reiterfeld, aktiver Reiter, tabs, Tabs block, tab panel, active tab
type: help
---
# Reiter mit kurzen Texten

Drei Reiter: Beginners, Advanced, Pros.

*Lebenszyklus: Bauen, Beispiel 15 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Tabs** (Gruppe *Basic*) in die Zone `main`.
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Tabs (Title|Content per line)** und **Active tab index (0-based)** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Mit Templates

1. Kopieren Sie `extension/explayouts/design/standard/templates/explayouts/block/tabs.tpl` nach `extension/mysite/design/media/templates/explayouts/block/tabs.tpl` und ändern Sie Ihre Kopie; jeder Block dieses Typs ändert sich in jedem Layout ([Wie ein Block seinen Templates zugeordnet wird](../../../../../eng-us/help/build-two-ways/block-to-template-map.md)). Damit ändern Sie das Markup der Reiter für jeden Tabs-Block.

## Welchen Weg Sie wählen

Der Block für den Inhalt; der Override für barrierefreies Markup auf der ganzen Site.

## Prüfen

- Der in Active tab index angegebene Reiter ist zuerst geöffnet.

## Verwandte Seiten

- [Wie ein Block seinen Templates zugeordnet wird](../../../../../eng-us/help/build-two-ways/block-to-template-map.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
