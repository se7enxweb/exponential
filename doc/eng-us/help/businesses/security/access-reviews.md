---
title: Access reviews
audience: businesses, admin, project-manager
category: business-solutions
order: 60
summary: A step-by-step yearly review of who can do what on an Exponential site - roles marked as full access, sensitive policies, accounts nobody uses, people who left - using the role pages, Check user accounts and the audit trail, with a record to keep as evidence.
keywords: access review, permissions, roles, policies, least privilege, full access, administrator, user accounts, leavers, user hygiene, audit trail, role assignment, evidence, recertification
type: help
---
# Access reviews

This page is for owners and managers who must confirm, at least once a year, that only the right people can reach
the right parts of the site, and for the administrator who prepares the review. An access review catches the
editor who left last spring, the temporary administrator who was never downgraded, and the role that quietly grew.

> **Note**
> Basics, not legal advice. Some regulations and contracts require access reviews at set intervals; follow them
> where they apply.

## Contents

1. [How access works in Exponential](#how-access-works-in-exponential)
2. [Before the review](#before-the-review)
3. [Step by step](#step-by-step)
4. [Sensitive permissions to look at first](#sensitive-permissions-to-look-at-first)
5. [Between reviews](#between-reviews)
6. [A review record to keep](#a-review-record-to-keep)

## How access works in Exponential

- A **role** is a set of **policies**; each policy allows one module or function, possibly limited to sections,
  classes, subtrees, languages or siteaccesses.
- A role does nothing until it is **assigned** to users or user groups. A user has every policy of every role
  assigned to them or to their groups.
- A role with a policy over **every module** is full access, marked red in the role list; a policy over the **role**
  module lets its users give themselves any access, marked amber.

Source: [Roles and policies](../../../guides/roles-and-policies.md#in-short). `PreventPrivilegeEscalation` is enabled
by default (`settings/site.ini`), so nobody can grant more than they hold
([Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md#accounts-and-access)).

## Before the review

- [ ] Get a current list of staff, contractors and partners from HR or the contract owner.
- [ ] Agree who signs the review (the site owner) and who prepares it (an administrator).
- [ ] Note the date of the last review; the audit trail can show what changed since then.

## Step by step

1. **List the roles.** Open **Users > Roles**. Note every role marked red (full access) or amber (can change roles).
   For each, ask: does this role still need that?
2. **Read each role.** Open it and read its policies. Remove policies nobody needs; prefer limits to sections or
   subtrees over whole modules ([Roles and policies](../../../guides/roles-and-policies.md)).
3. **Check the assignments.** For each role, list the users and groups it is assigned to. Compare with the staff
   list. Pay most attention to administrators.
4. **Find unused accounts.** Open **Users > Check user accounts** and use the filters **Never signed in** and **Not
   seen for N days** (180 by default) ([Check user accounts](../../../features/6.0/user-hygiene.md)).
5. **Find shared and broken accounts.** Use the filters **Shared login**, **Shared e-mail address** and **Orphaned**.
6. **Act.** Disable accounts that are not needed (the page asks for confirmation and shows the count). Removing a user
   object is final, so disable first; the optional **Deactivate and move** setting gathers disabled accounts in one
   hidden group without roles.
7. **Check two-step sign-in** for administrators and editors, if your policy requires it
   ([User accounts and passwords](user-accounts-and-passwords.md)).
8. **Look at the record.** In the audit trail, filter the `access` channel for role assignments since the last review
   and confirm each was intended ([Compliance evidence from the audit trail](compliance-evidence-from-the-audit-trail.md)).
9. **Export the evidence.** Export the account list as CSV from Check user accounts, and the audit filter as CSV.
10. **Sign the record** below and set the next review date.

## Sensitive permissions to look at first

| Permission | Why it matters | Source |
|---|---|---|
| Any role with full access | can do everything | [Roles and policies](../../../guides/roles-and-policies.md) |
| `role` module | can grant itself any access | same |
| `infocollector/read` | reads every form submission | [Collected information](../../../guides/collected-information.md) |
| `mailpreferences/export` | exports people's mail data | [Compliance and GDPR](../../hosting/security/compliance-and-gdpr.md#requests-from-data-subjects) |
| `audit/read`, `audit/manage` | reads or manages the audit trail; only Administrator holds them on a new installation | [The audit trail](../../../features/6.0/audit-trail.md#who-may-look) |
| `user/register` | who may register at all | [Roles and policies](../../../guides/roles-and-policies.md#8-decide-who-may-register-and-who-may-read-node-data) |
| OAuth client authorization | who may connect apps | [Roles and policies](../../../guides/roles-and-policies.md#9-decide-who-may-authorize-oauth-clients) |
| Shop administration | sees customers and orders | [Store dashboard](../../../features/6.0/store-dashboard.md) |

Seeing e-mail addresses in Check user accounts itself needs the right `role/assign`
([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Between reviews

- Disable accounts on a person's last working day; do not wait for the yearly review.
- Let the audit trail's alert rules watch for an administrator role being granted; check who receives the alert mail
  with `./console exp:audit alerts recipients` ([The audit trail](../../../features/6.0/audit-trail.md#try-it-in-two-minutes)).
- Give temporary access an end date in your notes, and remove it on that date.
- For extranets with partner accounts, see [Extranet permissions review](../extranets/extranet-permissions-review.md).

## A review record to keep

```text
ACCESS REVIEW RECORD
Site: ____________________   Review date: __________   Previous review: __________
Prepared by: ______________   Approved by (site owner): ______________

Roles with full access: ______________________   Still needed? yes / no  Action: ________
Roles that can change roles: _________________   Still needed? yes / no  Action: ________
Administrators (named): ______________________   All current staff? yes / no
Accounts never signed in: ___   disabled: ___
Accounts not seen for 180 days: ___   disabled: ___
Shared logins or e-mail addresses: ___   resolved: ___
Two-step sign-in on for all administrators? yes / no
Role assignments since last review checked in the audit trail? yes / no
Evidence files (CSV exports) stored at: ______________________
Next review date: __________
Signature: ______________
```

## Related pages

- [Security basics for businesses](security-basics-for-businesses.md)
- [Restricting the administration](../../hosting/security/restricting-the-administration.md)
- [Departments, roles and sections](../intranets/departments-roles-and-sections.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
