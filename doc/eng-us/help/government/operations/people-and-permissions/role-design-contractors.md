---
title: "Role design: contractors and temporary staff"
audience: government, admin, project-manager
category: public-sector
order: 100
summary: How to give an agency, a contractor or temporary staff exactly the access a contract covers - one folder, one site, a fixed period - with personal accounts, a separate user group, limited assignments, two-step sign-in and a clean end of contract.
keywords: contractor, supplier, agency, temporary staff, external staff, vendor access, third party access, fixed term, one folder, Assign with limitation, Deactivate and move, offboarding, contract end
type: help
---
# Role design: contractors and temporary staff

This page is for administrators and contract managers in public bodies who let an outside agency, a supplier or
temporary staff work on the website. The goal is access that matches the contract and ends with it. Everything uses
the role model of [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md) and the account
tools of [Check user accounts](../../../../features/6.0/user-hygiene.md).

## Contents
1. [Principles](#principles)
2. [A role for a content contractor](#a-role-for-a-content-contractor)
3. [A role for a development or design contractor](#a-role-for-a-development-or-design-contractor)
4. [During the contract](#during-the-contract)
5. [At the end of the contract](#at-the-end-of-the-contract)
6. [Related pages](#related-pages)

## Principles

- **Personal accounts only.** One account per person, never a shared "agency" login: the audit trail records people,
  and **Check user accounts** can then find accounts nobody uses. The filter **Shared e-mail address** finds accounts
  created with one team address.
- **A group per contract.** Put the contractor's accounts in a user group of their own (for example "Contract
  2026-07 - web agency"), so one assignment gives and one removal takes away.
- **Limit by place.** Assign the role with **Assign with limitation: Subtree** to the folder the contract covers.
- **No administration rights.** No `role`, `setup/administrate`, `audit` or user-account rights; contractors who
  need more than content work usually need a test copy, not production access.
- **Time-box it.** Exponential has no expiry date on a role assignment or an account. Put the end date in your
  contract register and act on it (below).

## A role for a content contractor

For an agency that writes or migrates content in one folder (the "Department editor" pattern, narrowed):

| Module | Function | Limitations | Why |
|---|---|---|---|
| user | login | SiteAccess: the editor siteaccess (not the administration) | sign in where content is edited |
| content | read | Section: Standard, Media | see the context of their folder |
| content | create, edit | Class: the classes in the brief | create only what the contract covers |
| content | versionread, diff | none | follow their own versions |
| content | dashboard | none | **My drafts**, **My pending items** |
| user | password | none | change their own password |

Assign it to the contract's group **with limitation: Subtree** = the contract folder. Do not give `content/remove`
unless the contract includes removing content; do not exclude the group from the approval workflow, so a staff
member approves everything before it is public ([Role design: approvers](role-design-approvers.md)).

## A role for a development or design contractor

Template and code work belongs in version control and a test copy, not in production: with
[Configuration managed by deployment](../upgrades-and-change/managed-by-deployment.md) switched on, the template
editor and settings pages are locked on the live site anyway. On production such a contractor usually needs at most:

| Module | Function | Why |
|---|---|---|
| user | login (SiteAccess: administration) | sign in |
| setup | logs | read masked logs to diagnose a problem |
| setup | system_info | read the system information report |

Debug output for their address only is possible with an expiry date in `DebugIPList[]`
([Security and audit, step 4](../../../../guides/security-and-audit.md#4-debug-output-for-your-address-only-5-minutes)).

## During the contract

- Require two-step sign-in for the contract's accounts ([Two-factor for staff](two-factor-for-staff.md)).
- Look at the `access` channel of the audit trail for their sign-ins if your contract requires monitoring.
- When the folder changes, change the limited assignment, not the role.

## At the end of the contract

1. On the role page, tick the contract group's assignment and **Remove selected**: the accounts lose the role at once.
2. **Users > Check user accounts**: search for the accounts, tick them and **Disable**; their sessions end.
3. If your site uses it, **Deactivate and move** the accounts instead: they are disabled and moved into a hidden group
   without roles, keeping their content and the record of what they did
   ([Deactivate and move](../../../../features/6.0/user-hygiene.md#deactivate-and-move)).
4. Export the list as CSV for the contract file (**Export the list as CSV**).
5. Remove IP addresses of the contractor from any debug or network allow-list.

Removing a user account is final (user objects never go to the trash), which is why disabling or deactivating is
preferred: the audit trail and the content keep a valid owner.

## Related pages
- [Let a contractor edit only one folder](../../tasks/people-and-permissions/let-a-contractor-edit-only-one-folder.md)
- [Joiners, movers and leavers](joiners-movers-leavers.md)
- [Customer offboarding checklist (hosting)](../../../hosting/checklists/customer-offboarding.md)
- [Least-privilege principles](least-privilege-principles.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
