---
title: Icons in a design
audience: designer
category: theme-design
order: 290
summary: Icons for public designs and admin pages: inline SVG files from the design, the Bootstrap Icons set shipped in share/icons, admin4's .a4-bi-* mask classes and IconList, class and file type icon themes, and accessible icon buttons.
keywords: icons, svg icons, bootstrap icons, icon font, a4-bi, IconList, mask-image, icon theme, class icons, share/icons, accessible icons, aria-label, icon button
type: help
---
# Icons in a design

This page is for designers who add icons. It answers: which icon sets the installation already has, how admin4 draws
its icons, how to add one, and how to make icon-only controls accessible.

## Contents

1. [What is already there](#what-is-already-there)
2. [Icons in a public design](#icons-in-a-public-design)
3. [Icons in the administration (admin4)](#icons-in-the-administration-admin4)
4. [Class and file type icons](#class-and-file-type-icons)
5. [Accessible icons](#accessible-icons)

## What is already there

| Set | Where | Used by |
|---|---|---|
| Bootstrap Icons 1.13.2 (SVG) | `share/icons/bootstrap-icons/svg/` with its `LICENSE` | admin4 and admin4l, and the icon theme `bootstrap-icons` |
| Crystal | `share/icons/crystal`, `crystal-admin` | the classic class and file type icons |
| Font Awesome (CSS and font) | `extension/sevenx_themes_simple/design/simple/stylesheets/libs/fontawesome/`, the starter design's `fonts/` | the simple and starter designs |

`share/icons/` is served as static files by the shipped rewrite rules.

## Icons in a public design

For a handful of icons, ship SVG files in your design's `images/icons/` and reference them:

```tpl
<a class="social-link" href="{$profile_url|wash}">
    <img src={'icons/rss.svg'|ezimage} alt="" width="20" height="20">
    <span>{'News feed'|i18n( 'design/mytheme' )}</span>
</a>
```

For icons that follow the text color, use them as CSS masks (the admin4 method below) or paste the SVG markup into a
small template you include. Avoid loading a whole icon font for a few icons.

## Icons in the administration (admin4)

admin4 draws Bootstrap Icons as CSS masks, colored by `currentColor`. `components.css` has one rule per icon name:

```css
.a4-bi-gear { -webkit-mask-image: url(../../../share/icons/bootstrap-icons/svg/gear.svg);
              mask-image: url(../../../share/icons/bootstrap-icons/svg/gear.svg); }
```

and markup uses the class:

```html
<span class="a4-icon a4-bi-gear" aria-hidden="true"></span>
```

Settings name icons too: `menu.ini` tab icons (`Icon=palette`) and Setup tiles (`LinkIcons[]`) take names listed in
`design.ini [AdminDesignSettings] IconList[]`; a name not listed draws the plug icon (comment in `settings/design.ini`).
An extension that needs another Bootstrap icon adds its SVG, a `.a4-bi-<name>` rule in a stylesheet of
`BackendCSSFileList[]` and `IconList[]=<name>` in its `design.ini.append.php`.

## Class and file type icons

The small icons for content classes and file types come from **icon themes** (`settings/icon.ini`). admin4 and admin4l use
the `bootstrap-icons` theme through `[IconSettings] DesignTheme[]`. An extension can ship its own icon theme; see
[Icon themes in extensions](../../../features/6.0/icon-themes-in-extensions.md).

## Accessible icons

- An icon next to visible text is decoration: `aria-hidden="true"` (or `alt=""` on an `<img>`).
- An icon-only button needs a name: visible-to-screen-readers text (`<span class="a4-sr-only">` in the admin, your own
  visually-hidden class elsewhere) or `aria-label`, and usually a `title` for mouse users.
- Icons that point along the line (arrows, chevrons) are mirrored in right-to-left pages; admin4 does it with
  `scale: -1 1` ([Right-to-left designs](right-to-left-designs.md)).
- Masked icons follow the text color, so they keep their contrast in dark mode.

## Related pages

- [Images in a design](images-in-a-design.md)
- [SVG images](svg-images.md)
- [admin4 components](admin4-components.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
