---
title: "An embed code from another service"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1109
summary: The HTML snippet block (Raw HTML) for a widget code, and what to watch out for; or the code in a template.
keywords: HTML snippet, embed, widget, raw HTML, iframe, script, security, CSP
type: help
---
# An embed code from another service

A widget from another service (a booking form, a feed) that comes as a piece of HTML.

*Life cycle: Build, example 10 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **HTML snippet** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Raw HTML**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The HTML is printed as it is. Only give the editor role this block when the people using it may publish raw HTML; scripts in it are subject to the site's Content Security Policy.

## With templates

1. Put the code in a template of your design extension (for example a template block template), where it is reviewed with the code.

## Which to choose

One-off embeds by trusted editors: the block. Anything that runs on many pages or loads scripts: a template.

## Check

- The widget appears and the browser console shows no Content Security Policy errors.

## Related pages

- [Your own template blocks](../../template-blocks.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
