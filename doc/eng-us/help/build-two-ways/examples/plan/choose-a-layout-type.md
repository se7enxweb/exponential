---
title: "Choose the layout type for a page type"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1001
summary: The layout type fixes the zones: Header / Main / Footer (layout_2) for most Fit & Healthy pages, Header / Left / Right / Footer (layout_4) for a sidebar.
keywords: layout type, zones, layout_2, layout_4, Header / Main / Footer, sidebar, planning
type: help
---
# Choose the layout type for a page type

Pick the zones a new page type needs before creating its layout.

*Life cycle: Plan, example 2 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. **Layouts > Layouts > New layout** shows the types: 1 column, 2 columns, 3 columns, 4 columns, Hero + 3 columns, Sidebar left, Sidebar right, Featured, Mosaic, Single zone, Header / Main / Footer, Header / Left / Right / Footer.
2. On the media theme, choose **Header / Main / Footer** or **Header / Left / Right / Footer**: the theme's `explayouts/layout.tpl` places the zones `header` and `footer` around `<main>` and arranges `left` and `right` for `layout_4`.

## With templates

1. The zones of each type are in `extension/explayouts/settings/explayouts.ini.append.php`, `[LayoutType_<id>] Zones[]`; a new type is a new block there plus its template.

## Which to choose

Use the two types the theme draws; a new type needs template work in the theme's layout template too.

## Check

- The new layout shows the zones you expect in the editor.

## Related pages

- [Build a campaign page, start to finish](../../build-a-campaign-page.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
