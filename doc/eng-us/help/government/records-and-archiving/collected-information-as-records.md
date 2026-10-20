---
title: Form submissions as records
audience: government, admin, project-manager
category: public-sector
order: 240
summary: For public bodies whose web forms start cases (requests, complaints, applications) - how Exponential stores submissions, exporting them into a case or records system, removing them on schedule, and the audit records of exports and removals.
keywords: form submissions, information collection, collected information, records, cases, requests, complaints, CSV export, infocollector, removal, retention, audit, data.export.csv, data.infocollection.remove, case management, public sector
type: help
---
# Form submissions as records

This page is for records managers, administrators and the teams that receive web forms. A complaint or an application
sent through the website may be the start of a case and a record. Exponential stores each submission as a collection
([Collected information](../../../guides/collected-information.md)); it is not a case management system, so records
should move to the system that manages the case.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Where submissions are](#where-submissions-are)
2. [Moving them into your case system](#moving-them-into-your-case-system)
3. [Removing them](#removing-them)
4. [Related pages](#related-pages)

## Where submissions are

- **Setup > Collected information** (`/infocollector/overview`, policy `infocollector/read`): the forms with figures; per
  form the list of collections, each with every value.
- A mail to the receiver if the form sends one: another copy, in a mailbox.
- Nothing removes submissions automatically.

## Moving them into your case system

| Way | How |
|---|---|
| By mail | the form's mail to a functional mailbox that your case system reads |
| By export | **Export CSV** per form (`infocollector/export/<object id>`): collection id, time sent and changed (UTC), sender user id, one column per field; recorded in the audit as `data.export.csv` with the number of rows |
| By development | a workflow or extension that passes each submission to the case system; your developers' work |

Record the collection id in the case, so the link to the original stays traceable while it exists.

## Removing them

When the submission is safely in the case system (or its retention has ended):

1. Setup > Collected information, open the form, tick the collections (or tick whole forms on the overview).
2. **Remove selected**; the confirmation says how many go; the forms themselves stay.
3. Delete exported CSV files that are no longer needed.

Removals are recorded as `data.infocollection.remove`. Collections of objects that were removed or are in the trash are
not listed (the overview counts them in red); restore the object from the trash to reach them.

## Related pages

- [Collected information](../../../guides/collected-information.md)
- [Form submissions](../../creators/website-creators/form-submissions.md)
- [Information collection forms and privacy](../privacy/information-collection-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
