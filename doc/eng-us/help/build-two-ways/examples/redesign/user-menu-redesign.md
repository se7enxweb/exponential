---
title: "Change the header's user menu"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1423
summary: The user menu (Sign in, Register, basket, administration ...) is pagelayout/header/user_menu.tpl and menu.ini [HeaderUserMenu].
keywords: user menu, HeaderUserMenu, header, override, redesign
type: help
---
# Change the header's user menu

No basket entry, a "My plans" entry instead.

*Life cycle: Redesign, example 24 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change.

## With templates

1. Settings first: `[HeaderUserMenu] ShowBasket` and the other switches of `menu.ini`.
2. For new entries, override `pagelayout/header/user_menu.tpl`.

## Which to choose

Settings for switches, template for new entries.

## Check

- The menu shows the planned entries, signed in and out.

## Related pages

- [How the header is built](../../how-the-header-is-built.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
