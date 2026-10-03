# xrowmetadata: page titles, descriptions, Open Graph and sitemaps

`xrowmetadata` ("Xrow Meta Data") gives every content object an editable **meta data** attribute (page title, keywords,
description, canonical link and, since 1.3.6, an **Open Graph image**) and builds **XML sitemaps** with cronjobs. It is the
simple SEO building block of Exponential: attach the datatype to the classes you want to describe, print the meta data
in your page layout, and publish sitemaps for search engines.

## Print the meta data in your design

```
{def $meta = metadata( $module_result.node_id ) }
{if $meta}
    {if $meta.title}<title>{$meta.title|wash}</title>{/if}
    {if $meta.keywords}<meta name="keywords" content="{$meta.keywords|implode(',')|wash}">{/if}
    {if $meta.description}<meta name="description" content="{$meta.description|wash}">{/if}
{else}
    <title>{$site_title}</title>
    {foreach $site.meta as $key => $item}
        <meta name="{$key|wash}" content="{$item|wash}">
    {/foreach}
{/if}
```

## Sitemaps

Four cronjob parts write sitemaps for every siteaccess: `sitemap`, `archivesitemap`, `newssitemap` and `mobilesitemap`.

```bash
php runcronjobs.php sitemap
```

The files are written below `var/storage/sitemap/<siteaccess>/` (for example `urlset_standard_<siteaccess>.xml`; the names are set
in `xrowsitemap.ini`). Make sure your rewrite rules let the XML through (`RewriteRule ^sitemap[^/]*\.xml - [L]`) and register the sitemap
in `robots.txt`:

```
Sitemap: https://www.example.com/sitemaps/index
```

The sitemap cronjob prints the name of each file it wrote (1.4.4); before it failed on the file object. The news sitemap stops
quietly when the news subtree is empty.

## Open Graph image (1.3.6, 1.3.7)

The meta data struct carries `og_image`, width, height, alt and type. In the **object** edit form you choose the image with the
same browse/remove interface as an object relation; in the **class** attribute you can set a **default** Open Graph image that
objects use when they have none (the `data_text5` default, falling back to `data_int4`; the per-object value is stored in
`data_text`). The class and object edit forms show the selected image inline under the name field, and a "no relation" message
when none is selected. `xrowMetaDataFunctions` falls back to the class-level default when an object has no value.

## What changed in the Exponential 6 releases

* 1.3.5 to 1.3.7 (January 2024 to August 2026): README in Markdown; the Open Graph image above.
* 1.4.0 (22 September 2026): module views are safe on a persistent worker (Velocity); see
  [Behaviour changes](../../../bc/6.0/extensions-behaviour-changes.md#persistent-workers).
* 1.4.1, 1.4.2: the extension states its version, license and website, the about page names it "Xrow Meta Data", the description
  names Exponential.
* 1.4.3 (1 October): a complete English translation catalogue (`translations/eng-US/translation.ts`; before, on an English siteaccess
  every text was reported as a missing translation in the debug output), German for the canonical link, Open Graph image and page
  texts, and the "more" checkbox of the meta data field uses jQuery 4 (`.on()`).
* 1.4.4 (2 October): commands and cronjob parts list a description; the command line scripts and module views are classes the files
  call ([details](../../../bc/6.0/cli_cronjob_view_abstractions.md)).

## Related

* [xrowextract](xrowextract.md): the export has a column handler for this datatype
* [Chronicle](../../../history/extensions/xrowmetadata.md) and [release notes](../../../changelogs/extensions/xrowmetadata.md)
