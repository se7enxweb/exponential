---
title: People and permissions tasks
audience: government, admin, project-manager
category: public-sector
order: 10
summary: Index of step-by-step tasks for staff access on a public-sector Exponential site - roles for departments, approvers, auditors and contractors, joiners and leavers, passwords and two-step sign-in, sections, states and access reviews.
keywords: tasks, how to, roles, permissions, approver, department editor, contractor, auditor, leaver, new staff, password reset, two-step sign-in, lockout, access review, sections, states, index
type: help
---
# People and permissions tasks

This folder holds short, numbered procedures for administrators and service-desk staff who manage staff access to a
public website. Each task names the rights it needs, the exact screens and commands, how to check the result and what
to write in a change record. The explanations behind them are in
[People and permissions](../../operations/people-and-permissions/README.md) and
[Editorial workflows](../../operations/editorial-workflows/README.md).

## Contents
1. [Roles and assignments](#roles-and-assignments)
2. [Accounts: joiners, movers and leavers](#accounts-joiners-movers-and-leavers)
3. [Sign-in security](#sign-in-security)
4. [Sections, states and approvals](#sections-states-and-approvals)
5. [Reviews and audits](#reviews-and-audits)
6. [Suggested order](#suggested-order)
7. [Related pages](#related-pages)

## Roles and assignments

| Task | Answer in one line |
|---|---|
| [Create a department editor role for a subtree](create-a-department-editor-role-for-a-subtree.md) | One role, assigned per department group with a subtree limitation. |
| [Create a user group for a department](create-a-user-group-for-a-department.md) | A group per department carries the roles; people join and leave groups. |
| [Assign a role with a subtree limitation](assign-a-role-with-a-subtree-limitation.md) | **Assign with limitation**, Subtree, the folder, the group. |
| [Assign a role with a section limitation](assign-a-role-with-a-section-limitation.md) | **Assign with limitation**, Section, the section, the group. |
| [Copy a role for a new department](copy-a-role-for-a-new-department.md) | Usually assign instead; copy only when the policies differ. |
| [Remove a role assignment](remove-a-role-assignment.md) | Tick the assignment on the role page, **Remove selected**. |
| [Give an approver rights](give-an-approver-rights.md) | Add to the approvers' group; the role needs the collaboration module and versionread. |
| [Give an auditor read access to the audit trail](give-an-auditor-read-access-to-the-audit-trail.md) | `audit/read` by channel, masked logs, no write rights. |
| [Give log access without admin rights](give-log-access-without-admin-rights.md) | `setup/logs`; `managelogs` and `unmasklogs` only when needed. |
| [Give a colleague cache-clearing rights](give-a-colleague-cache-clearing-rights.md) | `setup/managecache` alone. |
| [Let a contractor edit only one folder](let-a-contractor-edit-only-one-folder.md) | A contract group, a narrow role on one folder, approval on, a clean end. |
| [Keep editors out of the admin siteaccess](keep-editors-out-of-the-admin-siteaccess.md) | Limit `user/login` by SiteAccess. |
| [Check what a role allows](check-what-a-role-allows.md) | Read the policy sentences and badges, then test with a test account. |

## Accounts: joiners, movers and leavers

| Task | Answer in one line |
|---|---|
| [Add a new staff member](add-a-new-staff-member.md) | Create the account inside the department's group. |
| [Move a staff member to another department](move-a-staff-member-to-another-department.md) | Add the new location, set it main, remove the old one. |
| [Remove a leaver](remove-a-leaver.md) | Disable on the last day, hand over, then retire. |
| [Deactivate and move a leaver](deactivate-and-move-a-leaver.md) | Disabled and moved into a hidden group without roles; reversible. |
| [Find accounts not used for months](find-accounts-not-used-for-months.md) | **Not seen for N days** and **Never signed in** on Check user accounts. |
| [Unlock a locked account](unlock-a-locked-account.md) | Look at the failed sign-ins first, then **Unlock**. |
| [Close open registration](close-open-registration.md) | No `user/register` for visitors; `PolicyOmitListExclude[]` on upgraded sites. |

## Sign-in security

| Task | Answer in one line |
|---|---|
| [Reset a staff password](reset-a-staff-password.md) | Prefer **Forgot your password?**; otherwise the Password card. |
| [Reset a password from the command line](reset-a-password-from-the-command-line.md) | `resetuserpassword.php -u <login> -g`, as root or authorized by an administrator. |
| [Switch on account lockout](switch-on-account-lockout.md) | `MaxNumberOfFailedLogin` with a trusted network list. |
| [Turn on two-step sign-in for staff](turn-on-two-step-sign-in-for-staff.md) | Field, policies, encryption key, enroll administrators, then `Enforce2FA`. |
| [Help a user who lost their phone](help-a-user-who-lost-their-phone.md) | Verify the person, switch the field to e-mail codes, set up again. |

## Sections, states and approvals

| Task | Answer in one line |
|---|---|
| [Create a section for restricted content](create-a-section-for-restricted-content.md) | A new section is invisible to visitors; give staff read access. |
| [Let visitors read a new section](let-visitors-read-a-new-section.md) | Add the section to the Anonymous role's `content/read`. |
| [Set up an approval workflow for a section](set-up-an-approval-workflow-for-a-section.md) | Approve event, trigger **Before publishing content**, workflow cronjob. |
| [Set up a review state group](set-up-a-review-state-group.md) | Draft, In review, Approved; approve existing content first. |

## Reviews and audits

| Task | Answer in one line |
|---|---|
| [Audit who has admin rights](audit-who-has-admin-rights.md) | Badges on the role list, role pages, account rows, the audit trail. |
| [Export the account list for a review](export-the-account-list-for-a-review.md) | **Export the list as CSV**; handle it as personal data. |

## Suggested order

For a new site: create the groups, the department editor role, the approver role and the approval workflow; then
lock down sign-in (keep editors out of the administration, two-step sign-in, lockout if required); then add staff.
After go-live, the account tasks and the review tasks become routine.

Up: [Tasks](../README.md) and [Running Exponential in the public sector](../../operations/README.md). The
section landing page is [Governments: start here](../../government-start-here.md).

## Related pages
- [Operations tasks](../operations/README.md)
- [Roles and policies guide](../../../../guides/roles-and-policies.md)
- [Site administrators course](../../../trainers/site-administrators/README.md)
- [Security and audit guide](../../../../guides/security-and-audit.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
