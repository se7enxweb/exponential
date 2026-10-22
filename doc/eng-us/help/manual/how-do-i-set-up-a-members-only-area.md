---
title: How do I set up a members-only area?
audience: admin
category: manual
order: 605
summary: Put the pages in a section of their own, which visitors cannot read, and give a role with content/read limited to that section to the user group of the members.
keywords: members-only, protected area, private pages, intranet, section, content/read, role, user group, Members, login, registration
context: role/view
type: help
---
# How do I set up a members-only area?

Visitors who are not logged in may read only what the Anonymous role allows, which on a standard site is the
section Standard. Pages in another section are therefore closed to them. Members see the pages because one of
their roles allows reading that section.

## Who can do this

An administrator, or a user whose roles give `section/edit`, `section/assign` and `role/*`.

## Steps

1. Create a section, for example "Members area", and assign the folder with the members' pages to it, as described in
   [How do I create a section and put content into it?](how-do-i-create-a-section-and-assign-content.md).
2. Log out, or open the site in a private browser window, and open one of the pages.
   The page is refused and is gone from menus and lists.
3. In the administration, open **Users** in the top menu and choose **Roles and policies**.
4. Click **New role** and name it, for example "Members area reader".
5. Click **New policy**, choose module **content**, then **Grant access to one function**, function **read**, and
   **Grant limited access**.
6. Under **Section**, choose "Members area" and click **OK**.
7. Click **Save**.
8. On the role's page, click **Assign** and choose the user group of your members.
9. Log in on the site as a test member and open the page again.
   The page is shown.

## What can go wrong

- **Members cannot log in at all.** Logging in on a siteaccess needs the policy `user/login` for that siteaccess in one
  of the user's roles. On a standard site the role that lets visitors log in already carries it; a group you created
  yourself may lack it. Check the member's **Roles** and **Policies** tabs
  ([How do I see what a user may do?](how-do-i-see-what-a-user-may-do.md)).
- **Images on the members' pages are broken.** Images and files usually live in the media library, in the section
  Media. If you put them in the members' section too, they are closed to visitors as well, which is often what you
  want.
- **Visitors still see the pages.** A cached page from before the change can be served for a short time, and the
  Anonymous role may have a `content/read` policy without a section limit. Check the Anonymous role first, then clear
  the content cache ([Which cache do I clear, and how?](which-cache-do-i-clear.md)).
- **A member sees the page only after logging in again.** Rights are cached per user and are read again at the next
  login.
- **New members end up in the wrong group.** People who register on the site are created in the group named by
  `site.ini [UserSettings] DefaultUserPlacement` (a node ID). Assign the role to that group, or change the setting.

**Data protection:** Hiding pages in a section keeps them from visitors and search engines, but every member of the
group can read and copy them. Do not put data there that only some members may see; give those pages a section and a
role of their own.

## Related pages

- [Sections](../../guides/sections.md): section 13.1 describes the same setup with the section pages
- [Roles and policies](../../guides/roles-and-policies.md)
- [How do I add a user or a user group?](how-do-i-add-a-user-or-a-user-group.md)
- [Users and registration](../cms-developers/users-and-registration.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
