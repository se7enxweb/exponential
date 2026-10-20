---
title: Search backends
audience: hosting, admin
category: hosting
order: 190
summary: The search options a host can offer for Exponential sites: the built-in database search with its limits and delayed indexing, rebuilding the index, the optional Solr search through the eZ Find extension, the exp:solr command and settings/solr.ini, and what each costs to run.
keywords: search, search backend, eZSearchEngine, built-in search, DelayedIndexing, indexcontent, ezpending_actions, updatesearchindex, Solr, eZ Find, ezfind, solr.ini, exp:solr, SolrSettings, Enabled, Url, BinaryPath, LogSearchStats, personal data, hosting, add-on
type: help
---
# Search backends

This page is for product managers and system administrators at a hosting company who decide whether Exponential sites
get the built-in search only or an optional Solr search server, and who run either. It answers: how the built-in
search works and what it needs, when indexing happens, how to rebuild the index, and what Solr through the eZ Find
extension involves. Sources: [chapter 10.8](../../../install/10-after-installing.md#108-search-indexing-and-image-handling),
`settings/site.ini [SearchSettings]`, `settings/solr.ini` and
[Server control commands](../../../features/6.0/web-server-and-solr-commands.md).

## Contents

1. [The two options](#the-two-options)
2. [The built-in search](#the-built-in-search)
3. [Delayed indexing and cron](#delayed-indexing-and-cron)
4. [Rebuilding the index](#rebuilding-the-index)
5. [Solr through eZ Find](#solr-through-ez-find)
6. [The exp:solr command](#the-expsolr-command)
7. [Offering search in a plan](#offering-search-in-a-plan)
8. [Checklist](#checklist)
9. [Related pages](#related-pages)

## The two options

| | Built-in search | Solr through eZ Find |
|---|---|---|
| Engine | `eZSearchEngine` (`site.ini [SearchSettings] SearchEngine`) | the `ezfind` extension with a Solr server |
| Index lives in | the site's database | the Solr server |
| Extra service to run | none | a Solr server, local or remote |
| Installed by default | yes | no: `composer.json` suggests `se7enxweb/ezfind` |
| On MongoDB | run the cronjobs once after the install to build the index | not stated in this repository |

## The built-in search

The built-in engine keeps its index in the database, so it needs nothing beyond the site itself. Its limits, from
`settings/site.ini [SearchSettings]`:

| Key | Shipped | Meaning |
|---|---|---|
| `MaximumSearchLimit` | `30` | the most results a request may ask for |
| `MaximumSearchTextWords`, `MaximumSearchTextLength` | `16`, `1024` | longer search texts are cut |
| `AllowEmptySearch` | `disabled` | empty searches take a lot of resources |
| `EnableWildcard` | `false` | wildcard search "might heavily influence the performance" |
| `LogSearchStats` | `disabled` | the statistics store every phrase a visitor typed, which can hold personal data |

> **Note**
> Keep `LogSearchStats` off unless a customer needs the statistics and has considered the personal data they hold.
> This is not legal advice; customers must take their own counsel on what they may store.

## Delayed indexing and cron

Content is indexed when it is published, unless `[SearchSettings] DelayedIndexing` is `enabled` (or `classbased` with
`DelayedIndexingClassList[]`): then publishing only queues the object in the table `ezpending_actions`, and the
default cron part's `indexcontent.php` indexes it later. Publishing large objects is faster that way.

For a host this means: **a site with delayed indexing depends on cron.** Without the cronjobs, new content never
appears in search. Make sure the default cron part runs for every such site
([Cron jobs](../operations/cron-jobs.md)).

## Rebuilding the index

After an import, a database move, or when search results are wrong:

```bash
php bin/php/updatesearchindex.php --clean --allow-root-user      # --clean removes the old index first
```

Run it as the site's user, and on a large site in a quiet hour. With eZ Find, reindex
with the extension's own tools ([chapter 14](../../../install/14-migrating-from-4x.md)).

## Solr through eZ Find

eZ Find is "a Solr based advanced search engine for Exponential with a lot more features than the built in search"
(`composer.json` suggest). Adding it to a site:

```bash
composer require se7enxweb/ezfind:~3.0.7
php bin/php/ezpgenerateautoloads.php -e
```

then `ActiveExtensions[]=ezfind` in `settings/override/site.ini.append.php`, clear the INI cache and restart Velocity
if it serves the site ([chapter 3.5](../../../install/03-getting-the-code.md#35-extensions-are-composer-packages)).
This repository does not state which Solr versions eZ Find needs; take that from the extension's own repository
before offering Solr, rather than assuming it.

`settings/solr.ini` tells the installation where Solr is. Solr is off until switched on:

```ini
# settings/override/solr.ini.append.php
[SolrSettings]
Enabled=true
# a running Solr, local or remote, base URL with scheme and port
Url=http://127.0.0.1:8983
# or, for a Solr installed on this machine and managed by exp:solr:
#BinaryPath=/opt/solr/bin/solr
#Host=127.0.0.1
#Port=8983
#DataDir=
```

| Key | Shipped | Meaning |
|---|---|---|
| `Enabled` | `false` | master switch |
| `Url` | empty | a running Solr; with it set, Solr is treated as managed elsewhere |
| `BinaryPath` | empty | the Solr control binary of a local instance (used when `Url` is empty) |
| `Host`, `Port` | `127.0.0.1`, `8983` | where a local Solr listens |
| `DataDir` | empty | Solr's own default applies |
| `RunDir`, `LogDir` | `var/vc/solr/run`, `var/vc/solr/log` | pid and log files |
| `StartTimeout` | `60` | seconds `start` waits for Solr to answer |

## The exp:solr command

```bash
php bin/php/console exp:solr status --allow-root-user
php bin/php/console exp:solr ping --allow-root-user
php bin/php/console exp:solr start --allow-root-user      # local instance only
```

| Verb | What it does |
|---|---|
| `status` | whether Solr is configured and, if so, whether it answers |
| `start`, `stop`, `restart` | run Solr's own start and stop for a local instance (`BinaryPath`); with a remote `Url` they report that it is managed elsewhere |
| `ping` | ask Solr whether it is alive |

Until Solr is configured, every verb says so and `start` exits non-zero, so a monitoring check never mistakes "not
configured" for "running" ([Server control commands](../../../features/6.0/web-server-and-solr-commands.md#expsolr-an-optional-search-server)).

## Offering search in a plan

Options a host can choose from:

- **Built-in search only.** Nothing extra to run; suits most sites.
- **Solr as an add-on**, on a Solr server the host runs for several customers (`Url` set) or on the customer's
  dedicated server (`BinaryPath`). Plan its memory and disk separately from PHP, and back up or be able to rebuild its
  index.
- **Customer-provided Solr.** `Url` points at it; `exp:solr status` and `ping` check it, start and stop are the
  customer's.

> **Tip**
> Whatever you offer, add `exp:solr status` to monitoring for sites with Solr, and a check that the cronjobs run for
> sites with delayed indexing.

## Checklist

- [ ] Every site knows its engine: `SearchEngine` and, for eZ Find, `[SolrSettings] Enabled`.
- [ ] Cron runs the default part where `DelayedIndexing` is on.
- [ ] `LogSearchStats` off unless a customer asked for it.
- [ ] Solr's memory, disk and backups planned when offered.
- [ ] `exp:solr status` in monitoring for Solr sites.

## Related pages

- [Chapter 10.8: Search indexing and image handling](../../../install/10-after-installing.md#108-search-indexing-and-image-handling)
- [Searching](../../searching.md) (for editors)
- [Cron jobs](../operations/cron-jobs.md) and [Health checks and uptime](../operations/health-checks-and-uptime.md)
- [Server control commands](../../../features/6.0/web-server-and-solr-commands.md)
- [Composer and Git on servers](composer-and-git-on-servers.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
