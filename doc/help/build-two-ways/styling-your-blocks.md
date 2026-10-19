---
title: Styling your blocks
audience: designer, developer
category: build-two-ways
order: 560
summary: Where the CSS for your own blocks, view types and template blocks goes on the Fit & Healthy theme - its stylesheet list is in pagelayout/head/style.tpl, not design.ini - and how to add your file so it is packed with the theme's. Tested before and after.
keywords: CSS, stylesheet, styling, ezcss_load, style.tpl, CSSFileList, packer, ezjscore, mysite.css, design stylesheets, block styles
type: help
---
# Styling your blocks

New markup needs CSS. On the Fit & Healthy theme the place for it is not where you might look first.

## Contents

1. [Where the theme lists its stylesheets](#where-the-theme-lists-its-stylesheets)
2. [Add your stylesheet](#add-your-stylesheet)
3. [Check it](#check-it)
4. [Rules for the CSS](#rules-for-the-css)

## Where the theme lists its stylesheets

The media design loads its CSS in `pagelayout/head/style.tpl` with one call:

```smarty
{ezcss_load( array( 'inter.css',
                    'explayouts_standard/grid.css',
                    'index.css',
                    ...
                    'exp-mobile-layout.css' ) )}
```

`ezcss_load` (ezjscore) packs the files into one file under `var/<site>/cache/public/stylesheets/` and links it.
Paths are resolved through the design fallback, so `mysite.css` is found in
`extension/mysite/design/media/stylesheets/mysite.css`.

On the test copy, adding `CSSFileList[]=mysite.css` to `[StylesheetSettings]` in `design.ini` (as the theme's own
`design.ini.append.php` does for its files) did **not** put the file into the page's packed CSS: this theme's page
layout does not read that list.

## Add your stylesheet

1. Write `extension/mysite/design/media/stylesheets/mysite.css`, for example:

   ```css
   .latest-fitness ul { list-style: none; padding: 0; }
   .latest-fitness li { padding: 0.5rem 0; border-bottom: 1px solid #e5e5e5; }
   .list-compact { list-style: none; padding: 0; }
   .notice { padding: 1rem 1.25rem; border-left: 4px solid #2563eb; background: #eff6ff; }
   .notice-warning { border-left-color: #b45309; background: #fffbeb; }
   ```

2. Copy `pagelayout/head/style.tpl` from the theme to `extension/mysite/design/media/templates/pagelayout/head/style.tpl`
   and add your file at the end of the list:

   ```smarty
                       'exp-mobile-layout.css',
                       'mysite.css' ) )}
   ```

3. Clear **Template & content** (`./console exp:cache clear --tag=template,content`): that also clears the packed files
   (`ezjscore-packer`).

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
