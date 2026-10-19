---
title: Turn on search statistics
audience: government, admin
category: public-sector
order: 330
summary: Task for administrators: record what visitors search for on a public siteaccess (LogSearchStats), read Setup > Search statistics, and reset the phrases on a schedule because they can contain personal data.
keywords: search statistics, LogSearchStats, Setup Search statistics, search/stats, zero results, reset statistics, privacy
type: help
---
# Turn on search statistics

This task is for administrators. Background: [Search statistics and privacy](../../building/search/search-statistics-and-privacy.md).

## Steps

1. In each public siteaccess that should record searches:

   ```ini
   # settings/siteaccess/<public siteaccess>/site.ini.append.php
   [SearchSettings]
   LogSearchStats=enabled
   ```

2. Clear the INI cache.
3. Mention the recording in the privacy notice.
4. After some days, open **Setup > Search statistics** (`/search/stats`); use **Show** for phrases that found nothing,
   and **Order** by **Most searched**.
5. Act on the findings (add content, add words people use) and set a date to **Reset statistics**.

## Check

The page names the siteaccesses that record searches; new phrases appear after visitors search.

## Problems

| Problem | Fix |
|---|---|
| The page warns that no siteaccess records searches | the setting is on the wrong siteaccess, or INI cache |
| Phrases contain names | reset the statistics and keep only aggregates |

## Related pages

- [Links, URL aliases, wildcards and search statistics](../../../../guides/urls-and-aliases.md#5-search-statistics)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
