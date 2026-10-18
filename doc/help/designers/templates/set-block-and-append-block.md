---
title: Set-block and append-block
audience: designer, developer
category: templates
order: 120
summary: Capturing rendered output into a variable with set-block, collecting several pieces into a list with append-block, the scope parameter, and typical uses (rendering a part once and printing it twice, mail subjects, cache_ttl).
keywords: set-block, append-block, capture output, template variable from output, scope global, scope root, mail subject, render once
type: help
---
# Set-block and append-block

This page is for designers who need rendered markup in a variable instead of in the output.

## Contents

1. [set-block](#set-block)
2. [append-block](#append-block)
3. [Scope](#scope)
4. [Uses in the shipped templates](#uses-in-the-shipped-templates)

## set-block

Renders its body and stores the text in a variable (`eZTemplateBlockFunction`):

```tpl
{set-block variable=$share_bar}
    {include uri='design:parts/share.tpl' node=$node}
{/set-block}
<header>{$share_bar}</header>
...
<footer>{$share_bar}</footer>
```

The part is rendered once and printed twice. The admin4l pagelayout uses this to render zones of the admin layout into
variables (`{set-block variable=$header_html}...{/set-block}`) and place them later.

## append-block

Adds each body to a list:

```tpl
{append-block scope=global variable=page_notes}
    <li>{'Updated today'|i18n( 'design/mytheme' )}</li>
{/append-block}
```

## Scope

`scope=global` and `scope=root` write the variable outside the current template, so a view template can hand a value to
the code that called it. Mail templates use it for the subject:

```tpl
{set-block scope=root variable=subject}{'Your order'|i18n( 'design/mytheme/mail' )}{/set-block}
```

and full views for their view cache lifetime:

```tpl
{set-block scope=root variable=cache_ttl}400{/set-block}
```

(the ezwebin event calendar). For values the pagelayout needs, prefer the persistent variable
([The persistent variable](persistent-variable.md)).

## Uses in the shipped templates

- `cache_ttl` in full views (ezwebin's `full/event_calendar.tpl`, `full/multicalendar.tpl`).
- Rendering parts once in the admin4l pagelayout.
- Mail subjects and headers in mail templates.

## Related pages

- [Variables and def](variables-and-def.md)
- [Caching for designers](../theme-design/caching-for-designers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
