---
title: "The main layout: pagelayout.tpl and a style sheet"
audience: builder, designer
category: books
order: 110
summary: Building the page frame of the club site step by step: a bare pagelayout, loading a style sheet through the design settings, then the header, the menu, the content area and the footer.
keywords: pagelayout, template, ezdesign, ezcss_load, page_head, design.ini, FrontendCSSFileList, style sheet, menu, footer, module_result, Building book
type: help
---
# The main layout: pagelayout.tpl and a style sheet

The layout of the TSCM site is simple: a banner and a menu at the top, the changing content in the middle, a small footer.
The page frame is one template, `pagelayout.tpl`. The system always looks for it first in your design; with none, it uses the
standard one. [Template principles](../book-learning/template-principles.md) explains the idea; this page does it.

## A bare-bones pagelayout

`extension/exp_tscm_design/design/tscm/templates/pagelayout.tpl`:

```
{*?template charset=utf-8?*}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
{ezcss_load( array(
    'core.css',
    ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ),
    ezini( 'StylesheetSettings', 'FrontendCSSFileList', 'design.ini' )
) )}
{include uri='design:page_head.tpl'}
</head>
<body>
Hello world!
</body>
</html>
```

Clear the template override cache and load the site: "Hello world!". Look at the page source: `page_head.tpl` has written the
title, the meta tags and the link tags. It must always be included. What the lines mean (the book explains each):

- `{*?template charset=utf-8?*}` tells the engine the file's character set. Write templates in UTF-8.
- `{include uri='design:page_head.tpl'}` inserts the standard head file (it sets the page title, meta information and more).
- `ezcss_load( ... )` writes the link to the style sheets. The book wrote a `<link>` with the `ezdesign` operator, which
  puts the current design's path in front of a file name; `ezcss_load` does the same for each file in the list, and
  the ezjscore packer bundles them ([CSS and JavaScript in an extension](../extension-development/part-3-extension-developer/css-and-javascript-in-an-extension.md)).
  The book's `charset=latin1` and its XHTML 1.0 doctype are gone: use `<!DOCTYPE html>` and UTF-8.

## A style sheet

Put the club's style in `design/tscm/stylesheets/tscm.css` and list it in `extension/exp_tscm_design/settings/design.ini.append.php`:

```ini
[StylesheetSettings]
FrontendCSSFileList[]
FrontendCSSFileList[]=tscm.css
```

(The book put its rules in `tscm.css` too and loaded them by a link in the template.) The book's CSS sets a page
background, a 732 pixel wide centered column `.main`, dark `.border` and `.menu` strips and a `.footer`. Write your own with the
same hooks, in a design where the colors are few and chosen once. Never write `style=""` or a `<style>` block in a template
([Content Security Policy for extension authors](../extension-development/part-3-extension-developer/content-security-policy-for-extensions.md)).
A start:

```css
body { margin: 0; background: #fff; color: #222; font: 100%/1.5 sans-serif; }
.main { max-width: 46rem; margin: 0 auto; }
.menu { background: #474747; text-align: center; padding: .25rem; }
.menu a { color: #ccc; margin: 0 .5rem; }
.content { padding: 2rem; }
.footer { background: #333; color: #bbb; padding: .25rem; text-align: center; font-size: .8rem; }
```

## The frame

Extend the body to five parts: the header, the menu, the content and the footer.

```
<body>
<div class="main">
    <header><h1><a href={'/'|ezurl}>{$site.title|wash}</a></h1></header>
    {* The menu: the folders just below the root, fetched, not typed. *}
    {def $top=fetch( 'content', 'list', hash( 'parent_node_id', 2,
                                              'class_filter_type', 'include',
                                              'class_filter_array', array( 'folder' ) ) )}
    <nav class="menu">
        {foreach $top as $item}
            <a href={$item.url_alias|ezurl}>{$item.name|wash}</a>
        {/foreach}
    </nav>
    <main class="content">
        {$module_result.content}
    </main>
    <footer class="footer">The Scandinavian Check Mates</footer>
</div>
</body>
```

`{$module_result.content}` is where the content view goes ([content and presentation](../book-learning/content-and-presentation.md)).
The book wrote its menu as static links; this menu fetches the folders, so a new folder is a new menu entry without
touching the template ([fetching content](../book-learning/fetching-content-in-templates.md); other menus:
[common template tasks](../book-learning/common-template-tasks.md)). Cache the menu with `{cache-block}`
([caching in templates](../book-learning/caching-in-templates.md)) when the site grows.

Next: [sections and the welcome page](sections-and-the-welcome-page.md).

## Source and license

Adapted from the section on creating a new site and the main layout of of the tutorial *Building an eZ publish site* (written for eZ publish 3.2, 2003), originally by
ez.no and released again under the GNU Free Documentation License by 7x in 2024,
[Book-Building-An-eZPublish-Site](https://github.com/se7enxweb/Book-Building-An-eZPublish-Site). Copyright (c)
1999-2024 7x and Graham Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later
version, with no Invariant Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rewritten for Exponential 6: extensions instead of renamed folders, current settings and operators, no inline style, and a modern page frame.
