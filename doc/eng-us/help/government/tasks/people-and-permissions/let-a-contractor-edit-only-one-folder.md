---
title: Let a contractor edit only one folder
audience: government, admin, project-manager
category: public-sector
order: 140
summary: Step by step - give an agency or contractor personal accounts in a contract group, a narrow content role assigned to one folder, approval of everything they publish, and a clean end on the contract's last day.
keywords: contractor, agency, supplier, one folder, external editor, temporary access, contract group, subtree, Assign with limitation, offboarding, approval
type: help
---
# Let a contractor edit only one folder

This task is for administrators and contract managers. At the end, the contractor's people can create and edit
content in one folder, their work is approved before it is public, and the access is ready to be ended with the
contract. Design: [Role design: contractors and temporary staff](../../operations/people-and-permissions/role-design-contractors.md).

## Contents
1. [Before you start](#before-you-start)
2. [Create the group and accounts](#create-the-group-and-accounts)
3. [Create and assign the role](#create-and-assign-the-role)
4. [Keep approval on](#keep-approval-on)
5. [Check it worked](#check-it-worked)
6. [At the end of the contract](#at-the-end-of-the-contract)
7. [Related pages](#related-pages)

## Before you start

- The contract: which folder, which content types, start and end dates, the names of the people.
- An account with the `role` module and the right to create users.

## Create the group and accounts

1. In the **Users** tree create a user group named after the contract, for example "Contract 2026-07 - web agency".
2. Create one personal account per person in it ([Add a new staff member](add-a-new-staff-member.md)). No shared
   team accounts.

## Create and assign the role

1. **Users > Roles and policies > New role** "Contractor - content".
2. Policies:

   | Module | Function | Limitations |
   |---|---|---|
   | user | login | SiteAccess: the editor siteaccess |
   | content | read | Section: Standard, Media |
   | content | create | Class: the classes in the contract |
   | content | edit | none (the assignment limits it) |
   | content | versionread, diff | none |
   | content | dashboard | none |
   | user | password | none |

3. **Save**. On the role page choose **Subtree**, **Assign with limitation**, choose the contract folder, then the
   contract group.

Because `user/login` is in this role, the subtree assignment applies to it too; if sign-in fails, move `user/login` to
a separate role assigned to the contract group without limitation.

## Keep approval on

Make sure the folder's section is in **Affected sections** of the approval workflow and the contract group is not in
**Excluded user groups** ([Setting up the Approve event](../../operations/editorial-workflows/setting-up-the-approve-event.md)).

## Check it worked

- As a contractor account: the folder offers **Edit**; other folders do not; publishing waits for approval.
- The administration siteaccess refuses the sign-in if `user/login` names only the editor siteaccess.

## At the end of the contract

1. On the role page, tick the contract group's assignment and **Remove selected**.
2. **Users > Check user accounts**: select the group in **User group**, tick the accounts, **Disable**, **Confirm**.
3. **Export the list as CSV** for the contract file.
4. Optionally **Deactivate and move** the accounts ([Deactivate and move a leaver](deactivate-and-move-a-leaver.md)).

## Related pages
- [Assign a role with a subtree limitation](assign-a-role-with-a-subtree-limitation.md)
- [Keep editors out of the admin siteaccess](keep-editors-out-of-the-admin-siteaccess.md)
- [Remove a leaver](remove-a-leaver.md)
- [Separation of duties](../../operations/people-and-permissions/separation-of-duties.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
