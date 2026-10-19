---
title: Reset search statistics
audience: government, admin
category: public-sector
order: 320
summary: Step by step - check which siteaccesses record search phrases, use the figures, and remove all recorded phrases with Reset statistics on Setup > Search statistics, because phrases can contain names and case numbers.
keywords: search statistics, search phrases, reset statistics, LogSearchStats, personal data, minimization, Setup Search statistics, public sector
type: help
---
# Reset search statistics

For administrators. Search statistics are off by default (`[SearchSettings] LogSearchStats=disabled`); where they are on,
they keep what visitors typed, which can include names and case numbers ([Search phrases](../../privacy/search-phrases.md)).

## Contents

1. [Steps](#steps)
2. [Check that it worked](#check-that-it-worked)
3. [Related pages](#related-pages)

## Steps

1. Open **Setup > Search statistics** (`/search/stats`). The page says which siteaccesses record searches, or warns that
   none does.
2. Use the figures first: **Show** the phrases that found nothing; note missing content or words for the editors.
3. **Reset statistics**: a confirmation in place says how many phrases go; confirm. They are removed for good.
4. If you no longer need the statistics, switch them off in the siteaccess's `site.ini.append.php`:

   ```ini
   [SearchSettings]
   LogSearchStats=disabled
   ```

   and clear the INI cache.
5. Put the next reset in your calendar (for example quarterly).

## Check that it worked

The page shows no phrases; after new searches on a recording siteaccess, only new phrases appear.

## Related pages

- [Search phrases](../../privacy/search-phrases.md)
- [URLs and search statistics](../../../../guides/urls-and-aliases.md#5-search-statistics)
- [Data minimization](../../privacy/data-minimization.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
