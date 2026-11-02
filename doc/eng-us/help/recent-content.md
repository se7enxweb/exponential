---
title: "Recent content"
audience: editor, admin
category: content
order: 40
summary: Dashboard > Recent content - the newest content of the whole site, newest first, with what is held for review, drafts waiting for a date and who submitted what from where; filters, page sizes up to 1000 and actions on a selection (release, hold, hide, reveal, trash, delete, section, state).
keywords: recent content, moderation, moderate, held for review, review hold, auto-hold, comments, submissions, spam, release, hold, hide, reveal, trash, delete, bulk, mass action, selection, moderation/read, moderation/act, moderation/personaldata, IP address, browser, dashboard block
type: help
---
# Recent content

**Dashboard > Recent content** (the second link of the Dashboard menu, after *Dashboard*) shows the newest content of the whole site, newest first:
what visitors submitted, what is **held for review**, drafts that were never published and versions waiting for an
approval or a date. Moderators release or remove a whole selection at once there.

## What the list shows

- **Modified** and **Created**: the last change and the first publication (*not yet* for a draft).
- **Name** (opens the item) and **Class**.
- **Status**: *Published*, *Draft* or *In the trash*; *Pending* when a version waits for an approval or a date;
  *Hidden*; *Held for review*, or *Held: new submission* when it was held automatically, with the reason.
- **Publication**: the published version, a version in progress or pending, and *Scheduled for* its date.
- **Section** and **Creator** (opens the user).
- **Submitted from**: the address, the browser and the siteaccess, for users who may see personal data only. The values
  are what the audit recorded, shortened by its privacy settings; *not recorded* when it recorded nothing.

## Finding items

Use the filters above the list: a date range on the modification or the creation date, the class, the status, held or
not, hidden or visible, the section, the siteaccess the item was submitted on, and the creator (a login or a user id),
then **Filter**. **Clear filters** shows everything again. Click a column head to sort by it; click it again for the
other order. **Per page** under the list shows 25, 100, 300, 700 or 1000 items; your choice is remembered. Every
filtered page has its own address, so you can bookmark it.

## Actions on a selection

Tick the items (or **Select all on this page**), choose the action and click **Apply to the selected**:

- **Release the hold**: the items may be edited and published again, and what was hidden when it was held
  automatically is shown on the site again.
- **Hold for review**: give the reason; nobody edits or publishes the items until a reviewer releases them.
- **Hide** and **Reveal** the items on the site.
- **Move to trash** (restorable) and **Delete** (for good): you confirm first and are asked to verify that it is you.
  Items with sub items are left alone; remove them in the content structure.
- **Assign section** and **Assign state**.

Each item is checked against your own permissions. The result lists every item as done, refused (with the reason, for
example "You may not hide it" or "It is held already") or failed. More than 100 items run in the background: a
progress page opens, you may leave it, and the result per item is shown when it is done.

## Held submissions

When the site holds new submissions automatically (comments, for example), a visitor's new item is hidden and held
right after it is sent: visitors do not see it until a moderator releases it here or in the website sidebar. Filter on
**Held for review: Held** to see what waits.

## The dashboard block

The **Recent content** block on the dashboard shows how many items are held for review and how many were created
today; each number opens the list filtered on them.

## Who may do what

- *moderation/read*: see the list and the dashboard block.
- *moderation/act*: the actions (your other permissions still decide per item).
- *moderation/personaldata*: the **Submitted from** column.

Editors and administrators have the first two in a new installation. Details for administrators:
[Recent content](../features/6.0/moderation.md) and [the auto-hold](../features/6.0/review-hold.md#auto-hold).
