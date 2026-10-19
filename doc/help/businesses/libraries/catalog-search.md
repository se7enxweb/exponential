---
title: Catalog search
audience: businesses, website-creator, admin
category: business-solutions
order: 40
summary: How readers find records in a catalog on Exponential - the built-in search engine, advanced search, what gets indexed, searching inside files, search statistics, and when to add Solr through the ezfind extension, with its costs.
keywords: catalog search, library search, site search, advanced search, Solr, ezfind, facets, relevance, search statistics, full text, PDF search, indexing
type: help
---
# Catalog search

This page is for library and archive managers deciding how readers will search the catalog, and for the website
creator who sets it up. It explains what the built-in search does, how to learn from what readers search for, and when
the extra work of a Solr server through `ezfind` pays off.

## Contents

1. [Two engines](#two-engines)
2. [The built-in search](#the-built-in-search)
3. [What gets indexed](#what-gets-indexed)
4. [Searching inside files](#searching-inside-files)
5. [Learning from what readers search](#learning-from-what-readers-search)
6. [When Solr is worth it](#when-solr-is-worth-it)
7. [Switching to Solr, in outline](#switching-to-solr-in-outline)
8. [Browsing complements searching](#browsing-complements-searching)
9. [Search checklist](#search-checklist)

## Two engines

| | Built-in engine | Solr through `ezfind` |
|---|---|---|
| Installed with the product | Yes (`SearchEngine=eZSearchEngine`) | No: `se7enxweb/ezfind` is a Composer suggestion, plus a separate Solr server |
| Find records by words | Yes | Yes |
| Relevance ranking and boosting | Basic | Yes |
| Facets (filter by class, tag, date) | No | Yes |
| Very large numbers of objects | Slows down | Built for it |
| Tag clouds from search | No | Yes |

Sources: [Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md#when-solr-is-worth-it)
and the `suggest` block of `composer.json`.

## The built-in search

- `site.ini [SearchSettings] SearchEngine=eZSearchEngine` indexes the attributes marked **Searchable** when content is
  published. "Visitors only find what they may read", so restricted records stay hidden from the public.
- `content/search` takes a search box; `content/advancedsearch` offers more fields (phrase, class, date), useful for
  a catalog: "books only, published after 2000".
- Shipped limits include `MaximumSearchLimit=30` results per page and wildcards off (`EnableWildcard=false`), because
  `term*` searches are "costly on large sites".

Source: [Site search](../../creators/website-creators/site-search.md).

## What gets indexed

Mark only useful fields **Searchable**: "titles, intros, bodies; not internal codes" (same page). For a catalog, also
mark the identifiers readers type, such as an ISBN, and the creators' names on Person objects.

Subject headings from `eztags` are indexed with their synonyms by default (`IndexSynonyms=enabled`), so a reader who
types a non-preferred term still finds the record ([Tagging and subject headings](tagging-and-subject-headings.md#headings-in-search)).

After a large import, rebuild the whole index:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

With `DelayedIndexing` enabled, content is indexed by cron instead of on publish, which keeps publishing fast during
imports ([Site search, Indexing](../../creators/website-creators/site-search.md#indexing)).

## Searching inside files

`settings/binaryfile.ini` has handlers that extract text from PDF and Word files for the search index
(`[PDFHandlerSettings]`, `[WordHandlerSettings]`), "using external programs you install". With Solr, file contents can
be indexed too ([Files and downloads, Searching inside files](../../creators/website-creators/files-and-downloads.md#searching-inside-files)).
Ask your hosting partner to install the extraction programs if digitized documents should be found by their text.

> **Note**
> Text extraction works on documents that contain text. A scan stored only as an image has no text to extract unless
> it was made searchable (with OCR) before upload. The code and settings of the repository contain no OCR feature,
> so do that step in your scanning software.

## Learning from what readers search

With `LogSearchStats=enabled` on the public siteaccess, **Setup > Search statistics** (`/search/stats`) shows every
phrase, how often it was searched and the average number of results; "phrases that found nothing show what content is
missing". Phrases can be personal data, which is why logging is off by default
([Site search, Search statistics](../../creators/website-creators/site-search.md#search-statistics)).

Use it monthly: searches with no results point to missing records, missing synonyms, or spelling variants to add as
tag synonyms. Decide with your data protection contact whether to switch it on and how long to keep the phrases.

## When Solr is worth it

Consider Solr when one of these is true:

- [ ] Readers need to narrow results by facets (format, subject, date, language).
- [ ] The catalog has grown to the point where searches slow down.
- [ ] Relevance ranking matters (best matches first) and the built-in ranking is not good enough.
- [ ] The full text of many files must be searchable.

And weigh the cost: "Solr is a separate server (Java) to install, run, monitor and back up. For a small site the
built-in engine is fine" ([Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md#when-solr-is-worth-it)).
For hosting, see [Search backends](../../hosting/basics/search-backends.md).

## Switching to Solr, in outline

1. Install a Solr server (locally, or a remote one).
2. Set `[SolrSettings] Enabled=true` and its address in `settings/override/solr.ini.append.php`.
3. Check with `./console exp:solr status`, then `ping`.
4. Install and activate `ezfind`; its settings switch `[SearchSettings] SearchEngine` to `ezsolr`.
5. Clear the INI cache and rebuild the index.

Source: [Search with ezfind and Solr, Switching it on](../../creators/website-creators/search-with-ezfind-and-solr.md#switching-it-on)
and [Server control commands](../../../features/6.0/web-server-and-solr-commands.md). `content/search` keeps working;
facets need templates that use ezfind's own fetch functions.

## Browsing complements searching

Many readers browse rather than search. Give them:

- subject pages from tags ([Tagging and subject headings](tagging-and-subject-headings.md#subject-pages-and-lists-by-subject));
- collections as containers in the menu ([Modeling catalog records](modeling-catalog-records.md#collections-and-series));
- a "new acquisitions" list and feed ([Feeds for new acquisitions](feeds-for-new-acquisitions.md)).

## Search checklist

- [ ] Searchable set on purpose for each field of each record class.
- [ ] A search results template that shows the number of results, paging and what to try when nothing is found.
- [ ] Advanced search offered if readers need class or date filters.
- [ ] Index rebuilt after every large import.
- [ ] Text extraction programs installed if file contents must be found.
- [ ] Search statistics decided (on or off, retention) with the data protection contact.
- [ ] A dated decision on Solr: not yet, or planned with a hosting partner.

## Related pages

- [Libraries and catalogs](README.md)
- [Site search](../../creators/website-creators/site-search.md)
- [Search with ezfind and Solr](../../creators/website-creators/search-with-ezfind-and-solr.md)
- [Searching (for editors)](../../searching.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
