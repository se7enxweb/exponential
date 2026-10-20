---
title: "A layout for every other page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1204
summary: Default Layout catches the rest: a Path prefix "/" mapping at priority 10, and [ResolverSettings] DefaultLayout as the fallback.
keywords: default layout, catch-all, path prefix, fallback, DefaultLayout
type: help
---
# A layout for every other page

No page is left without header and footer.

*Life cycle: Launch, example 5 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layout mappings > New mapping**; **Link layout**: *Default Layout*; **Priority**: the lowest (10).
2. Target: **Path prefix** `/`, **Add target**.
3. **Add mapping**.

Default Layout has only its `header` and `footer` linked to Header / Footer and one block of its own.

## With templates

1. `explayouts.ini [ResolverSettings] DefaultLayout` names the layout used when no mapping matches (on this installation the identifier of Default Layout).

## Which to choose

Keep both: the mapping is visible to editors, the setting is the safety net.

## Check

- A page of a class without its own mapping still has the site header.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
