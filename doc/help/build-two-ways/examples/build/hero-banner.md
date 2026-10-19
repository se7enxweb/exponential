---
title: "A hero banner at the top of a page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1100
summary: A large image with a title, a sentence and a button: the Hero block with its six fields, or a template block with the same markup.
keywords: hero, banner, Hero block, image, call to action, button, landing page, template block
type: help
---
# A hero banner at the top of a page

A hero banner opens a landing page: a picture, a title, one sentence and a button to the next step.

*Life cycle: Build, example 1 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Hero** (group *Components*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Title**, **Content**, **Image URL**, **Image alt text**, **Link text** and **Link URL**; **CSS class** and **CSS ID** are optional.
4. **Publish layout**. The pages that use the layout show the block on their next view.

The Hero renders with `explayouts/block/hero.tpl` of Layouts' standard design. The complete walkthrough with screenshots is [Build a campaign page, start to finish](../../build-a-campaign-page.md).

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/hero_spring.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `hero_spring` ([Your own template blocks](../../template-blocks.md)).
2. Link the image and the text to content instead of typing them: fetch the node and print its fields.

```smarty
{def $hero = fetch( 'content', 'node', hash( 'node_id', 92 ) )}
<section class="container hero-spring">
    <h2>{$hero.name|wash}</h2>
    {attribute_view_gui attribute=$hero.data_map.full_intro}
    <a class="btn" href={$hero.url_alias|ezurl}>{'See the plans'|i18n( 'mysite' )}</a>
</section>
{undef $hero}
```

## Which to choose

Editors change campaign heroes often: use the Hero block. Use the template when the hero must always show one content item and follow its changes.

## Check

- The page source has `<div class="ngl-block ngl-hero ...">` (block) or your `hero-spring` section (template).
- The image has the alternative text you entered.

## Related pages

- [Build a campaign page, start to finish](../../build-a-campaign-page.md)
- [Your own template blocks](../../template-blocks.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
