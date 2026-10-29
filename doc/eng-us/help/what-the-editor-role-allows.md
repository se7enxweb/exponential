---
title: What the Editor role allows
audience: editor, admin
category: account
order: 8
summary: The roles an account in the Editors group has (Editor, Member, Member management, Editor account requests) and, in plain words, every permission of the Editor role: creating, editing, hiding, restoring, uploading many files, the image editor, the website toolbar, the interface choices, and what stays with administrators.
keywords: Editor role, editors, permissions, policies, roles, what can an editor do, content/create, hide, reveal, trash, restore, reverse related list, multi-upload, image editor, website toolbar, interface language, administration design, Member management, Editor account requests, access denied
context: content/view, role/view
type: help
---
# What the Editor role allows

An account in the **Editors** group gets the roles of that group. This page says what they allow, in plain words, so
that an editor knows what to expect and an administrator can tell whether something missing is intended. It
describes the roles of a new installation; your site's administrators may have changed them.

To see your own roles, open your account (**Edit profile** in the dashboard's left column, or your name in the user
menu) and its **Roles** tab. **Policies** lists every single permission from all roles together.

![The Roles tab of an editor's account: Editor limited to the subtrees Content and Media, Member, Member management and Editor account requests](images/accounts/onboarding/new-editor-roles.webp)

## The four roles of the Editors group

| Role | Assigned with | What it is for |
|---|---|---|
| **Editor** | twice: limited to the Content tree and to the Media tree | the everyday work: writing, editing, publishing, media |
| **Member** | no limitation | the own account: password, profile, notifications |
| **Member management** | no limitation; each permission is limited to the Members group | managing the site's members ([Managing members](managing-members.md)) |
| **Editor account requests** | no limitation; limited to the Editors group | asking for a new colleague's account, which an administrator approves ([Approving new editor accounts](../guides/approving-new-editor-accounts.md)) |

Because the Editor role is assigned to the Content and Media trees, every permission below that concerns an item
works there only: an editor cannot change setup, user or design objects through it.

## Everyday work (the Editor role)

| What an editor can do | Permission |
|---|---|
| Open the Editor and the dashboard; see the own drafts and pending items | `user/login`, `content/dashboard`, `content/pendinglist` |
| Read items in the sections Standard, Restricted and Media | `content/read` (limited to those sections) |
| Create items of the site's content classes in the places they belong (see below) | `content/create` (limited by class and by the class of the parent) |
| Edit, translate and publish items | `content/edit`, `content/translate` |
| Move items and add or remove locations | `content/manage_locations` |
| **Hide and reveal** an item (Hide / Reveal in its menu) | `content/hide` |
| Remove an item to the **trash**, look into the trash and **restore** an item from it | `content/remove`, `content/restore` |
| See which items **use** an item before moving or removing it | `content/reverserelatedlist` |
| Compare versions, read old versions, remove old versions | `content/diff`, `content/versionread`, `content/versionremove` |
| Bookmarks, embedded items in rich text | `content/bookmark`, `content/view_embed` |
| Upload **many files at once** into the media library (Upload files on the dashboard) | `ezmultiupload/*` |
| Edit an image in the edit form (**Edit image**: crop, rotate, filters) | `expimageeditor/edit` |
| Edit from the website: the **website toolbar** for the classes the editor may create | `websitetoolbar/use` (limited by class) |
| The rich text editor and its dialogs | `ezoe/*` |
| Import and export text documents (OpenDocument and Word) | `ezodf/*` |
| Choose the **interface language** and the **administration design** in the right column (Interface) | `uiswitch/language`, `uiswitch/design` |
| Set up two-step sign-in for the own account, and confirm it at sign-in | `user2fa/setup`, `user2fa/verify` |
| The writing assistant in the edit form (Exponential Intelligence) | `expai/write` |
| Read the help pages | `exphelp/read` |
| Follow items by e-mail; RSS feeds | `notification/use`, `rss/feed` |
| Allow an app to act for the own account (sign in with Exponential) | `oauth/authorize` |

### Which items an editor can create

The Editor role lists the content classes an editor may create and where. On a new installation that is the site's
content classes below a folder (articles, folders, files, images, links, galleries, events and calendars, forums,
blogs, documentation pages and the like, with their sub-items such as blog posts below a blog and images below a
gallery). An installation with the media site classes adds them too: landing pages, NG articles, NG blog posts, NG
galleries, news, categories, containers, the components (hero, features, about, logos, quote, lead), banners,
contact and feedback forms, job positions, menu items and the other media site classes, below a folder, the front
page and the media site's containers. The website toolbar is allowed for the same classes.

If **Create new** does not offer a class you need, or offers it only in some places, the role does not list it there:
ask an administrator.

## Your own account (the Member role)

Change your password (`user/password`), edit your own name, e-mail address and image (`user/selfedit`), and manage
your notifications (`notification/use`), and set up two-step sign-in. As on the public site, the role also lets you
read the Restricted section, write comments and forum posts and edit your own. See
[Your account and signing in](accounts-and-sign-in.md).

## A new colleague's account (Editor account requests)

This role lets an editor open the Editors group and create a **User** there, nothing else. The new account is not
published until an administrator approves it, and it cannot sign in before
([Approving new editor accounts](../guides/approving-new-editor-accounts.md)).

## What stays with administrators

| Not for editors | Why |
|---|---|
| Emptying the trash for everybody (`content/cleantrash`) | it removes everybody's trashed items for good; editors restore, administrators empty |
| The installation's languages, URL aliases and wildcards, content jobs | they change the whole site, not one item |
| Setup, classes, roles, sections, workflows, object states | the administration of the site |
| Accounts of administrators and of other editors | protected: editors manage **members** only ([Managing members](managing-members.md)) |
| Assigning roles | administrators only |
| The layouts editor and the object states | switched off in the Editor; administrators use them in the administration |
| The Git manager and the updates | updating the installation |

An editor who opens one of these sees *Access denied* or *The requested page could not be displayed*. That is
intended; ask an administrator.

## For administrators: keeping the role complete

A new installation sets the role up as described. On an installation that exists, the command
`php bin/php/addeditorpolicies.php --dry-run` lists what the Editor role lacks (hide, restore, reverse related list,
multi-upload, the image editor, and the own account's permissions) and warns about permissions an editor role should
not have; without `--dry-run` it adds what is missing. `php bin/php/addmembermanagement.php` does the same for the
role *Member management*. Details: [The everyday policies of the Editor role](../bc/6.0/editor-role-policies.md) and
[Member management](../bc/6.0/member-management.md).

To change what editors may do, edit the roles at **Setup > Roles and policies**
([Roles and policies](../guides/roles-and-policies.md)); never move an editor into Administrator users to solve a
missing permission.

## Related pages

- [Setting up accounts for editors and administrators](setting-up-editor-and-admin-accounts.md)
- [Managing members](managing-members.md)
- [How do I see what a user may do?](manual/how-do-i-see-what-a-user-may-do.md)
- [Roles and policies](../guides/roles-and-policies.md)
- [Free documentation](free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
