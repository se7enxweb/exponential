---
title: Styling your blocks
audience: designer, developer
category: build-two-ways
order: 560
summary: Where the CSS for your own blocks, view types and template blocks goes on the Fit & Healthy theme - the theme lists its own files in pagelayout/head/style.tpl and then loads what extensions add to design.ini - and how to add your file so it is packed with the theme's. Tested before and after.
keywords: CSS, stylesheet, styling, ezcss_load, style.tpl, CSSFileList, packer, ezjscore, mysite.css, design stylesheets, block styles
type: help
---
# Styling your blocks

New markup needs CSS. On the Fit & Healthy theme it goes into a stylesheet of your extension, listed in `design.ini` like on any other design.

## Contents

1. [Where the theme lists its stylesheets](#where-the-theme-lists-its-stylesheets)
2. [Add your stylesheet](#add-your-stylesheet)
3. [Check it](#check-it)
4. [Rules for the CSS](#rules-for-the-css)

## Where the theme lists its stylesheets

The media design loads its own CSS in `pagelayout/head/style.tpl`, in the order it needs:

```smarty
{def $media_css_own = array( 'inter.css',
                             'explayouts_standard/grid.css',
                             'index.css',
                             ...
                             'exp-mobile-layout.css' )}
```

After those it loads every entry of `[StylesheetSettings] CSSFileList[]` and `FrontendCSSFileList[]` in `design.ini`
that extensions add, in that order and each file once, and hands the whole list to one `ezcss_load` call. `ezcss_load`
(ezjscore) packs the files into one file under `var/<site>/cache/public/stylesheets/` and links it. Paths are resolved
through the design fallback, so `mysite.css` is found in `extension/mysite/design/media/stylesheets/mysite.css`.

The entries the media design replaces are never loaded: the simple theme's base styles (`main.css`, `main.res.css`,
Font Awesome, `magnific-popup.css`, `websitetoolbar.css`) and the theme's retired files. They are named in
`MediaReplacedCSSFileList[]` in the theme's `design.ini.append.php`.

Before sevenx_themes_media 1.5.74 the page layout did not read `CSSFileList[]` or `FrontendCSSFileList[]` at all, and
the only way in was a copy of `style.tpl`. That copy still works, but it stops following the theme's own list when the
theme changes it.

## Add your stylesheet

1. Write `extension/mysite/design/media/stylesheets/mysite.css`, for example:

   ```css
   .latest-fitness ul { list-style: none; padding: 0; }
   .latest-fitness li { padding: 0.5rem 0; border-bottom: 1px solid #e5e5e5; }
   .list-compact { list-style: none; padding: 0; }
   .notice { padding: 1rem 1.25rem; border-left: 4px solid #2563eb; background: #eff6ff; }
   .notice-warning { border-left-color: #b45309; background: #fffbeb; }
   ```

2. Add it to `extension/mysite/settings/design.ini.append.php`:

   ```ini
   [StylesheetSettings]
   FrontendCSSFileList[]=mysite.css
   ```

   It goes into the same packed file, after the theme's files.

3. Clear the **INI** and **Template & content** caches (`./console exp:cache clear --tag=ini,template,content`): that
   also clears the packed files (`ezjscore-packer`).

## Check it

The packed file's name changes, and it contains your rules:

```text
$ grep -o "latest-fitness[^}]*}" var/site/cache/public/stylesheets/<hash>_all.css
latest-fitness ul { list-style:none;padding:0}
latest-fitness li { padding:0.5rem 0;border-bottom:1px solid #e5e5e5}
```

Before and after, the *Latest in Fitness* template block:

![The Latest in Fitness list with bullets, before the stylesheet](images/tb-03-latest-fitness-on-site.webp)

![The same list with the bullets gone and a line between the items](images/st-01-latest-fitness-styled.webp)

A browser that still shows the old styles has the old file cached: reload without the cache.

## Rules for the CSS

- Last in the list wins on equal specificity: your file after the theme's lets you adjust the theme without
  `!important`.
- Use the classes the block wrapper gives you: `ngl-block`, `ngl-<block type>`, `ngl-vt-<view type>` and the CSS class
  an editor sets on the Design tab.
- Keep markup free of `style=""`; the styles belong in the stylesheet so they are packed and cached.

## Related pages

- [Add a block type](add-a-block-type.md)
- [Add a view type](add-a-view-type.md)
- [Your own design extension](your-own-design-extension.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
