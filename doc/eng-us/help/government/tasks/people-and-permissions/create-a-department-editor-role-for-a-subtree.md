---
title: Create a department editor role for a subtree
audience: government, admin
category: public-sector
order: 20
summary: Step by step - create one "Department editor" role and assign it to a department's user group so its editors can create, edit and publish only below the department's own folder.
keywords: department editor, create role, subtree, Assign with limitation, New role, New policy, Grant limited access, user group, department, agency, editors
type: help
---
# Create a department editor role for a subtree

This task is for administrators. At the end, the editors of one department can work on the pages below their
department's folder and nowhere else. The design behind it is on
[Role design: department editors](../../operations/people-and-permissions/role-design-department-editors.md); the
screens are those of **Users > Roles and policies** ([Roles guide](../../../../guides/roles-and-policies.md)).

## Contents
1. [Before you start](#before-you-start)
2. [Create the role](#create-the-role)
3. [Assign it to the department](#assign-it-to-the-department)
4. [Check it worked](#check-it-worked)
5. [Pitfalls](#pitfalls)
6. [Related pages](#related-pages)

## Before you start

- An account with the `role` module (normally an administrator). With `PreventPrivilegeEscalation=enabled` (the
  default) you can grant only what your own roles cover.
- The department's folder exists in the content tree, and the department's user group exists
  ([Create a user group for a department](create-a-user-group-for-a-department.md)).
- A decision on which content classes the department may create.

## Create the role

1. Open **Users > Roles and policies** and click **New role**. Name it `Department editor`.
2. Click **New policy**. Step 1, Module: `content`. Step 2, Function: `read`. **Grant limited access**. Step 3:
   Section `Standard` and `Media`. **OK**.
3. **New policy**: `content` / `create`, **Grant limited access**, Class: your page, article, file and image classes.
   **OK**.
4. **New policy**: `content` / `edit`, **Grant limited access**, Section `Standard`, `Media`. **OK**.
5. **New policy**: `content` / `hide` and then `content` / `remove`, each limited to Section `Standard`.
6. **New policy**: `content` / `versionread` and `content` / `diff`, Section `Standard`.
7. **New policy**: `content` / `dashboard` (**Grant full access** for the function).
8. The editor lists the policies as sentences and says **Unsaved changes**. Press **Save**.

Sign-in (`user/login`) and `user/password` belong in a small role for all staff, assigned without limitation, so
that signing in never depends on a subtree assignment.

## Assign it to the department

1. On the role's page, below the list of assignments, choose **Subtree** in the limitation select.
2. Press **Assign with limitation**, choose the department's top folder in the content browser and press **Select**.
3. Choose the department's user group and press **Select**.
4. The role page lists the group with *Only in the subtree ...*.

Repeat steps 1 to 3 for each further department: one role, one limited assignment per department.

## Check it worked

1. Sign in as a member of the group in a private browser window.
2. Open a page of the department: **Edit** is offered. Open a page of another department: it is not.
3. Type the edit address of another department's page: the request is refused and the audit trail records
   `access.permission.refused`.
4. On the role page, **users affected in all** counts the group's members.

Change record: role name, its policies (copy the sentences from the role page), the group, the subtree, the date and
who approved the request.

## Pitfalls

- Using **Assign** instead of **Assign with limitation** gives the role everywhere.
- Content linked into the department's folder as a secondary location stays outside the editors' reach where its
  main location is elsewhere.
- If departments must not publish without approval, add the role's groups to the approval workflow, not to its
  excluded groups ([Set up an approval workflow for a section](set-up-an-approval-workflow-for-a-section.md)).

## Related pages
- [Assign a role with a subtree limitation](assign-a-role-with-a-subtree-limitation.md)
- [Copy a role for a new department](copy-a-role-for-a-new-department.md)
- [Sections and subtree limits](../../operations/people-and-permissions/sections-and-subtree-limits.md)
- [Check what a role allows](check-what-a-role-allows.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
