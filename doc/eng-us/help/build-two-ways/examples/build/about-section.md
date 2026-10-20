---
title: "An about-us section with picture and text"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1101
summary: Title, text, image and link side by side: the About block, or the same markup in a template block.
keywords: about, about us, About block, image and text, company, team, components
type: help
---
# An about-us section with picture and text

An "about us" band: who you are in two sentences, a picture and a link to the full page.

*Life cycle: Build, example 2 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **About** (group *Components*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Title**, **Content**, **Image URL**, **Image alt text**, **Link text**, **Link URL**.
4. **Publish layout**. The pages that use the layout show the block on their next view.

About, Hero, Features and Lead share one handler (`expLayoutsComponentBlockHandler`) and so the same eight fields; each type has its own template.

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/about_us.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `about_us` ([Your own template blocks](../../template-blocks.md)).
2. Read the text from a content page so it is edited where it belongs, for example an `ng_landing_page` "About us" with `title`, `full_intro` and `image`.

```smarty
{def $about = fetch( 'content', 'node', hash( 'node_id', <node id of About us> ) )}
<section class="container about-us">
    <h2>{$about.name|wash}</h2>
    {attribute_view_gui attribute=$about.data_map.full_intro}
    {attribute_view_gui attribute=$about.data_map.image}
</section>
{undef $about}
```

## Which to choose

A fixed text that rarely changes fits either way; the block keeps it with the layout, the template keeps it in git.

## Check

- `ngl-about` on the wrapper (block).
- The link goes to the full page.

## Related pages

- [Your own template blocks](../../template-blocks.md)
- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
