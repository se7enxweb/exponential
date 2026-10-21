---
title: content_view_gui
audience: designer, developer
category: templates
order: 460
summary: Ein Inhaltsobjekt (ohne Knoten) mit content_view_gui in einem Ansichtsmodus zeichnen - wenn Sie ein Objekt statt eines Knotens haben (Beziehungen, Einbettungen, Suchtreffer), die Templates content/view und die Override-Schlüssel, die es setzt.
keywords: content_view_gui, Ansicht eines Inhaltsobjekts, content/view, verwandte Objekte, Objektbeziehung, Ansichtsmodus, eingebettetes Objekt, Override-Schlüssel, Objekt-Template, content object view, related objects, object relation, view mode, embed object, override keys, object template
type: help
---
# content_view_gui

Diese Seite ist für Designer, die Objekte zeichnen, die ohne Knoten kommen: verwandte Objekte, Objekte in Beziehungsfeldern,
eingebettete Objekte. `{content_view_gui}` ist das Gegenstück zu [node_view_gui](node-view-gui.md) für Objekte.

## Inhalt

1. [Verwendung](#verwendung)
2. [Templates](#templates)
3. [Override-Schlüssel](#override-schlüssel)
4. [Knoten oder Objekt?](#knoten-oder-objekt)

## Verwendung

```tpl
{foreach $node.object.related_contentobject_array as $related}
    {content_view_gui content_object=$related view='text_linked'}
{/foreach}
```

`content_object` ist das Objekt, `view` der Ansichtsmodus. Das Template erhält das Objekt als `$object`.

## Templates

`content/view/<view>.tpl`, über die Design-Kaskade. Das `design/standard/templates/content/view/` des Kernels hat
`embed.tpl`, `embed-inline.tpl`, `full.tpl`, `line.tpl`, `plain.tpl`, `text.tpl`, `text_linked.tpl`, `tiny.tpl` und einige
weitere für die Administration.

## Override-Schlüssel

Aus der Definition der Funktion in `kernel/common/eztemplateautoload.php`: `object`, `class`, `class_identifier`,
`class_group`, `section`, `remote_id`, `state`, `state_identifier`. Ein Override mit `Source=content/view/embed.tpl` und
`Match[class_identifier]=image` gibt Bildern ihr eigenes Embed-Template.

## Knoten oder Objekt?

Haben Sie einen Knoten, verwenden Sie `node_view_gui`: Es kennt den Platz des Knotens (URL, Elternknoten), und seine
Overrides können auf den Elternknoten und die Tiefe abgleichen. Von einem Objekt aus liefert `$object.main_node` seinen
Hauptknoten, wenn es einen hat.

## Verwandte Seiten

- [node_view_gui](node-view-gui.md)
- [Embed-Ansicht](embed-ansicht.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
