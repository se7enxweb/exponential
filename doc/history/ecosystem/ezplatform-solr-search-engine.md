# Ecosystem repository: ezplatform-solr-search-engine

**Group:** Search, cache and API. **Period in the ledger:** 2024-03-26 to 2026-04-12. **Changes:** 6 (3 made by the se7enxweb team, 3 upstream history carried by the fork).

## What it is

Solr search engine integration.

## How it relates to Exponential

Renamed to the se7enxweb organisation with a replace shim.

## What a user gets

Full-text search through Solr.

Install it with Composer (a project that already requires the platform pulls it in by itself):

```bash
composer require se7enxweb/ezplatform-solr-search-engine
```

## Counts by kind

| Kind | Changes |
|---|---|
| Fixes | 4 |
| Behaviour and upgrade changes | 2 |

## Releases made by the se7enxweb team

| Date | Tag | Commit | Change |
|---|---|---|---|
| 2025-09-28 | v3.3.18 | `8d0ab01` | Update symfony/http-client version to 5.4.45 |
| 2026-04-12 | v3.3.19 | `4daf39b` | add replace shim for ezsystems/* original package |

## Changes made by the se7enxweb team, by theme

### Package renamed to the se7enxweb vendor (1)

- 2025-09-28 `d58e0e4` bc: Update package details for se7enxweb organization

### Other changes to the fork (1)

- 2025-09-28 `8d0ab01` fix: Update symfony/http-client version to 5.4.45

### Replace declarations for the upstream package (1)

- 2026-04-12 `4daf39b` bc: add replace shim for ezsystems/* original package

## Upstream history carried by the fork, by month

The fork contains the full upstream history. The table counts it by month and kind; the busiest changes of each month (by files touched) are named.

| Month | Changes | Features | Fixes | BC | Security | Perf | Docs | Tooling | Release | No benefit | Busiest changes |
|---|---|---|---|---|---|---|---|---|---|---|---|
| 2024-03 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `6ddce40` Fixed distribution link forcing https redirection |
| 2024-05 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `2fb300d` Fixed distribution link in generate-solr-config.sh forcing redirection |
| 2024-07 | 1 | 0 | 1 | 0 | 0 | 0 | 0 | 0 | 0 | 0 | `19ff229` IBX-8378: Fixed handling non-indexable field types |

## Full record

- Every change with date, kind, size and release tag: [ledger of ezplatform-solr-search-engine](ledger/ezplatform-solr-search-engine.md).
- Overview of all platform repositories: [Ecosystem](../ecosystem.md).
