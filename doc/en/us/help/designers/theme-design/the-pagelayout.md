---
title: The pagelayout
audience: designer
category: theme-design
order: 60
summary: The template that frames every page: how the kernel calls it, what it must print, a minimal working pagelayout, how real designs split it into parts, and how a module can ask for another one.
keywords: pagelayout, pagelayout.tpl, page frame, page shell, module_result, layout template, head, body, header, footer, master template, base template
featured: true
type: help
---
# The pagelayout

This page is for designers who build or change the outer frame of a site: the `<html>`, `<head>`, header, navigation
and footer around every page. In Exponential this frame is one template, `pagelayout.tpl`. The page answers: when it
runs, what it must contain, how to start one, and how the designs of this installation organize theirs.

## Contents

1. [When the pagelayout runs](#when-the-pagelayout-runs)
2. [What it must print](#what-it-must-print)
3. [A minimal pagelayout](#a-minimal-pagelayout)
4. [Splitting it into parts](#splitting-it-into-parts)
5. [Getting the current node](#getting-the-current-node)
6. [Pages without the frame](#pages-without-the-frame)
7. [Pagelayout and the layouts editor](#pagelayout-and-the-layouts-editor)
8. [Mistakes to avoid](#mistakes-to-avoid)

## When the pagelayout runs

Every web request runs a module view first (for example `content/view/full/42`, `user/login`, `content/search`).
The view produces its HTML, the **module result**. Then the kernel (`kernel/private/classes/ezpkernelweb.php`) sets a
few variables and fetches `design:pagelayout.tpl` through the design cascade. The pagelayout places
`$module_result.content` somewhere in its markup, and the result is the page.

So the pagelayout runs **after** the content. It cannot change what the view rendered, but it can read what the view
reported about itself in `$module_result` (see [Pagelayout variables](pagelayout-variables.md)).

## What it must print

- A complete HTML document: `<!DOCTYPE html>`, `<html lang="...">`, `<head>`, `<body>`.
- `{$module_result.content}` exactly once. Without it the page is empty.
- The stylesheets and scripts of the design, through ezjscore (`ezcss_load`, `ezscript_load`), so that whatever
  a content template asked for with `ezcss_require` / `ezscript_require` is printed too.
- For right-to-left languages, `dir="rtl"` on `<html>` (the kernel provides `$site.text_direction`).

## A minimal pagelayout

A complete, working pagelayout for a new design. It uses no inline CSS or JavaScript, so it also works under a strict
Content Security Policy.

```tpl
{* design/mytheme/templates/pagelayout.tpl *}
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}"{if eq( first_set( $site.text_direction, 'ltr' ), 'rtl' )} dir="rtl"{/if}>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {def $page_path = first_set( $module_result.title_path, $module_result.path )
         $page_last = cond( is_array( $page_path ), $page_path|extract_right( 1 ), array() )}
    <title>{if $page_last|count}{$page_last.0.text|wash} - {/if}{$site.title|wash}</title>
    {undef $page_path $page_last}
    {ezcss_load( array( 'mytheme.css' ) )}
    {ezscript_load( array( 'mytheme.js' ) )}
</head>
<body>
    <a class="skip-link" href="#main">{'Skip to content'|i18n( 'design/mytheme' )}</a>
    <header class="site-header">
        <a class="site-name" href={'/'|ezurl}>{$site.title|wash}</a>
    </header>
    <main id="main">
        {$module_result.content}
    </main>
    <footer class="site-footer">
        {'Powered by Exponential'|i18n( 'design/mytheme' )}
    </footer>
</body>
</html>
```

The title takes the last entry of the page's path (each entry is a hash with `text` and `url`), then the site
name. Most designs build the title in a part template of their own; the media design's
`pagelayout/head/title.tpl` reads the node's name or its meta title, and a readable text for error pages. The `|wash` operator escapes every value that comes from
content or settings ([Wash and escaping](../templates/wash-and-escaping.md)).

## Splitting it into parts

Real pagelayouts grow. Both public designs of this installation keep `pagelayout.tpl` short and include parts:

```tpl
<head>
    <meta charset="utf-8">
    {include uri='design:pagelayout/head/title.tpl'}
    {include uri='design:pagelayout/head/meta.tpl'}
    {include uri='design:pagelayout/head/link.tpl'}
    {include uri='design:pagelayout/head/style.tpl'}
    {include uri='design:pagelayout/head/script.tpl'}
</head>
<body>
{include uri='design:pagelayout/accessibility_links.tpl'}
...
```

(from `extension/sevenx_themes_media/design/media/templates/pagelayout.tpl`; the starter design does the same in
`extension/cjw_themes_jumper/design/starter/templates/pagelayout.tpl`).

Benefits:

- each part can be replaced by a design higher in the cascade without copying the whole pagelayout;
- template path comments show which part produced which markup;
- a part can be wrapped in a `{cache-block}` on its own (menus, footers), see [Caching for designers](caching-for-designers.md).

Variables defined with `{def}` in the pagelayout are visible to the parts it includes. The starter design defines
its page variables with a `pl_` prefix at the top for that reason, and documents them in a comment.

## Getting the current node

The pagelayout does not get a `$node` variable. When the page is a content view, the node id is in
`$module_result.content_info.node_id`; fetch the node when you need it:

```tpl
{def $current_node = false()}
{if and( is_array( $module_result.content_info ), is_set( $module_result.content_info.node_id ) )}
    {set $current_node = fetch( 'content', 'node', hash( 'node_id', $module_result.content_info.node_id ) )}
{/if}
```

Both public designs here do exactly this. Prefer the values already in `content_info` (`class_identifier`,
`url_alias`, `node_depth`, `parent_node_id`) when they are enough: they cost no fetch.

## Pages without the frame

- A view can ask for a different pagelayout by setting `$Result['pagelayout']` in PHP (the kernel reads it), or
  for none (`false`). AJAX and JSON views usually do this.
- The media design uses its own view modes for frameless output: `payload` prints only `{$module_result.content}`,
  `layout_preview` prints a reduced frame. A view mode like this is a cheap way to give the layouts editor or
  scripts a page without header and footer.
- The setup wizard uses `site.ini [SetupSettings] PageLayout=setup_pagelayout.tpl`.

## Pagelayout and the layouts editor

On sites that use the layouts editor, the pagelayout asks the resolver which layout applies to the page and draws it,
falling back to `$module_result.content` when no rule matches:

```tpl
{def $layout = fetch( 'explayouts', 'resolve_layout', hash() )}
{if $layout}
    {include uri='design:explayouts/layout.tpl' layout=$layout}
{else}
    {$module_result.content}
{/if}
```

(pattern from `doc/eng-us/bc/6.0/LAYOUTS.md`, "How rendering actually works"). The starter design uses
`fetch( 'explayouts', 'resolve_layout_for_node', hash( 'node_id', ... ) )` for full views. See
[Layouts for designers](layouts-for-designers.md).

## Mistakes to avoid

- **A `<style>` or `<script>` block in the pagelayout.** Move the code into a file and load it with ezjscore. A strict
  Content Security Policy blocks inline code ([CSP-safe design](csp-safe-design.md)).
- **Fetching the same node in several parts.** Fetch once in the pagelayout and use the variable in the parts.
- **Printing the CSP nonce inside a cache-block.** `csp_nonce()` changes with every request; a cached copy carries a
  stale one (`settings/site.ini`, `[ContentSecurityPolicy]` comment).
- **Hard-coding the site name or URLs.** Use `$site.title` and the `ezurl` operator, so the frame works under every
  siteaccess and path prefix.

## Related pages

- [Pagelayout variables](pagelayout-variables.md)
- [The design directory layout](design-directory-layout.md)
- [Landmarks and headings](landmarks-and-headings.md)
- [Loading stylesheets](loading-stylesheets.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
