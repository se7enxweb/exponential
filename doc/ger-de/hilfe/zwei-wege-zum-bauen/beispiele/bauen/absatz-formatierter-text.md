---
title: "Ein Absatz formatierter Text"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1107
summary: Der Rich-text-Block (Content) oder der Text-Block für einfachen Text; in Templates der Text eines Inhaltsfelds.
keywords: Rich Text, Text, Absatz, Rich-text-Block, Text-Block, Inhalt, Formatierung, rich text, paragraph, Rich text block, Text block, content, formatting
type: help
---
# Ein Absatz formatierter Text

Ein kurzer formatierter Absatz zwischen zwei Listen.

*Lebenszyklus: Bauen, Beispiel 8 von 35. Ausgearbeitet auf der Site Fit & Healthy von Exponential 6.0.15+.*

## Inhalt

1. [Im Layout-Editor](#im-layout-editor)
2. [Mit Templates](#mit-templates)
3. [Welchen Weg Sie wählen](#welchen-weg-sie-wählen)
4. [Prüfen](#prüfen)

## Im Layout-Editor

1. Öffnen Sie **Layouts > Layouts** und das Layout *Landing Page / Home*; der Editor öffnet es in einem Entwurf.
2. Öffnen Sie die Blockpalette (**+** oben links) und ziehen Sie **Rich text** (Gruppe *Basic*) in die Zone `main` (oder **Text** für einfachen Text, der Zeilenumbrüche behält).
3. Wählen Sie den Block aus, öffnen Sie den Reiter **Design** und füllen Sie **Content** aus.
4. **Layout veröffentlichen**. Die Seiten, die das Layout verwenden, zeigen den Block beim nächsten Aufruf.

## Mit Templates

1. Halten Sie den Text in einem Inhaltsobjekt und geben Sie sein Rich-Text-Feld aus, damit Übersetzungen und Versionen wie bei jedem Inhalt funktionieren:

```smarty
{def $intro = fetch( 'content', 'node', hash( 'node_id', 93 ) )}
{attribute_view_gui attribute=$intro.data_map.full_intro}
{undef $intro}
```

## Welchen Weg Sie wählen

Text, der zur Zusammenstellung der Seite gehört: der Block. Text, der wie Inhalt übersetzt und versioniert werden muss: ein Inhaltsfeld.

## Prüfen

- Der Absatz erscheint mit seiner Formatierung.

## Verwandte Seiten

- [Der Reiter Design](../../der-reiter-design.md)
- [Beispiele zum Bauen, alle Seiten](README.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
