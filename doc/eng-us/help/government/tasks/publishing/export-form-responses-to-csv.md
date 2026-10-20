---
title: Export form responses to CSV
audience: government, editor
category: public-sector
order: 510
summary: For editors who process forms - download a form's submissions as a CSV file from Setup > Collected information, open it correctly, and handle the file as the personal data it is.
keywords: export CSV, form responses, collected information, infocollector, submissions, spreadsheet, UTF-8, audit data.export.csv, personal data, consultation responses
type: help
---
# Export form responses to CSV

This page is for editors who process what visitors send: consultation responses, reports, event sign-ups. Each form's
submissions can be downloaded as a CSV file for a spreadsheet ([Collected information, 2](../../../../guides/collected-information.md#2-reading-and-exporting-collections)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Your role has `infocollector/read` (everything in **Setup > Collected information** needs it).
- You have a place to keep the file where personal data belongs (not a personal laptop or a shared public drive).

## Steps

1. **Setup > Collected information** (`/infocollector/overview`).
2. Find the form: **Find an object** with part of its name, or sort the list.
3. On its card, press **Export CSV**. The file `<name>-collected.csv` downloads.
4. Open it in your spreadsheet program. It is UTF-8 with a byte order mark, so accents show correctly.

The columns: collection ID, sent and changed times (UTC), the ID of the user who sent it (the anonymous user for
visitors), then one column per form field in the order of the class. Values that a spreadsheet would read as a
formula begin with an apostrophe. At most 50 000 collections, newest first.

## Check the result

- [ ] The row count matches the collection count on the card.
- [ ] Times are in UTC; convert if you report local times.
- [ ] The export is recorded in the audit as `data.export.csv` with the number of rows (never the values).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Mailing the CSV around | store it in one place; share access, not copies |
| Keeping exports after the work is done | delete them on the same schedule as the collections ([Remove form responses that are no longer needed](remove-form-responses-that-are-no-longer-needed.md)) |
| A column empty or missing | the field was made a collector after those responses, or is no longer a collector |
| Formulas from visitors | the apostrophe protects you; do not remove it before checking |

## Related pages

- [Publishing tasks](README.md)
- [Reading and exporting form submissions](../../../creators/website-creators/form-submissions.md)
- [Retention of collected information](../../building/forms/retention-of-collected-information.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
