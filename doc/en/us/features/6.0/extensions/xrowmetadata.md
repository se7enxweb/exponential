# xrowmetadata: page titles, descriptions, Open Graph and sitemaps

This page is for site builders who care about search engines and link previews. `xrowmetadata` ("Xrow Meta Data") is
the simple SEO building block of Exponential:

- it gives every content object an editable **meta data** attribute: page title, keywords, description, canonical link
  and, since 1.3.6, an **Open Graph image**;
- it builds **XML sitemaps** with cronjobs.

## Use it

1. Add the meta data datatype to the classes you want to describe (**Setup > Classes**).
2. Print the meta data in your page layout (see the example).
3. Run the sitemap cronjob and register the sitemap in `robots.txt`.

## Example: print the meta data in your design

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

Four cronjob parts write sitemaps for every siteaccess: `sitemap`, `archivesitemap`, `newssitemap` and
`mobilesitemap`.

```bash
php runcronjobs.php sitemap
```

The command prints the name of each file it wrote (since 1.4.4; before, it failed on the file object). The files are
written below `var/storage/sitemap/<siteaccess>/`, for example `urlset_standard_<siteaccess>.xml`; the names are set in
`xrowsitemap.ini`. The news sitemap stops quietly when the news subtree is empty.

Then:

1. Make sure your rewrite rules let the XML through: `RewriteRule ^sitemap[^/]*\.xml - [L]`.
2. Register the sitemap in `robots.txt`:

```
Sitemap: https://www.example.com/sitemaps/index
```

## Open Graph image

Since 1.3.6 and 1.3.7 the meta data carries `og_image`, width, height, alt and type.

- In the **object** edit form, choose the image with the same browse and remove interface as an object relation. The
  value is stored in `data_text`.
- In the **class** attribute, set a **default** Open Graph image that objects use when they have none (the `data_text5`
  default, falling back to `data_int4`). `xrowMetaDataFunctions` falls back to this default when an object has no
  value.
- The class and object edit forms show the selected image inline under the name field, and a "no relation" message when
  none is selected.

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 1.3.5 to 1.3.7 | January 2024 to August 2026 | README in Markdown; the Open Graph image. |
| 1.4.0 | 22 September 2026 | Module views are safe on a persistent worker (Velocity); see [behaviour changes](../../../bc/6.0/extensions-behaviour-changes.md#1-persistent-php-workers-module-views-no-longer-declare-at-file-level-without-a-guard). |
| 1.4.1, 1.4.2 | | The extension states its version, license and website; the about page names it "Xrow Meta Data"; the description names Exponential. |
| 1.4.3 | 1 October 2026 | A complete English translation catalogue (`translations/eng-US/translation.ts`; before, on an English siteaccess every text was reported as a missing translation in the debug output); German for the canonical link, Open Graph image and page texts; the "more" checkbox of the meta data field uses jQuery 4 (`.on()`). |
| 1.4.4 | 2 October 2026 | Commands and cronjob parts list a description; command line scripts and module views are classes the files call ([details](../../../bc/6.0/cli_cronjob_view_abstractions.md)). |

## Related pages

- [xrowextract](xrowextract.md): the export has a column handler for this datatype
- [bcgooglesitemaps](bcgooglesitemaps.md): plain sitemaps
- [robots.txt](../robots-txt.md)
- [Velocity engines](../../../bc/6.0/velocity-engines.md)
- [Chronicle](../../../history/extensions/xrowmetadata.md) and [release notes](../../../changelogs/extensions/xrowmetadata.md)
- [Change ledger](../../../history/ledger/xrowmetadata.md)
- Months: [2026-08](../../../history/extensions/months/2026-08.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
