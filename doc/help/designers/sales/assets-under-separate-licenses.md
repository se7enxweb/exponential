---
title: "Images and fonts under separate licenses"
audience: designer, sales-marketing
category: theme-sales
order: 60
summary: How to keep a theme's own photos, illustrations or fonts under terms different from its GPL code - separate files, a clear asset license, a theme that works without them - and the third-party assets you may never relicense. Not legal advice.
keywords: assets, images, fonts, separate license, asset license, stock photos, illustrations, GPL theme, premium assets
type: help
---
# Images and fonts under separate licenses

This page is for designers whose own artwork is a large part of a theme's value. Code and artwork can carry different
licenses if they are kept apart. It is not legal advice.

## How it is usually done

1. **Keep the assets in their own folders**, for example `design/mytheme/images/premium/`, not mixed into templates
   or CSS.
2. **Ship their license next to them**: `design/mytheme/images/premium/LICENSE.txt`, stating what the buyer may do
   (for example: use in the buyer's own sites; no redistribution as images).
3. **Make the theme work without them.** Templates fall back to neutral placeholders, so the GPL part is complete on
   its own.
4. **Say so on the product page and in the README**: "Code: GPL-2.0-or-later. Images in images/premium: licensed to the
   buyer for use in their own sites."

## What you may not do

- Relicense images, fonts or icons made by others. Their terms travel with them
  ([Third-party fonts, icons and libraries](../distribution/third-party-fonts-icons-and-libraries.md)).
- Ship stock photos whose license does not allow redistribution, even to a paying buyer.
- Claim that the GPL code may not be shared because the assets may not.

## Options for asset terms

| Option | Fits |
|---|---|
| Creative Commons (CC BY, CC BY-SA) | You want them shared, with credit |
| A use license for the buyer's sites | Premium artwork you sell |
| No separate license (all GPL) | Simplicity; the code and art travel together |

## Related pages

- [Creative Commons for images](../distribution/creative-commons-for-images.md)
- [Licensing models](licensing-models.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
