---
title: Processing volunteer applications
audience: non-profit, editor, businesses, admin
category: business-solutions
order: 40
summary: The volunteer coordinator's routine with Exponential's collected information - who may read applications (infocollector/read), the overview with its figures, reading one application, answering, exporting to CSV safely, removing applications on a schedule, and what the audit trail records - with a weekly routine and a retention plan.
keywords: volunteer applications, collected information, infocollector/read, Export CSV, remove selected, retention, audit trail, coordinator, weekly routine, personal data, reply
type: help
---
# Processing volunteer applications

This page is for the volunteer coordinator. Applications arrive by mail and are stored in **Setup > Collected
information**. Both contain personal data, so the routine below is as much about deleting as about reading. The
reference is [Collected information](../../../guides/collected-information.md).

## Contents

1. [Who may read applications](#who-may-read-applications)
2. [The overview](#the-overview)
3. [Reading and answering](#reading-and-answering)
4. [Exporting safely](#exporting-safely)
5. [Removing applications](#removing-applications)
6. [What the audit trail records](#what-the-audit-trail-records)
7. [The weekly routine](#the-weekly-routine)
8. [A retention plan](#a-retention-plan)

## Who may read applications

Everything in **Collected information** needs the policy `infocollector/read`. "Give it to the people who process the
forms, not to every editor" ([Reading and exporting form submissions](../../creators/website-creators/form-submissions.md)).
Name the holders in the [Roles and permissions worksheet](../templates/roles-and-permissions-worksheet.md), and give
them two-factor login ([Two-factor login](../../../guides/two-factor-login.md)).

## The overview

**Setup > Collected information** shows figures (collections in all, in the last 30 and 7 days, the latest), and one
card per form with **Collections**, **Export CSV** and **Object**. The figures for the last 30 days are a quick way
to see whether recruitment is working.

## Reading and answering

1. Open **Collections** on the volunteer form's card: one row per application.
2. Open a row to see every value.
3. Answer from the shared mailbox that received the form mail, so the reply history stays with the organization.
4. Record your decision where your organization keeps volunteer records (not in the site).

> **Tip**
> Answer every application, even with a short "not right now". The site's thank-you page promised a reply; keep the
> promise in the time it named.

## Exporting safely

**Export CSV** downloads the applications of one form: UTF-8 with a byte order mark, one column per field, at most
50 000 rows, values that a spreadsheet would read as a formula start with an apostrophe. Each export is recorded in
the audit trail with the number of rows, never the values ([Collected information](../../../guides/collected-information.md#2-reading-and-exporting-collections)).

- Export only when you need to (for example to plan an induction day).
- Store the file where personal data belongs, not on a personal laptop or a shared drive everyone can open.
- Delete the file when the task is done.

## Removing applications

Tick applications on the form's list and choose **Remove selected**; the confirmation says how many go and that the
form stays. Removals are recorded in the audit trail as `data.infocollection.remove` (same source). Export first if
you still need anything.

## What the audit trail records

| Action | Recorded as | Values recorded? |
|---|---|---|
| CSV export | `data.export.csv` with the row count | No |
| Removal | `data.infocollection.remove` | No |

So you can show that applications were exported and deleted according to your plan, without the audit trail itself
holding the personal data ([The audit trail](../../../features/6.0/audit-trail.md)).

## The weekly routine

- [ ] Open the overview; check the 7-day figure.
- [ ] Read and answer new applications.
- [ ] Remove spam in bulk.
- [ ] Delete any CSV exports you made this week.
- [ ] Remove applications older than your retention period.

## A retention plan

Write it down and put it next to the form ("we keep applications for ... months"):

| Kind | Keep for (your decision) | Then |
|---|---|---|
| Applications not taken forward | | Remove in Collected information |
| Accepted volunteers' applications | Until the details are in your volunteer records | Remove |
| Spam | Not at all | Remove weekly |
| CSV exports | Until the task is done | Delete the file |

Backups also hold old applications; they expire with your backup rotation. See
[Data retention and deletion](../../businesses/security/data-retention-and-deletion.md).

> **Note**
> Basics, not legal advice.

## Related pages

- [Volunteers](README.md)
- [Donor and volunteer data protection](../operations/donor-and-volunteer-data-protection.md)
- [Information collection forms (privacy)](../../government/privacy/information-collection-forms.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
