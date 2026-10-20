---
title: "Space and a line between sections"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1111
summary: The Spacer (Height in pixels, default 32) and Divider (Style, Margin) blocks, or vertical whitespace on the blocks themselves.
keywords: spacer, divider, whitespace, margin, line, separator, vertical whitespace
type: help
---
# Space and a line between sections

Room between two sections, and a thin line.

*Life cycle: Build, example 12 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Spacer** (group *Basic*) into the zone `main`. **Height in pixels** (default 32).
3. Open the block palette (**+** at the top left) and drag **Divider** (group *Basic*) into the zone `main`. **Style** (default `solid`) and **Margin in pixels** (default 16).
4. **Publish layout**. The pages that use the layout show the block on their next view.

Often better: **Use vertical whitespace** on the Design tab of the blocks themselves (On top, On bottom), which writes `whitespace-top-<size>` classes the design styles.

## With templates

1. Add the spacing in the stylesheet of your design extension, on the classes of the blocks ([Styling your blocks](../../styling-your-blocks.md)).

## Which to choose

Spacing is design: prefer the whitespace option or CSS; the Spacer block for a one-off.

## Check

- The spacing is the same at phone width.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Styling your blocks](../../styling-your-blocks.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
