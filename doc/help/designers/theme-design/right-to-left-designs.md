---
title: Right-to-left designs
audience: designer
category: theme-design
order: 580
summary: Making a design work for Arabic, Hebrew, Persian and other right-to-left languages: where the direction comes from ($site.text_direction, the locale files), dir on html, logical CSS properties, mirror rules, which icons to flip, content direction, and names inside text.
keywords: right to left, rtl, dir rtl, Arabic, Hebrew, Persian, Urdu, text direction, TextDirection, logical properties, mirror, bidi, bdi, dir auto, text_direction
type: help
---
# Right-to-left designs

This page is for designers who make a design work in right-to-left languages. The administration does it already
(since 6.0.15); its rules apply to any design. The full feature description is
[Right-to-left languages in the administration](../../../features/6.0/right-to-left.md).

## Contents

1. [Where the direction comes from](#where-the-direction-comes-from)
2. [The page frame](#the-page-frame)
3. [The four CSS rules](#the-four-css-rules)
4. [Content in another direction](#content-in-another-direction)
5. [Names inside text](#names-inside-text)
6. [Testing](#testing)

## Where the direction comes from

- `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl` is the only source; 28 locale files carry it.
- In templates, `$site.text_direction` is `rtl` or `ltr` for the current interface locale (set by the kernel for every
  page), and a locale object has `text_direction` and `is_right_to_left`.

## The page frame

Print `dir="rtl"` only for a right-to-left language, so left-to-right pages stay byte for byte the same:

```tpl
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Every administration pagelayout uses exactly this line. `first_set()` keeps the page working on a kernel without the
variable.

## The four CSS rules

1. **Logical properties for layout**: `margin-inline-start`, `padding-inline-end`, `inset-inline-start`,
   `border-inline-start`, `text-align: start`, the logical corner radii. Left to right they equal the physical ones;
   right to left they swap by themselves ([Logical CSS properties](logical-css-properties.md)).
2. **A mirror rule where no logical property exists** (four-value shorthands with different sides, `float`,
   `background-position`, horizontal shadows), right after the rule it mirrors:

   ```css
   .x { margin: 0 4px 0 12px; }
   :where([dir="rtl"]) .x { margin: 0 12px 0 4px; }
   ```

   `:where()` adds no specificity, so the mirror wins and loses exactly where the original does.
3. **Flip only icons that point along the line**: arrows, chevrons, carets, reply, sign-out. admin4 uses
   `scale: -1 1` on those `.a4-bi-*` icons. A check mark, a clock or a logo is never flipped.
4. **Do not mirror alignment an editor chose**: `text-left`, `align-right` and similar classes from rich text keep
   their side.

Scripts that measure or move things use the direction too (`getComputedStyle(document.documentElement).direction`,
or logical style properties such as `marginInlineStart`).

## Content in another direction

A right-to-left text can appear on a left-to-right page and the other way round. The kernel's
`design/standard/templates/parts/exp_content_direction.tpl` prints ` lang="..." dir="rtl"` for a right-to-left
content locale and nothing otherwise; use it on the element that holds the content
([Content languages](../../../guides/content-languages.md)).

## Names inside text

A name an editor typed, inserted into a translated sentence, must be isolated so its punctuation stays at its own end:
`dir="auto"` on an inline element that holds only the name, `<bdi>` around a name inside a sentence or a block.

```tpl
{'Written by %name'|i18n( 'design/mytheme',, hash( '%name', concat( '<bdi>', $author|wash, '</bdi>' ) ) )}
```

## Testing

- Sign in with a test account and choose a right-to-left interface language (Hebrew, Arabic), or add a right-to-left
  siteaccess for a public site.
- Check the frame (columns swap), icons (only directional ones flip), forms, tables, paging and sliders.
- Check a left-to-right page again: it must not have changed.
- The admin's `tests/tests/design/AdminRightToLeftTest.php` reads the logical property names; copy its idea for your
  design.

## Related pages

- [Logical CSS properties](logical-css-properties.md)
- [Tutorial: a right-to-left theme](tutorial-right-to-left-theme.md)
- [Checklist: right to left](checklist-right-to-left.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
