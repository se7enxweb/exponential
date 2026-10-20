---
title: Image operators
audience: developer, designer, app-creator
category: development
order: 270
summary: texttoimage, image and imagefile from eZTemplateImageOperator - rendering text as an image with a TrueType font from texttoimage.ini, composing layers, and reading an image file's size - which ones write files under var/cache/texttoimage, and examples from alpha.
keywords: texttoimage, image, imagefile, GD, font, texttoimage.ini, eZImageLayer, eZImageTextLayer, eZImageObject, eZTemplateImageOperator
type: help
---
# Image operators

Three operators of `eZTemplateImageOperator` (`lib/eztemplate/classes/eztemplateimageoperator.php`), registered
through `lib/eztemplate/classes/eztemplateautoload.php`, work with images through PHP's GD extension (the class checks
for `ImageTTFBBox`, `ImageCreate`, `ImageTTFText`, `ImagePNG` and the other functions it needs, and logs an error
instead of working when one is missing):

- `texttoimage` renders a text as a PNG image with a TrueType font;
- `image` combines layers into one image;
- `imagefile` reads an existing image file.

They have nothing to do with the images of content objects, which come from the image datatype and its aliases.
All three return an image object (`eZImageLayer`, `eZImageTextLayer` or `eZImageObject`), which prints as an `<img>`
tag and offers the attributes `filepath`, `filename`, `width`, `height`, `alternative_text`, `imagepath` and
`has_size`.

## Contents

1. [Settings: texttoimage.ini](#settings-texttoimageini)
2. [texttoimage](#texttoimage)
3. [image](#image)
4. [imagefile](#imagefile)
5. [Related pages](#related-pages)

## Settings: texttoimage.ini

`settings/texttoimage.ini` holds the paths and the font classes:

| Setting | Value on alpha | Meaning |
|---|---|---|
| `[ImageSettings] UseCache` | `enabled` | reuse an image already rendered with the same text and settings |
| `[PathSettings] FontDir[]` | `design/standard/fonts` | where the `.ttf` fonts are looked for |
| `[PathSettings] CacheDir` | `var/cache/texttoimage` | where rendered images are written |
| `[DefaultSettings]` | `Family=1942`, `PointSize=40`, white background, black text | used when a class does not say otherwise |
| `[1942]`, `[a_d_mono]`, `[sketchy]`, `[smartie]` ... | font family and adjustments | the classes a template can name |

## texttoimage

### texttoimage

Renders the piped text with the font settings of a class, overridden by any parameter given. The image is written to
`<CacheDir>/imagetext/` under a name made from an MD5 of the text and the settings; with `UseCache` an existing file is
reused.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `class` | string, a section of `texttoimage.ini` | no | `'default'` (falls back to `[DefaultSettings]`) |
| `family` | string, font file name without `.ttf` | no | from the class |
| `pointsize` | integer | no | from the class |
| `angle` | integer | no | from the class |
| `bgcolor` | color (`#rrggbb` or an array of three numbers) | no | from the class |
| `textcolor` | color | no | from the class |
| `x`, `y`, `w`, `h` | integer adjustments | no | from the class |
| `usecache` | boolean, or a string used as the cache name | no | `[ImageSettings] UseCache` |
| `storeimage` | boolean | no | `true` |

Input: the text, piped. Returns an `eZImageTextLayer`. When the font is not found it returns nothing; when the font
cannot be opened it logs "Could not open font". Generated reference:
[texttoimage](../../examples/template-operators/texttoimage.md).

**It writes files.** The template below was rendered once on alpha while this page was prepared, and it created the
directory `var/cache/texttoimage/imagetext/` with the PNG file (a cache directory: it can be cleared, and the image is
made again on the next request):

```tpl
{'Fit & Healthy'|texttoimage( '1942' )}
```

Its output on alpha:

```text

<img border="0" src="/var/cache/texttoimage/imagetext/b/d/3/bd3fdc7ef41d4f843b93d8d9703be5d4/Fit_amp_Healthy.png" alt="Fit &amp;amp; Healthy" width="450" height="52" />
```

Note the `alt` text: the operator escapes the text with `htmlspecialchars()` when it creates the layer
(`$alternativeText`), and the `<img>` output escapes it again, so `&` arrives as `&amp;amp;`. Pass text without
characters that need escaping, or write the `<img>` tag yourself from `imagepath`, `width` and `height`.

## image

### image

Builds one image from layers. Every parameter is either a string, which becomes the alternative text, or an array of
a layer and a hash of placement options: `halign` (`left`, `right`, `center`), `valign` (`top`, `bottom`, `center`),
`x`, `y`, `xrel`, `yrel` and `transparency`. The result is written to `<CacheDir>/imageobject/` (reused with
`UseCache`).

| Parameter | Type | Required | Default |
|---|---|---|---|
| layers and alternative text | `array( layer, hash( options ) )` or string, any number | yes | none |

Input: none. Returns an `eZImageObject`. Generated reference: [image](../../examples/template-operators/image.md).

**It writes files**, like `texttoimage`. This template, also rendered once on alpha, created
`var/cache/texttoimage/imageobject/`:

```tpl
{image( array( 'Fit & Healthy'|texttoimage( '1942' ), hash( 'halign', 'center', 'valign', 'center' ) ), 'Fit and Healthy' )}
```

Its output on alpha:

```text

<img border="0" src="/var/cache/texttoimage/imageobject/5/e/1/5e1e308e65728a71d3bcfdcfd00dbd81/Fit_and_Healthy.png" alt="Fit and Healthy" width="450" height="52" />
```

## imagefile

### imagefile

Reads an image file and returns it as a layer: `eZImageLayer::createForFile()` reads the size with `getimagesize()`
and loads the image into memory. Nothing is written. The alternative text is the file name without its extension.

| Parameter | Type | Required | Default |
|---|---|---|---|
| `filename` | string, a path relative to the installation | yes | none |
| `options` | array | no | `array()` |

Input: none. Returns an `eZImageLayer`, which can be used as a layer of `image`. Generated reference:
[imagefile](../../examples/template-operators/imagefile.md).

```tpl
{def $img = imagefile( 'design/standard/images/add.png' )}
filepath: {$img.filepath}
filename: {$img.filename}
size: {$img.width} x {$img.height} (has_size: {$img.has_size|choose( 'no', 'yes' )})
alternative_text: {$img.alternative_text}
```

Output on alpha:

```text
filepath: design/standard/images
filename: add.png
size: 16 x 16 (has_size: yes)
alternative_text: add
```

The `imagefile` example gave the same output compiled on alpha. The two writing examples were not run a second time,
interpreted or compiled.

## Related pages

- [Icon operators](icon-operators.md): icons for classes, MIME types and flags
- [Image datatype examples](../../examples/datatypes/ezimage.md): images of content objects
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
