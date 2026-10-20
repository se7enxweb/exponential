---
title: Embed view
audience: designer, developer
category: templates
order: 530
summary: How objects embedded in rich text are drawn: the embed tag template, content_view_gui with view embed, the content/view/embed.tpl and node/view/embed.tpl defaults, alignment and classes from the editor, and per-class embed templates (images, videos, galleries).
keywords: embed, embed view, rich text embed, ezxmltags embed.tpl, content/view/embed.tpl, embed_node, object_parameters, align, classification, embedded image, embed override
type: help
---
# Embed view

This page is for designers who style content that editors embed in rich text: an image in an article, a related
article as a box, a video. The rich text's `embed` tag draws the object in the `embed` view mode.

## Contents

1. [The chain](#the-chain)
2. [What the embed template receives](#what-the-embed-template-receives)
3. [Per-class embeds](#per-class-embeds)
4. [Alignment and classes](#alignment-and-classes)

## The chain

1. The rich text tag template `content/datatype/view/ezxmltags/embed.tpl` wraps the object in a `<div>` with the
   alignment and class the editor chose, and calls
   `{content_view_gui view=$view content_object=$object object_parameters=$object_parameters ...}`.
   (`embed_node.tpl` does the same with `node_view_gui` when a node is embedded.)
2. `content/view/embed.tpl` draws the object; the kernel's default prints its name, linked to its main node.
3. Overrides on `Source=content/view/embed.tpl` give classes their own embed.

## What the embed template receives

`$object`, `$object_parameters` (from the editor: `align`, `id`, and custom attributes such as `size` for images),
`$link_parameters`, `$classification` (the class the editor chose), and `$view`.

## Per-class embeds

```ini
[mytheme_embed_image]
Source=content/view/embed.tpl
MatchFile=embed/image.tpl
Subdir=templates
Match[class_identifier]=image
```

```tpl
{* override/templates/embed/image.tpl *}
<figure class="embed-image">
    {attribute_view_gui attribute=$object.data_map.image image_class=first_set( $object_parameters.size, 'medium' )}
    {if $object.data_map.caption.has_content}
        <figcaption>{attribute_view_gui attribute=$object.data_map.caption}</figcaption>
    {/if}
</figure>
```

The media design has its embed overrides in `override/templates/embed/`. `content.ini [embed] CustomAttributes[]`
lists extra attributes editors may set (the shipped settings add `gallery_view_mode` for embedded galleries).

## Alignment and classes

The wrapper's classes are `object-left`, `object-right`, `object-center` from `align`, plus the editor's class.
Style them in your stylesheet (with logical properties for left and right in right-to-left pages,
see [Logical CSS properties](../theme-design/logical-css-properties.md)).

## Related pages

- [Embed inline view](embed-inline-view.md)
- [Rich text templates](rich-text-templates.md)
- [content_view_gui](content-view-gui.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
