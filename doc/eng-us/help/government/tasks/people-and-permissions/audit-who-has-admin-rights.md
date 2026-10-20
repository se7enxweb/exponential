---
title: Audit who has admin rights
audience: government, admin, project-manager
category: public-sector
order: 120
summary: Step by step - list every account with full access or the right to change roles, using the role list badges, the role pages, Check user accounts and the audit trail, and record the result for an access review.
keywords: admin rights, administrators, full access, Can change roles, role list, access review, audit, privileged accounts, admin_role_granted, recertification
type: help
---
# Audit who has admin rights

This task is for administrators and auditors preparing an access review. At the end you have a dated list of the
accounts that hold full access or can change roles, with evidence of how they got it. Background:
[Admin access reviews](../../operations/people-and-permissions/admin-access-reviews.md).

## Contents
1. [Before you start](#before-you-start)
2. [Find the privileged roles](#find-the-privileged-roles)
3. [Find the people](#find-the-people)
4. [Check the history](#check-the-history)
5. [Record the result](#record-the-result)
6. [Related pages](#related-pages)

## Before you start

- To read role pages you need the `role` module (an administrator does this step, or shows the pages to the
  auditor). For the audit steps, `audit/read` on the `access` channel.
- Shell access for the commands (optional).

## Find the privileged roles

1. Open **Users > Roles and policies** (`/role/list`).
2. Look for the badges: *Full access* (red, a policy over every module) and *Can change roles* (amber, a policy over
   the `role` module).
3. Note each such role. The Administrator role has *Full access* on every installation.

## Find the people

1. Open each privileged role. The page shows the assignments (users and groups, with limitations) and **users
   affected in all**.
2. For each group, open the group in the Users tree to list its members.
3. Cross-check on **Users > Check user accounts** (`/user/hygiene`): search for each account; the row shows its roles
   (own and inherited), state and last visit.
4. Mark accounts that are disabled, never signed in or not seen for a long time.

## Check the history

On the shell:

```bash
./console exp:audit alerts list --allow-root-user
./console exp:audit search --name='access.*' --from='<start of the period>' --limit=100 --allow-root-user
./console exp:audit verify --allow-root-user
```

The alert rule `admin_role_granted` fires when an administrator role is granted; each fired alert should match an
approved request. `verify` shows the record is intact. In the browser: **Audit** tab, **Alerts** (`/audit/alerts`)
and **Console**.

## Record the result

| Field | Content |
|---|---|
| Date and reviewer | |
| Privileged roles | names and badges |
| Accounts | login, how assigned (group or direct), last visit, two-step sign-in on or off |
| Findings | accounts to remove, grants without a request |
| Evidence | the audit search output, the `verify` result |

Then act: remove assignments that are not needed (**Remove selected** on the role page) and disable unused accounts
([Remove a role assignment](remove-a-role-assignment.md)).

## Related pages
- [Export the account list for a review](export-the-account-list-for-a-review.md)
- [Check what a role allows](check-what-a-role-allows.md)
- [Least-privilege principles](../../operations/people-and-permissions/least-privilege-principles.md)
- [Audit alerts](../../operations/monitoring/audit-alerts.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
