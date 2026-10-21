---
title: Images in REST answers
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 510
summary: What an ezimage field looks like in a REST answer (the path of the original file, without alternative text), how to turn it into a URL, why the original is the wrong size for a phone, how to get the alternative text and image information from expimage, and how image objects differ from image fields.
keywords: images, ezimage, original, path, URL, alternative text, alt, expimage::info, expimage::alt, expimage::attributes, image object, image field, mobile app
type: help
---
# Images in REST answers

This page is for app developers who display images from Exponential content. It explains exactly what the REST API
gives for an image field and what to do with it.

## Contents

1. [The value of an image field](#the-value-of-an-image-field)
2. [From path to URL](#from-path-to-url)
3. [Why not to load the original](#why-not-to-load-the-original)
4. [Alternative text and image information](#alternative-text-and-image-information)
5. [Image objects and image fields](#image-objects-and-image-fields)

## The value of an image field

For a field of the datatype `ezimage`, the REST `value` is the string form of the image (`eZImageType::toString()`
gives `<url of the original>|<alternative text>`) cut at the `|` by `ezpRestContentModel`. So the app receives the
**path of the original image file** as the image datatype stores it, and **not** the alternative text. An empty
image field gives an empty value.

```json
"image": {"type": "ezimage", "identifier": "image", "value": "<path of the original file>", "id": 0, "classattribute_id": 0}
```

(The values are placeholders.) No image alias, width or height is in the REST answer.

## From path to URL

The path is relative to the site root. Join it with the installation's base address:

```text
https://example.com/ + <path>
```

Image files are files on the server; whether the web server delivers them without a credential depends on the
installation and its hosting configuration. Check one URL from a signed-out client before you design the app's
image loading around it. The `expimage` services answer an `absolute_url` for each variation, which saves the
joining step ([Image aliases for screen sizes](image-aliases-for-screen-sizes.md)).

## Why not to load the original

The original is whatever the editor uploaded: often several megabytes and thousands of pixels wide. Loading it in a
list costs the user's data plan and memory. Ask for a named size (an image alias) instead, which the server makes on
demand and stores ([Image aliases for screen sizes](image-aliases-for-screen-sizes.md)). Use the original only for a
full-screen zoom view, if at all.

## Alternative text and image information

| Need | Service | Arguments |
|---|---|---|
| Alternative text for screen readers | `expimage::alt` | node id, field identifier |
| File name, size, dimensions, MIME type, alternative text of the original | `expimage::info` | node id, field identifier |
| The image fields of a node and whether they have content | `expimage::attributes` | node id |

All three need `content/read` on the node. Always give images an accessibility label in the app; app store reviewers
and users with screen readers notice ([App store review readiness](../checklists/app-store-review-readiness.md)).

## Image objects and image fields

Exponential has image **fields** inside other classes (an article's image) and image **objects** (items of the class
`image` in the media library, often related to or embedded in articles). For an image object, read it like any node;
its `image` field holds the picture. To list image objects below a folder with one alias each, use
`expimage::list::<parent node id>::<limit>::<offset>::<alias>`.

## Related pages

- [Image aliases for screen sizes](image-aliases-for-screen-sizes.md)
- [Fields and field values](../rest-api/fields-and-field-values.md)
- [Images and image variations](../../creators/website-creators/images-and-image-variations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
