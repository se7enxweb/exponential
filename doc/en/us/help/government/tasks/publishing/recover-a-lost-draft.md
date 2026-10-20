---
title: Recover a lost draft
audience: government, editor
category: public-sector
order: 580
summary: For editors - find work you started and did not publish in My drafts (filters, search, age), continue it with Edit, handle an edit conflict, and clean up old drafts; plus autosave and what cannot be recovered.
keywords: lost draft, My drafts, content/draft, unsaved work, autosave, edit conflict, Possible edit conflict, Remove old drafts, store draft, recover
type: help
---
# Recover a lost draft

This page is for editors who started editing, closed the browser or got interrupted, and want their work back. Every
edit is a draft until it is published, and drafts are kept in **My drafts** (`/content/draft`)
([Drafts and pending items, 1](../../../../guides/drafts-and-pending.md#1-my-drafts)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Drafts are yours: only you see them on this page.
- Where the `ezautosave` extension is active, the draft is saved automatically every 180 seconds while you edit
  (`autosave.ini [AutosaveSettings] Interval`); what you typed in the last minutes before closing may be missing.

## Steps

1. Open **My drafts** (dashboard, left column, or `/content/draft`).
2. Find the draft: **Find** searches name, location and class; filter by translation, class or age; order by last
   modified.
3. On its card (name, class, location or "New, to be published below ...", translation, version, created, modified),
   press **Edit**. **View** shows it first.
4. Finish and **Send for publishing**.

**If you opened the page again and got "Possible edit conflict":** the page lists existing drafts with creator and
time. Select your draft and press **Edit selected** to continue it, or **New draft** to start again from the published
version.

## Check the result

- [ ] After publishing, the draft is gone from **My drafts** and the page shows your change.
- [ ] Old drafts you no longer need: **Remove old drafts** (7, 30, 90 or 365 days without a change), or tick and
  **Remove selected**.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Removing the draft of a new item | removes the new item too (it was never published) |
| Drafts of the same page by colleagues | you cannot edit their drafts; talk to them, or make a new draft |
| Expecting drafts to be kept for ever | old drafts may be cleaned up by a cronjob on your site |
| Long work without saving | press **Store draft** now and then |

## Related pages

- [Publishing tasks](README.md)
- [Preview before publishing](preview-before-publishing.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md#my-drafts)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
