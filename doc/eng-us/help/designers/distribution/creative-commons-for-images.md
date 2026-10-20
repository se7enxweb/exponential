---
title: "Creative Commons for images and graphics"
audience: designer
category: theme-distribution
order: 270
summary: License the images, illustrations and demo photos of a theme - Creative Commons licenses compared, which ones fit a GPL theme, NonCommercial and NoDerivatives pitfalls, and how to record where each image came from. Not legal advice.
keywords: Creative Commons, CC BY, CC BY-SA, CC0, NonCommercial, NoDerivatives, images, photos, illustrations, attribution, image credits
type: help
---
# Creative Commons for images and graphics

This page is for designers whose theme ships images: a logo placeholder, icons they drew, background textures, demo
photos. Images are not software, and Creative Commons licenses were made for works like them. It is not legal advice.

## The licenses compared

| License | Others may | Fits a theme everyone may use and change? |
|---|---|---|
| CC0 1.0 | Anything, no conditions | Yes |
| CC BY 4.0 | Anything, with attribution | Yes |
| CC BY-SA 4.0 | Anything, with attribution, changed versions under the same license | Yes |
| CC BY-ND 4.0 | Share, not change | Only if nobody needs to change the image |
| CC BY-NC (and NC-SA, NC-ND) 4.0 | Only non-commercial use | Problematic: a business website using the theme may count as commercial |

All of these are configured in `package.ini [LicenseSettings]`, so the wizards can record them.

## Rules for a theme

- **Avoid NonCommercial and NoDerivatives** for anything a site owner will display or adapt; they make the theme
  unusable for many users.
- **Only ship images you may redistribute.** A photo from a stock site licensed for your own use usually may not be
  redistributed inside a theme.
- **No people without their consent** in demo photos, and no customer material.

## Step by step

1. Make a list of every image in `design/<name>/images/` and `share/`.
2. For each, write its source, author and license in `design/<name>/images/CREDITS.md` (or one file for the whole theme).
3. Replace every image whose license is unclear.
4. Name the image license in the README: "Images in design/mytheme/images are CC BY 4.0, (C) <you>, except those listed
   in CREDITS.md."

## Related pages

- [Third-party fonts, icons and libraries](third-party-fonts-icons-and-libraries.md)
- [Images and fonts under separate licenses](../sales/assets-under-separate-licenses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
