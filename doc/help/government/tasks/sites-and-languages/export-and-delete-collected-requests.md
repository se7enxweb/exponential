---
title: Export and delete collected requests
audience: government, admin, editor
category: public-sector
order: 290
summary: Task for the FOI team: export the submissions of a request form as CSV from Setup > Collected information for the case file, then remove them from the website according to the retention rule.
keywords: collected information, export CSV, remove collections, retention, infocollector, requests, personal data
type: help
---
# Export and delete collected requests

This task is for the people who handle requests (policy `infocollector/read`). Background:
[Handling requests from collected information](../../transparency-and-foi/handling-requests-from-collected-information.md).

## Steps

1. **Setup > Collected information** (`/infocollector/overview`).
2. On the request form's card press **Export CSV**: `<name>-collected.csv`, UTF-8, one row per submission (at most
   50 000, newest first).
3. Store the file only where case files may be kept.
4. When the cases are created and your retention rule allows: tick the form's card, **Remove selected**, read the
   confirmation (it says how many collections go and that the form stays), **Remove**.

To remove only some submissions: open **Collections** of the form, tick them, **Remove selected**.

## Check

The card shows no (or fewer) collections; the audit trail records `data.export.csv` and `data.infocollection.remove`
with counts, never values.

## Problems

| Problem | Note |
|---|---|
| Removed by mistake | removal cannot be undone; only a database backup has the data |
| The export opens with garbled characters | import as UTF-8 |

## Related pages

- [Collected information](../../../../guides/collected-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
