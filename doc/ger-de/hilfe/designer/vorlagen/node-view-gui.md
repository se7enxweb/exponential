---
title: node_view_gui
audience: designer, developer
category: templates
order: 470
summary: Einen Knoten mit node_view_gui in einem Ansichtsmodus zeichnen - Listen von Zeilenansichten, Karten und Teasern, die Templates node/view, zusätzliche Parameter übergeben, die Override-Schlüssel, die es setzt (Klasse, Elternknoten, Tiefe, Sektion, Remote-IDs), und warum es für Inhalte besser ist als include.
keywords: node_view_gui, Ansichtsmodus, Zeilenansicht, listitem, Teaser, node/view, content_node, Override-Schlüssel, parent_node, class_identifier, Knoten darstellen, Liste von Knoten, view mode, line view, override keys, render node, list of nodes
type: help
---
# node_view_gui

Diese Seite ist für Designer, die Listen von Inhalten zeichnen: die Kinder eines Ordners, Suchergebnisse, verwandte Artikel.
Verwenden Sie `{node_view_gui}`, um jeden Knoten in einem Ansichtsmodus zu zeichnen; das Override-System wählt dann pro Klasse
das richtige Template.

## Inhalt

1. [Verwendung](#verwendung)
2. [Was das Template erhält](#was-das-template-erhält)
3. [Override-Schlüssel](#override-schlüssel)
4. [Zusätzliche Parameter](#zusätzliche-parameter)
5. [Warum nicht include?](#warum-nicht-include)

## Verwendung

```tpl
<ul class="article-list">
{foreach $children as $child}
    <li>{node_view_gui content_node=$child view='line'}</li>
{/foreach}
</ul>
```

Verwendet wird das Template `node/view/line.tpl` oder ein Override für diese Ansicht und die Klasse des Knotens.

## Was das Template erhält

`$node` (den Knoten) und `$view_parameters` mit standardmäßig `offset` 0 (aus der Definition der Funktion in
`kernel/common/eztemplateautoload.php`). Hinweis: nicht `$module_result` und nicht die Variablen des aufrufenden Templates,
außer denen, die als Parameter übergeben werden.

## Override-Schlüssel

`node`, `object`, `class`, `class_identifier`, `class_group`, `section`, `section_identifier`, `state`, `state_identifier`,
`parent_node`, `depth`, `url_alias`, `remote_id`, `node_remote_id`, `parent_class_identifier`, `parent_node_remote_id`,
`parent_object_remote_id`. Eine Regel kann Artikeln in einem Ordner „News“ also eine andere Zeilenansicht geben als Artikeln
anderswo ([Override nach Elternknoten](override-nach-elternknoten.md)).

## Zusätzliche Parameter

Jeder andere Parameter wird zu einer Variablen im View-Template:

```tpl
{node_view_gui content_node=$child view='line' image_class='small' show_date=true()}
```

und im Zeilen-Template: `{default image_class='medium' show_date=false()}`.

## Warum nicht include?

| | `node_view_gui` | `include` |
|---|---|---|
| Templates pro Klasse über Overrides | ja | nein |
| andere Designs können eine Klasse umgestalten | ja | nur durch Ersetzen des ganzen Teils |
| Ansichtsmodus anderswo wiederverwendet (Layout-Blöcke, Suche) | ja | nein |

## Verwandte Seiten

- [Ansichtsmodi](ansichtsmodi.md)
- [Zeilenansicht](zeilenansicht.md)
- [Knoten-Templates nach Klasse](knoten-templates-nach-klasse.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
