---
title: Images in a design
audience: designer
category: theme-design
order: 260
summary: The two kinds of images a designer handles: design images (logos, icons, backgrounds) found with ezimage and ezdesign, and content images uploaded by editors and shown in aliases; how to link each, quoting, and alt text.
keywords: images, design images, ezimage, ezdesign, logo, background image, content images, image alias, alt text, img tag, quotes, url in css
type: help
---
# Images in a design

This page is for designers who put images on pages. It answers: where design images go and how to link them, how
content images differ, and the attributes every image needs.

## Contents

1. [Two kinds of images](#two-kinds-of-images)
2. [Design images with ezimage](#design-images-with-ezimage)
3. [Any design file with ezdesign](#any-design-file-with-ezdesign)
4. [Images in CSS](#images-in-css)
5. [Content images](#content-images)
6. [Alt text and size](#alt-text-and-size)

## Two kinds of images

| | Design images | Content images |
|---|---|---|
| Who adds them | the designer, in the design | editors, in the administration |
| Where | `design/<design>/images/` | the content repository (`var/<site>/storage/images/`) |
| How linked | `ezimage` / `ezdesign` operators | the image field, drawn in an alias |
| Examples | logo, icons, background patterns | article photos, gallery images, banners |

A logo editors must be able to change is content (an image field of a site settings object), not a design image.

## Design images with ezimage

`ezimage` looks for the file under `images/` in every design base of the cascade and returns its URL in double quotes:

```tpl
<img src={'logo.svg'|ezimage} alt="{$site.title|wash}" width="160" height="40">
```

Note: no quotes around `{...}` in `src=`; the operator adds them. The quote style is its first parameter:

| Call | Output |
|---|---|
| `'logo.svg'\|ezimage` | `"/extension/mytheme/design/mytheme/images/logo.svg"` |
| `'logo.svg'\|ezimage( 'single' )` | the same in single quotes |
| `'logo.svg'\|ezimage( 'no' )` | no quotes, for use inside a string or a `data-` value you quote yourself |

A file that is not found is reported as a template warning ("Image '...' does not exist in any design").

## Any design file with ezdesign

`ezdesign` works the same way for any path inside a design base:

```tpl
<link rel="icon" href={'images/favicon/favicon.svg'|ezdesign} type="image/svg+xml">
```

Prefer `ezcss_load` for stylesheets; `ezdesign` is for single files that are not packed (icons, manifests,
downloads that ship with the design).

## Images in CSS

Use relative paths from the stylesheet to the image:

```css
/* design/mytheme/stylesheets/mytheme.css */
.site-header { background-image: url(../images/header-pattern.svg); }
```

The packer rewrites the relative path when it moves the stylesheet into the cache, so the image is still found. The
image must be in your design (relative paths are not looked up through the cascade).

## Content images

An image field is drawn through its datatype template, in an **alias** (a size defined in `image.ini`):

```tpl
{attribute_view_gui attribute=$node.data_map.image image_class='medium'}
```

or, when you need the URL yourself:

```tpl
{def $image = $node.data_map.image.content['large']}
<img src={$image.url|ezroot} width="{$image.width}" height="{$image.height}" alt="{$image.alternative_text|wash}">
{undef $image}
```

See [Image aliases](image-aliases.md).

## Alt text and size

- Every `<img>` has an `alt`. Decorative images get `alt=""`; images that carry meaning get a short description. For
  content images, the image field's alternative text is the editor's job; print it with `|wash`.
- Give `width` and `height` (or a CSS `aspect-ratio`), so the page does not jump while images load
  ([Measuring CLS and LCP](measuring-cls-and-lcp.md)).
- Use SVG for logos and icons, modern formats for photos ([SVG images](svg-images.md), [Image performance](image-performance.md)).

## Related pages

- [Image aliases](image-aliases.md)
- [Icons in a design](icons-in-a-design.md)
- [URL operators](../templates/url-operators.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
