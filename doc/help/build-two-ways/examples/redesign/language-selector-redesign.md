---
title: "Change the language selector"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1422
summary: Override pagelayout/header/language_selector.tpl in your design extension.
keywords: language selector, header, override, multilingual, redesign
type: help
---
# Change the language selector

Languages as flags instead of a list.

*Life cycle: Redesign, example 23 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/pagelayout/header/language_selector.tpl` to `extension/mysite/design/media/templates/pagelayout/header/language_selector.tpl`.
2. Change the markup.
3. Clear **Template & content** and check a page that uses it.

## Which to choose

Templates.

## Check

- The selector switches siteaccess as before.

## Related pages

- [How the header is built](../../how-the-header-is-built.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
