---
title: How do I add a user or a user group?
audience: admin
category: manual
order: 610
summary: In the Users tab, open the group the account belongs to, press Create new and choose User (or User group), fill in the name and the user account fields and publish; the new account gets the roles of its group.
keywords: add user, new user, create account, user group, Users tab, user account, username, password, email, Create new, roles of a group
context: content/view
type: help
---
# How do I add a user or a user group?

Users and user groups are content objects in the **Users** part of the content tree. A user gets its rights from the
roles of the groups it is placed in, so the place where you create the account decides what the person may do.

## Who can do this

Users who may create objects of the classes **User** and **User group** in the user tree (`content/create` with
those classes, usually limited to the Users section), and read it. Administrators may.

## Steps

1. Click **Users** in the top menu.
   The user tree opens with its groups.
2. Open the group the person belongs in, for example "Editors".
3. Under **Sub items**, press **Create new** and choose **User**.
   To add a group instead, choose **User group**, fill in its name and publish; then open it and continue here.
4. Fill in the name fields of the person.
5. Under the user account, fill in **Username**, **Email** and, if the form asks for it, **Confirm email**.
6. Fill in **Password** and **Confirm password**.
   The password must meet the site's password rules, for example a minimum length.
7. Click **Send for publishing**.
   The account is created and listed in the group.
8. Open the new account and look at its **Roles** tab to check what it received from its group.

## What can go wrong

- **"The username already exists, please choose another one." or "A user with this email already exists."** Each
  username exists once. With
  `site.ini [UserSettings] RequireUniqueEmail=true`, the default, each e-mail address may also belong to one account
  only.
- **The username has a typo.** It cannot be changed in the form after the account is created. Remove the account
  and create it again, or ask a developer.
- **You left the password empty.** The site then makes a random password that nobody is told. The person sets their
  own with the forgotten-password link on the login page.
- **The new user cannot do anything.** The group has no role, or the role has no policy for the place they work in.
  Assign a role to the group ([How do I give a group edit rights on one section only?](how-do-i-give-a-group-edit-rights-on-one-section.md)).
- **User or User group is not in the Create new list.** Your role does not allow creating these classes here, or the
  classes have other names on your site.

**Data protection:** You are storing personal data (name, e-mail address). Collect only what the person's work on
the site needs, and remove or disable the account when they leave
([How do I disable or unlock a user account?](how-do-i-disable-or-unlock-a-user-account.md)).

## Related pages

- [How do I move a user to another group?](how-do-i-move-a-user-to-another-group.md)
- [How do I see what a user may do?](how-do-i-see-what-a-user-may-do.md)
- [Roles and policies](../../guides/roles-and-policies.md)
- [Change the admin password](../change-the-admin-password.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
