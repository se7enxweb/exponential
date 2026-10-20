---
title: Search and findability of disclosures
audience: government, website-creator, editor, admin
category: public-sector
order: 200
summary: How to make published responses, reports and registers easy to find on an Exponential site: searchable fields, text extraction from PDFs, search statistics, URL aliases, feeds and sitemaps, and what each costs.
keywords: search, findability, searchable attributes, binaryfile.ini, PDF text, search statistics, LogSearchStats, sitemaps, RSS, URL alias, disclosure log, ezfind, Solr
type: help
---
# Search and findability of disclosures

This page is for site builders and editors who want people to find information the body has already published, so
fewer requests are needed. Exponential has a built-in search engine and, optionally, Solr through `ezfind`
([Site search](../../creators/website-creators/site-search.md)).

## Contents

1. [Make the right fields searchable](#make-the-right-fields-searchable)
2. [Search inside documents](#search-inside-documents)
3. [Learn from what people search](#learn-from-what-people-search)
4. [Addresses, feeds and sitemaps](#addresses-feeds-and-sitemaps)
5. [When to move to Solr](#when-to-move-to-solr)

## Make the right fields searchable

The built-in engine indexes attributes marked **Searchable** in each class when content is published. For a
disclosure log entry, mark title, summary and response; for a decision, title and decision text. Do not mark
internal codes. After changing a class, rebuild the index:

```bash
php bin/php/updatesearchindex.php --clean
```

Visitors only find what they may read, so content in a non-public section never appears in their results.

## Search inside documents

`settings/binaryfile.ini` has handlers that extract text from PDF and Word files for the search index
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), using external programs you install on the server. Without them,
only the file's name and the fields of the object are searchable. Scanned PDFs without a text layer give no text
either way. With Solr (`ezfind`), file contents can be indexed too
([Files and downloads](../../creators/website-creators/files-and-downloads.md#searching-inside-files)).

## Learn from what people search

Turn on search statistics on the public siteaccess:

```ini
# settings/siteaccess/<public siteaccess>/site.ini.append.php
[SearchSettings]
LogSearchStats=enabled
```

**Setup > Search statistics** then shows each phrase, how often it was searched and the average number of results;
phrases that found nothing point to missing content or missing words. Phrases are what visitors typed and can contain
personal data; reset the statistics when you no longer need them ([Search statistics](../../../guides/urls-and-aliases.md#5-search-statistics)).

## Addresses, feeds and sitemaps

- Readable URL aliases come from the names; choose clear titles ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).
- An RSS export of the disclosure log lets people follow new releases ([RSS feeds](../../../guides/rss-feeds.md)).
- XML sitemaps (`xrowmetadata`, `bcgooglesitemaps`) help search engines find every entry ([Sitemaps](../../creators/website-creators/sitemaps.md)).
- A meta description per page helps search engine results ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)).

## When to move to Solr

| Need | Built-in | Solr (`ezfind`) |
|---|---|---|
| Find by words | yes | yes |
| Facets by year, outcome, subject | no | yes |
| Relevance tuning | basic | yes |
| Very many documents | slows down | built for it |

Solr is a separate Java server to run, monitor and back up ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)).

## Related pages

- [Site search](../../creators/website-creators/site-search.md)
- [Searching (for editors)](../../searching.md)
- [Disclosure logs](disclosure-logs.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
