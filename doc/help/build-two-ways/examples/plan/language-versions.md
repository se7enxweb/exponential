---
title: "Plan a second language"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1009
summary: Each language version is a siteaccess with its own settings (menus) and can get its own layouts through a Siteaccess condition.
keywords: language, translation, siteaccess, bold_ger, condition, multilingual, planning
type: help
---
# Plan a second language

A German version of a site with its own menu and, where needed, its own layouts.

*Life cycle: Plan, example 10 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. A mapping can have a **Siteaccess** condition (a list: site, admin, classic, bold, bold_ger ...). The Starter mappings use `siteaccess: starter, starter_ger`.
2. Plan which page types need a layout per language and which share one.

## With templates

1. Each siteaccess has its own `menu.ini` (`exp:ini where menu.ini/SiteInfo/MainMenuID <siteaccess>`), so the header menu can differ.
2. Translatable template texts use `|i18n( 'context' )` and the translation files of your extension.

## Which to choose

Share layouts across languages unless the composition really differs; the content is translated anyway.

## Check

- The plan lists the siteaccess of each language.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
