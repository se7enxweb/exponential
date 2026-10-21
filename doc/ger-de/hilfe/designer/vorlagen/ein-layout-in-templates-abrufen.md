---
title: Ein Layout in Templates abrufen
audience: designer, developer
category: templates
order: 780
summary: Die Fetch-Funktionen der Layout-Engine (layout, resolve_layout, resolve_layout_for_node, rules_for_node, layout_summary_for_node und die der Administration), ihre Parameter, das Zeichnen des Ergebnisses mit explayouts/layout.tpl und der Rückfall, wenn kein Layout gilt.
keywords: fetch explayouts, resolve_layout, resolve_layout_for_node, rules_for_node, Layout-Fetch, layout.tpl, Layouts im Pagelayout, explayouts function_definition, layout_summary_for_node, layout fetch, layouts in pagelayout
type: help
---
# Ein Layout in Templates abrufen

Diese Seite ist für Designer, die Layouts des Layout-Editors in einem Design zeichnen. Die Fetch-Funktionen sind in
`extension/explayouts/modules/explayouts/function_definition.php` definiert.

## Inhalt

1. [Die Funktionen](#die-funktionen)
2. [Im Pagelayout](#im-pagelayout)
3. [Für einen Knoten](#für-einen-knoten)
4. [Regeln debuggen](#regeln-debuggen)

## Die Funktionen

| Funktion | Parameter | Gibt zurück |
|---|---|---|
| `layout` | `identifier` (Pflicht) | ein Layout nach seinem Bezeichner |
| `resolve_layout` | `path` (optional) | das Layout, das die Regeln für die aktuelle Anfrage (oder den Pfad) wählen |
| `resolve_layout_for_node` | `node_id` (Pflicht) | das Layout, das die Regeln für diesen Knoten wählen würden |
| `rules_for_node` | `node_id` | die Regeln, die auf den Knoten zutreffen |
| `layout_summary_for_node` | `node_id` | eine Zusammenfassung des Layouts des Knotens (von der Administration verwendet) |
| `resolve_admin_layout`, `admin_layout_cache_key`, `admin_layouts_enabled` | Modul, View | die Layouts des Rahmens admin4l |

## Im Pagelayout

```tpl
{def $page_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $page_layout}
    {include uri='design:explayouts/layout.tpl' layout=$page_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
{undef $page_layout}
```

Der Zweig `else` hält Seiten ohne passende Regel funktionsfähig (`doc/eng-us/bc/6.0/LAYOUTS.md`, „How rendering actually
works“).

## Für einen Knoten

Das Design starter löst das Layout des Knotens der Seite nur für Vollansichten auf und behält es nur, wenn es zu seinem Design
gehört:

```tpl
{def $pl_resolved = fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', $pl_node.node_id ) )}
```

(`extension/cjw_themes_jumper/design/starter/templates/pagelayout.tpl`).

## Regeln debuggen

`fetch( 'explayouts', 'rules_for_node', hash( 'node_id', $node.node_id ) )` listet auf, was zutraf. Der Resolver speichert
seine Antwort pro Pfad für `explayouts.ini [ResolverSettings] CacheTTL` Sekunden ([Layout-Regeln](../theme-design/layout-regeln.md)).

## Verwandte Seiten

- [Layouts für Designer](../theme-design/layouts-fuer-designer.md)
- [Das Pagelayout](../theme-design/das-pagelayout.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
