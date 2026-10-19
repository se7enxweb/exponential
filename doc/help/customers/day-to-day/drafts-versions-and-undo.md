---
title: Drafts, versions and undo
audience: customer, editor
category: customer-success
order: 13
summary: What a site owner should know about drafts and versions in Exponential - how many versions are kept, how to undo a published mistake, what cannot be undone, and a small monthly clean-up of old drafts - with links to the editor pages for the clicks.
keywords: drafts, versions, undo, restore, version history, compare versions, archived, old drafts, mistakes, rollback
type: help
---
# Drafts, versions and undo

This page is for the owner and editor-in-chief who want to know how safe the team's everyday work is. In Exponential
every save is a **version**; the site shows the published one while a new **draft** is being written, and older
published versions are kept as **archived** so you can go back
([Publishing, versions and the trash](../../publishing-and-versions.md)). That makes most mistakes cheap to undo.

## Contents

1. [What is kept](#what-is-kept)
2. [Undoing a published mistake](#undoing-a-published-mistake)
3. [What cannot be undone](#what-cannot-be-undone)
4. [Old drafts](#old-drafts)
5. [Rules for the team](#rules-for-the-team)

## What is kept

| Thing | Kept how long | Source |
|---|---|---|
| Versions of an item | up to a limit per item; then the oldest archived version is removed to make room. The shipped default is 45 (`[VersionManagement] DefaultVersionHistoryLimit=45`), with a separate limit per class possible | `settings/content.ini` |
| The published version | always; it can never be removed from the versions page | [Versions of an object](../../../guides/content-history.md) |
| Drafts | until published, discarded or removed | [My drafts and my pending items](../../../guides/drafts-and-pending.md) |
| Removed items | in the trash until someone empties it | [Archiving and the trash](archiving-and-the-trash.md) |

Your site may use other limits; ask your administrator for the value in your `content.ini`.

## Undoing a published mistake

1. Open the item and press **Manage versions** (the round button with a clock).
2. **Compare** the current version with the one before: changes are marked field by field.
3. Choose the good version's translation under **New draft from this version, in** and press **Make a new draft**.
4. Check the draft and publish it. The current version is not changed until you publish.

Full steps: [Publishing, versions and the trash](../../publishing-and-versions.md#go-back-to-an-earlier-version) and
[Versions of an object](../../../guides/content-history.md).

> **Tip**
> The same works for someone else's version: the new draft is yours. That is how a colleague "takes over" a page.

## What cannot be undone

| Action | Why it is final | Safer alternative |
|---|---|---|
| **Empty trash**, or removing items from the trash | deletes for good | restore first if in doubt |
| Removing an item's **translation** | deletes all its fields in that language, in every version ([Content languages](../../../guides/content-languages.md)) | translate first, change the main language, then remove |
| Removing a **user** | users never go to the trash ([Check user accounts](../../../features/6.0/user-hygiene.md)) | disable the account |
| Removing **form submissions** | the collections are deleted; the forms stay ([Collected information](../../../guides/collected-information.md)) | export as CSV first |
| Changes to a **layout** after publishing | layouts have drafts but not the item version history; two people editing the same layout overwrite each other ([Landing pages with layouts](../../creators/website-creators/landing-pages-with-layouts.md)) | one person per layout at a time |

## Old drafts

Drafts pile up: started and forgotten, or left by someone who moved on. *My drafts* shows each person their own and
offers **Remove old drafts** after 7, 30, 90 or 365 days without a change
([My drafts and my pending items](../../../guides/drafts-and-pending.md)). Removing the draft of a **new** item also
removes that new item, which was never published; a draft of a published item leaves the published version as it is.

A monthly five minutes:

- [ ] Each editor opens *My drafts*, filters by age 30 days, finishes or removes.
- [ ] The editor-in-chief asks leavers to do this before their last day ([Offboarding staff](offboarding-staff.md)).
- [ ] Your administrator can also clean old drafts site-wide by cron: the script `old_drafts_cleanup.php` removes
      drafts older than `[VersionManagement] DraftsDuration` (90 days shipped), at most `DraftsCleanUpLimit=100` per
      run (`settings/content.ini`). It is not in the shipped global part; ask whether your site schedules it
      ([Cronjobs](../../../guides/cronjobs.md)).

## Rules for the team

- Save as draft when interrupted (**Store draft and exit**), do not leave the form open.
- One person edits an item at a time; the edit form warns when someone else is editing
  ([Creating and editing content](../../editing-content.md)).
- Before a big change, note the current version number. Going back is then one step.

## Related pages

- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Versions of an object](../../../guides/content-history.md)
- [Archiving and the trash](archiving-and-the-trash.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
