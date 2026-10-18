---
title: Layout rules
audience: designer
category: theme-design
order: 750
summary: How a page gets its layout: rules with targets (path, node, subtree), conditions (siteaccess, content type, query parameter, time) and priority; the first match wins; the resolver cache; and the fetch that shows which rules match a node.
keywords: layout rules, mappings, targets, conditions, priority, subtree target, content_type condition, resolver, resolver cache, rules_for_node, wrong layout, DefaultLayout
type: help
---
# Layout rules

This page is for designers who wonder why a page shows a certain layout. Rules (called mappings in the editor) decide
it. Full reference: [Exponential Layouts](../../../bc/6.0/LAYOUTS.md), "Rules".

## Contents

1. [A rule](#a-rule)
2. [Targets](#targets)
3. [Conditions](#conditions)
4. [First match wins](#first-match-wins)
5. [Debugging the layout of a page](#debugging-the-layout-of-a-page)

## A rule

A rule has a layout, targets, conditions, a priority and an enabled switch. The resolver
(`expLayoutsResolver::resolve()`) walks the enabled rules by priority and returns the first published layout whose rule
matches the request.

## Targets

| Type | Matches when |
|---|---|
| `path_prefix` | the request path starts with the value |
| `path` | the path equals the value |
| `path_regex` | the path matches a regular expression |
| `node` | the request is this content node |
| `subtree` | the request's node is below this location |

`subtree` survives an editor renaming a folder; a `path_prefix` does not.

## Conditions

| Type | Restricts by |
|---|---|
| `siteaccess` | the siteaccess serving the page |
| `content_type` / `class` | the content class of the node |
| `query_parameter`, `route_parameter` | a parameter in the URL |
| `time` | a date range (campaign layouts that switch themselves on and off) |

An unknown condition type matches rather than fails, so a typo widens a rule instead of disabling it.

## First match wins

When a layout does not appear, a broader rule with a higher priority usually catches the page first. Look at the
priorities before anything else.

## Debugging the layout of a page

- In a template: `fetch( 'explayouts', 'rules_for_node', hash( 'node_id', $node.node_id ) )` lists the rules that match a
  node, and `fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', ... ) )` returns the layout it would get
  (`extension/explayouts/modules/explayouts/function_definition.php`).
- The resolver caches its result per path under `var/<site>/cache/explayouts/resolver/` with
  `explayouts.ini [ResolverSettings] CacheTTL` (3600 seconds). Publishing in the editor clears it; a rule changed
  directly in the database does not.
- `[ResolverSettings] DefaultLayout` names the layout used when no rule matches, if any.

## Related pages

- [Layouts for designers](layouts-for-designers.md)
- [Fetching a layout in templates](../templates/layouts-template-fetch.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
