---
title: "A wall of partner logos from a folder"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1103
summary: The Logos block reads images from a parent node (Parent node ID, Limit, Class filter, Image attribute identifier, Thumbnail size alias); or a fetch in a template.
keywords: logos, partners, clients, Logos block, parent node, image attribute, thumbnail size
type: help
---
# A wall of partner logos from a folder

Show the logos of partners kept as images in one folder.

*Life cycle: Build, example 4 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Open **Layouts > Layouts** and the layout *Landing Page / Home*; the editor opens it in a draft.
2. Open the block palette (**+** at the top left) and drag **Logos** (group *Components*) into the zone `main`.
3. Select the block, open the **Design** tab and fill **Parent node ID** (the folder with the logos), **Limit** (default 12), **Class filter** (for example `image`), **Image attribute identifier** (default `image`) and **Thumbnail size alias** (default `small`).
4. **Publish layout**. The pages that use the layout show the block on their next view.

The Media library of the site has a folder **Clients & Partners** for such images.

## With templates

1. Write `extension/mysite/design/media/templates/explayouts/tpl_block/partner_logos.tpl` in your design extension, clear the template cache, and place it with a **Template block** whose **Template name** is `partner_logos` ([Your own template blocks](../../template-blocks.md)).

```smarty
{def $logos = fetch( 'content', 'list', hash( 'parent_node_id', <folder node id>, 'class_filter_type', 'include', 'class_filter_array', array( 'image' ), 'limit', 12 ) )}
<ul class="container partner-logos">
{foreach $logos as $logo}
    <li>{attribute_view_gui attribute=$logo.data_map.image image_class=small}</li>
{/foreach}
</ul>
{undef $logos}
```

## Which to choose

Both read the folder, so editors add a logo by uploading an image there. The block needs no code.

## Check

- A new image in the folder appears after publishing it.

## Related pages

- [The Design tab](../../design-tab-options.md)
- [Build examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
