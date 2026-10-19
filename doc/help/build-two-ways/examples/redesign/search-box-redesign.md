---
title: "Change the header search"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1424
summary: Override pagelayout/header/search_box.tpl; the form posts to /content/search.
keywords: search box, header, override, redesign
type: help
---
# Change the header search

A larger search field with a placeholder text.

*Life cycle: Redesign, example 25 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/pagelayout/header/search_box.tpl` to `extension/mysite/design/media/templates/pagelayout/header/search_box.tpl`.
2. Keep `name="SearchText"` and the action `/content/search`.
3. Clear **Template & content** and check a page that uses it.

## Which to choose

Templates.

## Check

- Searching from the header still lands on the results page.

## Related pages

- [How the header is built](../../how-the-header-is-built.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
