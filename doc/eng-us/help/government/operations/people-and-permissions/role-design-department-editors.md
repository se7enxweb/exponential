---
title: "Role design: department editors"
audience: government, admin, editor
category: public-sector
order: 70
summary: One reusable role for the editors of every department or agency on a shared public website, assigned once per department with a subtree limitation, with optional variants for translators and for editors whose work is approved before publishing.
keywords: department editor, role design, subtree limitation, Assign with limitation, agencies, ministries, departments, decentralized publishing, shared website, editors, translators, approval
type: help
---
# Role design: department editors

This page is for administrators of a website where many departments, agencies or offices publish their own pages.
The pattern is one role, "Department editor", assigned to each department's user group with a subtree limitation.
It follows the assignment model of [Roles, policies and unactivated users](../../../../guides/roles-and-policies.md#5-assign-the-role)
and uses only policy functions defined in `kernel/content/module.php` and `kernel/user/module.php`.

## Contents
1. [The pattern](#the-pattern)
2. [The role "Department editor"](#the-role-department-editor)
3. [Assigning it per department](#assigning-it-per-department)
4. [Variants](#variants)
5. [Checking it](#checking-it)
6. [Pitfalls](#pitfalls)
7. [Related pages](#related-pages)

## The pattern

```
Users
  Departments
    Department of Example editors      -> Department editor, limited to /services/example/
    Office of Placeholder editors      -> Department editor, limited to /services/placeholder/
```

One role keeps the definition in one place: a change (for example adding the right to hide items) applies to every
department at once. Each assignment carries the department's boundary.

## The role "Department editor"

| Module | Function | Limitations | Why |
|---|---|---|---|
| user | login | SiteAccess: the editor or administration siteaccess your editors use | sign in where they work |
| content | read | Section: Standard, Media | see the public site and the media library |
| content | create | Class: your page, article, file and image classes | create only the content types the department uses |
| content | edit | Section: Standard, Media | edit within the boundary given by the assignment |
| content | publish, hide | Section: Standard | publish and hide within the boundary |
| content | remove | Section: Standard | remove within the boundary (restorable from the trash by the web team) |
| content | versionread, diff | Section: Standard | read their own and colleagues' versions |
| content | dashboard, pendinglist | none | the dashboard, **My drafts** and **My pending items** |
| user | password, preferences | none | change their own password and preferences |

The subtree comes from the assignment, not from the policies: assigned with **Assign with limitation: Subtree**,
every policy of the role applies only within that subtree. Keep `user/login` and the dashboard functions in a separate
small role ("Staff sign-in", assigned without limitation to all staff), so that signing in and the dashboard
never depend on a subtree assignment.

Media: if departments upload images to the shared media library, assign the role a second time to the same group
with the subtree of that department's media folder.

## Assigning it per department

1. Create the role once ([Create a department editor role for a subtree](../../tasks/people-and-permissions/create-a-department-editor-role-for-a-subtree.md)).
2. Create one user group per department ([Create a user group for a department](../../tasks/people-and-permissions/create-a-user-group-for-a-department.md)).
3. On the role page choose **Subtree** in the limitation select, press **Assign with limitation**, pick the
   department's top page, then the department's group.
4. Repeat for every department. The role page then lists each group with *Only in the subtree ...*.

## Variants

| Variant | Change |
|---|---|
| Translators | `content/translate` and `content/edit` with a `Language` limitation instead of full edit |
| Editors whose work needs approval | the same role; add the group to the approval workflow and do not exclude it ([Setting up the Approve event](../editorial-workflows/setting-up-the-approve-event.md)) |
| Senior editors who approve | add them to the approvers' group ([Role design: approvers](role-design-approvers.md)) |
| Editors of one content type across the site (for example news) | `Class` limitation with a subtree of the news folder |

## Checking it

- Sign in as a member in a private window: the department's pages have an Edit button, other departments' pages do
  not.
- The role page counts **users affected in all** and lists each limited assignment; keep that as your record.
- Try to edit a page of another department by typing its edit address: the page is refused and the refusal is
  recorded as `access.permission.refused`.

## Pitfalls

- **Content moved out** of a department's subtree leaves its editors' reach at once; content moved in becomes
  theirs.
- **Secondary locations** of an object outside the subtree are not covered by the subtree assignment.
- **Edit opens versions**: whoever may edit an object may also open its version list
  ([Security and audit, step 2](../../../../guides/security-and-audit.md#2-give-people-only-the-access-they-need-10-minutes)).
- **Do not give the `role` module** to department leads so they can manage their own editors; with escalation
  prevention they could only grant what they hold, but the module also lets them edit and remove roles and
  assignments of other departments.

## Related pages
- [Sections and subtree limits](sections-and-subtree-limits.md)
- [Role design: the central web team](role-design-web-team.md)
- [Assign a role with a subtree limitation](../../tasks/people-and-permissions/assign-a-role-with-a-subtree-limitation.md)
- [Copy a role for a new department](../../tasks/people-and-permissions/copy-a-role-for-a-new-department.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
