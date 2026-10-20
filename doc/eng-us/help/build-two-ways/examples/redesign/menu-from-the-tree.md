---
title: "Replace the fixed header menu by the tree"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1404
summary: Override pagelayout/header/main_menu.tpl to list the children of the root node by class; tested code.
keywords: dynamic menu, main_menu.tpl, override, tree, redesign
type: help
---
# Replace the fixed header menu by the tree

Sections join the menu by being published.

*Life cycle: Redesign, example 5 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

Nothing in the Layouts editor; `MainMenuID[]` is no longer read by your template.

## With templates

1. Follow [A header menu that follows the tree](../../header-menu-from-the-tree.md): settings `[MySiteMenu]`, the template, the activation order, the cache clear.

## Which to choose

Decide the length limit with the design.

## Check

- A new folder below the root appears in the menu.

## Related pages

- [A header menu that follows the tree](../../header-menu-from-the-tree.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
