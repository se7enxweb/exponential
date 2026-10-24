---
title: Editing images with ezie
audience: individual, editor
category: personal-sites
order: 1613
summary: How one person uses the ezie image editor inside the Exponential edit form to crop, flip, rotate, pixelate, adjust contrast and brightness, convert to black and white or sepia, and add a watermark - what it is good for, how saving works, and when to use a desktop program instead.
keywords: ezie, image editor, crop, rotate, flip, watermark, contrast, brightness, black and white, sepia, pixelate, portfolio, edit form, GD, ImageMagick
type: help
---
# Editing images with ezie

This page is for individuals who want to make small changes to an image without leaving the browser. The `ezie`
image editor is a separate package (`composer require se7enxweb/ezie zetacomponents/image-conversion`; the kernel
package no longer installs it, admin4 has `expimageeditor` instead) and works inside the edit form of
any object that has an image field, such as *Image* and *Gallery*. The technical page is
[ezie: the image editor](../../../features/6.0/extensions/ezie.md).

## Contents

1. [What it can do](#what-it-can-do)
2. [Step by step](#step-by-step)
3. [Saving and undo](#saving-and-undo)
4. [Watermarks](#watermarks)
5. [When to use a desktop program instead](#when-to-use-a-desktop-program-instead)
6. [If the editor does not open](#if-the-editor-does-not-open)

## What it can do

| Tool | Use it to |
|---|---|
| Select and crop | Remove an edge, change the frame, make a square detail |
| Flip horizontal, flip vertical | Mirror an image or a selection |
| Rotate | Straighten a photo taken sideways |
| Watermark | Place a registered watermark image on the picture |
| Contrast, brightness | Small corrections |
| Black and white, sepia | Quick filters |
| Pixelate | Hide a face, a license plate or a house number in a selection |
| Zoom, undo, redo | Work precisely and step back |

The tools are listed in the feature page; keyboard shortcuts are shown in each tool's title.

## Step by step

1. Edit the *Image* object (or any object with an image field).
2. Open the image editor from the image field.
3. Choose a tool in the **Actions** window.
4. For crop, watermark or pixelate, draw a selection first: keep ratio, free, or type a size.
5. Apply. Undo if the result is not what you wanted.
6. Click **Save & Close**. The edit form now shows the changed image.
7. **Publish** the object. Until then, visitors still see the old image.

## Saving and undo

- **Save & Close** stores the edited image as a new version of the image field. It does not touch other versions.
- **Quit** asks before throwing away your changes, and only if you changed something.
- Because the result is a new version of the object, you can go back to the previous version after publishing
  ([Publishing and versions](../../publishing-and-versions.md)).
- Keep your untouched original on your own computer anyway; the editor works on what was uploaded.

## Watermarks

A watermark is an image (usually a PNG with transparency) placed on a picture. `ezie` only offers watermarks that are
registered in the settings: a file in a `design/standard/images/watermarks` folder of an extension and a line
`watermarks[]=<file name>` in that extension's `image.ini.append.php` under `[eZIE]`, then a cache clear. The
shipped watermarks are example logos; for your own mark, add your file this way
([ezie, Add your own watermark](../../../features/6.0/extensions/ezie.md#add-your-own-watermark)).

Two things to know:

- Watermarking is per image, by hand. Nothing watermarks every upload automatically.
- A watermark discourages copying; it does not prevent it.

## When to use a desktop program instead

| Task | Use |
|---|---|
| Crop, rotate, a quick watermark, hide a detail | `ezie` |
| Color correction, retouching, layers | A desktop or mobile photo program |
| Resizing hundreds of files | A batch tool before [uploading many images](uploading-many-images.md) |
| Removing location data | Your photo program's export options, before upload |

## If the editor does not open

- The editor needs GD2 or ImageMagick on the server; the setup wizard checks for one
  ([Image processing and media](../../hosting/basics/image-processing-and-media.md)).
- It needs the edit form scripts of the administration or a design that loads them.
- Reload the page once after an update; your browser may hold old scripts.
- If a tool fails with an error, note the time and ask your host or partner to look at the log
  ([When something goes wrong](../../when-something-goes-wrong.md)).

## Related pages

- [Portfolios](README.md)
- [ezie: the image editor](../../../features/6.0/extensions/ezie.md)
- [Building an image gallery](building-an-image-gallery.md)
- [Image variations for portfolios](image-variations-for-portfolios.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
