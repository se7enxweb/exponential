---
title: Moving a template from Smarty
audience: developer, designer, app-creator
category: templates
order: 50
summary: A porting guide - a typical Smarty list template translated to the template language in three steps (variables, data from fetch, the loop with conditions, links, dates and escaping), each step rendered on alpha, plus a checklist of the replacements to make.
keywords: Smarty, port, migrate, convert, translate, porting guide, checklist, fetch, foreach, sequence, cycle, ezurl, wash, datetime
type: help
---
# Moving a template from Smarty

This page ports one small Smarty template to the template language, step by step, and ends with a checklist for your
own templates. Each step was rendered on alpha against the "Fit & Healthy" front page (node 89).

## Contents

1. [The Smarty template](#the-smarty-template)
2. [Step 1: variables](#step-1-variables)
3. [Step 2: the data comes from fetch](#step-2-the-data-comes-from-fetch)
4. [Step 3: the loop, links, dates and escaping](#step-3-the-loop-links-dates-and-escaping)
5. [Checklist](#checklist)
6. [Related pages](#related-pages)

## The Smarty template

A list of the latest pages of a section, as it might look in Smarty. The PHP that renders it assigns `$site` and
`$pages`. This is Smarty, for comparison; it was not run here:

```smarty
{assign var=limit value=3}
<h2>{$site.name|escape}</h2>
{if $pages|@count == 0}
<p>No pages yet.</p>
{else}
<ul>
{foreach from=$pages item=page}
  <li class="{cycle values='odd,even'}"><a href="{$page.url}">{$page.name|truncate:30|escape}</a>
      {$page.published|date_format:'%Y-%m-%d'}</li>
{/foreach}
</ul>
{/if}
```

## Step 1: variables

`{assign}` becomes `{def}`. Several variables can be defined in one tag, on several lines. Instead of receiving `$site`
from PHP, the template fetches the node. `escape` becomes `wash`:

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $limit = 3}
<h2>{$site.name|wash}</h2>
```

Output on alpha:

```text
<h2>Fit &amp; Healthy</h2>
```

## Step 2: the data comes from fetch

The list is a `content/list` fetch with the parent, the limit and the sort order. The count test uses the `count`
operator; there is no `@` and no `==`:

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $limit = 3
     $pages = fetch( 'content', 'list', hash( 'parent_node_id', $site.node_id,
                                              'limit', $limit,
                                              'sort_by', array( 'published', false() ) ) )}
<h2>{$site.name|wash}</h2>
<p>{$pages|count} of {$site.children_count} pages</p>
```

Output on alpha:

```text
<h2>Fit &amp; Healthy</h2>
<p>3 of 16 pages</p>
```

## Step 3: the loop, links, dates and escaping

The finished template. What changed:

- `{if $pages|@count == 0}` becomes `{if $pages|count|eq( 0 )}`.
- `{foreach from=$pages item=page}` becomes `{foreach $pages as $page}`.
- `{cycle values='odd,even'}` becomes the loop parameter `sequence array( 'odd', 'even' ) as $row`.
- A link is the node's `url_alias` passed through `ezurl`, which adds the siteaccess path and the quotes:
  `href={$page.url_alias|ezurl}`.
- `truncate:30` becomes `shorten( 30 )`, and `escape` becomes `wash` (after shortening, so an entity is not cut).
- `date_format` becomes `datetime( 'custom', ... )` on the object's `published` timestamp.
- `{undef}` at the end removes the variables; inside a full view template, variables made with `{def}` are removed at
  the end of the file anyway.

```tpl
{def $site = fetch( 'content', 'node', hash( 'node_id', 89 ) )
     $limit = 3
     $pages = fetch( 'content', 'list', hash( 'parent_node_id', $site.node_id,
                                              'limit', $limit,
                                              'sort_by', array( 'published', false() ) ) )}
<h2>{$site.name|wash}</h2>
{if $pages|count|eq( 0 )}
<p>No pages yet.</p>
{else}
<ul>
{foreach $pages as $page sequence array( 'odd', 'even' ) as $row}
  <li class="{$row}"><a href={$page.url_alias|ezurl}>{$page.name|shorten( 30 )|wash}</a>
      {$page.object.published|datetime( 'custom', '%Y-%m-%d' )}</li>
{/foreach}
</ul>
{/if}
{undef $site $limit $pages}
```

Output on alpha:

```text
<h2>Fit &amp; Healthy</h2><ul>  <li class="odd"><a href="/slash-quotes">Slash Quotes</a>
      2026-10-17</li>  <li class="even"><a href="/exponential-live">Tour</a>
      2026-10-05</li>  <li class="odd"><a href="/fit-healthy-site-info">Fit &amp; Healthy site info - [...</a>
      2026-08-31</li></ul>
```

The list shows what was published last on alpha when this page was built, so the names will differ on your site.

## Checklist

| Smarty | Template language |
|---|---|
| `{assign var=x value=...}`, `{$x = ...}` | `{def $x = ...}`, later `{set $x = ...}` |
| `{$a + $b}`, `{$a * 2}` | `{sum( $a, $b )}`, `{$a\|mul( 2 )}` |
| `==`, `!=`, `>`, `<`, `>=`, `<=` | `eq`, `ne`, `gt`, `lt`, `ge`, `le` |
| `&&`, `\|\|`, `!` | `and( ... )`, `or( ... )`, `not( ... )` |
| `\|default:'x'` | `first_set( $var, 'x' )` |
| `\|escape`, `\|escape:'url'` | `\|wash`, `\|rawurlencode` |
| `\|truncate:n` | `\|shorten( n )` |
| `\|upper`, `\|lower`, `\|capitalize` | `\|upcase`, `\|downcase`, `\|upword` |
| `\|date_format:'...'` | `\|datetime( 'custom', '...' )` or `\|l10n( 'shortdate' )` |
| `\|@count` | `\|count` |
| `{foreach from=$a item=i key=k}` | `{foreach $a as $k => $i}` |
| `{foreachelse}` | `{if $a\|count}...{else}...{/if}` around the loop |
| `{cycle values='a,b'}` | `sequence array( 'a', 'b' ) as $s` |
| `{section name=i loop=$a}` | `{foreach}`, or `{for 0 to ... as $i}` for ranges |
| `{include file='x.tpl'}` | `{include uri='design:x.tpl'}` |
| `{capture name=x}` | `{set-block variable=x}` |
| `{extends}`, `{block}` | overrides and the pagelayout |
| `{php}` | an operator or fetch function in an extension |
| `{$smarty.get.x}` | `ezhttp( 'x', 'get' )`, washed before output |

After porting, render the template with debug output on and read the warnings: an operator that does not exist and a
`set` on a variable that was never defined both fail quietly in the page (see
[Smarty and the template language: the differences in depth](smarty-differences-in-depth.md)).

## Related pages

- Previous: [Smarty and the template language: the differences in depth](smarty-differences-in-depth.md)
- Next: [Lexical structure](lexical-structure.md)
- [Fetching nodes and lists](../designers/templates/fetching-nodes-and-lists.md)
- [URL operators](../designers/templates/url-operators.md)
- [Dates, numbers and localization](../designers/templates/dates-numbers-and-l10n.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
