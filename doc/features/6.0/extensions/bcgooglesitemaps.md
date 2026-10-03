# bcgooglesitemaps: Google XML sitemaps

`bcgooglesitemaps` generates a sitemap (`sitemap.xml` protocol) of your public content for search engines, as a cronjob. It writes one file per
siteaccess (usually `sitemap_<siteaccess>.xml`; names are set in the ini file) and has a multilingual variant. Run it from the command line or
add it to cron:

```bash
php runcronjobs.php googlesitemaps
php runcronjobs.php googlesitemapsmultilingual
```

## Settings (`bcgooglesitemaps.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| BCGoogleSitemapSettings | `SitemapRootNodeID` | `2` | Start node of the sitemap |
| BCGoogleSitemapSettings | `Protocol` | `https` | Protocol written into the URLs |
| BCGoogleSitemapSettings | `Filename`, `Filesuffix`, `Path` | `sitemap`, `.xml`, empty | Where and how the file is named |
| Classes | `ClassFilterType`, `ClassFilterArray[]` | `exclude`, empty | Include or exclude classes |
| NodeSettings | `Main_Node_Only` | `false` | Only main nodes |
| NodeSettings | `ExcludeNodes`, `ExcludedNodeIDs[]` | `disabled`, empty | Exclude subtrees. Disabled by default (1.1.6) to stay compatible with sites that never had exclusions |

## What changed

* 1.1.6 (7 January 2024): the new `ExcludeNodes` setting; the dependency on the ezsystems package replaced by the 7x one; the license terms
  allow an upgrade of the license; the cronjob tool was forked for inclusion in Exponential.
* 1.1.6.1: license clarified in `composer.json`.
* 1.1.6.2 to 1.1.6.4 (27 September to 2 October 2026): the license is named in full; the description names Exponential; the command line
  scripts and module views are classes the files call, entry point files carry a header of 7x and the Exponential Foundation, commands list
  a description.

For richer SEO (titles, descriptions, Open Graph images, news and mobile sitemaps) see [xrowmetadata](xrowmetadata.md).

## Related

* [Chronicle](../../../history/extensions/bcgooglesitemaps.md) and [release notes](../../../changelogs/extensions/bcgooglesitemaps.md)
