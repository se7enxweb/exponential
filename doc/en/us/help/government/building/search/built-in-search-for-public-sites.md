---
title: Built-in search for public sites
audience: government, website-creator, admin
category: public-sector
order: 20
summary: What the search engine that ships with Exponential does for a public site (database index, permissions respected, searchable fields, advanced search), its settings, and when it is enough for a public body.
keywords: site search, built-in search, eZSearchEngine, content/search, advancedsearch, SearchSettings, permissions, public site search
type: help
---
# Built-in search for public sites

This page is for site builders and administrators of public sites. Exponential has a built-in search engine that
keeps its index in the database; it is enough for most sites ([Site search](../../../creators/website-creators/site-search.md)).

## Contents

1. [What it does](#what-it-does)
2. [Settings that matter](#settings-that-matter)
3. [Is it enough?](#is-it-enough)
4. [Maintenance](#maintenance)

## What it does

- `site.ini [SearchSettings] SearchEngine=eZSearchEngine` indexes the attributes marked **Searchable** in each class
  when content is published.
- Visitors only find what they may read: content in a non-public section never appears in their results.
- `content/search` takes a search text; `content/advancedsearch` adds phrase, class and date fields. Both are
  disallowed for crawlers in the shipped `robots.txt`.
- Results are limited to the languages the siteaccess shows (see the languages section, "Multilingual search").

## Settings that matter

| Key | Shipped | Meaning |
|---|---|---|
| `MaximumSearchLimit` | `30` | the most results one page may ask for |
| `AllowEmptySearch` | `disabled` | an empty search is refused |
| `EnableWildcard`, `MinCharacterWildcard` | `false`, `3` | `term*` searches; costly on large sites |
| `StopWordThresholdPercent` | `60` | words in more than this share of objects are ignored |
| `DelayedIndexing` | `disabled` | `enabled` or `classbased`: index by cron instead of on publish |
| `LogSearchStats` | `disabled` | record search phrases |

## Is it enough?

| Need | Built-in |
|---|---|
| Find pages by words | yes |
| Respect permissions | yes |
| Relevance ranking | basic |
| Facets (filter by type, date, theme) | no |
| Very many objects | slows down |
| Stemming per language | no |

When the "no" rows matter, see "Solr with ezfind".

## Maintenance

Rebuild the index after imports or a database move:

```bash
php bin/php/updatesearchindex.php --clean
```

With delayed indexing, the `indexcontent.php` cronjob part must run
([10.8 Search](../../../../install/10-after-installing.md#108-search-indexing-and-image-handling)).

## Related pages

- [Site search](../../../creators/website-creators/site-search.md)
- [Searching (for editors)](../../../searching.md)
- [Search hosting backends](../../../hosting/basics/search-backends.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
