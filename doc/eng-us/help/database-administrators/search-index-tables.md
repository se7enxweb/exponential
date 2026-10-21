---
title: The built-in search index tables
audience: dba
category: database-administration
order: 36
summary: How Exponential's built-in search engine keeps its index in the database - the tables ezsearch_word, ezsearch_object_word_link and ezsearch_search_phrase, delayed indexing through ezpending_actions and indexcontent.php, rebuilding the index with updatesearchindex.php, numbers that do not fit an integer column, search statistics as personal data, and where Solr fits.
keywords: search, search index, eZSearchEngine, ezsearch_word, ezsearch_object_word_link, ezsearch_search_phrase, DelayedIndexing, ezpending_actions, index_object, index_moved_node, indexcontent.php, updatesearchindex.php, --clean, LogSearchStats, MAX_INT, clampInteger, Solr, ezfind
type: help
---
# The built-in search index tables

This page is for database administrators of a site that uses Exponential's built-in search engine. That engine keeps
its index in the site's own database, so its tables are among the largest and busiest. This page answers: which
tables hold the index, how content gets indexed, how to rebuild the index, and what to watch. Sources:
[10.8 Search](../../install/10-after-installing.md#search) and
[Operating a site, numbers in the search index](../../guides/operating-a-site.md#numbers-in-the-built-in-search-index).

## Contents

1. [The engine](#the-engine)
2. [The tables](#the-tables)
3. [When content is indexed](#when-content-is-indexed)
4. [Rebuilding the index](#rebuilding-the-index)
5. [Large numbers](#large-numbers)
6. [Search statistics are personal data](#search-statistics-are-personal-data)
7. [Solr instead](#solr-instead)
8. [Checklist](#checklist)

## The engine

The built-in engine is `eZSearchEngine` (`site.ini [SearchSettings] SearchEngine`). It works on every supported
database. Related objects are indexed with the object that points to them, one level deep.

## The tables

| Table | Holds | Notes |
|---|---|---|
| `ezsearch_word` | one row per distinct word, with `object_count` | indexed on `word` and `object_count` |
| `ezsearch_object_word_link` | one row per word occurrence in an object: object, class, attribute, word, previous and next word, placement, frequency, `integer_value`, section, published | the largest search table; indexed on object, word, identifier, frequency, `integer_value` |
| `ezsearch_search_phrase` | phrases visitors searched for, with counts | only filled with `LogSearchStats` enabled |

## When content is indexed

At publish, unless `[SearchSettings] DelayedIndexing` is `enabled` (or `classbased` with
`DelayedIndexingClassList[]`). With delayed indexing publishing only queues the work in `ezpending_actions`
(`index_object`), and the default cronjob part's `indexcontent.php` indexes it later, oldest first, removing each row
once done. This makes publishing faster for large objects.

```sql
SELECT action, COUNT(*), MIN(created) FROM ezpending_actions GROUP BY action;
```

A growing `index_object` count with an old `MIN(created)` means the cronjobs do not run
([Housekeeping: cronjobs and queue tables](housekeeping-cronjobs-and-queues.md)).

## Rebuilding the index

After an import, a database move, a restore without the search tables, or when results are wrong:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user
```

`--clean` removes all search data before indexing. Other options from its help: `--db-host`, `--db-user`,
`--db-password`, `--db-database`, `--db-driver` (`--db-type`) to index into another database, and `--sql` to display
the queries. The console name is `exp:updatesearchindex`. A rebuild reads every object and writes the two large
tables; run it off-peak and expect the database to grow its log files meanwhile. On MongoDB, run the cronjobs once
after install to build the index ([Setting up MongoDB](mongodb-setup.md#build-the-search-index)).

## Large numbers

The engine stores each word with its numeric value in `integer_value`, so a search can filter on numbers. A very large
number in a text (a 20-digit order number) no longer breaks indexing: each driver knows the range of its integer
columns (`$db::MAX_INT`, `$db::MIN_INT`: signed 32-bit for MySQL, PostgreSQL and Oracle, signed 64-bit for SQLite and
MongoDB), and `$db->clampInteger()` stores the largest value the column holds. Nothing to do on an upgrade.

## Search statistics are personal data

`LogSearchStats` is disabled by default, because it stores every phrase visitors type, which can be personal data.
If you enable it, include `ezsearch_search_phrase` in your retention rules.

## Solr instead

Large sites often move search to Solr through the ezfind extension, when it is installed; `php bin/php/console
exp:solr` controls a Solr server once it is installed and configured. The built-in tables then stop growing; Solr's
index lives outside the database and needs its own backup or rebuild plan. With ezfind, moves and visibility changes
queue `index_moved_node` actions as well.

## Checklist

- [ ] The cronjobs run, so `ezpending_actions` does not grow.
- [ ] A full rebuild is planned after imports, restores and engine moves.
- [ ] `ezsearch_object_word_link` is included in capacity planning.
- [ ] `LogSearchStats` stays disabled unless statistics are needed and covered by retention rules.

## Related pages

- [Searching](../searching.md)
- [The tables a database administrator should know](tables-you-should-know.md)
- [Search backends](../hosting/basics/search-backends.md) (for hosting companies)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
