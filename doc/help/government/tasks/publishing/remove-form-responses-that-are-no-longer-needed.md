---
title: Remove form responses that are no longer needed
audience: government, editor
category: public-sector
order: 520
summary: For editors who process forms - remove submissions from Setup > Collected information when their retention period ends, for whole forms or single responses, with the confirmation and audit record, and remember the copies elsewhere.
keywords: remove responses, delete submissions, collected information, retention, Remove selected, data.infocollection.remove, personal data, storage limitation, cleanup
type: help
---
# Remove form responses that are no longer needed

This page is for editors who handle form submissions. Exponential keeps them until someone removes them; there is no
automatic deletion. Removing on schedule keeps personal data only as long as needed
([Collected information, 3](../../../../guides/collected-information.md#3-removing-collections),
[Retention of collected information](../../building/forms/retention-of-collected-information.md)).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Know the retention period of the form (set by your organization).
- Export first if the data is still needed for a report ([Export form responses to CSV](export-form-responses-to-csv.md)).
- Your role has `infocollector/read`.

## Steps

**All responses of one or more forms:**

1. **Setup > Collected information**. Sort by **first collection** to find the oldest.
2. Tick the forms whose responses should go, press **Remove selected**.
3. Read the confirmation: it says how many collections go, lists the forms with their counts, and says the forms
   themselves stay. Press **Remove**.

**Some responses of one form:**

1. On the form's card, press **Collections**.
2. Tick the responses to remove (for example spam, or those older than the period), **Remove selected**, confirm.

## Check the result

- [ ] The message says how many collections of how many forms were removed.
- [ ] The card's count dropped.
- [ ] The removal is recorded in the audit as `data.infocollection.remove`.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Forgetting copies | delete the related mails in the receiving mailbox and old CSV exports too |
| Removing a form object instead | the responses stay in the database, counted in red on the overview; restore the form from the trash to remove them properly |
| Removing before the period ends | some responses may be records (consultations); check |
| Removal is final | there is no trash for collections; export first if unsure |

## Related pages

- [Publishing tasks](README.md)
- [Export form responses to CSV](export-form-responses-to-csv.md)
- [Privacy standards](../../../../standards/privacy.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
