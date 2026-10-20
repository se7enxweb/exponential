---
title: Breadcrumbs
audience: designer, developer
category: templates
order: 680
summary: A breadcrumb trail from $module_result.path: the markup (nav, ol, aria-current), skipping the tree root, pages of other modules, structured data, and the breadcrumb templates of the shipped designs.
keywords: breadcrumbs, breadcrumb trail, path, module_result.path, you are here, aria-current, navigation, BreadcrumbList, schema.org, page_toppath
type: help
---
# Breadcrumbs

This page is for designers who show "where am I" above the content. The path is already in `$module_result.path`;
the breadcrumb template turns it into links.

![An article on the public site in the media design, with the site's header above it](../../images/designers/site-breadcrumb.webp)

## Contents

1. [Markup](#markup)
2. [The template](#the-template)
3. [Skipping the root](#skipping-the-root)
4. [In the shipped designs](#in-the-shipped-designs)

## Markup

A labeled navigation landmark with an ordered list; the current page last, marked with `aria-current` and not linked:

```html
<nav class="breadcrumbs" aria-label="Breadcrumb">
  <ol>
    <li><a href="/">Home</a></li>
    <li><a href="/news">News</a></li>
    <li aria-current="page">An article</li>
  </ol>
</nav>
```

Separators come from CSS (`li + li::before`), so screen readers do not read them.

## The template

```tpl
{* pagelayout/breadcrumbs.tpl *}
{if and( is_array( $module_result.path ), $module_result.path|count|gt( 1 ) )}
<nav class="breadcrumbs" aria-label="{'Breadcrumb'|i18n( 'design/mytheme' )}">
    <ol>
    {foreach $module_result.path as $step}
        {if $step.url}
            <li><a href={first_set( $step.url_alias, $step.url )|ezurl}>{$step.text|wash}</a></li>
        {else}
            <li aria-current="page">{$step.text|wash}</li>
        {/if}
    {/foreach}
    </ol>
</nav>
{/if}
```

`url_alias` gives the readable address; `url` is the system URL (`/content/view/full/<id>`) and the fallback.

## Skipping the root

The first entry of a content path is the top of the tree. The media design leaves out the shared root (depth 1) and
builds its own `path_array` of `text`/`url` pairs in the pagelayout; with the starter design, the large-header classes
(the front page) show no breadcrumb at all. Choose what suits the site: usually "Home" first, linked to `'/'|ezurl`.

## In the shipped designs

- media: `pagelayout/breadcrumbs.tpl`, fed by the `$path_array` the pagelayout builds.
- starter: `pagelayout/breadcrumbs.tpl`, drawn as a template block in its chrome layout.
- ezwebin: `page_toppath.tpl`.
- admin4: an `ol.a4-crumbs` with `aria-current="page"` inside the `#path` navigation landmark.

## Related pages

- [Module result and content info](module-result-and-content-info.md)
- [Landmarks and headings](../theme-design/landmarks-and-headings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
