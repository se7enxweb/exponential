# Versions of an object: compare, restore, clean up

This guide explains the **versions page** of an object in the administration (`/content/history/<object id>`,
opened with **Manage versions** in the editor or from the context menu of an item): what a version is, the overview,
finding versions by status, translation and creator, comparing two versions, making a new draft from an old one,
removing versions, and who sees what.

It is written for editors and administrators. Every page, field and rule below was checked against the code of this
repository on 6 October 2026; the files are named in [References](#references).

[Guides](README.md) · Related: [Edit access to objects that were never published](../bc/6.0/draft-edit-access.md) ·
[List paging](../features/6.0/admin-list-paging.md)

## In short

- Each save of an object is a **version**. A draft becomes the **published** version when it is published, and the
  version before it is **archived**. An approval workflow can leave a version **pending** or **rejected**.
- You edit only **your own drafts** in place. To change any other version, make a **new draft from it**: the new
  draft is yours, in the translation you choose, and you edit and publish it as usual.
- The page shows an overview, filters, and one card per version with what you may do with it; an action you may
  not use says why.
- Filters, order and page are kept in the address, so a link to the page shows the same list.
- Everything works without JavaScript. With JavaScript you also get **Select all**, the count of ticked versions and
  the list of versions in the remove confirmation.

## 1. The page

| Part | What it shows |
| --- | --- |
| Introduction | What versions are and how a new draft is made from one |
| Notices | What the last action did (removed, copied), what it did not do and why, and, for an editor who may edit but not read the object, what that editor can do here |
| Figures | All versions, the number in each status, the translations they were made in, your drafts. A status figure filters the list |
| Filters | **Status**, **Translation** and **Creator** (shown when there is more than one), and the **Order**: newest first, oldest first, last modified |
| Versions | One card per version: number, status, *Current version*, *Yours*, its translations (the one changed in this version is marked), creator, created and modified, and the actions |
| Paging | 10, 25 or 50 versions per page (`admininterface.ini [PaginationSettings]`, key `content/history`; your choice is a preference) |
| Compare two versions | Two pickers (older, newer) and the translation |
| Bottom bar | **Back**, **Remove selected versions** (confirmed in place), and whether to stay on the page after making a new draft |

## 2. The actions of a version

| Action | Offered when | Otherwise the card says |
| --- | --- | --- |
| **View** | `content/versionread` allows the version | Viewing it needs the policy content/versionread |
| **Edit** | It is your own draft and you may edit the object | Only drafts are edited, or the draft belongs to someone else: make a new draft from it |
| **Compare** | You may read the version and have `content/diff` | (not shown) |
| **Make a new draft** | You may edit the object, read the version and edit one of its translations | You may not read it, may not edit any of its translations, or it is an untouched draft |
| Select for removal | Draft, archived, rejected or untouched draft, with `content/versionremove` | The published version and versions in a running workflow are never removed |

**Compare** on a card compares that version with the current one (or, for the current one, with the newest other
version you may read). The two pickers compare any two. The differences are shown above the list as inline changes,
block changes, the old or the new version.

## 3. A new draft from an old version

1. Find the version (filter by status *Archived*, or by translation).
2. On its card, choose the translation the new draft starts in, and click **Make a new draft**.
3. With **Stay on this page** ticked (the default), the page says which draft was made; edit it from its card. Untick
   it to open the new draft in the editor at once.
4. Publish the draft as usual. When the object already has as many versions as the version history limit
   (`content.ini [VersionManagement]`) allows, the oldest archived version is removed to make room.

## 4. Removing versions

Tick the versions, open **Remove selected versions**, check the list of versions it names, and click **Remove for
good**. The page says which versions were removed and which were not (for example a pending version, which belongs
to a workflow that is still running).

## 5. Back

**Back** returns to where you opened the page from: the edit that opened it with **Manage versions**, or the page you
came from. Opened directly, it goes to the object's own location in the content tree, or to the dashboard for an
object that was never published. It never opens an edit that would make a draft.

## 6. Who sees what

Whoever may edit the object opens its versions, also without a read policy for it. Such an editor sees what each
version is and may compare and copy the published, archived and rejected versions and their own; someone else's
draft or pending version needs `content/versionread`. The full rules are in [Edit access to objects that were never
published](../bc/6.0/draft-edit-access.md#the-versions-of-such-an-object-contenthistory).

## Problems

| Symptom | Cause and fix |
| --- | --- |
| No **Make a new draft** on a version | You may not read it (someone else's draft), may not edit any of its translations, or it is an untouched draft; the card says which |
| No **Remove selected versions** | No version on the page can be removed by you; the page says why above the list |
| A version is gone after making a new draft | The version history limit was reached and the oldest archived version made room |
| "No version was removed" | Nothing was ticked |

## References

- View: `kernel/private/classes/views/content/history.php` (`History::canOpen()`, `canSeeVersionContent()`,
  `originURI()`); list, filters and actions: `kernel/classes/expcontenthistorylist.php`
- Templates: `design/admin4/templates/content/history.tpl` (the same in `design/admin` and `design/admin3`),
  `content/history_exp_style.tpl`, `content/history_access_messages.tpl`
- Tests: `tests/tests/kernel/classes/expContentHistoryListTest.php`, `eZContentHistoryAccessTest.php` (no database),
  `eZContentHistoryAccessLiveTest.php`
