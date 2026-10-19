---
title: Export and remove form submissions
audience: government, admin, editor
category: public-sector
order: 310
summary: Step by step - the periodic retention routine for web form submissions in Exponential: export what must be kept to your case or records system as CSV, then remove the collections on Setup > Collected information, and delete the export files.
keywords: form submissions, information collection, collected information, CSV export, remove collections, retention, routine, infocollector/read, data.export.csv, data.infocollection.remove, public sector
type: help
---
# Export and remove form submissions

For administrators and form owners. Nothing removes form submissions automatically; a monthly or quarterly routine
keeps them within your retention rules ([Information collection forms and privacy](../../privacy/information-collection-forms.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Before you start

- The policy `infocollector/read` (needed for every page under Setup > Collected information).
- The retention rule per form, and where kept submissions go (case system, records system).

## Steps

1. Open **Setup > Collected information** (`/infocollector/overview`). Sort by **last collection** or **collections**.
2. For each form whose rule says "export":
   **Export CSV** (`infocollector/export/<object id>`, file `<name>-collected.csv`, UTF-8 with a byte order mark,
   newest first, at most 50 000). Import it into the case or records system.
3. Remove what is due:
   - whole forms: tick them on the overview, **Remove selected**, check the confirmation (it says how many collections
     go and that the forms stay), **Remove**;
   - some collections only: open the form's **Collections**, tick them, **Remove selected**.
4. Delete the CSV files from your computer and any shared drive.
5. Record the run: date, forms, counts.

## Check that it worked

- The overview's figures show the new counts.
- The audit shows `data.export.csv` for each export (rows counted, never values) and `data.infocollection.remove` for
  each removal: `/audit/console/(name)/data.*`.

## Related pages

- [Collected information](../../../../guides/collected-information.md)
- [Form submissions as records](../../records-and-archiving/collected-information-as-records.md)
- [Form submissions for website creators](../../../creators/website-creators/form-submissions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
