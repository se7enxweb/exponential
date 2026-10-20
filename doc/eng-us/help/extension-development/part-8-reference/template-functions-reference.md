---
title: Built-in template functions
audience: developer, designer
category: extension-development
order: 280
summary: The template functions of the engine - variables, conditions, loops, includes, blocks, caching, debugging - and the kernel's view functions such as node_view_gui, listed from the running engine.
keywords: template functions, def, undef, set, let, if, switch, foreach, for, while, do, include, set-block, append-block, run-once, cache-block, debug-timing-point, node_view_gui, attribute_view_gui, attribute_edit_gui, content_view_gui
type: help
---
# Built-in template functions

Part 8, reference 24. Template functions are the `{name ...}...{/name}` constructs of the template language. Like the
operators on the previous page, this list was taken from the template engine of a running installation (October
2026). Usage, with examples, is in the designer help: [Function reference](../../designers/templates/function-reference.md).

## Variables

| Function | Use |
|---|---|
| `{def $name=value}`, `{undef $name}` | define a variable for this template; undefine it at the end |
| `{set $name=value}` | change a defined variable |
| `{let}`, `{default}` | older forms kept for old templates; use `def` in new ones |

## Conditions

| Function | Use |
|---|---|
| `{if}`, `{elseif}`, `{else}`, `{/if}` | conditions |
| `{switch match=...}`, `{case}`, `{/switch}` | one of several values |

## Loops

| Function | Use |
|---|---|
| `{foreach $list as $key => $item}` | loop over an array; parameters `offset`, `max`, `reverse`, and `{delimiter}`, `{skip}`, `{break}`, `{continue}` inside |
| `{for 1 to 10 as $i}` | count |
| `{while}`, `{do}` | loops with a condition |
| `{section}` | an old loop form, kept for old templates |
| `{sequence}` | cycle through values (row classes) |

Note the parameter syntax of `foreach`: `{foreach $items as $item offset 1 max 3}` (no `=`).

## Including and blocks

| Function | Use |
|---|---|
| `{include uri='design:parts/menu.tpl' name=value}` | render another template with variables |
| `{set-block variable=$name}...{/set-block}`, `{append-block}` | capture output into a variable |
| `{run-once}` | output something once per page (a script tag) |
| `{ldelim}`, `{rdelim}` | literal `{` and `}` |

## Caching

`{cache-block keys=... expiry=... subtree_expiry=...}` caches its output
([Cache tags, cache blocks and clearing caches](cache-tags-and-clearing.md)).

## Debugging

`{debug-timing-point id=...}`, `{debug-accumulator id=...}`, `{debug-log var=...}`, `{debug-trace}`: write to the
debug output.

## Views of content (kernel)

| Function | Renders |
|---|---|
| `{node_view_gui content_node=$node view='line'}` | a node in a view mode, with override rules |
| `{content_view_gui content_object=$object view='embed'}` | an object in a view mode |
| `{attribute_view_gui attribute=$node.data_map.title}` | an attribute's view template (its datatype's) |
| `{attribute_edit_gui attribute=$attribute}` | an attribute's edit form |
| `{class_attribute_view_gui ...}`, `{class_attribute_edit_gui ...}` | class attribute settings |
| `{content_version_view_gui ...}`, `{attribute_diff_gui ...}`, `{attribute_result_gui ...}`, `{attribute_pdf_gui ...}`, `{content_pdf_gui ...}` | versions, differences, collected results, PDF |
| `{event_view_gui ...}`, `{event_edit_gui ...}` | workflow event views |
| `{related_view_gui ...}`, `{shop_account_view_gui ...}` | relations, shop accounts |
| `{collaboration_view_gui ...}` and the other collaboration functions | the collaboration system |
| `{tool_bar ...}`, `{menu ...}` | toolbars and menus of the administration |

The view functions are the reason templates should never print attribute values by hand: they choose the right
template for the datatype and the view mode, and designs can override it.

## Functions of shipped extensions

ezjscore adds its template functions as operators (`ezscript`, `ezcss` and the rest, previous page); extensions such
as the layouts suite add their own block functions when active.

## Related pages

- Previous: [Built-in template operators](template-operators-reference.md)
- Next: [Built-in fetch functions by module](fetch-functions-reference.md)
- [Template functions](../part-3-extension-developer/template-functions.md)
- [Templates for builders](../part-2-builder/templates-for-builders.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
