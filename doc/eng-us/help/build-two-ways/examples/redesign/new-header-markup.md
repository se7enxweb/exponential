---
title: "Rebuild the header markup"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1402
summary: Override pagelayout/header.tpl (and its parts) in your design extension; the shared layout keeps placing it.
keywords: header, pagelayout/header.tpl, override, redesign, logo, search
type: help
---
# Rebuild the header markup

A new header with the logo centered and the search as a full-width bar.

*Life cycle: Redesign, example 3 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change: the header template block of Header / Footer includes `pagelayout/header.tpl` ([How the header is built](../../how-the-header-is-built.md)).

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/pagelayout/header.tpl` to `extension/mysite/design/media/templates/pagelayout/header.tpl`.
2. Rearrange the includes (`content/parts/site_logo.tpl`, `pagelayout/header/main_menu.tpl`, `search_box.tpl`, `user_menu.tpl`, `language_selector.tpl`) or write new markup; keep the menu include unless the menu changes too.
3. Clear **Template & content** and check a page that uses it.

## Which to choose

The header is design: templates.

## Check

- Every page, with and without a layout, shows the new header.

## Related pages

- [How the header is built](../../how-the-header-is-built.md)
- [Your own design extension](../../your-own-design-extension.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
