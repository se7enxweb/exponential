---
title: "Common template tasks"
audience: builder, designer
category: books
order: 450
summary: Recipes from the book (top menu, breadcrumb, tree menu, edit links, dates, shortening text, user panel, keywords) rewritten with foreach, current operators and no inline style.
keywords: menu, breadcrumb, treemenu, edit link, can_edit, datetime, currentdate, shorten, wash, autolink, keyword, user panel, template recipes, Learning book
type: help
---
# Common template tasks

The book closes its template chapter with recipes. Here they are with today's syntax: `foreach` instead of `section`,
no inline `style=`, and the operators that exist now. All of them go in a design's templates; see
[template principles](template-principles.md) for where.

## Navigation menus

**A top-level menu** (the folders just below the root, the current one marked). `treemenu` takes the path of the current
page (`$module_result.path`), a node ID (no longer used: pass `false()`), the class filter, how many levels to skip and how
many to show ([menu and tree operators](../template-operators/menu-and-tree-operators.md)):

```
{def $top=treemenu( $module_result.path, false(), array( 'folder' ), 1, 1 )}
<ul class="topmenu">
{foreach $top as $item}
    <li{if $item.is_selected} class="selected"{/if}>
        <a href={$item.url_alias|ezurl}>{$item.text|wash}</a>
    </li>
{/foreach}
</ul>
```

**Breadcrumb**: loop over the path. Wrap it in a cache block keyed with the node:

```
{cache-block keys=array( 'path', $module_result.node_id )}
<p class="path">
{foreach $module_result.path as $p offset 1}
    {if $p.url}<a href={$p.url_alias|ezurl}>{$p.text|shorten( 60 )|wash}</a>
    {else}{$p.text|shorten( 60 )|wash}{/if}
{delimiter} / {/delimiter}
{/foreach}
</p>
{/cache-block}
```

(Check `$p.url_alias`: the path entries hold a `url_alias`; the last one, the page itself, has no `url`.)

**A tree menu**: `treemenu` with more levels returns a flat list in menu order; each item has `level` and `indent`. Do the
indentation with a CSS class made from the level (`level-1`, `level-2` ...) in the design's style sheet, never with `style=`:

```
{foreach treemenu( $module_result.path, false(), false(), 1, 5 ) as $item}
    <li class="level-{$item.level}{if $item.is_selected} selected{/if}">
        <a href={$item.url_alias|ezurl}>{$item.text|wash}</a>
    </li>
{/foreach}
```

## Edit functions in the site

Let the right users edit from the public page. Ask the object whether the current user may:

```
{if $node.object.can_edit}
    <a href={concat( 'content/edit/', $node.contentobject_id )|ezurl}>Edit</a>
{/if}
```

Removing and commenting are buttons in a form that posts to `content/action` (`ActionRemove`, `NewButton`) with a form token.
Copy the markup from a shipped template, such as `design/standard/templates/node/view/full.tpl`, rather than typing it from
memory. [View modes](../extension-development/part-2-builder/view-modes-for-builders.md) shows where such a template sits.

## Dates and times

`currentdate()` is the time now as a timestamp; do arithmetic with `sum`, format with `datetime`:

```
{currentdate()|sum( 86400 )|datetime( 'custom', '%j %F %Y' )}
```

for tomorrow's date. The formats and the named classes (`shortdate`, `time`, ...) are in
[locale operators](../template-operators/locale-operators.md).

## Text

- `{$text|shorten( 120 )}` limits a plain string; always add `|wash` when you print user text.
- Rich text is limited by its own output; the old advice to cut the XML ahead is replaced by using a separate short text
  attribute (an *intro*) in the class.
- `{$text|autolink}` turns addresses in plain text into links.

## A custom user experience

- **A dummy node** (the class of the planning chapter, with only a name) can act as the hook for a special page such as
  *My site*, with an override of its own ([overriding templates](overriding-templates.md)).
- **A user panel**: show the current user's groups and roles with `fetch( 'user', 'current_user' )` and the role operators
  ([role operators](../template-operators/role-operators.md)); the user preferences function is in
  [What is on my account page](../manual/what-is-on-my-account-page.md).

## Other tasks

- **An audit trail**: loop over `fetch( 'content', 'version_list', ... )`, showing the creator and the date of each version.
- **Creator, modifier, publishing date**: `$node.object.owner.name`, `$node.object.published|datetime(...)`.
- **Keywords and automatically related objects**: an `ezkeyword` attribute holds the keywords
  (`$node.data_map.keywords.content.keywords`); the objects that share a keyword are fetched by the `keyword` function of the
  `content` module (see [Fetch functions](../template-operators/fetch-functions-how-they-work.md)). The "advanced keyword
  facility" of the book (listing by keyword) is a fetch with `alphabet` or `classid` parameters, as described there.
- **A threaded forum**: a forum is a folder of topics, each topic a tree of replies; the template walks the children
  recursively with a template that `include`s itself.

Back to [the chapters](README.md).

## Source and license

Adapted from chapter 3, "Displaying Content with eZ publish Templates", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; syntax, operators, settings and examples updated to Exponential 6 (foreach, current settings, no inline style or script).
