---
title: "The site style package"
audience: designer
category: theme-extensions
order: 350
summary: The Site style wizard packs two stylesheets (site and classes), their images and a thumbnail into an Exponential package; what it is for, each step, and when a full theme extension is the better choice.
keywords: site style, ezstyle, package wizard, site.css, classes.css, SiteCSS, ClassesCSS, thumbnail, color scheme, style package
type: help
---
# The site style package

This page is for designers who want to share a **color scheme** for an existing design rather than a whole theme.
The Site style wizard (`ezstyle`) is the oldest package creator for exactly that.

## What it packs

From the creator's code (`kernel/classes/packagecreators/ezstyle/`):

- a **site stylesheet** and a **classes stylesheet** (stored with the variable names `sitecssfile` and
  `classescssfile`);
- any number of **images**, stored below `images` in the package;
- a **thumbnail** of the look.

These correspond to the two stylesheet settings of `design.ini [StylesheetSettings]`: `SiteCSS` and `ClassesCSS`
(in the kernel's defaults, `stylesheets/t02/site-colors.css` and `stylesheets/t02/classes-colors.css`).

## Step by step

1. Prepare the two stylesheets and their images, and a thumbnail image of a page in this style.
2. Open **Setup > Packages > Create new package** and choose **Site style**.
3. **Package thumbnail**: choose the thumbnail.
4. **CSS files**: choose the site stylesheet and the classes stylesheet.
5. **Image files**: **Add image** adds the chosen file and stays on the step; add each image, then **Continue**.
6. **Package information**: name, summary, version, license (from the configured list), format.
7. **Package maintainer** and **Package changelog**, then review and **Create package**.
8. On the package's page, **Download** writes `<name>-<version>.ezpkg`.

## When to use a theme extension instead

| A site style package fits when | A theme extension fits when |
|---|---|
| Only colors and a few images change | Templates, layout or scripts change |
| The design underneath stays the same | You need settings, translations, operators |
| Users pick a look among several | Users install, update and configure a theme |

## Related pages

- [The package creator wizard, step by step](package-creator-wizard-step-by-step.md)
- Loading stylesheets
- [The package wizard](../../../features/6.0/package-wizard.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
