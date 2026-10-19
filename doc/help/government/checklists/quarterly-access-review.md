---
title: Quarterly access review
audience: government, admin, project-manager
category: public-sector
order: 80
summary: A quarterly checklist for reviewing who can do what in an Exponential site of a public body - accounts, unused accounts, roles and assignments, administrators, API keys, sessions and the audit trail - with the admin pages and commands for each step.
keywords: access review, user accounts, roles, policies, least privilege, unused accounts, check user accounts, administrators, API keys, sessions, audit, quarterly, public sector
type: help
---
# Quarterly access review

This checklist is for the site administrator and the site owner of a public body, every quarter. People change jobs
and leave; their access must follow. The pages and commands come from [Roles, policies and unactivated users](../../../guides/roles-and-policies.md),
[Check user accounts](../../../features/6.0/user-hygiene.md) and [Security and audit](../../../guides/security-and-audit.md).

## Contents

1. [Prepare](#prepare)
2. [Accounts](#accounts)
3. [Roles and assignments](#roles-and-assignments)
4. [Keys and sessions](#keys-and-sessions)
5. [Audit trail](#audit-trail)
6. [Record the review](#record-the-review)

## Prepare

- [ ] Get the current list of staff and contractors from HR or the contract manager.
- [ ] Get last quarter's review record to compare.
- [ ] Sign in with your own named administrator account.

## Accounts

- [ ] Open **Check user accounts** (`/user/hygiene`) and narrow the list to accounts unused for your chosen number of days ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] For each leaver or unused account: deactivate it, or with **Deactivate and move** enabled move it to the hidden group without roles; the tool does not remove accounts by itself.
- [ ] Remove accounts that must go, one at a time; removal erases mail preferences and anonymizes the consent log ([Privacy standards](../../../standards/privacy.md#gdpr)).
- [ ] Check unactivated accounts waiting for activation ([Roles, policies and unactivated users](../../../guides/roles-and-policies.md)).
- [ ] No shared or generic accounts are in use.

## Roles and assignments

- [ ] Open **Roles** (`/role/list`) and review each role's policies; a role that grants everything should have very few members ([Roles and policies in order](../../../features/6.0/role-policy-order.md)).
- [ ] For each role, check its assigned users and groups and their limitations (section, subtree) ([Role assignment paging](../../../features/6.0/role-assignment-paging.md)).
- [ ] Each person's role matches their job and their training ([Roles to courses](../procurement/training/roles-to-courses.md)).
- [ ] Contractors have only what the current contract needs, limited to their sections.

## Keys and sessions

- [ ] Personal API keys: revoke those of leavers and those no longer used ([Personal API keys](../../../guides/api-keys.md)).
- [ ] OAuth clients reviewed (**Setup > oAuth admin**) if the REST API is used.
- [ ] **Setup > Sessions**: sign out sessions of removed accounts ([Sessions](../../../guides/sessions.md)).
- [ ] Two-step sign-in still set up for every administrator, where your site uses it ([Two-step sign-in](../../../guides/two-factor-login.md)).

## Audit trail

- [ ] Search for role grants this quarter: the alert rules watch an administrator role being granted ([Security and audit](../../../guides/security-and-audit.md)).
- [ ] Review failed sign-ins and alerts on the audit dashboard (`/audit/dashboard`).
- [ ] Check the chain: `./console exp:audit verify`.

```bash
./console exp:audit search --name='access.*' --limit=50 --allow-root-user
./console exp:audit verify --allow-root-user
```

## Record the review

- [ ] Date, reviewer, accounts deactivated or removed, roles changed, keys revoked.
- [ ] Open issues with an owner and a date.
- [ ] Keep the record with personal data limited to names and roles.

## Related pages

- [Government checklists](README.md)
- [Offboarding a contractor](offboarding-a-contractor.md)
- [Annual review](annual-review.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
