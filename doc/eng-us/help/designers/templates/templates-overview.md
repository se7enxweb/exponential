---
title: Templates overview
audience: designer, developer
category: templates
order: 10
summary: A map of Exponential's template language (TPL) for designers: what templates are, the kinds of templates a design has, the building blocks of the language (variables, functions, operators, fetches), how templates find their data, and where to read on.
keywords: templates, tpl, template language, eZ template, template overview, smarty like, template functions, template operators, fetch, view templates, override templates, pagelayout
featured: true
type: help
---
# Templates overview

This page is for designers who start writing templates. Exponential's templates are text files ending in `.tpl`,
written in its own template language (TPL), which mixes HTML with tags in braces: `{$node.name|wash}`,
`{foreach ...}`, `{include ...}`. This page is the map; each section links to the details.

## Contents

1. [What a template does](#what-a-template-does)
2. [The kinds of templates in a design](#the-kinds-of-templates-in-a-design)
3. [The language in one screen](#the-language-in-one-screen)
4. [Where templates get their data](#where-templates-get-their-data)
5. [Reading path](#reading-path)

## What a template does

A template turns data (a node, a list, settings) into HTML. It has no access to the database or the file system except
through **fetch functions** and **operators** the kernel and extensions provide, which keeps templates safe to hand to
designers. Templates are compiled into PHP the first time they run and cached
([Template compilation and cache](template-compilation-and-cache.md)).

## The kinds of templates in a design

| Kind | Example path | Called by |
|---|---|---|
| Pagelayout | `pagelayout.tpl` | the kernel, around every page ([The pagelayout](../theme-design/the-pagelayout.md)) |
| Node view templates | `node/view/full.tpl`, `node/view/line.tpl` | content views, `node_view_gui` ([View modes](view-modes.md)) |
| Override templates | `override/templates/full/article.tpl` | `override.ini` rules ([Override basics](override-ini-basics.md)) |
| Datatype templates | `content/datatype/view/ezstring.tpl` | `attribute_view_gui` ([Datatype view templates](datatype-view-templates.md)) |
| Module templates | `user/login.tpl`, `content/search.tpl` | module views |
| Parts | `pagelayout/header.tpl`, `parts/teaser.tpl` | `{include}` from other templates |
| Layout block templates | `explayouts/block/list.tpl` | the layouts engine ([Layouts for designers](../theme-design/layouts-for-designers.md)) |

## The language in one screen

```tpl
{* a comment *}
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', $node.node_id,
                                                 'sort_by', $node.sort_array,
                                                 'limit', 10 ) )}
{if $children|count}
<ul class="children">
    {foreach $children as $child}
        <li><a href={$child.url_alias|ezurl}>{$child.name|wash}</a></li>
    {/foreach}
</ul>
{else}
<p>{'Nothing here yet.'|i18n( 'design/mytheme' )}</p>
{/if}
{undef $children}
```

- `{def}`, `{if}`, `{foreach}`, `{include}` are **functions** ([Function reference](function-reference.md)).
- `|wash`, `|ezurl`, `|count`, `|i18n` are **operators**; `fetch()`, `hash()` too, called with parentheses
  ([Operators overview](operators-overview.md)).
- `$node.name` reads an **attribute** of an object ([Attributes and dot notation](attributes-and-dot-notation.md)).

## Where templates get their data

- **Variables the caller sets**: a full view gets `$node`; a datatype template gets `$attribute`; the pagelayout gets
  `$module_result` and `$site` ([Pagelayout variables](../theme-design/pagelayout-variables.md)).
- **Fetch functions**: `fetch( 'content', 'list', ... )`, `fetch( 'user', 'current_user' )`, `fetch( 'explayouts', ... )`
  ([Fetch basics](fetch-basics.md)).
- **Settings**: `ezini( 'Section', 'Setting', 'file.ini' )` ([The ezini operator](ezini-operator.md)).
- **The request**: `ezhttp()` for GET and POST values, carefully ([The ezhttp operator](ezhttp-operator.md)).

## Reading path

1. [TPL syntax basics](tpl-syntax-basics.md), [Variables and def](variables-and-def.md), [If and conditions](if-and-conditions.md),
   [Foreach loops](foreach-loops.md), [Include](include.md).
2. [Wash and escaping](wash-and-escaping.md), [URL operators](url-operators.md), [The i18n operator](i18n-operator.md).
3. [Fetch basics](fetch-basics.md), [Nodes, objects and attributes](nodes-objects-attributes.md), [View modes](view-modes.md).
4. [Override basics](override-ini-basics.md), [Override match keys](override-match-keys.md).
5. [Template style guide](template-style-guide.md), [Common template mistakes](common-template-mistakes.md).

## Related pages

- [Operator reference](operator-reference.md)
- [Function reference](function-reference.md)
- [Designers: start here](../designers-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
