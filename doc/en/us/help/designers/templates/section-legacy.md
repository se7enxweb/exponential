---
title: Section (legacy)
audience: designer, developer
category: templates
order: 110
summary: Reading the old section function found in older templates (loop, sequence, show, section-else, namespaced variables like $Item:item) and converting it to foreach, if and def.
keywords: section, section loop, section-else, section show, legacy templates, namespace variables, $:item, convert section to foreach, old template syntax
type: help
---
# Section (legacy)

This page is for designers who maintain older templates. `{section}` was the general-purpose loop and condition of
early template versions; it still works (`eZTemplateSectionFunction`) and still appears in shipped templates. New
templates use `{foreach}`, `{if}` and `{def}`.

## Contents

1. [Reading it](#reading-it)
2. [Converting it](#converting-it)

## Reading it

```tpl
{section name=Child loop=$children}
    <li>{$Child:item.name|wash}</li>
{section-else}
    <li>{'None'|i18n( 'design/mytheme' )}</li>
{/section}
```

- `loop=` the array (or a number: loop that many times).
- `name=` a namespace; inside, the current item is `$Child:item`, the key `$Child:key`, the index `$Child:index`.
- `section-else` runs when the array is empty.
- `show=` makes the section a condition: `{section show=$node.can_edit} ... {/section}`.
- `sequence=`, `max=`, `offset=`, `{delimiter}` work like their foreach counterparts.

## Converting it

| Section | Modern |
|---|---|
| `{section name=X loop=$list}` ... `$X:item` | `{foreach $list as $item}` ... `$item` |
| `{section-else}` | `{if $list\|count}{foreach ...}{/foreach}{else}...{/if}` |
| `{section show=$cond}` | `{if $cond}` |
| `{let a=1}` ... `{/let}` | `{def $a=1}` ... `{undef $a}` |
| `$:item` (current namespace) | the loop variable |

Convert a template completely, then compare the output of a few pages before and after.

## Related pages

- [Foreach loops](foreach-loops.md)
- [If and conditions](if-and-conditions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
