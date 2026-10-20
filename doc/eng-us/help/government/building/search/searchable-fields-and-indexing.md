---
title: Searchable fields and indexing
audience: government, website-creator, admin
category: public-sector
order: 30
summary: Which fields of public sector content classes to mark Searchable, how indexing on publish and delayed indexing work, how to rebuild the index, and the pitfalls (internal codes, hidden content, large imports).
keywords: searchable attribute, indexing, search index, updatesearchindex, DelayedIndexing, indexcontent, content classes, relevance, search quality
type: help
---
# Searchable fields and indexing

This page is for site builders and administrators. What visitors can find depends on which attributes are marked
**Searchable** in the class editor and on the index being current
([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md#attribute-settings)).

## Contents

1. [What to mark searchable](#what-to-mark-searchable)
2. [When content is indexed](#when-content-is-indexed)
3. [Rebuilding the index](#rebuilding-the-index)
4. [Pitfalls](#pitfalls)

## What to mark searchable

| Class | Searchable | Not searchable |
|---|---|---|
| Service page | title, summary, body, keywords | internal service code, owner |
| News | title, intro, body | publish flags |
| Document (file) | name, description; the file text if extraction is set up | file size |
| Decision, meeting | title, text, reference | internal notes |
| Dataset | title, description, keywords | license code |

Marking fewer, meaningful fields improves results; internal codes create noise.

## When content is indexed

- By default, on publish.
- With `site.ini [SearchSettings] DelayedIndexing=enabled` (or `classbased`), by the `indexcontent.php` cronjob
  part, which makes publishing faster for large sites; results lag until the cronjob runs.

## Rebuilding the index

After changing which fields are searchable, after imports, or after a database move:

```bash
php bin/php/updatesearchindex.php --clean
```

On a large site run it at a quiet time; with Solr the same command fills Solr.

## Pitfalls

- A field marked searchable after content exists is only indexed for that content after a rebuild or a re-publish.
- Hidden content: the built-in engine checks visibility when it searches; Solr keeps visibility in its index, and
  the `hide.php` cronjob tells the search engine when it hides content ([Cronjobs](../../../../guides/cronjobs.md)).
- Words in more than `StopWordThresholdPercent` (60%) of objects are ignored; a word in every page title (the body's
  name) does not help searching.

## Related pages

- [Built-in search for public sites](built-in-search-for-public-sites.md)
- [Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
