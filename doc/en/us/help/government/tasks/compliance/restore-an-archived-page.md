---
title: Restore an archived page
audience: government, editor, admin
category: public-sector
order: 470
summary: Step by step - bring a page back from the archive to the current site in Exponential, whichever way it was archived (moved to an archive folder, hidden, set to an archived state, or put into an archive section), and check that visitors see it.
keywords: restore, archived page, move back, reveal, hidden, object state, section, archive folder, unarchive, visibility, public sector
type: help
---
# Restore an archived page

For editors and administrators, when an archived notice or page must be current again (a consultation reopened, a notice
still valid). How to undo depends on how it was archived ([Archiving old notices](../../records-and-archiving/archiving-old-notices.md)).

## Contents

1. [Find out how it was archived](#find-out-how-it-was-archived)
2. [Steps by method](#steps-by-method)
3. [Check that it worked](#check-that-it-worked)
4. [Related pages](#related-pages)

## Find out how it was archived

Open the item in the administration:

- its location in the tree: under an archive folder?
- the visibility badge in the node header: **Hidden** or **Hidden by a parent**?
- the **Details** tab: its section and its content states.
- the audit for the node: `/audit/console/(object)/node:<node id>` shows moves, hides, state and section changes.

## Steps by method

| Archived by | Restore |
|---|---|
| moving into an archive folder | **Move** it back to its former parent (the audit's move record names it) |
| hiding | **Reveal** (if hidden by a parent, reveal the parent or move the item out) |
| an archive state | Details tab, **Content state**, set the current state |
| an archive section | assign the former section (section's **Assign content**, or the Details tab) |
| the scheduled hide (`hide.php` and a hide date) | clear or change the hide date, then Reveal; otherwise the next cronjob run hides it again |

If it is in the **trash**, it was removed, not archived: see "Restore an item from the trash".

## Check that it worked

- Open the page in a private window on the public siteaccess: it shows.
- It appears in the current list (if the list should include it) and in search after reindexing.

## Related pages

- [Archive last year's notices](archive-last-years-notices.md)
- [Hiding content](../../records-and-archiving/hiding-content.md)
- [Object states for records](../../records-and-archiving/object-states-for-records.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
