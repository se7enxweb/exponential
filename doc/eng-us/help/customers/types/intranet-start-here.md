---
title: Intranets: start here
audience: customer, admin
category: customer-success
order: 70
summary: For organizations that run their intranet on Exponential: sign-in and two-factor login, roles per department, news for staff, documents people can find, notifications, and the routines of joiners and leavers.
keywords: intranet, staff, employees, sign-in, LDAP, directory, two-factor, departments, roles, sections, documents, file library, notifications, news, search
type: help
---
# Intranets: start here

An intranet is used by people who did not choose it: every employee, every day, for the news, the forms, the
documents and the phone list. It succeeds when people find what they need without asking a colleague, and when
nobody sees what they should not. The work of running it is mostly about people: who joins, who leaves, who may
see which department's pages, and who keeps each part current.

This page is for the intranet owner (often in communications, HR or IT) and the editors in each department.
Planning an intranet is covered in [Intranets for businesses](../../businesses/intranets/README.md).

## What Exponential gives an intranet

| Need | What carries it | Where it is explained |
|---|---|---|
| Staff sign in, ideally with their company account | User accounts; a directory (LDAP) sign-in where your partner set it up | [Directory sign-in and LDAP](../../businesses/intranets/directory-sign-in-and-ldap.md) |
| A second factor for sign-in | Two-factor login | [Two-factor login](../../../guides/two-factor-login.md) |
| Each department sees and edits its own part | Roles, policies and sections | [Departments, roles and sections](../../businesses/intranets/departments-roles-and-sections.md), [Roles and policies](../../../guides/roles-and-policies.md) |
| News for staff | Articles and front pages, laid out with Layouts where used | [News and announcements](../../businesses/intranets/news-and-announcements.md) |
| Documents people can find | Files, a file library and the search | [Documents and file libraries](../../businesses/intranets/documents-and-file-libraries.md), [Intranet search](../../businesses/intranets/intranet-search.md) |
| Staff told about changes | Notifications | [Notifications for staff](../../businesses/intranets/notifications-for-staff.md) |
| Changes checked before they go out | Approval workflows | [Approval workflows](../../businesses/intranets/approval-workflows.md) |
| Evidence of who changed what | The tamper-evident audit trail | [Audit trail](../../../features/6.0/audit-trail.md) |

## Who owns what

- **The intranet owner** keeps the structure, the roles and the contact with IT, the host or the partner.
- **Department editors** keep their department's pages current; each department has a named editor.
- **IT or the partner** connects sign-in to the company directory, runs upgrades and backups.
- **HR** tells the intranet owner when people join, change department or leave, so access follows.

## The routines that matter most

- **Joiners**: when someone starts, their account and roles are ready on day one
  ([Onboarding new staff](../day-to-day/onboarding-new-staff.md)).
- **Movers**: when someone changes department, their roles change the same day; old rights are removed, not
  kept "just in case" ([Choosing roles for new staff](../day-to-day/choosing-roles-for-new-staff.md)).
- **Leavers**: when someone leaves, their account is disabled on their last day
  ([Offboarding staff](../day-to-day/offboarding-staff.md)). If sign-in uses the company directory, check with IT
  what disabling the directory account does to the intranet account.
- **Every quarter**: open **Users > Check user accounts** (`user/hygiene`, `doc/eng-us/features/6.0/user-hygiene.md`) and
  review accounts that never signed in or have not been seen for a long time; and ask each department editor to
  review their pages ([Content audits](../day-to-day/content-audits.md)).

> **Warning**
> An intranet often holds information that must not leave the organization. Before anyone connects an assistant or
> an external service (for example Exponential Intelligence with a hosted provider), read its data policy and decide
> which sections it may read ([Exponential Intelligence for owners](../growth/exponential-intelligence.md)).

## Your next step

Follow the [intranet reading path](intranet-reading-path.md). An intranet in several languages should add the
[multilingual reading path](multilingual-site-reading-path.md).

## Related pages

- [Intranet reading path](intranet-reading-path.md)
- [Intranet rollout checklist (businesses)](../../businesses/intranets/intranet-rollout-checklist.md)
- [Intranet FAQ (businesses)](../../businesses/intranets/intranet-faq.md)
- [Extranets](../../businesses/extranets/README.md): areas for customers and partners
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
