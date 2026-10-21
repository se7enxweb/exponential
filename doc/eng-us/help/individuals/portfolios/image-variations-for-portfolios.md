---
title: Image variations for portfolios
audience: individual, website-creator
category: personal-sites
order: 1614
summary: What image variations (aliases) are, which ones ship in settings/image.ini, how a portfolio uses them for thumbnails, gallery views and large views, how to add a larger alias for your work, quality and formats, and what to do after changing one.
keywords: image variations, image alias, image.ini, reference, small, medium, large, original, thumbnails, quality, WebP, responsive images, portfolio, ImageMagick, GD, cache
type: help
---
# Image variations for portfolios

This page is for individuals whose site is mostly images and who want them sharp and quick to load. You upload one
file; Exponential makes smaller copies, called **image variations** or **aliases**, for each place the image
appears. The builder's page with every detail is
[Images and image variations](../../creators/website-creators/images-and-image-variations.md).

## Contents

1. [How it works](#how-it-works)
2. [The shipped aliases](#the-shipped-aliases)
3. [What a portfolio needs](#what-a-portfolio-needs)
4. [Adding a larger alias](#adding-a-larger-alias)
5. [Quality and formats](#quality-and-formats)
6. [After changing an alias](#after-changing-an-alias)
7. [Checklist](#checklist)

## How it works

- You upload the original. It is stored as it is (the `original` alias).
- When a template asks for an alias, the site makes that copy once from the original and keeps it.
- Every page shows the copy that fits: a thumbnail in a list, a medium size in a gallery, a large one on the
  image's own page.

So you never resize by hand for each place, and visitors on a phone do not download a large original for a thumbnail.

## The shipped aliases

From `[AliasSettings] AliasList[]` in `settings/image.ini`:

| Alias | Size | Typical use |
|---|---|---|
| `reference` | fits 600 x 600; the base of the others | Large view in older designs |
| `small` | 100 x 100 | List thumbnails |
| `tiny` | 30 x 30 | Icons |
| `medium` | 200 x 200 | Gallery grids |
| `large` | 300 x 300 | Article images |
| `rss` | 88 x 31 | RSS feeds |
| `original` | the uploaded file | Downloads |

Images are only scaled down, never enlarged. Themes add their own aliases in their `image.ini.append.php`, so your
site may have more; the builder's page shows how to list them.

## What a portfolio needs

The shipped sizes were chosen for text sites. For photography or art, 600 pixels is small on a modern screen. Ask
yourself:

| Question | If yes |
|---|---|
| Do visitors look at single works full screen? | Add a larger alias for the image's full view |
| Do you show a grid of many thumbnails? | Square-ish thumbnails of the same size look calmer |
| Are your originals very large? | Fine; but do not link the original if you do not want it copied |

## Adding a larger alias

Aliases are settings in a design extension, not something you change in the administration. A simple example, in
your own extension's `settings/image.ini.append.php`:

```ini
[AliasSettings]
AliasList[]=portfolio_large

[portfolio_large]
Reference=
Filters[]
Filters[]=geometry/scaledownonly=1600;1600
```

Then a template uses it, for example `{attribute_view_gui attribute=$node.data_map.image image_class='portfolio_large'}`.
JPEG quality is 75 by default (`[MIMETypeSettings] Quality[]`); photographers sometimes raise it. Changing templates is website-creator
work; see [Images and image variations, Adding your own aliases](../../creators/website-creators/images-and-image-variations.md#adding-your-own-aliases)
and [Template overrides](../../creators/website-creators/template-overrides.md). If that is beyond you, this is a
small, well-defined job for a partner ([When to hire help](../running-it/when-to-hire-help.md)).

## Quality and formats

- The server converts with GD first and ImageMagick for formats GD cannot read
  ([Image processing and media](../../hosting/basics/image-processing-and-media.md)).
- JPEG suits photographs, PNG suits drawings with flat colors and transparency.
- Output formats and quality are settings; see
  [Images and image variations, Converters, formats and quality](../../creators/website-creators/images-and-image-variations.md#converters-formats-and-quality)
  and [SVG and image formats](../../creators/website-creators/svg-and-image-formats.md).
- Responsive images (several sizes for different screens) are a template matter
  ([Responsive images](../../creators/website-creators/images-and-image-variations.md#responsive-images)).

## After changing an alias

Copies already made are not remade by themselves. After you change an alias definition, the existing variations
must be cleared so they are made again with the new rule, then the content view cache
(`php bin/php/cache.php imagealias clear --allow-root-user`; see
[After changing an alias](../../creators/website-creators/images-and-image-variations.md#after-changing-an-alias)).
Plan it for a quiet moment: the first visits after the clear are slower while the copies are made.

## Checklist

- [ ] You know which alias each page of your portfolio uses.
- [ ] Single works are shown large enough for your kind of work.
- [ ] Thumbnails in a grid have a consistent size.
- [ ] Originals are only linked where you want them downloaded.
- [ ] After any alias change, old variations were cleared.

## Related pages

- [Portfolios](README.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Performance and caching](../../creators/website-creators/performance-and-caching.md)
- [Image and file guidelines](../../customers/day-to-day/image-and-file-guidelines.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
