---
title: XML sitemaps
audience: website-creator, admin
category: website-creation
order: 390
summary: Generate XML sitemaps for search engines with bcgooglesitemaps or xrowmetadata - the cronjob parts, the settings, multilingual and multisite sitemaps, serving the file and registering it in robots.txt.
keywords: sitemap, XML sitemap, bcgooglesitemaps, xrowmetadata, googlesitemaps, sitemap cronjob, robots.txt, search engines, multilingual sitemap, website creator
type: help
---
# XML sitemaps

This page is for website creators who give search engines a list of every page. Exponential generates sitemaps with
one of two extensions; both run as cronjob parts and write a file the web server serves.

## Contents

1. [Choose an extension](#choose-an-extension)
2. [bcgooglesitemaps](#bcgooglesitemaps)
3. [xrowmetadata sitemaps](#xrowmetadata-sitemaps)
4. [Serving and registering](#serving-and-registering)
5. [Checklist](#checklist)

## Choose an extension

| | `bcgooglesitemaps` | `xrowmetadata` |
|---|---|---|
| Part of the distribution | yes ([robots.txt feature](../../../features/6.0/robots-txt.md)) | yes |
| Cronjob parts | `googlesitemaps`, `googlesitemapsmultilingual` | `sitemap`, `archivesitemap`, `newssitemap`, `mobilesitemap` |
| Choose when | you want a plain sitemap | you already use xrowmetadata for page metadata |

## bcgooglesitemaps

From [bcgooglesitemaps](../../../features/6.0/extensions/bcgooglesitemaps.md):

```bash
php runcronjobs.php googlesitemaps
php runcronjobs.php googlesitemapsmultilingual
```

Settings in `settings/override/bcgooglesitemaps.ini.append.php`:

| Block | Key | Default | Meaning |
|---|---|---|---|
| `BCGoogleSitemapSettings` | `SitemapRootNodeID` | `2` | start node |
| `BCGoogleSitemapSettings` | `Protocol` | `https` | scheme written into the addresses |
| `BCGoogleSitemapSettings` | `Filename`, `Filesuffix`, `Path` | `sitemap`, `.xml`, empty | the file |
| `Classes` | `ClassFilterType`, `ClassFilterArray[]` | `exclude`, empty | include or exclude classes |
| `NodeSettings` | `Main_Node_Only` | `false` | only main locations (avoids duplicates) |
| `NodeSettings` | `ExcludeNodes`, `ExcludedNodeIDs[]` | `disabled`, empty | leave out subtrees |

Exclude classes that are not pages (images, files, banners) and set `Main_Node_Only=true` when content has several
locations.

## xrowmetadata sitemaps

```bash
php runcronjobs.php sitemap
```

writes files below `var/storage/sitemap/<siteaccess>/` (names in `xrowsitemap.ini`) and prints each file name; the
rewrite rules must let them through (`RewriteRule ^sitemap[^/]*\.xml - [L]`)
([xrowmetadata, Sitemaps](../../../features/6.0/extensions/xrowmetadata.md#sitemaps)).

## Serving and registering

1. Run the part once and check the file exists and lists your pages.
2. Add the part to cron, for example nightly ([Cronjobs](../../../guides/cronjobs.md)).
3. Make sure the web server (or Velocity) serves the file rather than passing it to `index.php`.
4. Add it to `robots.txt`:

   ```text
   Sitemap: https://www.example.com/sitemap.xml
   ```

5. Submit it in the search engines' webmaster tools.

## Checklist

- [ ] Only real pages are listed, each once.
- [ ] Hidden and members-only content is not listed (test with a page you know is restricted).
- [ ] The addresses use the public host and `https`.
- [ ] Each language siteaccess has its sitemap.
- [ ] Cron regenerates it.

## Related pages

- [robots.txt for your site](robots-txt.md)
- [SEO basics for Exponential sites](seo-basics.md)
- [Cronjobs for applications](../app-creators/cronjobs-for-applications.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
