---
title: "Plan how items look in lists"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1007
summary: Item view types (Standard, Overlay, Card, Line, Mini, List item, with or without intro) decide each item's look; plan them per list.
keywords: item view type, card, line, overlay, mini, list item, content views
type: help
---
# Plan how items look in lists

The same article looks right as a card on the home page and as a line in the sidebar.

*Life cycle: Plan, example 8 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. On the **Design** tab of a list, **Item view type** offers Standard, Standard (with intro), Overlay, Card, Card (with intro), Line, Mini, List item and List item (with intro).

## With templates

1. Each item view is a template under `content/views/` of the media design (`standard`, `overlay`, `card`, `line`, `mini`, `listitem` ...); a new look per class is `content/views/<view>/<class>.tpl` in your extension.

## Which to choose

Choose from the existing views first; add a view in the template way only when none fits.

## Check

- Every list in the plan has its item view type.

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
