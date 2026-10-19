---
title: Archiving old notices
audience: government, editor, admin, project-manager
category: public-sector
order: 60
summary: For public bodies that publish many time-bound notices (consultations, tenders, announcements) - the ways to take them off the current site while keeping them, using Exponential's moving, sections, object states, hiding and the scheduled hide cronjob, with their effects compared.
keywords: archive, archiving, old notices, consultations, announcements, move, archive folder, section, object state, hide, hide date, HideSettings, hide.php cronjob, unpublish, trash, public sector, content lifecycle
type: help
---
# Archiving old notices

This page is for editors, administrators and project managers. A public site publishes notices that expire:
consultations, tenders, road works, events. Visitors should find current information first, yet the old notices often
must stay available or at least kept. Exponential has no "archive" button; it has building blocks that combine well.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The options compared](#the-options-compared)
2. [Pattern: a public archive folder](#pattern-a-public-archive-folder)
3. [Pattern: hide on a date](#pattern-hide-on-a-date)
4. [Pattern: an archive state](#pattern-an-archive-state)
5. [What not to use for archiving](#what-not-to-use-for-archiving)
6. [Related pages](#related-pages)

## The options compared

| Option | Visitors see it | URL | Effort | Page |
|---|---|---|---|---|
| Move to an archive folder | yes, under the archive | old URL redirects to the new one | a move per item or subtree | this page |
| Move into an archive section | as the section's read policy allows | redirects after the move | assign section | "Sections for archives" |
| Set an object state "Archived" | as the read policy limited by state allows | unchanged | set state, subtree possible | "Object states for records" |
| Hide | no (unless hidden nodes are shown) | answers as not found to visitors | one click; or scheduled | "Hiding content" |
| Scheduled hide by a date field | no, from the date | as hide | class field and settings | below |

## Pattern: a public archive folder

1. Create a folder "Archive" (and per year if you like) under the notices.
2. At the end of each period, move the expired notices into it: select, **Move**, choose the folder.
3. Old addresses keep working: a moved node's old URL alias redirects to its new one
   ([Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md#what-happens-automatically)).
4. Exclude the archive from the main lists in your templates (fetch by parent), and keep it in the search.

Moves are recorded in the audit as `content.node.move`.

## Pattern: hide on a date

The kernel's cronjob script `hide.php` (in the default cronjob part) hides objects of chosen classes when a date
attribute has passed. It does nothing until configured:

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]
RootNodeList[]=<node id of your notices folder>
HideDateAttributeList[]
HideDateAttributeList[notice]=hide_date
```

Add a date attribute `hide_date` to the class `notice` (your class), and let editors fill it. Each run hides the nodes
whose date has passed (and updates the search index). Hidden nodes stay in the tree and can be revealed.

Note: a similar `unpublish.php` script (also in the default part, configured with `[UnpublishSettings] RootNodeList[]`
and `ClassList[]`, reading an `unpublish_date` attribute) **removes** the objects to the trash on the date. For
records, prefer hiding or moving to an archive; removal starts the road to deletion.

## Pattern: an archive state

Create a state group, for example `lifecycle` with Current (default) and Archived; limit the Anonymous role's
`content/read` to Current where archived items must not be public, or show a notice in templates for archived items
([Object states](../../../guides/object-states.md)). See "Object states for records".

## What not to use for archiving

- **The trash**: items there are on their way to deletion; a trash purge cronjob may empty it.
- **Removing old versions** of an object to "clean up": versions may be the record.
- **Draft copies**: drafts are personal work in progress.

## Related pages

- [Object states](../../../guides/object-states.md)
- [Sections](../../../guides/sections.md)
- [Structuring the content tree](../../creators/website-creators/structuring-the-content-tree.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
