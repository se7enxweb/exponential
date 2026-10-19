---
title: "A call-to-action button"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1105
summary: The Button / Link block (Text, Style, Target, Use link), or a link in a template.
keywords: button, call to action, CTA, Button / Link block, link, target
type: help
---
# A call-to-action button

One button that leads to the sign-up page.

*Life cycle: Build, example 6 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Button / Link** (group *Basic*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Text**, **Style** (default `default_button`), **Target** (default `_self`) and **Use link** with the link.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The media design draws it with its own `explayouts/block/button.tpl`.

## With templates

1. In any template of your design extension:

```smarty
<a class="btn btn-primary" href={'/user/register'|ezurl}>{'Sign up'|i18n( 'mysite' )}</a>
```

## Which to choose

Buttons that change with campaigns: the block. A button that belongs to the page design (always there): the template.

## Check

- The link opens the right page and, with Target `_blank`, a new window.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
