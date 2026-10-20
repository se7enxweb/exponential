---
title: "Rebuild the footer markup"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1403
summary: Override pagelayout/footer.tpl; keep its data sources (FooterMenuID, site info, Footer links) or change them.
keywords: footer, pagelayout/footer.tpl, override, redesign
type: help
---
# Rebuild the footer markup

A footer in three columns: menu, social links, address.

*Life cycle: Redesign, example 4 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The footer template block of Header / Footer includes your template.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/pagelayout/footer.tpl` to `extension/mysite/design/media/templates/pagelayout/footer.tpl`.
2. Keep the three parts' code (the `ezini( 'SiteInfo', 'FooterMenuID', 'menu.ini' )` loop, the site info fields, the Footer links fetch) and move them into columns.
3. Clear **Template & content** and check a page that uses it.

## Which to choose

Templates.

## Check

- Both link lines and the social icons are still there.

## Related pages

- [How the footer is built](../../how-the-footer-is-built.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
