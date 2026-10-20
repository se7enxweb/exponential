---
title: Handling requests from collected information
audience: government, editor, admin
category: public-sector
order: 70
summary: How a FOI team reads, exports and removes request submissions in Setup > Collected information, how to move them into a case process, and how long to keep them.
keywords: collected information, infocollector, request handling, FOI requests, export CSV, remove collections, retention, case management, infocollector/read
type: help
---
# Handling requests from collected information

This page is for the people who handle requests for information that arrive through a web form. In Exponential the
submissions are kept under **Setup > Collected information** (`/infocollector/overview`), with a CSV export and a
removal that asks first ([Collected information](../../../guides/collected-information.md)). Exponential has no case
management: deadlines, assignments and reminders belong in your case process.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Who may read requests](#who-may-read-requests)
2. [Reading a request](#reading-a-request)
3. [Moving it into the case process](#moving-it-into-the-case-process)
4. [Exporting](#exporting)
5. [Removing](#removing)
6. [A simple register as content](#a-simple-register-as-content)

## Who may read requests

Everything on the page needs the policy `infocollector/read`. Give it only to the request team, through a role
assigned to their user group ([Roles and policies](../../../guides/roles-and-policies.md)). Exports and removals are
recorded in the audit trail as `data.export.csv` and `data.infocollection.remove`, with counts but never values.

## Reading a request

1. Open **Setup > Collected information**. Each card is a form object with its number of collections.
2. Press **Collections** on the request form's card: one row per submission.
3. Open a row to see every value the requester sent.

## Moving it into the case process

For each new submission:

1. Create the case in your case system, with the date of the submission (the time is shown in UTC).
2. Send the acknowledgment from the team mailbox.
3. Note the case reference next to the collection ID in the case, not in the website.
4. Once the case is created, the submission in Exponential is only a copy; remove it according to your retention rule.

## Exporting

**Export CSV** on the card downloads `<name>-collected.csv`: collection ID, when it was sent and changed, the ID of
the user who sent it (the anonymous user for visitors), then one column per collected field. UTF-8 with a byte
order mark; values that a spreadsheet would read as a formula begin with an apostrophe; at most 50 000 collections,
newest first. The file contains personal data: store it only where your case files are allowed to be, and delete
it when it is no longer needed.

## Removing

- All submissions of a form: tick the card on the overview, **Remove selected**, read the confirmation, **Remove**.
- Some submissions: on the form's collection list tick them and **Remove selected**.

The form itself stays. Removal cannot be undone; export first if the data is still needed.

## A simple register as content

Bodies without a case system sometimes keep a minimal internal register as content: a class `request_case` with
reference, received date, due date, status (object state group "Request status": Received, In progress, Answered,
Closed) and handler team, in a section only the team may read. This gives versions and an audit trail, but no
reminders: someone must review the due dates. Exponential does not calculate legal deadlines.

## Related pages

- [A request form with the information collector](a-request-form-with-the-information-collector.md)
- [Object states](../../../guides/object-states.md)
- [Privacy standards](../../../standards/privacy.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
