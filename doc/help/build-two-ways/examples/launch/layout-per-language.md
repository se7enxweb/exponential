---
title: "A different layout for one language"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1216
summary: A mapping with a Siteaccess condition gives the German version of a page type its own layout.
keywords: language, siteaccess condition, mapping, multilingual layout
type: help
---
# A different layout for one language

The German home page has a different hero than the English one.

*Life cycle: Launch, example 17 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *the German home layout*; **Priority**: above the general home mapping.
2. Target: **Location** with the home node, **Add target**.
3. Condition: **Siteaccess** and choose the German siteaccess in the list, **Add condition**.
4. **Add mapping**.

The Starter mappings of this installation work that way: `siteaccess: starter, starter_ger`.

## With templates

1. Per-siteaccess `override.ini` in your extension's `settings/siteaccess/<sa>/` is the template way.

## Which to choose

Only when the composition differs; translated content alone needs no extra layout.

## Check

- Each siteaccess shows its layout.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
