---
title: How do I move a user to another group, or put them in two groups?
audience: admin
category: manual
order: 615
summary: Move the user account like any other item to change its group; add a location in a second group to give the user the roles of both groups at once.
keywords: move user, user group, change group, second group, two groups, location, add locations, roles, permissions, Users tab
context: content/view
type: help
---
# How do I move a user to another group, or put them in two groups?

A user account sits in the user tree like any other item. Its groups are the groups above each of its locations,
and the user has every role of every one of them. Moving the account changes its group; an extra location adds a
group without taking one away.

## Who can do this

- Moving needs `content/edit` and `content/remove` on the account, and `content/create` of class User in the target
  group.
- Adding a location needs `content/edit` or `content/manage_locations` on the account, and `content/create` of class
  User in the new group.
- Administrators may do both.

## Steps

To move the account to another group:

1. Click **Users** in the top menu and open the account.
2. Click **Move**.
   A browser with the user tree opens.
3. Select the new group with its radio button and click **Select**.
   The account is now listed in the new group.
4. Open the account's **Roles** tab and check the roles it has now.

To put the account in a second group as well:

5. Open the account and click the **Locations** tab.
6. Click **Add locations**, select the second group and click **Select**.
   The account is listed in both groups and has the roles of both.

## What can go wrong

- **The user still has the old rights.** Rights are cached per user. They apply at the next login, or after the user
  cache is cleared.
- **The user has more rights than expected.** Rights add up: every role of every group above every location counts,
  and the most generous policy wins. Look at the **Policies** tab of the account to see all of them together.
- **Removing the account from one group removed it everywhere.** Take a group away by removing that one location in
  the **Locations** tab, not by removing the account. Removing the account itself removes the user.
- **The section of the account changed.** Moving content can carry the section of the new parent along. User accounts
  normally stay in the section Users, which is what you want.

## Related pages

- [How do I see what a user may do?](how-do-i-see-what-a-user-may-do.md)
- [How do I add a user or a user group?](how-do-i-add-a-user-or-a-user-group.md)
- [How do I show an item in more than one place?](how-do-i-show-an-item-in-more-than-one-place.md)
- [Roles and policies](../../guides/roles-and-policies.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
