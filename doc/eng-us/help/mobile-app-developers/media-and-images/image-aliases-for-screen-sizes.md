---
title: Image aliases for screen sizes
audience: mobile-app-developer, app-creator, developer, designer
category: mobile-apps
order: 520
summary: Get images in the right size for phones and tablets - the shipped image aliases (reference, small, tiny, medium, large, rss), listing them with expimage::aliases, asking for one alias URL with expimage::url, adding aliases for the app's cells and screen densities in image.ini, and caching.
keywords: image alias, image variation, image.ini, AliasList, reference, small, tiny, medium, large, rss, expimage::url, expimage::urls, expimage::aliases, absolute_url, screen density, retina, cache, mobile app
type: help
---
# Image aliases for screen sizes

This page is for app developers who want images that fit the screen, and for the designer or developer who adds
sizes for the app on the server. Exponential calls a named size an **image alias** and makes each one on demand from
the original.

## Contents

1. [The shipped aliases](#the-shipped-aliases)
2. [Asking for an alias URL](#asking-for-an-alias-url)
3. [Aliases for the app](#aliases-for-the-app)
4. [Densities and choosing a size](#densities-and-choosing-a-size)
5. [Caching](#caching)

## The shipped aliases

From `settings/image.ini [AliasSettings] AliasList[]` ([Images and image variations](../../creators/website-creators/images-and-image-variations.md#the-shipped-aliases)):

| Alias | Rule |
|---|---|
| `reference` | scaled down to fit 600 x 600; the base of the others |
| `small` | fits 100 x 100 |
| `tiny` | fits 30 x 30 |
| `medium` | fits 200 x 200 |
| `large` | fits 300 x 300 |
| `rss` | 88 x 31, for feeds |

Themes and sites add their own. To see what your installation has, call the public service:

```bash
curl -s 'https://example.com/ezjscore/call/expimage::aliases?ContentType=json'
```

It answers each alias with its name, width, height, reference and filters.

## Asking for an alias URL

```bash
curl -s -H "X-Exp-Token: $EXPSERVICES_TOKEN" \
     'https://example.com/ezjscore/call/expimage::url::<node id>::image::medium?ContentType=json'
```

Arguments: node id, the identifier of the image field, the alias name. The `data` holds `url`, `absolute_url`,
`width`, `height`, `mime_type`, `filename` and `filesize`. The alias file is generated when it is missing. An unknown
alias answers `404` "No image alias"; an image without that variation also answers `404`. The service needs
`content/read` on the node.

`expimage::urls::<node id>::<field identifier>` answers every configured alias at once. It makes every alias that does
not exist yet, so on a first call for a new image it does more work than you may need; prefer `url` for the one or two
sizes a screen shows.

The REST API itself gives no alias URLs ([Images in REST answers](images-in-rest-answers.md)). A list screen with one
image per row therefore needs one `expimage::url` call per item, or a back end or server function that adds the URL to
the list answer ([A back end for your app](../auth/a-backend-for-your-app.md)).

## Aliases for the app

The shipped sizes were made for web pages. Ask the site team for aliases named after the app's uses, in a design or
app extension's `image.ini.append.php`:

```ini
# extension/<your extension>/settings/image.ini.append.php
[AliasSettings]
AliasList[]=app_cell
AliasList[]=app_hero

[app_cell]
Reference=reference
Filters[]
Filters[]=geometry/scaledownonly=480;480

[app_hero]
Reference=
Filters[]
Filters[]=geometry/scalewidthdownonly=1600
```

The pattern and the advice to name aliases after their purpose come from
[Images and image variations](../../creators/website-creators/images-and-image-variations.md#adding-your-own-aliases);
the alias names and sizes above are examples. After a change the server needs its caches cleared
([After changing an alias](../../creators/website-creators/images-and-image-variations.md#after-changing-an-alias)).

## Densities and choosing a size

- Pick the alias whose width is at least the view's width in pixels times the screen scale (a 160-point cell on a 3x
  screen needs 480 pixels).
- `scaledownonly` never enlarges a small original, so an alias can be smaller than its rule; read `width` and `height`
  from the answer instead of assuming.
- Keep the number of aliases small: each one is a file per image on the server.

## Caching

Alias files are generated once and then served as files. In the app, cache images by their URL with your platform's
image cache. Cache the result of `expimage::url` with the item, and when the item's `dateModified` changes, ask for
the URL again and drop the old image from the cache if the URL is the same
([Caching in the app](../offline-and-performance/caching-in-the-app.md)).

## Related pages

- [Images in REST answers](images-in-rest-answers.md)
- [SVG and image formats](../../creators/website-creators/svg-and-image-formats.md)
- [expimage services (reference)](../../../bc/6.0/backend_ezjscore_services.md#images-and-aliases-expimage-18-services)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
