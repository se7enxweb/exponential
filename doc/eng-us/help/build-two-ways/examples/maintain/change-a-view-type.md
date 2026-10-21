---
title: "Change a list from grid to list"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1317
summary: View type on the Design tab; before explayouts_ui_api 1.3.29 the change was stored only with the next change of another field.
keywords: view type, grid to list, Design tab, quirk, maintenance
type: help
---
# Change a list from grid to list

The recipe grid becomes a plain list.

*Life cycle: Maintain, example 18 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Select the List, **Design**, **View type** `list`.
2. The change is saved at once (before explayouts_ui_api 1.3.29: change another field once, such as a character typed and deleted in **CSS class**, so the form is saved).
3. Check the block header shows `LIST`, **Publish layout**.

## With templates

1. Not a template task.

## Which to choose

Design option; on explayouts_ui_api before 1.3.29 mind the save quirk.

## Check

- The block header shows the new view type before publishing.

## Related pages

- [Known editor behaviors](../../known-editor-behaviors.md)
- [The Design tab](../../design-tab-options.md)
- [Maintain examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
