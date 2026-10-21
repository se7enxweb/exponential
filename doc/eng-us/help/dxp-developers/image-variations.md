---
title: Image variations
audience: dxp-developer
category: dxp-development
order: 23
summary: How the DXP serves images in sizes - variations defined per siteaccess scope, generated on demand by Liip Imagine, used from Twig, cleared after a change - plus the console commands for originals and storage paths.
keywords: image variations, image aliases, Liip Imagine, image_variations, geometry/scaledownonly, ibexa_image_alias, liip:imagine:cache:remove, resize-original, normalize-paths, ImageMagick
type: help
---
# Image variations

This page is for developers who define how images are cropped and scaled. On the DXP an image field stores the
original; variations (sizes) are generated on demand by Liip Imagine the first time a template asks for them
(skeleton guide, section 13). The idea matches image aliases in Exponential 6.

## Contents

1. [Defining variations](#defining-variations)
2. [Using them in Twig](#using-them-in-twig)
3. [Clearing generated variations](#clearing-generated-variations)
4. [Working with originals](#working-with-originals)
5. [Advice](#advice)

## Defining variations

Variations are a siteaccess-aware setting under `ibexa.system.<scope>.image_variations`. The guide's example:

```yaml
ibexa:
    system:
        site_group:
            image_variations:
                small:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [160, 120] }
                medium:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [480, 360] }
                large:
                    reference: ~
                    filters:
                        - { name: geometry/scaledownonly, params: [960, 720] }
```

`reference` lets a variation start from another variation instead of the original. ImageMagick is optional; its
path is `IMAGEMAGICK_PATH` ([Environment variables](environment-variables.md#search-cache-mail-and-the-rest)).

## Using them in Twig

```twig
{{ ibexa_render_field(content, 'image', { parameters: { alias: 'medium' } }) }}

{% set variation = ibexa_image_alias(content.getField('image'), content.versionInfo, 'large') %}
{% if variation %}
    <img src="{{ variation.uri }}" width="{{ variation.width }}" height="{{ variation.height }}" alt="">
{% endif %}
```

Give every image its width and height so the page does not shift while it loads, and real alternative text from a
field where the image carries meaning.

## Clearing generated variations

After you change a variation's filters, remove the generated files so they are built again:

```bash
php bin/console liip:imagine:cache:remove                  # all variations
php bin/console liip:imagine:cache:remove --filter=small   # one variation
php bin/console cache:clear
```

## Working with originals

| Command | Use |
|---|---|
| `exponential:images:resize-original <field> <content type> --filter=<variation>` | Batch-resize stored originals, for example after editors uploaded camera-size images |
| `exponential:images:normalize-paths` | Fix image field storage paths |
| `exponential:io:migrate-files` | Move binary files between storage handlers |

`resize-original` changes stored files: run it on a copy first ([Console commands](console-commands-and-renames.md)).

## Advice

- Define a small set of variations that match the design's breakpoints; each one costs disk space for every image.
- Generated variations live under `public/var/`; the web server must write there ([File permissions](file-permissions.md)).
- A 404 for a variation after a database conversion usually means rows are missing
  ([Databases, After a conversion](databases-mysql-postgresql-sqlite.md#after-a-conversion)).

## Related pages

- [Twig functions you use daily](twig-functions-you-use-daily.md#images)
- [Image aliases in Exponential 6 themes](../designers/theme-design/image-aliases.md)
- [Image performance](../designers/theme-design/image-performance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
