---
title: Reindexing
audience: dxp-developer
category: dxp-development
order: 32
summary: When and how to rebuild the DXP's search index with exponential:reindex - the full rebuild, batches, single content items, one content type or one subtree - and how to run it safely on a large site.
keywords: reindex, exponential:reindex, ibexa:reindex, search index, iteration-count, content-type, subtree, content-ids, Solr commit, memory, cron
type: help
---
# Reindexing

This page is for developers and administrators who keep the search index in step with the content. Publishing
updates the index by itself; you reindex after events that bypass publishing.

## Contents

1. [When to reindex](#when-to-reindex)
2. [The commands](#the-commands)
3. [Large sites](#large-sites)
4. [Checking the result](#checking-the-result)

## When to reindex

- After the install (the install command does it unless you pass `--skip-indexing`).
- After importing content or restoring a database.
- After switching search engines.
- After a content type change that affects searchable fields.
- After `composer update` when the content model schema may have changed (the guide's update steps include it).

## The commands

`exponential:reindex` is the primary name; `ibexa:reindex` is its alias.

```bash
php bin/console exponential:reindex                              # everything
php bin/console exponential:reindex --iteration-count=100        # batch size
php bin/console exponential:reindex --content-ids=2,34,68        # only these content items
php bin/console exponential:reindex --content-type=article       # one content type
php bin/console exponential:reindex --subtree=45                 # one subtree
```

With Solr, the guide forces a commit after indexing:

```bash
curl "http://localhost:8983/solr/<your core>/update?commit=true"
```

## Large sites

- Run it with `--env=prod` and outside busy hours.
- Lower `--iteration-count` when memory is tight; raise it when the database is fast and memory is plenty.
- The guide runs it "without memory limit in production" for big indexes; prefer a sensible batch size first.
- Rebuild one content type or subtree at a time when only part of the site changed.

## Checking the result

- Search in `/adminui/` for a known recent item.
- With Solr, compare the number of documents with the number of content items.
- "Search results outdated" in [Troubleshooting](troubleshooting-dxp.md) lists the usual causes.

## Related pages

- [Search: the database engine and Solr](search-legacy-and-solr.md)
- [Console commands and renames](console-commands-and-renames.md)
- [Platform console command names](../../specifications/6.0/platform-console-commands.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
