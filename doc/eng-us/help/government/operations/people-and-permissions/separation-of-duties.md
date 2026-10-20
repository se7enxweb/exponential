---
title: Separation of duties
audience: government, admin, project-manager
category: public-sector
order: 140
summary: How to split the powers over a public website between people - authors, approvers, account administrators, system administrators and auditors - so no single account can publish unreviewed content, grant itself rights and erase the record, and where Exponential needs your process to close a gap.
keywords: separation of duties, segregation of duties, four eyes, two-person rule, conflict of interest, administrator, approver, auditor, account administrator, internal control, audit finding
type: help
---
# Separation of duties

This page is for managers and administrators who must show an auditor that no single person controls a public
website end to end. It names the duties, the Exponential rights that carry each one, and the places where a process
has to back the software up.

## Contents
1. [The duties](#the-duties)
2. [Which rights carry which duty](#which-rights-carry-which-duty)
3. [Combinations to avoid](#combinations-to-avoid)
4. [Where the software needs your process](#where-the-software-needs-your-process)
5. [Small teams](#small-teams)
6. [Related pages](#related-pages)

## The duties

| Duty | Typical holder |
|---|---|
| Author content | department editors |
| Approve publication | approvers, legal reviewers |
| Administer accounts | service desk or account administrators |
| Grant roles and change settings | system administrators (few) |
| Operate the platform (caches, cronjobs, logs) | web team or operations |
| Review the record | auditors, information security officer |

## Which rights carry which duty

| Duty | Rights in Exponential | Role page |
|---|---|---|
| Author | `content/create`, `edit` (and so publish in the edit form) within a subtree | [Department editors](role-design-department-editors.md) |
| Approve | named in an Approve event; collaboration module; `content/versionread` | [Approvers](role-design-approvers.md) |
| Administer accounts | `user/hygiene`, `user/deactivate`, `content/edit` on the user objects | [User account hygiene](user-account-hygiene.md) |
| Grant roles, settings | the `role` module, `setup/administrate`, full access | [Least-privilege principles](least-privilege-principles.md) |
| Operate | `setup/managecache`, `managecronjobs`, `logs`, `system_info` | [Web team](role-design-web-team.md) |
| Review | `audit/read` by channel, `setup/logs`, `content/versionread` | [Auditors](role-design-auditors.md) |

## Combinations to avoid

| Combination | Risk | Control |
|---|---|---|
| Author and sole approver of the same area | self-approval | approvers' own content is let through by the Approve event; keep approvers out of authoring there, or give them a separate author account path covered by another approver ([Two-person rule](../editorial-workflows/two-person-rule-for-publishing.md)) |
| Account administration and role granting | create an account and give it any right | `role` module only for system administrators; `PreventPrivilegeEscalation=enabled` limits what others could grant |
| Full access and audit management | change settings and the record of it | the audit is hash-chained and verified (`exp:audit verify`); send records to a syslog or webhook sink outside the server ([Audit trail](../../../../features/6.0/audit-trail.md#sinks-alerts-and-archives)) |
| Operations and unmasked logs | casual access to personal data | `setup/unmasklogs` only during investigations |

## Where the software needs your process

- **Administrators can do everything.** A policy over every module cannot be limited. Keep administrator accounts
  personal and few, require two-step sign-in, and rely on the audit trail and the `admin_role_granted` alert.
- **The server operator is outside the roles.** Commands run on the shell check no policy; for example,
  `./console exp:logs` is "the server's operator: no policy is checked" ([Log viewer](../../../../features/6.0/log-viewer.md#on-the-command-line)).
  Shell access is a duty of its own, governed by your server access rules.
- **The audit trail is tamper-evident, not tamper-proof.** Someone with shell access could delete files; the
  hash chain and signed manifests show it, and an outside sink keeps a copy
  ([Audit trail verification](../monitoring/audit-trail-verification.md)).
- **Approval of settings changes** is outside Exponential: put settings in version control and lock them on the live
  site with [ManagedByDeployment](../upgrades-and-change/managed-by-deployment.md), so changes pass your change board.

## Small teams

A team of two or three cannot separate everything. Common compromises: one person holds full access but publishes
through an author account that needs approval; the second person approves and reviews the audit export monthly;
an outside party (another department, the information security officer) reads the `access` channel quarterly.
Write the compromise down, so an auditor sees it was decided, not overlooked.

## Related pages
- [Roles, policies and limitations](roles-policies-and-limitations.md)
- [Admin access reviews](admin-access-reviews.md)
- [Change management and advisory boards](../upgrades-and-change/change-management-and-advisory-boards.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
