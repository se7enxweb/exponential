---
title: "An alert banner"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1112
summary: The Alert block (Message, Type, Dismissible) for a short notice, or a template block.
keywords: alert, notice, banner, Alert block, info, warning, dismissible
type: help
---
# An alert banner

A one-line notice: "Our gym is closed on Monday."

*Life cycle: Build, example 13 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Alert** (group *Basic*) into the zone `post_header`.
3. Select the block, open the **Design** tab and fill **Message**, **Type** (default `info`) and **Dismissible**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The standard template writes `<div class="slb slb-alert alert alert-<type>">`. For the same alert on every page, put it in a shared layout's zone and link that zone from the layouts: the Fit & Healthy layouts link only `header` and `footer` of Header / Footer, so its `post_header` is not shown anywhere yet.

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/closure_notice.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `closure_notice` ([Your own template blocks](../../template-blocks.md)).

## Which to choose

A notice editors switch on and off: the block. A notice driven by data (opening hours from a content item): the template.

## Check

- The alert appears on the pages whose layouts include it.

## Related pages

- [Shared layouts and zone linking](../../shared-layouts-and-zone-linking.md)
- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
