---
title: "Add a language and give it layouts"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1515
summary: A new siteaccess gets its settings (menu.ini) by exp:ini copy and its own layouts by mappings with a Siteaccess condition.
keywords: new language, siteaccess, migration, menu copy, siteaccess condition
type: help
---
# Add a language and give it layouts

A French version alongside English.

*Life cycle: Migrate, example 16 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Mappings for the French layouts with a **Siteaccess** condition on the new siteaccess; or no new mappings if the layouts are shared.

## With templates

1. Copy the menu settings to the new siteaccess (`exp:ini copy`), then change the IDs if pages differ.

## Which to choose

Share layouts unless the composition differs.

## Check

- The French site shows its menu and layouts.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [exp:ini for site builders](../../exp-ini-for-site-builders.md)
- [Migrate examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
