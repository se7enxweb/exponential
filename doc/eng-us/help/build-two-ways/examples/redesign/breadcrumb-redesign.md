---
title: "Change the breadcrumb"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1420
summary: The breadcrumb is a template block in the header zone, rendered by pagelayout/breadcrumbs.tpl; override it.
keywords: breadcrumb, pagelayout/breadcrumbs.tpl, template block, override, redesign
type: help
---
# Change the breadcrumb

A breadcrumb with the current page in bold and a schema.org list.

*Life cycle: Redesign, example 21 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. The breadcrumb template block of Header / Footer (inside a Column with background `primary`) stays.

## With templates

1. Copy `extension/sevenx_themes_media/design/media/templates/pagelayout/breadcrumbs.tpl` to `extension/mysite/design/media/templates/pagelayout/breadcrumbs.tpl`.
2. Change the markup; it reads the path from `$path_array`, which `pagelayout.tpl` builds from the page's ancestors.
3. Clear **Template & content** and check a page that uses it.

## Which to choose

Templates.

## Check

- Every page shows the new breadcrumb.

## Related pages

- [How the header is built](../../how-the-header-is-built.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
