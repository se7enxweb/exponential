---
title: "The pagelayout: your site's frame"
audience: website-creator, developer
category: website-creation
order: 130
summary: What pagelayout.tpl does, the variables it gets ($module_result, $site, $current_user), a minimal CSP-friendly pagelayout to start from, cache blocks and the mistakes that make a site slow or blank.
keywords: pagelayout, pagelayout.tpl, module_result, site, current_user, cache-block, ezpagedata, frame, header, footer, layout template, CSP, website creator
type: help
---
# The pagelayout: your site's frame

This page is for website creators who write or adapt a design. Every page of a siteaccess is the result of a module
view (for content: `content/view`) wrapped in one template, `pagelayout.tpl`. The pagelayout is the frame: the
`<head>`, the header, the menus, the footer. The view's output is placed inside it.

## Contents

1. [How a page is assembled](#how-a-page-is-assembled)
2. [What the pagelayout gets](#what-the-pagelayout-gets)
3. [A minimal pagelayout](#a-minimal-pagelayout)
4. [Cache blocks](#cache-blocks)
5. [Scripts without inline code](#scripts-without-inline-code)
6. [Alternative pagelayouts](#alternative-pagelayouts)
7. [Mistakes to avoid](#mistakes-to-avoid)

## How a page is assembled

1. The request reaches `index.php`; the siteaccess is chosen from the address.
2. The module view runs. For a content page, `content/view/full/<node>` renders `node/view/full.tpl` (or its
   override) and puts the HTML in `$module_result.content`.
3. The pagelayout of the siteaccess's design chain renders, with the view's result in its variables.

## What the pagelayout gets

| Variable | Holds |
|---|---|
| `$module_result.content` | the HTML of the view: print it where the main content goes |
| `$module_result.path` | the path (breadcrumb) items, each with `text`, `url` and, for content, `url_alias` |
| `$module_result.node_id` | the node of a content page |
| `$module_result.content_info` | class, section, node and object data of a content page |
| `$module_result.uri` | the address of the view |
| `$site.title` | the page title the kernel built |
| `$current_user` | the user viewing the page (`is_logged_in`, `contentobject_id` ...) |

The `ezwebin`-based designs add `ezpagedata()` (from `extension/ezwebin/autoloads/`), which returns ready-made values
such as `top_menu`, `left_menu`, `show_path`, `css_classes` and `persistent_variable`; the shipped
`sevenx_themes_simple` pagelayout uses it.

## A minimal pagelayout

Save as `design/<your design>/templates/pagelayout.tpl` in your design extension:

```text
<!DOCTYPE html>
<html lang="{$site.http_equiv.Content-language|wash}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{$site.title|wash}</title>
  {ezcss_load( ezini( 'StylesheetSettings', 'CSSFileList', 'design.ini' ) )}
</head>
<body>
  <a class="skip-link" href="#main">{'Skip to content'|i18n( 'design/mysite' )}</a>
  <header>
    <a href={'/'|ezurl}>{ezini( 'SiteSettings', 'SiteName' )|wash}</a>
    {include uri='design:parts/top_menu.tpl'}
  </header>
  <nav aria-label="{'Breadcrumb'|i18n( 'design/mysite' )}">
    {foreach $module_result.path as $item}
      {if $item.url}<a href={cond( is_set( $item.url_alias ), $item.url_alias, $item.url )|ezurl}>{$item.text|wash}</a> /{else}{$item.text|wash}{/if}
    {/foreach}
  </nav>
  <main id="main">
    {$module_result.content}
  </main>
  <footer>{include uri='design:parts/footer.tpl'}</footer>
  {ezscript_load( ezini( 'JavaScriptSettings', 'JavaScriptList', 'design.ini' ) )}
</body>
</html>
```

Notes:

- `$module_result.content` is already HTML; never `wash` it.
- `ezcss_load` and `ezscript_load` take the lists from your `design.ini` and pack them
  ([Stylesheets and scripts for your site](stylesheets-and-scripts.md)).
- The skip link and landmarks (`header`, `nav`, `main`, `footer`) are the start of an accessible site
  ([Accessibility for site builders](accessibility-for-site-builders.md)).
- Check the file with `php bin/php/eztemplatecheck.php -s<site> design/<design>/templates/pagelayout.tpl`.

## Cache blocks

The pagelayout runs on every request, so expensive parts (menus built from fetches) belong in a `cache-block`. The
shipped simple theme wraps most of its pagelayout in one, keyed by the address, the basket state and the user:

```text
{cache-block keys=array( $module_result.uri, $current_user.contentobject_id )}
  {include uri='design:parts/top_menu.tpl'}
{/cache-block}
```

The keys must include everything the block's output depends on: a menu that differs per user needs the user in its
keys, a block that shows the current page needs the address. Without `keys`, a block is the same for every page. The
caches are cleared on publish ([View cache and cache blocks](view-cache-and-cache-blocks.md)).

## Scripts without inline code

Public siteaccesses may run under a Content Security Policy (`[ContentSecurityPolicy] Mode` in `site.ini`; see
[Content Security Policy](../../../features/6.0/content-security-policy.md)). Keep the pagelayout ready for it: no
`<script>` with code, no `onclick=`, no `style=""`. Pass data to scripts the way the simple theme does, as a JSON
data block read by a script file:

```text
{set-block variable=$page_conf}{json_encode( hash( 'root', '/'|ezurl( no ) ) )}{/set-block}
<script type="application/json" id="mysite-page">{$page_conf|explode( '<' )|implode( '<' )}</script>
{ezscript( array( 'mysite_page.js' ) )}
```

A `type="application/json"` block is data, not code, so the policy does not block it.

## Alternative pagelayouts

A view can ask for another pagelayout, for example a print or a pop-up layout; `[TemplateSettings]` in `site.ini` and
the layout module (`/layout/set/<name>/...`) choose them. To tell a layout view apart in a template, use the `layout`
key ([11. Telling a layout view apart](../../../guides/templates-and-design.md#11-telling-a-layout-view-apart-the-layout-key)).

## Mistakes to avoid

| Mistake | Result | Do instead |
|---|---|---|
| Fetches of whole subtrees in the pagelayout without a cache block | every page slow | cache-block with the right keys |
| A cache block without the user in its keys around "Hello, <name>" | one visitor's name shown to others | add `$current_user.contentobject_id` to the keys, or keep it outside |
| Inline `<script>` and `onclick` | blocked under a Content Security Policy | script files and data blocks |
| Printing user text without `wash` | cross-site scripting | `|wash` everywhere |
| Editing the theme's pagelayout in place | lost on the next update | your own design extension |

## Related pages

- [Template overrides for site builders](template-overrides.md)
- [Menus and navigation](menus-and-navigation.md)
- [Templates and design](../../../guides/templates-and-design.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
