---
title: "Checklist: right to left"
audience: designer
category: theme-design
order: 910
summary: A right-to-left checklist for a design: dir on html, logical properties, mirror rules, which icons flip, content in the other direction, isolated names, scripts that measure, fonts, and unchanged left-to-right pages.
keywords: rtl checklist, right to left checklist, Arabic, Hebrew, logical properties check, mirrored icons, bidi, rtl review
type: help
---
# Checklist: right to left

This page is for designers who check a design in right-to-left languages. Background:
[Right-to-left designs](right-to-left-designs.md).

## Contents

1. [Frame](#frame)
2. [CSS](#css)
3. [Text and content](#text-and-content)
4. [Behavior](#behavior)
5. [No regression](#no-regression)

## Frame

- [ ] `dir="rtl"` on `<html>` only for right-to-left languages (from `$site.text_direction`).
- [ ] `lang` matches the language.
- [ ] Side columns, header items and the skip link appear on the mirrored side.

## CSS

- [ ] No physical `left`/`right` properties left for layout ([Logical CSS properties](logical-css-properties.md)).
- [ ] Mirror rules (`:where([dir="rtl"])`) for floats, background positions, asymmetric shorthands, shadows, transforms.
- [ ] Only directional icons flipped (arrows, chevrons, carets, reply, sign-out); clocks, check marks, logos not.
- [ ] Alignment chosen by editors (`text-left` and the like) not mirrored.

## Text and content

- [ ] Content in a left-to-right language inside a right-to-left page (and the reverse) carries its own `lang` and `dir`.
- [ ] Names typed by editors inside translated sentences isolated with `<bdi>` or `dir="auto"`.
- [ ] The font, or its fallback, covers the script; line height suits it.
- [ ] Numbers, dates and phone numbers read correctly.

## Behavior

- [ ] Sliders move in the reading direction; previous and next buttons are on the right sides.
- [ ] Off-canvas menus open from the start side.
- [ ] Drag handles and resizers work.
- [ ] Keyboard Left and Right arrows in widgets follow the visual direction.

## No regression

- [ ] Left-to-right pages are unchanged (compare screenshots before and after).

## Related pages

- [Tutorial: a right-to-left theme](tutorial-right-to-left-theme.md)
- [Right-to-left languages in the administration](../../../features/6.0/right-to-left.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
