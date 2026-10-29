---
title: Quarterly checklist
audience: customer, admin, businesses
category: customer-success
order: 40
summary: The quarterly checklist for the owner of an Exponential site - a restore test, an access review, extension updates, a content audit, accessibility spot checks, costs against budget and the follow-up of the yearly decisions.
keywords: quarterly checklist, restore test, access review, roles, extension updates, content audit, accessibility, budget, follow-up, site owner
type: help
---
# Quarterly checklist

This page is the owner's check every three months. It holds the tasks that are too big for a month and too
important for a year: proving that backups restore, reviewing who has access, and checking that the year's plan is
on track. Book half a day for it, with the host or partner for the technical parts.

## The checklist

```text
QUARTERLY CHECKLIST - quarter: ________     Owner: ________

RECOVERY (with the host)
[ ] Restore test done on another machine; date ______, time it took ______
[ ] The restored copy opened and showed recent content and orders
[ ] Backup retention still matches the retention schedule

ACCESS
[ ] Every administrator and shop account belongs to a current person
[ ] Roles still match what people do (editors, approvers, store staff)
[ ] Two-step sign-in in place for administrators, as decided

SOFTWARE (with the partner)
[ ] Extensions updated, or the reason they are not
[ ] Unused extensions listed for removal
[ ] PHP version still supported; next upgrade planned

CONTENT
[ ] Content audit of one section: outdated pages updated, hidden or removed
[ ] Accessibility spot check of the main pages and one form
[ ] Menus and landing pages still lead where visitors go

MONEY
[ ] Costs of the quarter against the budget (cost register)
[ ] Figures for the ROI worksheet collected for the quarter

PLAN
[ ] Decisions of the annual value review: on track? Owners reminded
[ ] Campaigns of the next quarter: dates and preparation started
```

## Where each item is done

| Item | Read |
|---|---|
| Restore test | [Restore drills](../../../businesses/reliability/restore-drills.md), [Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md) |
| Access review | [Access reviews](../../../businesses/security/access-reviews.md), [Roles and policies](../../../../guides/roles-and-policies.md) |
| Two-step sign-in | [Two-factor login](../../../../guides/two-factor-login.md) |
| Extension updates | [Budgeting for upgrades and maintenance](../../../businesses/costs/budgeting-for-upgrades-and-maintenance.md) |
| PHP support | `README.md`: PHP 8.0 to 8.5 (PHP 7.4 usable without support) supported; your host plans the upgrade |
| Content audit | [The content checks](../../../../features/6.0/content-checks.md), [Publishing and versions](../../../publishing-and-versions.md) |
| Accessibility | [Testing accessibility](../../../creators/website-creators/testing-accessibility.md) |
| Costs and ROI figures | [Cost of ownership for owners](../cost-of-ownership-for-owners.md), [Measuring ROI](../measuring-roi.md) |
| Plan follow-up | [Annual value review](../annual-value-review.md) |

## Why the restore test matters most

A backup is only proven by a restore. The businesses section's maintenance calendar puts a restore test in every
quarter ([Budgeting for upgrades and maintenance](../../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)).
Ask for a short written note each time: the date, which backup, how long it took, and what was checked on the
restored copy. Keep the notes; they are the evidence that your recovery works.

> **Warning**
> A restore test runs on another machine or a copy, never over the live site. Make sure your host understands that
> before they start.

## Related pages

- [Monthly checklist](monthly.md)
- [Yearly checklist](yearly.md)
- [Restore drills](../../../businesses/reliability/restore-drills.md)
- [Access reviews](../../../businesses/security/access-reviews.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
