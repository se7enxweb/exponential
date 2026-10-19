---
title: "Lesson 4.3: One administration for several sites"
audience: trainer, admin, developer
category: training
order: 43
summary: Learners make the administration aware of every public site, limit a team to its own site with a role assigned to a subtree, and decide when a site needs an administration siteaccess of its own with FrontSiteAccess. 50 minutes.
keywords: multisite and site packages, administration, SiteList, FrontSiteAccess, Open the site, roles, Assign with limitation, subtree limitation, Editor role, editor siteaccess, several administrations
type: help
---
# Lesson 4.3: One administration for several sites

A multisite usually has one administration for every site, and several teams who should each work in their own site.
This lesson connects the administration to the public sites, gives the Club B team a role that works only in the
folder of their site, and shows the settings for the case where each site gets an administration of its own.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 4: Links between sites](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Administrators |
| Prerequisites | [Lesson 1.3](../module-01-siteaccesses-for-several-sites/adding-a-second-site.md); basic knowledge of roles and policies |

## Learning objectives

After this lesson, learners can:

- explain `SiteList[]` and the `FrontSiteAccess` of an administration siteaccess;
- give a team the rights of a role only below their site's folder with an assignment limited to a subtree;
- test the result with a test user whose address is at `example.invalid`;
- decide between one administration with limited roles and an administration siteaccess per site.

## Trainer's notes

### How to present it

Sources: [Setting up several sites in one installation, editors and the administration](../../../creators/website-creators/multisite-setup.md#editors-and-the-administration)
and [Roles and policies](../../../../guides/roles-and-policies.md#5-assign-the-role).

1. **The administration knows the sites** (4 minutes). `SiteList[]` in `site.ini [SiteSettings]` (the installer
   writes it into the global override) lists the public sites, so previews and links can use them. `FrontSiteAccess` names the
   site an administration belongs to: its **Open the site** and "View on site" links go there (the template operator
   `siteaccess_url()` without a parameter). Empty, it is `DefaultAccess`. Set it when one installation serves several
   sites, each with its own administration.
2. **Teams in their own site** (6 minutes). Roles grant policies; an assignment can be limited. **Assign with
   limitation** chooses *Subtree* or *Section*, then the subtree or section, then the users or groups. The role then
   applies only there. Prefer groups: every member, now and later, gets the role.
3. **The Editor siteaccess** (2 minutes). Every installation made by `exp:install` or the kickstarter has the
   Editor, the simplified administration for content editing; the same limited roles apply there.
4. **Several administrations** (3 minutes). When teams must not see each other's sites at all, a second
   administration siteaccess (a copy of `admin`) with its own `FrontSiteAccess` and its own matching is possible.
   Roles still decide what each user may do; the second administration is a convenience, not a security boundary.

### Common questions

**Do the team members see the other site in the tree?** They see what their roles let them read. A role limited to
the subtree of *Site B* for editing still lets them read elsewhere if another role grants reading; review all roles of
the group.

**Is a section better than a subtree?** A section works across places in the tree and is kept when content moves; a
subtree is simpler for "everything below this folder". Both are offered by **Assign with limitation**.

**What does a test user need?** An account with an address at `example.invalid`, in a group that has the limited
role, and the right to sign in to the administration or the Editor.

**Will the users get e-mail?** A training installation must not send real mail; addresses at `example.invalid` never
reach anyone.

### Pitfalls

- **Testing as administrator.** The administrator may do everything; sign in as the test user in a private browser
  window.
- **Assigning to the user instead of the group.** It works, but the next team member gets nothing. Assign to a group.
- **Content outside the subtree.** Images uploaded by the team land in the media library, outside *Site B*. Decide
  where the team's media go and limit that too, or give them a folder below *Site B*.

## Hands-on exercise

Use your training installation with *Site B* (lesson 1.3) and your administrator account.

1. Read the sites the administration knows:
   `php bin/php/console exp:ini get 'site.ini/SiteSettings/SiteList[]' -s admin`. `site_b` is there since lesson 1.3.
2. Read `FrontSiteAccess` of the administration: `php bin/php/console exp:ini get site.ini/SiteSettings/FrontSiteAccess -s admin`.
3. In the administration, open the **Users** area and create a user group `Club B team` below the members' group, then
   a user `clubb.editor` in it with the e-mail address `clubb.editor@example.invalid` and a password of at least 10
   characters.
4. In the **Users** area, under **Access control**, open **Roles and policies** and open the role **Editor**.
5. Assign it with a limitation: choose *Subtree*, pick the folder *Site B*, then choose the group `Club B team`.
6. Check the role page: the assignment is listed with its limitation.
7. In a private browser window, sign in as `clubb.editor` (in the administration or the Editor).
8. Try to create an article below *Site B*, then below another folder of the main site. Note what is offered.
9. Sign out, then in your administrator window open the header button **Open the site or another siteaccess** and
   note which siteaccesses it offers.
10. Discuss in pairs: does Club B need an administration of its own? Write the reason in one sentence.

## Expected result

- Step 6: the role **Editor** lists the group `Club B team` with a subtree limitation on *Site B*.
- Step 8: **Create new** works below *Site B* and is missing or refused elsewhere (unless another role of the group
  grants more).
- Step 9: the menu offers the public siteaccesses, including `site_b`.
- Step 10: most pairs answer no: one administration with limited roles is enough when one organization runs both
  sites.
- The trainer checks step 8 on one learner's screen.

## Quiz

1. What does `SiteList[]` do?
2. Which assignment gives a team the rights of a role only below their folder?
   A. **Assign** B. **Assign with limitation**, *Subtree* C. A new section D. A second administration
3. Why assign roles to groups rather than users?
4. Is a second administration siteaccess a security boundary?
5. Where do the links **Open the site** lead to in an administration with `FrontSiteAccess` empty?

<details>
<summary>Answers</summary>

1. It lists the public sites, so previews and links of the administration can use them.
2. **B.** ([Roles and policies](../../../../guides/roles-and-policies.md#5-assign-the-role))
3. Every member, now and later, gets the role.
4. No. Roles decide what a user may do; a second administration is a convenience.
5. To `DefaultAccess` (comment of `FrontSiteAccess` in `settings/site.ini`).

</details>

## Further reading

- [Roles and policies](../../../../guides/roles-and-policies.md): creating, assigning and testing roles.
- [Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md#editors-and-the-administration).
- [Logging into the Editor](../../../logging-into-the-editor.md) and [Logging into the admin](../../../logging-into-the-admin.md).
- [Visitor permissions](../../../creators/website-creators/visitor-permissions.md).

## Related pages

- [Module 4: Links between sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
