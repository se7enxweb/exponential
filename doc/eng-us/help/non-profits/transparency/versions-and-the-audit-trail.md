---
title: Versions and the audit trail
audience: non-profit, admin, editor, government
category: non-profits
order: 50
summary: How a non-profit uses Exponential's version history and tamper-evident audit trail for accountability - what each records, how many versions are kept (45 by default, per class configurable), comparing and restoring versions, what the audit trail records and protects, who may read it (audit/read), verifying it, and where both stop being a complete record.
keywords: versions, version history, compare, restore, VersionManagement, 45 versions, audit trail, tamper-evident, hash chain, audit/read, exp:audit verify, accountability, evidence, who changed what
type: help
---
# Versions and the audit trail

This page is for administrators, directors and anyone who must answer "what did this page say, and who changed it?".
Two features answer that question in Exponential: the **version history** of each object, and the **audit trail** of
the whole installation. Together they make a non-profit's site accountable without any extra tool.

## Contents

1. [Two records, two purposes](#two-records-two-purposes)
2. [The version history](#the-version-history)
3. [How many versions are kept](#how-many-versions-are-kept)
4. [The audit trail](#the-audit-trail)
5. [Who may read them](#who-may-read-them)
6. [Where they stop](#where-they-stop)
7. [Using them for accountability](#using-them-for-accountability)
8. [Checklist](#checklist)

## Two records, two purposes

| | Version history | Audit trail |
|---|---|---|
| Records | The content of each version of one object | Events across the installation: publishing, removals, sign-ins, settings, exports |
| Answers | "What did the page say on that date?" | "Who did what, and when?" |
| Can be changed? | Old versions can be removed by those allowed | Tamper-evident: a hash chain, `verify` detects edits |
| Read in | The versions page of an object | The **Audit** tab, or `exp:audit` on the command line |

## The version history

Every publication of an object is a version. The versions page lets you compare two versions, make a new draft from an
old one and publish it, and remove old versions ([Versions of an object](../../../guides/content-history.md)). For
accountability, see [Version history as accountability](../../government/transparency-and-foi/version-history-as-accountability.md).

Use it to:

- show what the Donate page or a policy said on a given date;
- undo a mistaken or malicious change;
- document corrections to financial documents ([Annual publications as downloads](annual-publications-as-downloads.md#corrections)).

## How many versions are kept

Exponential keeps **45 versions per object by default**, configurable per class; when the limit is reached the oldest
are removed ([Version history limit](../../government/records-and-archiving/version-history-limit.md)). For
documents you must be able to trace for years, an administrator can raise the limit for those classes
([Keeping versions longer](../../government/transparency-and-foi/keeping-versions-longer.md)).

## The audit trail

From [The audit trail](../../../features/6.0/audit-trail.md):

- It records "sign-ins and refusals, content changes, settings writes, cache clears, deploys, orders and VAT changes"
  in a tamper-evident log that is on by default and needs no setup.
- Every record carries the hash of the record before it; `exp:audit verify` tells you if anything was edited or cut.
- It respects privacy: addresses are truncated, sessions hashed; passwords, tokens and secrets are never recorded.
- Channels: `content`, `access`, `system`, `commerce`, and `read` (off unless switched on).
- Default keeping: 90 days live and 730 days archived for the main channels.

Once on a new installation: make sure the `frequent` cronjob group runs, check who gets alert mail, and back up
`settings/override/audit.ini.append.php`, which holds the signing keys (same source).

## Who may read them

| Record | Permission | Advice |
|---|---|---|
| Versions | Those who may edit the object | Normal for editors |
| Audit trail | `audit/read`; only Administrator holds it on a new installation | A reporting role limited to the channels you need can be made without rights to change anything ([Analytics and what to measure](../../customers/day-to-day/analytics-and-what-to-measure.md)) |

## Where they stop

- Versions beyond the limit are gone; removed versions are gone.
- The audit trail keeps records for its configured period, then they expire.
- Neither replaces your official records, minutes or accounts.
- Changes made directly in the database or files on the server, outside Exponential, are not content versions.

See [Where the history stops](../../government/transparency-and-foi/version-history-as-accountability.md) and
[Audit records as evidence](../../government/records-and-archiving/audit-records-as-evidence.md).

## Using them for accountability

| Situation | Use |
|---|---|
| A donor says the giving page promised something else | Compare versions of the page for that date |
| Someone changed the Donate link | Audit trail `content` channel, then restore the earlier version |
| A funder asks what was published during the grant | Audit trail publish events and versions ([Grant outcomes for funders](../grants/grant-outcomes-for-funders.md)) |
| A volunteer leaves and you need to know what they changed | Audit trail filtered by user ([When volunteers leave](../operations/when-volunteers-leave.md)) |
| You must show applications were deleted on schedule | Audit events `data.infocollection.remove` ([Processing volunteer applications](../volunteers/processing-volunteer-applications.md)) |

## Checklist

- [ ] The `frequent` cronjob group runs (audit indexing and rotation).
- [ ] Audit keys backed up with the settings.
- [ ] Version limit raised for classes you must trace for years.
- [ ] `audit/read` given only to those who need it.
- [ ] `exp:audit verify` run at least yearly ([Annual checklist](../checklists/annual-checklist.md)).

## Related pages

- [Transparency for non-profits](README.md)
- [The audit trail for who changed what](../../government/transparency-and-foi/the-audit-trail-for-who-changed-what.md)
- [Compliance evidence from the audit trail](../../businesses/security/compliance-evidence-from-the-audit-trail.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
