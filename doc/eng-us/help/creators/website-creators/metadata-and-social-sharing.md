---
title: Page metadata and social sharing
audience: website-creator, developer
category: website-creation
order: 380
summary: Give every page its own title, description, keywords and Open Graph image - with the xrowmetadata extension or fields of your own - and print them safely in the pagelayout.
keywords: metadata, meta description, title, keywords, Open Graph, og:image, Twitter card, social sharing, xrowmetadata, metadata operator, pagelayout, website creator
type: help
---
# Page metadata and social sharing

This page is for website creators who want each page to carry its own title, description and sharing image. Out of
the box a design prints site-wide values (`SiteName`, `SiteDescription`; see [Branding](branding-your-site.md)). Per
page values come from the `xrowmetadata` extension or from fields you add to classes.

## Contents

1. [Two ways](#two-ways)
2. [With xrowmetadata](#with-xrowmetadata)
3. [With your own fields](#with-your-own-fields)
4. [Open Graph and sharing images](#open-graph-and-sharing-images)
5. [Checklist](#checklist)

## Two ways

| | `xrowmetadata` | Your own fields |
|---|---|---|
| Editors fill | one metadata field per object: title, keywords, description, Open Graph image | ordinary fields (`meta_description`, `share_image`) |
| Defaults | a default Open Graph image per class | your template decides |
| Sitemaps | four sitemap cronjobs included | separate extension |
| Choose when | you want a complete package | you want only one or two fields |

## With xrowmetadata

From [xrowmetadata](../../../features/6.0/extensions/xrowmetadata.md):

1. Add the meta data datatype to the classes to describe (**Setup > Classes**).
2. Print it in the pagelayout:

   ```text
   {def $meta = metadata( $module_result.node_id )}
   {if $meta}
       {if $meta.title}<title>{$meta.title|wash}</title>{/if}
       {if $meta.description}<meta name="description" content="{$meta.description|wash}">{/if}
   {else}
       <title>{$site.title|wash}</title>
   {/if}
   {undef $meta}
   ```

3. Run the sitemap cronjob and register the sitemap ([XML sitemaps](sitemaps.md)).

## With your own fields

Add a `meta_description` field (`eztext`) and use the intro when it is empty:

```text
{if is_set( $module_result.node_id )}
  {def $page = fetch( 'content', 'node', hash( 'node_id', $module_result.node_id ) )
       $desc = ''}
  {if and( is_set( $page.data_map.meta_description ), $page.data_map.meta_description.has_content )}
    {set $desc = $page.data_map.meta_description.content}
  {elseif and( is_set( $page.data_map.intro ), $page.data_map.intro.has_content )}
    {set $desc = $page.data_map.intro.content.output.output_text|striptags|shorten( 160 )}
  {/if}
  {if $desc}<meta name="description" content="{$desc|wash}">{/if}
  {undef $page $desc}
{/if}
```

Place it inside the pagelayout's cache block, which is keyed by the address, or the fetch runs on every request.

## Open Graph and sharing images

Social networks read `og:title`, `og:description`, `og:image` and `og:url`. The simple theme already prints
`og:title`, `og:site_name` and an `og:image` from the design ([Branding](branding-your-site.md#meta-tags)). For a
per-page image, use `xrowmetadata`'s `og_image` (with a class default) or an image relation of your own, and print an
alias of a suitable size with the full address (`|ezroot( , 'full' )` or the host name):

```text
<meta property="og:image" content="{$image.url|ezroot( no, 'full' )}">
```

## Checklist

- [ ] Every page type has a title and description, with a sensible fallback.
- [ ] Values are printed with `|wash`.
- [ ] Sharing images are absolute URLs of an alias large enough for previews.
- [ ] The metadata is inside a cache block keyed by the page.
- [ ] Editors know which field becomes the description.

## Related pages

- [SEO basics for Exponential sites](seo-basics.md)
- [Branding: site name, logo, favicon and meta tags](branding-your-site.md)
- [The pagelayout: your site's frame](the-pagelayout.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
