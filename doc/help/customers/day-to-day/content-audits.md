---
title: Content audits
audience: customer, editor, project-manager
category: customer-success
order: 23
summary: How a site owner runs a yearly content audit on an Exponential site - choosing the scope, exporting an inventory from the sub-items list, using the content checks and search statistics, deciding keep, update, merge, archive or remove per page, and following up - with a worksheet.
keywords: content audit, content inventory, ROT, outdated content, sub-items CSV export, content checks, broken relations, archive, review, yearly
type: help
---
# Content audits

This page is for the owner and editor-in-chief who want to know what is on the site and whether it is still right.
Sites grow; a yearly audit keeps them useful. Exponential gives you the inventory (the sub-items list with its CSV
export), the problem lists (the content checks) and the visitors' view (search statistics). The decisions are yours.

## Contents

1. [Scope](#scope)
2. [Build the inventory](#build-the-inventory)
3. [Find the problems](#find-the-problems)
4. [Decide per page](#decide-per-page)
5. [Follow up](#follow-up)
6. [Audit worksheet](#audit-worksheet)

## Scope

A full audit of a large site takes weeks. Audit one area at a time: a folder, a section, one brand of a multisite,
or one class (all events, all products). Choose the areas visitors use most, or the ones nobody has looked at for
the longest.

## Build the inventory

The **sub-items list** of a folder lets each user choose columns from a registry of more than a hundred, including
every attribute of the classes in the list, and export the visible columns as **CSV**
([The sub-items list](../../../features/6.0/subitems-table-options.md)). Export is on by default, up to 5000 rows
(`settings/subitems.ini [SubitemsSettings] CSVExport`, `CSVLimit`).

Useful columns for an audit: name, class, modified date, owner or last modifier, section, visibility, translations,
address. Open the CSV in a spreadsheet and add the columns of the [audit worksheet](#audit-worksheet).

> **Tip**
> If you planned the site with a content inventory, start from it
> ([Site map and content inventory](../../creators/website-creators/site-map-and-content-inventory.md)); the audit
> then compares the plan with what grew.

## Find the problems

| Tool | Finds | Source |
|---|---|---|
| Broken relations | visible content pointing to something removed, hidden, in the trash or never published | [The content checks](../../../features/6.0/content-checks.md) |
| Unreferenced objects | images and files nothing uses | same |
| Link management, invalid | broken outgoing links | [URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md) |
| Search statistics | what visitors look for and do not find | [Site search and what visitors look for](site-search-and-what-visitors-look-for.md) |
| Setup > Languages | how many items have a translation in each language | [Content languages](../../../guides/content-languages.md) |
| *My drafts* of each editor | forgotten work | [Drafts, versions and undo](drafts-versions-and-undo.md) |

The content checks are on the Setup menu and the tree's context menu (**Advanced**); only Administrator has them on a
new installation, so plan the audit with your administrator.

## Decide per page

| Decision | When | How in Exponential |
|---|---|---|
| Keep | correct, used, current | nothing |
| Update | right topic, outdated facts | edit; a new version is kept ([Drafts, versions and undo](drafts-versions-and-undo.md)) |
| Merge | two pages say the same | keep the better one, add the other's address as an alias, remove the other ([URLs, redirects and moved pages](urls-redirects-and-moved-pages.md)) |
| Archive | no longer current, but worth keeping | hide it or move it to an archive area ([Archiving and the trash](archiving-and-the-trash.md)) |
| Remove | wrong, duplicate, or of no value | remove to the trash; decide where its visitors go |

Ask the page's owner before deciding, and record who decided.

## Follow up

1. Put the update work into the [Content calendar](content-calendar.md) with owners and dates.
2. Make large moves or removals as content jobs if the subtree is big: from 50 nodes on, the confirmation offers to
   run them in the background ([Content jobs](../../../features/6.0/content-jobs.md)).
3. Re-run the broken relations check after the removals.
4. Leave removed pages in the trash for a while before emptying it.
5. Write down what you would do differently next year.

## Audit worksheet

**Template.** Columns to add next to the CSV export:

| Address | Name | Class | Last modified | Owner | Still correct? | Used? (views, search, links) | Decision | Redirect to | Done |
|---|---|---|---|---|---|---|---|---|---|
| | | | | | yes / no | | keep / update / merge / archive / remove | | |
| | | | | | | | | | |

```text
AUDIT: area ______________________   Dates: __________ to __________
Pages reviewed: ____   keep ____ update ____ merge ____ archive ____ remove ____
Broken relations fixed: ____   Unused files trashed: ____
Signed off by: ______________
```

## Related pages

- [Archiving and the trash](archiving-and-the-trash.md)
- [Media library upkeep](media-library-upkeep.md)
- [Broken links and content checks](../../creators/website-creators/link-and-content-checks.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
