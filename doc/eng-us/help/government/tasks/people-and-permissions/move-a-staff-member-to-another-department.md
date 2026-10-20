---
title: Move a staff member to another department
audience: government, admin
category: public-sector
order: 70
summary: Step by step - move a colleague's account from one department's user group to another so the old department's rights end and the new one's begin, including approver duties and content handover.
keywords: mover, department change, transfer, move user, user group, locations, Add locations, Set main, roles, access change
type: help
---
# Move a staff member to another department

This task is for administrators and service-desk staff. At the end, a colleague who changed department has the
rights of the new department and none of the old one. Access follows user groups, so the move is a change of
location of the account. Background: [Joiners, movers and leavers](../../operations/people-and-permissions/joiners-movers-leavers.md#movers).

## Contents
1. [Before you start](#before-you-start)
2. [Move the account](#move-the-account)
3. [Duties that do not follow groups](#duties-that-do-not-follow-groups)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- The request with the old and new department and the date.
- Rights to edit and move user accounts (administrators have them).
- Optional: switch on the audit events for location changes, so the move is fully recorded
  ([Security and audit, step 3](../../../../guides/security-and-audit.md#3-read-the-audit-trail-5-minutes)):

  ```ini
  # settings/override/audit.ini.append.php
  [AuditEventSettings]
  Enabled[]=content.node.add
  Enabled[]=content.node.main
  ```

## Move the account

Either move the account node directly (context menu, Move, choose the new group), or work with locations:

1. Open the account in the **Users** tree and its **Locations** tab.
2. **Add locations**: choose the new department's group.
3. Tick the new location and press **Set main**.
4. Tick the old location and press **Remove selected**.

The account now has the roles of the new group only (plus roles of groups above and roles assigned directly).

## Duties that do not follow groups

| Duty | Where to change it |
|---|---|
| Approver of the old department | remove from its approvers' group; check **Users who approve content** in the Approve events (**Setup > Workflows**) |
| Roles assigned directly to the account | each role page; tick and **Remove selected** |
| Content owned by the account, if roles use `Owner = Self` | **Change owner** for the old department's content ([Content ownership](../../operations/editorial-workflows/content-ownership.md)) |
| Drafts in the old department | the person publishes or removes them, or hands them over |

## Check it worked

- The account page (`/user/edit/<id>`) lists the new group and its roles, not the old ones.
- Signed in as the person (or asked to check), the old department's pages no longer offer **Edit**.
- **Users > Check user accounts** shows the new group in the account's row.

Change record: old and new group, date, request reference.

## Related pages
- [Add a new staff member](add-a-new-staff-member.md)
- [Remove a role assignment](remove-a-role-assignment.md)
- [Give an approver rights](give-an-approver-rights.md)
- [Role design: department editors](../../operations/people-and-permissions/role-design-department-editors.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
