---
title: "Change the logo"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1425
summary: The logo is content/parts/site_logo.tpl and the site info object's site_logo field.
keywords: logo, site_logo, site info, rebrand, redesign
type: help
---
# Change the logo

The new logo in header and footer.

*Life cycle: Redesign, example 26 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

No layout change.

## With templates

1. `content/parts/site_logo.tpl` of the media design reads the site info object's **site_logo** field: upload the new logo there and publish; override the template only for new markup.

## Which to choose

Content if the template reads the field; template otherwise.

## Check

- Header and footer show the new logo.

## Related pages

- [How the header is built](../../how-the-header-is-built.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
