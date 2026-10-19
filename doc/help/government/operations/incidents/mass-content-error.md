---
title: Mass content error
audience: government, admin, editor, project-manager
category: public-sector
order: 100
summary: Runbook for a large mistake in content - a subtree removed, a wrong section or state set on many pages, a bulk import gone wrong, an announcement published too early - with the trash, version history, content jobs, the audit trail and, as a last resort, a database restore.
keywords: mass error, bulk mistake, removed subtree, trash restore, wrong section, wrong state, import error, mass delete, mass_delete alert, version history, content jobs, restore backup, incident
type: help
---
# Mass content error

This runbook is for web managers and administrators when many pages went wrong at once: an editor removed a whole
folder, a section or state was set on a large subtree, an import overwrote pages, or something was published before
its time. Start by stopping further damage, then use the most precise tool that undoes it.

## Contents
1. [Stop the damage](#stop-the-damage)
2. [Find out exactly what happened](#find-out-exactly-what-happened)
3. [Undo, from the most precise tool](#undo-from-the-most-precise-tool)
4. [The last resort: restore the database](#the-last-resort-restore-the-database)
5. [Tell people](#tell-people)
6. [Related pages](#related-pages)

## Stop the damage

- If wrong content is public and sensitive (an embargoed announcement, personal data), hide the item at once
  (`content/hide`), or switch on maintenance mode for the whole site.
- If a content job is still running (a large removal, copy or section assignment), its progress page lets you follow
  it; wait for it or stop new ones ([Content jobs](../../../../features/6.0/content-jobs.md)).
- Ask editors to pause changes in the affected area.

## Find out exactly what happened

```bash
./console exp:audit search --channel=content --from='<start>' --limit=100 --allow-root-user
./console exp:audit search --name='content.node.remove*' --from='<start>' --allow-root-user
```

A subtree removal is one parent event with one child per node below it; a content job is the parent of everything its
batches did; the alert `mass_delete` fires at 500 nodes removed by one user in ten minutes
([Audit trail, "Why you want it"](../../../../features/6.0/audit-trail.md#why-you-want-it)). Note who, when and which
top node.

## Undo, from the most precise tool

| Mistake | Undo |
|---|---|
| Items or a subtree removed to the trash | **Trash**: restore the items; the trash shows who trashed each item and where it was ([Trash](../../../../bc/6.0/trash.md)) |
| Wrong text published on many pages | each page's versions page, **Make a new draft** from the last good version, publish |
| Wrong section assigned to a subtree | **Setup > Sections**, **Assign content** of the correct section to the same top node |
| Wrong state set on a subtree | the top node's **Details** tab, Content state, the correct state with "Also for everything below this node" |
| Published too early | hide or move back to draft; check caches and feeds that already carried it ([Stale cache](stale-cache.md)) |
| Moved to the wrong place | move back; note that moving changes the section of objects that had the old parent's section |

Removed for good (trash emptied, or user accounts, which never go to the trash): only a backup brings them back.

## The last resort: restore the database

If the precise tools cannot undo it, restore the last backup from before the mistake, on a test server first to
check it holds what you need ([Restoring to a test server](../backups-and-recovery/restoring-to-a-test-server.md)).
Options: restore production entirely (losing every legitimate change since the backup), or copy the lost content
back from the restored copy by hand or with a package export. Decide with the service owner.

## Tell people

- Editors: what happened, what was restored, what they must redo.
- The public, if wrong information was visible: a correction where it appeared.
- The person who made the mistake: as a process problem, not blame; the review asks why the tool allowed it
  ([Post-incident review](post-incident-review.md)).

## Related pages
- [Version history and accountability](../editorial-workflows/version-history-and-accountability.md)
- [Defacement](defacement.md)
- [Rollback planning](../upgrades-and-change/rollback-planning.md)
- [Publishing and versions (help)](../../../publishing-and-versions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
