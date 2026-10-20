---
title: Drafts and pending items
audience: government, editor, admin
category: public-sector
order: 50
summary: How editors and web managers keep track of unfinished and waiting work with My drafts and My pending items, clean up old drafts, and find out what holds a version - with the rules that keep one person's drafts private.
keywords: drafts, my drafts, pending, my pending items, waiting for approval, old drafts, remove old drafts, unpublished work, version, editorial backlog
type: help
---
# Drafts and pending items

This page is for editors and web managers. Two pages of the administration dashboard show unfinished and waiting
work: **My drafts** (`/content/draft`) and **My pending items** (`/content/pendinglist`). The facts come from
[My drafts and my pending items](../../../../guides/drafts-and-pending.md).

## Contents
1. [My drafts](#my-drafts)
2. [My pending items](#my-pending-items)
3. [Cleaning up old drafts](#cleaning-up-old-drafts)
4. [Seeing the whole team's backlog](#seeing-the-whole-teams-backlog)
5. [Problems](#problems)
6. [Related pages](#related-pages)

## My drafts

- **Your drafts are yours.** Only you see them on this page, and the page removes only your own drafts.
- The figures count your drafts, those of new objects, those not modified for 30 days (or the age chosen),
  translations and classes. Search by name, location and class; filter by translation, class and age.
- Each card has **Edit**, **View** (with `content/versionread`) and **Remove**.
- A draft of a **new object** takes the new object with it when removed; a draft of a published object leaves the
  published version as it is.

## My pending items

- Figures: sent by you, waiting for your approval, held by an approval.
- Filters: all, sent by you, waiting for your approval; class; order (newest, waiting longest, name).
- Each card says **what holds** the version (the approval, its state and approvers, the workflows) and offers
  **Open the approval** or **Approval and comments** when you take part, and **Published page**.
- A pending version that no approval or workflow holds any more says so: an administrator then looks at
  **Setup > Workflow processes**.

## Cleaning up old drafts

**Remove old drafts** removes your drafts not changed for 7, 30, 90 or 365 days, after a confirmation that states the
count. Public bodies often agree a rule such as "drafts older than 90 days are removed at the end of each quarter";
each editor applies it on their own page. Exponential's version limit also applies:
`content.ini [VersionManagement] DefaultVersionHistoryLimit=45` versions per object by default; when it is reached,
the oldest archived version makes room ([Versions of an object](../../../../guides/content-history.md#3-a-new-draft-from-an-old-version)).

## Seeing the whole team's backlog

My drafts is personal by design, so there is no page listing every editor's drafts. What a web manager can see:

| Need | Where |
|---|---|
| Everything waiting for approval | **Setup > Workflow processes**, filter *Waiting*; each card names the content, the editor and the step |
| How long things wait | the overview figure **Oldest waiting** and the amber mark on processes unchanged for more than a day |
| Drafts of a leaver | ask the person before they leave; an administrator can open an object's versions page to see its drafts ([Versions of an object](../../../../guides/content-history.md)) |

## Problems

| You see | Why | Do |
|---|---|---|
| A draft you expected is missing | filtered, older than the age filter, or someone else's | clear the filters (they are kept in the address) |
| A version waiting for your approval is not listed | you may not read it | ask for `content/versionread` on that area |
| "Send for publishing" seemed to do nothing once | a failed process with the same key was removed first | see [Workflow troubleshooting](workflow-troubleshooting.md) |

## Related pages
- [Collaboration inbox](collaboration-inbox.md)
- [Version history and accountability](version-history-and-accountability.md)
- [Approval workflows overview](approval-workflows-overview.md)
- [My drafts and my pending items (guide)](../../../../guides/drafts-and-pending.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
