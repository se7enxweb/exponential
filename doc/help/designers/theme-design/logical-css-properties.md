---
title: Logical CSS properties
audience: designer
category: theme-design
order: 590
summary: A conversion table from physical CSS properties (left, right, margin-left, text-align: left) to logical ones (inline-start, inline-end), what has no logical form, and how to convert an existing stylesheet safely.
keywords: logical properties, margin-inline-start, padding-inline, inset-inline, text-align start, border-inline, logical radius, rtl css, writing direction, convert css
type: help
---
# Logical CSS properties

This page is a reference for designers writing CSS that works in both text directions. Logical properties name sides
by the flow of text (start and end) instead of the screen (left and right).

## Contents

1. [Conversion table](#conversion-table)
2. [What has no logical form](#what-has-no-logical-form)
3. [Converting a stylesheet](#converting-a-stylesheet)

## Conversion table

| Physical | Logical |
|---|---|
| `margin-left` / `margin-right` | `margin-inline-start` / `margin-inline-end` |
| `margin-left` + `margin-right` | `margin-inline` |
| `padding-left` / `padding-right` | `padding-inline-start` / `padding-inline-end` |
| `left` / `right` (positioned) | `inset-inline-start` / `inset-inline-end` |
| `top` / `bottom` | `inset-block-start` / `inset-block-end` |
| `border-left` / `border-right` | `border-inline-start` / `border-inline-end` |
| `border-top-left-radius` | `border-start-start-radius` |
| `border-top-right-radius` | `border-start-end-radius` |
| `border-bottom-left-radius` | `border-end-start-radius` |
| `border-bottom-right-radius` | `border-end-end-radius` |
| `text-align: left` / `right` | `text-align: start` / `end` |
| `width` / `height` | `inline-size` / `block-size` (optional; only matters for vertical writing) |

Left to right, each logical property computes to the same value as the physical one, so converting changes nothing
for left-to-right pages. This is how the administration's stylesheets were converted (`components.css`, `admin4.css`,
`pagelayout.css`, `core.css` and the module stylesheets).

## What has no logical form

| Property | What to do |
|---|---|
| `float: left` / `right` | `float: inline-start` / `inline-end` where supported, or a `:where([dir="rtl"])` mirror rule |
| four-value `margin`/`padding` with different left and right | split into `margin-block` and `margin-inline`, or add a mirror rule |
| `background-position: left ...` | a mirror rule |
| `box-shadow` / `text-shadow` with a horizontal offset | a mirror rule |
| `transform: translateX()` | a mirror rule (negate the value) |
| grid and flex | nothing: they follow the direction already |

A mirror rule follows the rule it mirrors:

```css
.teaser { background-position: left center; }
:where([dir="rtl"]) .teaser { background-position: right center; }
```

## Converting a stylesheet

1. Search for `left` and `right`: `grep -n -E "left|right" stylesheets/*.css`.
2. Convert each into its logical form from the table; leave alignment chosen by editors (`.text-left` classes from rich
   text) alone.
3. Add mirror rules for what has no logical form.
4. Compare screenshots of left-to-right pages before and after: they must be identical.
5. Check a right-to-left page.

## Related pages

- [Right-to-left designs](right-to-left-designs.md)
- [Responsive design](responsive-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
