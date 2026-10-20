---
title: "Give one page its own layout"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1202
summary: A Location (or Path) mapping with a high priority beats the class mapping for one page.
keywords: single page, Location target, Path target, override mapping, priority
type: help
---
# Give one page its own layout

The Contact page gets a layout with a map, other pages of its class do not.

*Life cycle: Launch, example 3 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *the layout for the page*; **Priority**: a priority above the general mappings.
2. Target: **Location** with the page, or **Path** with its URL without the leading slash (`contact`), **Add target**.
3. **Add mapping**.

The campaign walkthrough used a Path target `spring-2027` at priority 2500 ([Build a campaign page, start to finish](../../build-a-campaign-page.md)).

## With templates

1. `override.ini` with `Match[node]=<id>` or `Match[remote_id]=<remote id>` (the theme's tour page uses `Match[remote_id]=showcase-s1-tour`).

## Which to choose

Path targets are easy to read; Location targets survive a change of the URL.

## Check

- Only that page has the layout.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Build a campaign page, start to finish](../../build-a-campaign-page.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
