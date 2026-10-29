---
title: How do I see what a user may do?
audience: admin
category: manual
order: 620
summary: Open the account in the Users tab and read its Roles tab (every role from the user and its groups, with the limitation of each assignment) and its Policies tab (what those roles allow, module by module).
keywords: permissions, what may a user do, roles tab, policies tab, assigned roles, limitation, access denied, effective rights, role/view, user group
context: content/view
type: help
---
# How do I see what a user may do?

A user may do what the policies of their roles allow, and nothing else. The roles come from the account itself and
from every group above each of its locations. The account's page in the administration collects them for you.

## Who can do this

Anyone who may read the account in the user tree. Following a role to its own page needs `role/*`.

## Steps

1. Click **Users** in the top menu and open the account (or a user group).
2. Click the **Roles** tab.
   The tab lists each role the account has, directly or through a group. The **Limitation** column shows when a role
   was assigned only for one subtree or one section, with a link to it.
3. Click the **Policies** tab.
   The tab lists, role by role, the module, the function and the limitations of each policy. For a role with many
   policies it shows the first ones and links to the role for the rest.
4. To read a whole role, click its name.
   The role's page shows all its policies and every user and group it is assigned to.
5. To compare, open a user who can do the task and look at the same tabs.

## What can go wrong

- **A tab is missing.** The tabs can be switched off with the **Tabs** switch above them; switch them on again.
- **The user is refused although a policy seems to allow it.** Check every limitation of the policy: all of them must
  be met at once (for example Class *and* Section). Check also the limitation of the assignment in the **Roles** tab,
  and whether the content is in the subtree or section it names.
- **A policy is marked as giving no access.** Its limitation is handled by an extension that is not active, so the
  policy denies. Activate the extension or remove the policy.
- **The rights changed but the user does not see it.** Rights are cached per user. They apply at the next login, or
  after the user cache is cleared.
- **Login is refused on one site.** Logging in needs `user/login` for that siteaccess, which is a policy like any
  other and appears in the **Policies** tab with a SiteAccess limitation.

## Related pages

- [Roles and policies](../../guides/roles-and-policies.md)
- [What the Editor role allows](../what-the-editor-role-allows.md)
- [How do I give a group edit rights on one section only?](how-do-i-give-a-group-edit-rights-on-one-section.md)
- [How do I move a user to another group, or put them in two groups?](how-do-i-move-a-user-to-another-group.md)
- [Sections, roles and policies](../cms-developers/sections-roles-and-policies.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
