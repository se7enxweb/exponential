---
title: Templates for builders
audience: developer, designer, app-creator
category: extension-development
order: 80
summary: The template language in one chapter - output and escaping, variables with def, set and undef, arrays and hashes, if and foreach, operators and pipes, includes, comments - with examples rendered by the engine.
keywords: template language, tpl, def, set, undef, if, elseif, foreach, include, wash, array, hash, operators, pipe, comments, smarty-like
type: help
---
# Templates for builders

Part 2, chapter 8. Every page Exponential sends is drawn by templates: `.tpl` files in a design, written in a small
language with variables, conditions, loops, operators and includes. It looks like Smarty, and anyone who has written
HTML can learn it in an afternoon. This chapter teaches the core. Every example on this page was rendered by the
kernel's template engine before it was written down, with the output shown.

## Output and escaping

Anything in curly braces is template code; everything else is sent as it is.

```text
<h1>{$node.name|wash}</h1>
```

`{$node.name}` prints a variable; `|wash` escapes it for HTML. **Escape everything that can come from a person**: names,
titles, search words, view parameters. An unescaped value is a cross-site scripting hole. Numbers produced by the
kernel (`$node.node_id`) are safe as they are.

## Comments

```text
{* A comment: never sent to the browser *}
```

## Variables

```text
{def $title = 'Hello'
     $items = array( 'one', 'two', 'three' )
     $person = hash( 'name', 'Ada', 'year', 1815 )}
<h1>{$title|wash}</h1>
{set $title = concat( $title, ', ', $person.name )}
<p>{$title|wash}</p>
{undef $title $items $person}
```

renders

```text
<h1>Hello</h1>
<p>Hello, Ada</p>
```

- `{def}` defines new variables (several at once), `{set}` changes them, `{undef}` removes them.
- `array( ... )` makes a list, `hash( key, value, ... )` a map.
- `$person.name` and `$person['year']` read a key; the dot form is the usual one. On a node, `$node.name`,
  `$node.object`, `$node.data_map.title.content` follow attributes.

Always `{undef}` what you defined in a template that is included many times, so variables do not leak into the next
include.

## Conditions

```text
{if $items|count|gt( 2 )}
<p>More than two items.</p>
{elseif $items|count|eq( 0 )}
<p>No items.</p>
{else}
<p>A few items.</p>
{/if}
```

renders `<p>More than two items.</p>` for the three items above. Comparisons are operators: `eq`, `ne`, `gt`, `ge`,
`lt`, `le`; logic is `and( a, b )`, `or( a, b )`, `not( a )`. For a choice inside an expression use `cond()`:

```text
<p>{cond( $items|count|gt( 5 ), 'many', 'few' )}</p>
```

renders `<p>few</p>`.

## Loops

```text
<ul>
{foreach $items as $index => $item}
    <li>{$index|inc}. {$item|upfirst|wash}</li>
{/foreach}
</ul>
```

renders

```text
<ul>    <li>1. One</li>    <li>2. Two</li>    <li>3. Three</li></ul>
```

`{foreach}` also takes `offset <n>`, `max <n>`, `reverse` and `sequence <array> as $var` (written without `=`);
`{delimiter}...{/delimiter}` inside it prints something between items, and `{break}`, `{continue}`, `{skip}` control
the loop:

```text
[{foreach $items as $i offset 1 max 3}{$i}{delimiter},{/delimiter}{/foreach}]
[{foreach $items as $i sequence array( 'odd', 'even' ) as $class}{$class} {/foreach}]
```

renders `[b,c,d]` and `[odd even odd even odd ]` for the items `a` to `e`.

## Operators and pipes

An operator can be called like a function, `count( $items )`, or with a pipe, `$items|count`. Pipes chain:

```text
{$items|count|gt( 2 )}
```

reads "count the items, then compare with 2". The most useful operators are collected in
[The template operators builders use most](template-operators-builders-use-most.md); every built-in one is listed in
Built-in template operators.

## Fetching content

Templates read content with `fetch()`:

```text
{def $children = fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                                  'limit', 3,
                                                  'sort_by', array( 'published', false() ) ) )}
{foreach $children as $child}
- {$child.name|wash} {$child.url_alias|ezurl( 'no' )}
{/foreach}
```

prints the three newest children of the top node, each with its address. Fetch has its own chapter:
[Fetching content in templates](fetch-for-builders.md).

## Includes

```text
{include uri='design:parts/teaser.tpl' node=$child}
```

`design:` means "find this path through the design chain" ([The design chain](the-design-chain.md)); the other
parameters become variables in the included template. Templates that are included often belong in a `parts/` folder
of your design.

## Links and images

```text
<a href={$node.url_alias|ezurl}>{$node.name|wash}</a>
<link rel="stylesheet" href={'stylesheets/site.css'|ezdesign} />
```

`ezurl` makes a link inside the current siteaccess and prints it **with quotes**; `ezurl( 'no' )` without. `ezdesign`
finds a file of the design. Never write site addresses by hand: they break when the site moves or a siteaccess is
added.

## Where templates live

| Template | Path in a design |
|---|---|
| The page frame | `templates/pagelayout.tpl` |
| A content page in full | `templates/node/view/full.tpl`, or an override |
| A list item | `templates/node/view/line.tpl`, or an override |
| A field (attribute) | `templates/content/datatype/view/<datatype>.tpl` |
| A module's own page | `templates/<module>/<view>.tpl` |

## Rules for good templates

- Escape output (`|wash`) unless you know the value is safe.
- No inline `<script>` or `style=""`: scripts and styles go in files of the design and are loaded through the design's
  settings (a Content Security Policy forbids inline code in the administration). See
  [Theme assets, the packer and CSP](../part-6-theme-developers/theme-assets-the-packer-and-csp.md).
- Fetch once, outside loops.
- Keep logic small. When a template needs a page of logic, the logic belongs in an operator or a fetch function
  ([When settings are not enough](when-settings-are-not-enough.md)).

## Related pages

- Previous: [.ini.append.php files and keeping settings safe](append-php-files-and-safety.md)
- Next: [The design chain: where a template is found](the-design-chain.md)
- [Templates and design](../../../guides/templates-and-design.md)
- Built-in template functions
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
