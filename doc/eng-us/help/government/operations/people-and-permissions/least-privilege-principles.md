---
title: Least-privilege principles
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Seven working rules for giving public-sector staff only the access they need in Exponential, each tied to the feature that supports it, with the rights that deserve special care.
keywords: least privilege, minimum access, need to know, administrator role, full access, role design, privilege escalation, security policy, access control policy, NIST, ISO 27001
type: help
---
# Least-privilege principles

This page is for administrators and information security officers who write or apply an access policy for a public
website. Least privilege means every account holds only what its work needs. Many public bodies have it in their
security policy, often from frameworks such as the [NIST Cybersecurity Framework](https://www.nist.gov/cyberframework)
or [ISO/IEC 27001](https://www.iso.org/standard/27001). Below are working rules for Exponential, each with the feature
that supports it.

## Contents
1. [Seven rules](#seven-rules)
2. [Rights that deserve special care](#rights-that-deserve-special-care)
3. [What the administration does for you](#what-the-administration-does-for-you)
4. [A one-page access policy](#a-one-page-access-policy)
5. [Related pages](#related-pages)

## Seven rules

1. **Start from nothing.** A user has only what roles give; nothing is granted by default except through the roles
   of the groups the user is in. Design roles by adding policies, never by trimming a copy of Administrator.
2. **Never widen Administrator.** "Never edit the Administrator role to give a colleague a bit more; make a new
   role" ([Security and audit, step 2](../../../../guides/security-and-audit.md#2-give-people-only-the-access-they-need-10-minutes)).
   Keep the number of full administrators small; granting the role raises an audit alert.
3. **Limit every content policy.** Limit `content/edit`, `create`, `remove` and `publish` by subtree, section or
   class. An edit right also opens the object's versions, so limit edit as you limit read
   ([Security and audit, step 2](../../../../guides/security-and-audit.md#2-give-people-only-the-access-they-need-10-minutes)).
4. **Assign to groups, with limitations.** One role per job, assigned to the group of a department with **Assign
   with limitation**; people join and leave groups, roles stay stable.
5. **Separate the sign-in.** `user/login` takes a `SiteAccess` limitation: staff who only edit can be allowed the
   editor or public siteaccess and kept out of the administration siteaccess
   ([Keep editors out of the admin siteaccess](../../tasks/people-and-permissions/keep-editors-out-of-the-admin-siteaccess.md)).
6. **Give operations rights one by one.** The `setup` module has separate functions: `logs` (read logs),
   `managelogs`, `unmasklogs`, `managecache`, `managecronjobs`, `system_info`, `administrate`. A colleague who clears
   caches needs `setup/managecache`, not the whole module.
7. **Review on a schedule.** Access drifts; an [admin access review](admin-access-reviews.md) twice a year (or as
   your policy says) finds it.

## Rights that deserve special care

| Right | Why | Give it to |
|---|---|---|
| any policy over every module | full access, *Full access* badge | a few named administrator accounts |
| the `role` module | lets the holder change roles and so give themselves more; *Can change roles* badge | administrators only |
| `setup/unmasklogs` | shows e-mail and IP addresses in logs unmasked | the people who investigate incidents, when needed |
| `user/hygiene` | everyone with it sees name, login, state, groups and roles of every account | account administrators, not editors ([Check user accounts](../../../../features/6.0/user-hygiene.md#who-may-use-it)) |
| `role/assign` | needed to see e-mail addresses on Check user accounts; the `role` module defines no separate functions in `kernel/role/module.php`, so in practice this means a policy over the whole `role` module | administrators only; account administrators without it see accounts without addresses |
| `audit/manage` | archives, audit settings, "Verify now" | the audit owner |
| `content/cleantrash` | empties the trash for good | administrators |
| `section/edit` | creates and removes sections; removal cannot be undone | administrators |

## What the administration does for you

- **No escalation:** with `PreventPrivilegeEscalation=enabled` (default), nobody can grant beyond their own access
  ([Roles guide, "Who may do this"](../../../../guides/roles-and-policies.md#who-may-do-this)).
- **Badges:** the role list marks *Full access*, *Can change roles* and *Not assigned*.
- **Audit:** permission changes are recorded; the rule `admin_role_granted` sends an alert
  ([Audit alerts](../monitoring/audit-alerts.md)).
- **Account check:** **Users > Check user accounts** lists accounts never signed in, not seen for N days, locked or
  sharing an address ([User account hygiene](user-account-hygiene.md)).

## A one-page access policy

A template for the rules you adopt (fill in your own values):

| Topic | Your rule |
|---|---|
| Who may hold full access | e.g. two named administrator accounts of the web team, personal, never shared |
| How roles are requested | e.g. ticket approved by the content owner of the area |
| Default editor role | e.g. "Department editor" limited to the department subtree |
| Sign-in to the administration | e.g. web team and approvers only; editors use the editor siteaccess |
| Two-step sign-in | e.g. required for every role that can publish or change roles |
| Review interval | e.g. every six months, and when a contract ends |
| Leavers | e.g. disabled on the last working day, deactivated and moved within a week |

## Related pages
- [Roles, policies and limitations](roles-policies-and-limitations.md)
- [Separation of duties](separation-of-duties.md)
- [Restricting the administration (hosting)](../../../hosting/security/restricting-the-administration.md)
- [Security standard](../../../../standards/security.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
