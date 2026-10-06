# My drafts and my pending items

This guide explains two pages of the administration dashboard: **My drafts** (`/content/draft`), the versions you
started and have not published, and **My pending items** (`/content/pendinglist`), the versions you sent for
publishing that a workflow, usually an approval, still holds, and the versions waiting for your approval.

It is written for editors and approvers. Every page, field and rule below was checked against the code of this
repository on 6 October 2026; the files are named in [References](#references).

[Guides](README.md) · Related: [Versions of an object](content-history.md) ·
[Edit access to objects that were never published](../bc/6.0/draft-edit-access.md) ·
[Workflows](workflows.md) · [List paging](../features/6.0/admin-list-paging.md)

## In short

- **Your drafts are yours.** Only you see them on this page, and the page removes only your own drafts: every draft is
  checked again at the moment it is removed, whatever the request names.
- A draft of a **new object** (never published) takes the new object with it when it is removed. A draft of a
  published object leaves the published version as it is.
- Old drafts can be removed in one go: **Remove old drafts**, after 7, 30, 90 or 365 days without a change.
- A pending version shows **what holds it** (the approval, its state and approvers, the workflows) and links to the
  approval when you take part in it. A version waiting for your approval is listed only when you may read it
  (`content/versionread`).
- Filters, search, order and page are kept in the address. Everything works without JavaScript.

## 1. My drafts

| Part | What it shows |
| --- | --- |
| Figures | Your drafts, those of new objects, those not modified for 30 days (or the age chosen), translations, classes |
| Find | A search over the name, the location and the class (`?q=`) |
| Filters | Translation and class (when there is more than one), age (any, 7, 30, 90, 365 days), order (last modified, oldest, name, class) |
| Cards | Name, class, location (or "New, to be published below ..."), translation, version, created, modified and its age; **Edit**, **View** (with `content/versionread`) and **Remove**, confirmed in place |
| Bottom bar | **Remove selected** (the confirmation names the ticked drafts), **Remove old drafts** (choose the age; the count is shown), **Remove all**, each confirmed in place |
| Paging | 10, 25 or 50 per page (`admininterface.ini [PaginationSettings]`, key `content/draft`; your choice is a preference) |

A removal says how many drafts went. If a request names a version that is not one of your drafts (someone else's,
a published version), it is left as it is and the page says so.

## 2. My pending items

| Part | What it shows |
| --- | --- |
| Figures | Sent by you, waiting for your approval, held by an approval |
| Filters | Show all, sent by you or waiting for your approval; class; order (newest, waiting longest, name) |
| Cards | Name, the state of its approval, class, location, translation and version, who sent it and when, what holds it; **View** (with `content/versionread`), **Open the approval** or **Approval and comments** (when you take part in the approval), **Published page** |

A version that no approval or workflow process holds any more but is still pending says so: ask an administrator to
look at **Setup > Workflow processes**.

## Problems

| Symptom | Cause and fix |
| --- | --- |
| A draft you expected is missing | It may be filtered (check the address) or older than the age filter, or it is someone else's |
| "... are not drafts of yours and were left as they are" | The request named versions that are not your drafts; nothing of them was removed |
| A version waiting for your approval is not listed | You may not read it: approving needs `content/versionread` for it |

## References

- Views: `kernel/private/classes/views/content/draft.php`, `kernel/private/classes/views/content/pendinglist.php`
- Lists: `kernel/classes/expcontentdraftlist.php` (`removable()` decides what may be removed),
  `kernel/classes/expcontentpendinglist.php`
- Templates: `design/admin4/templates/content/draft.tpl`, `pendinglist.tpl`, `draft_exp_style.tpl` (the same in
  `design/admin`), with `content/history_exp_style.tpl`
- Test: `tests/tests/kernel/classes/expContentDraftListTest.php` (no database)
