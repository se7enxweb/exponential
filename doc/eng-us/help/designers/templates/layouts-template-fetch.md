---
title: Fetching a layout in templates
audience: designer, developer
category: templates
order: 780
summary: The fetch functions of the layouts engine (layout, resolve_layout, resolve_layout_for_node, rules_for_node, layout_summary_for_node and the admin ones), their parameters, drawing the result with explayouts/layout.tpl, and the fallback when no layout applies.
keywords: fetch explayouts, resolve_layout, resolve_layout_for_node, rules_for_node, layout fetch, layout.tpl, layouts in pagelayout, explayouts function_definition, layout_summary_for_node
type: help
---
# Fetching a layout in templates

This page is for designers who draw layouts of the layouts editor in a design. The fetch functions are defined in
`extension/explayouts/modules/explayouts/function_definition.php`.

## Contents

1. [The functions](#the-functions)
2. [In the pagelayout](#in-the-pagelayout)
3. [For a node](#for-a-node)
4. [Debugging rules](#debugging-rules)

## The functions

| Function | Parameters | Returns |
|---|---|---|
| `layout` | `identifier` (required) | one layout by its identifier |
| `resolve_layout` | `path` (optional) | the layout the rules choose for the current request (or the path) |
| `resolve_layout_for_node` | `node_id` (required) | the layout the rules would choose for that node |
| `rules_for_node` | `node_id` | the rules that match the node |
| `layout_summary_for_node` | `node_id` | a summary of the node's layout (used by the administration) |
| `resolve_admin_layout`, `admin_layout_cache_key`, `admin_layouts_enabled` | module, view | the admin4l frame's layouts |

## In the pagelayout

```tpl
{def $page_layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $page_layout}
    {include uri='design:explayouts/layout.tpl' layout=$page_layout module_result=$module_result}
{else}
    {$module_result.content}
{/if}
{undef $page_layout}
```

The `else` branch keeps pages without a matching rule working (`doc/en/us/bc/6.0/LAYOUTS.md`, "How rendering actually
works").

## For a node

The starter design resolves the layout of the page's node only for full views, and keeps it only when it belongs to its
design:

```tpl
{def $pl_resolved = fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', $pl_node.node_id ) )}
```

(`extension/cjw_themes_jumper/design/starter/templates/pagelayout.tpl`).

## Debugging rules

`fetch( 'explayouts', 'rules_for_node', hash( 'node_id', $node.node_id ) )` lists what matched. The resolver caches its
answer per path for `explayouts.ini [ResolverSettings] CacheTTL` seconds ([Layout rules](../theme-design/layout-rules.md)).

## Related pages

- [Layouts for designers](../theme-design/layouts-for-designers.md)
- [The pagelayout](../theme-design/the-pagelayout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
