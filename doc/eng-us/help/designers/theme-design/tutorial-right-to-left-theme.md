---
title: "Tutorial: a right-to-left theme"
audience: designer
category: theme-design
order: 820
summary: Step by step, make a theme work in Arabic or Hebrew: a right-to-left siteaccess for testing, dir on html, converting the stylesheet to logical properties, mirror rules, flipping directional icons, scripts, fonts, and checking that left-to-right pages did not change.
keywords: tutorial, right to left, rtl theme, Arabic, Hebrew, dir rtl, logical properties, mirror icons, rtl testing, bidirectional
type: help
---
# Tutorial: a right-to-left theme

This tutorial is for designers who make an existing theme work in right-to-left languages. Allow half a day for a
medium theme, most of it in step 3.

## Contents

1. [Step 1: a place to test](#step-1-a-place-to-test)
2. [Step 2: the direction on html](#step-2-the-direction-on-html)
3. [Step 3: logical properties](#step-3-logical-properties)
4. [Step 4: mirror rules and icons](#step-4-mirror-rules-and-icons)
5. [Step 5: scripts and fonts](#step-5-scripts-and-fonts)
6. [Step 6: compare](#step-6-compare)

## Step 1: a place to test

Before you change anything, take screenshots of the main page types in left to right; you will compare against them.

For a right-to-left test you need a siteaccess (or a test interface language) whose locale is right to left: the
locale files with `[LanguageSettings] TextDirection=rtl` in `share/locale/` (for example `heb-IL`, `ara-SA`). Ask your
administrator for a test siteaccess with `[RegionalSettings] Locale=heb-IL` and your design.

## Step 2: the direction on html

```tpl
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
```

Open the test siteaccess: text now starts at the right; everything built with flex and grid has turned around by
itself; what remains wrong is what used physical left and right.

## Step 3: logical properties

Go through the stylesheets and replace physical properties with logical ones
([Logical CSS properties](logical-css-properties.md)):

```bash
grep -n -E "(margin|padding|border)-(left|right)|(^|[^-])(left|right):|text-align: *(left|right)|float: *(left|right)" \
     extension/mytheme/design/mytheme/stylesheets/*.css
```

`margin-left` becomes `margin-inline-start`, `left: 0` becomes `inset-inline-start: 0`, `text-align: left` becomes
`text-align: start`.

## Step 4: mirror rules and icons

For what has no logical form (background positions, asymmetric shorthands, horizontal shadows, transforms), add a mirror
rule right after the original:

```css
.breadcrumb li + li::before { content: "›"; margin-inline: 0.5em; }
:where([dir="rtl"]) .breadcrumb li + li::before { content: "‹"; }
```

Flip icons that point along the line, and nothing else:

```css
:where([dir="rtl"]) .icon-arrow-next,
:where([dir="rtl"]) .icon-chevron { scale: -1 1; }
```

## Step 5: scripts and fonts

- Sliders, off-canvas menus, drag handles: read the direction
  (`getComputedStyle(document.documentElement).direction`) and swap left and right in calculations.
- Fonts: make sure your font, or the fallback stack, covers Arabic or Hebrew; a system stack is a safe start.
- Names typed by editors inside translated sentences: isolate them with `<bdi>` ([Right-to-left designs](right-to-left-designs.md)).

## Step 6: compare

- Right to left: header, menus, breadcrumbs, paging, forms, tables, sliders, icons.
- Left to right: compare with the screenshots from step 1. Logical properties change nothing left to right; if a
  page differs, a conversion was wrong.
- Run the [right-to-left checklist](checklist-right-to-left.md).

## Related pages

- [Right-to-left designs](right-to-left-designs.md)
- [Logical CSS properties](logical-css-properties.md)
- [Checklist: right to left](checklist-right-to-left.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
