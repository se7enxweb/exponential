---
title: "A section heading with the right level"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1106
summary: The Title block (Title, Heading level 1-6, HTML tag, Use link) for headings between blocks.
keywords: title, heading, h2, heading level, Title block, section title, accessibility
type: help
---
# A section heading with the right level

"Most popular articles" above a list, as an `h2`, so the page keeps one `h1`.

*Life cycle: Build, example 7 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Prefooter*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Title** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Title**, **Heading level (1-6)** (default 2) and, if needed, **HTML tag** and **Use link**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The Prefooter's own title uses the view `section_centered` and `tag = h2`; the media design dispatches titles by view type to `explayouts/block/title/<view type>.tpl`.

## With templates

1. Write the heading in the template that renders the section: `<h2 class="title">{'Most popular articles'|i18n( 'mysite' )}</h2>`.

## Which to choose

The block when editors arrange sections; in a template when the heading belongs to a fixed component.

## Check

- The page has one `h1` and the section titles are `h2` (an accessibility checker shows the outline).

## Related pages

- [How a block maps to its templates](../../block-to-template-map.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
