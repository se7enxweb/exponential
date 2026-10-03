# bcgooglesitemaps: Google XML sitemaps

This page is for site owners who want search engines to find all public pages. `bcgooglesitemaps` generates a
sitemap (`sitemap.xml` protocol) of your public content as a cronjob. It writes one file per siteaccess, usually
`sitemap_<siteaccess>.xml` (the names are set in the INI file), and has a multilingual variant.

## Use it

Run it once from the installation root:

```bash
php runcronjobs.php googlesitemaps
php runcronjobs.php googlesitemapsmultilingual
```

Check that the sitemap file was written, then add the same command to cron so it stays current.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `bcgooglesitemaps.ini` | `BCGoogleSitemapSettings` | `SitemapRootNodeID` | `2` | Start node of the sitemap |
| `bcgooglesitemaps.ini` | `BCGoogleSitemapSettings` | `Protocol` | `https` | Protocol written into the URLs |
| `bcgooglesitemaps.ini` | `BCGoogleSitemapSettings` | `Filename`, `Filesuffix`, `Path` | `sitemap`, `.xml`, empty | Where and how the file is named |
| `bcgooglesitemaps.ini` | `Classes` | `ClassFilterType`, `ClassFilterArray[]` | `exclude`, empty | Include or exclude classes |
| `bcgooglesitemaps.ini` | `NodeSettings` | `Main_Node_Only` | `false` | Only main nodes |
| `bcgooglesitemaps.ini` | `NodeSettings` | `ExcludeNodes`, `ExcludedNodeIDs[]` | `disabled`, empty | Exclude subtrees. Disabled by default (1.1.6) to stay compatible with sites that never had exclusions |

Put your values in `settings/override/bcgooglesitemaps.ini.append.php`.

## What changed

| Version | Date | Change |
|---|---|---|
| 1.1.6 | 7 January 2024 | New `ExcludeNodes` setting; the dependency on the ezsystems package replaced by the 7x one; the license terms allow a license upgrade; the cronjob tool was forked for inclusion in Exponential. |
| 1.1.6.1 | | License clarified in `composer.json`. |
| 1.1.6.2 to 1.1.6.4 | 27 September to 2 October 2026 | The license is named in full; the description names Exponential; command line scripts and module views are classes the files call; entry point files carry a header of 7x and the Exponential Foundation; commands list a description. |

For richer SEO (titles, descriptions, Open Graph images, news and mobile sitemaps) see [xrowmetadata](xrowmetadata.md).

## Related pages

- [xrowmetadata](xrowmetadata.md)
- [Cronjobs in the console](../cronjobs-console.md)
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Chronicle](../../../history/extensions/bcgooglesitemaps.md) and [release notes](../../../changelogs/extensions/bcgooglesitemaps.md)
- [Change ledger](../../../history/ledger/bcgooglesitemaps.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-01](../../../history/extensions/months/2024-01.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
