# Recent content: moderating what was created and changed (6.0.15)

Read this page if visitors submit content to your site (comments, reviews, forum posts, forms that create objects), if
editors need one place to see what changed lately, or if you set up roles for moderators.

**Dashboard > Recent content** (`moderation/recent`, the second link of the Dashboard menu, after *Dashboard*) lists the most recently created and
modified objects of the whole installation, newest first, and lets a moderator act on a selection of them.

## The list

| Column | What it shows |
|---|---|
| Modified, Created | the object's last modification and its first publication (*not yet* for a draft) |
| Name, Class | linked to the object's main location; its id |
| Status | Published, Draft (never published) or In the trash; Pending (a version waits for an approval or a date); Hidden; Held for review (*Held: new submission* when the [auto-hold](review-hold.md#auto-hold) held it), with the reason and who placed it |
| Publication | the version that is published, in progress or pending, and **Scheduled for** the date of a *Wait until date* workflow event |
| Section, Creator | the creator linked to their user account |
| Submitted from | the address, the browser and the siteaccess the submission came from (only with `moderation/personaldata`, see below) |

Filters: a date range on either date, the class, the status, held or not, hidden or visible, the section, the
siteaccess the submission came from and the creator (a login or a user id). The column heads Modified, Created, Name,
Class, Section and Creator sort the list; a second click reverses the order. The page sizes are 25, 100, 300, 700 and
1000 (admininterface.ini `[PaginationSettings] ItemsPerPageList_moderation_recent[]`); each user's choice is
remembered (the preference `admin_moderation_limit`). Every filter, sort and page has its own address, so a filtered
view can be bookmarked.

### Fast on a large database

The page is one indexed read of `ezcontentobject` (the index `ezcontentobject_modified`, new in 6.0.15, or
`ezcontentobject_pub` for the creation date) cut with an offset and a limit, plus one count. A filter on another table
(held, hidden, pending, siteaccess) is first read as a list of ids from that table's own index. The rows of a page are
completed with one query per table (locations, versions, workflow processes, states, hold data, creators, audit
events): about ten queries whatever the page size, never one per row. Every query reads one table, so the list works
the same on MySQL/MariaDB, PostgreSQL, SQLite, Oracle and MongoDB. Measured: 1000 rows in well under a second on
SQLite with 1,500 objects; the engine tests check a page of 1000 rows under two seconds on every engine.

The upgrade step `6.0.15-contentobject-modified-index` adds the index to an existing database (`./console exp:upgrade`;
not in the SQL update files, which also run on databases without the content tables).

### Where the address and the browser come from

The kernel does not store who submitted an object from where; the [audit](../../bc/6.0/audit.md) does. The column shows,
per object, the earliest of the events in content.ini `[ModerationSettings] SubmissionEvents[]`:
`content.object.hold.auto` (always recorded when the auto-hold holds something), `content.object.publish` and
`content.object.create` (both off by default; switch them on in audit.ini `[AuditEventSettings]` to see every
submission). The values are as the audit keeps them: by default the address is truncated to its /24 (IPv4) or /48
(IPv6) network and the browser to its first part (audit.ini `[AuditPrivacySettings]`). Without an event the column says
*not recorded*. They come from the audit index; the page brings it up to date for half a second before it reads it.

## Actions on a selection

Tick items (or *Select all on this page*), choose an action and *Apply to the selected*:

| Action | Runs | Decided by |
|---|---|---|
| Release the hold | the `updateobjectstate` operation (not_held), then reveals the locations the auto-hold hid | `state/assign` of not_held; `content/hide` to reveal |
| Hold for review (with a reason) | `updateobjectstate` (held); the reason is kept with the hold | `state/assign` of held |
| Hide, Reveal | the body of the `content/hide` operation per location | `content/hide` |
| Move to trash | the `content/delete` operation, to the trash | `content/remove`; an item with sub items is left alone |
| Delete | the `content/delete` operation; a draft that was never published is purged | `content/remove` |
| Assign section | `updateSection` for the object | `section/assign` (the object's allowed sections) |
| Assign state | `updateobjectstate` | `state/assign` (the object's allowed states) |

Each item is checked against the moderator's own policies, one by one, and the result page lists every item as
**done**, **refused** (the policy, or the item is not in a state the action applies to: "It is held already") or
**failed** (an error), with the reason. *Move to trash* and *Delete* show a confirmation first, and its button asks
**Verify it's you** (site.ini `[ReauthGroup_delete]`, rule `moderation/action ConfirmDestructiveButton`).

A selection of more than content.ini `[ModerationSettings] JobThreshold` items (100) runs as the content job
`moderation` in the background: its progress page `content/job/<id>` opens, the moderator can leave it, and when it is
done the page opens `moderation/report/<id>` with the result per item. The job runs as the moderator, in batches, each
in its own transaction ([content jobs](../../bc/6.0/content-jobs.md)).

Every action is recorded in the audit as `content.moderation.action` (the action, the counts and the ids done, refused
and failed); each item's own change is recorded by the operation (`content.object.state`, `content.node.hide`,
`content.object.remove` ...), and holds as `content.object.hold.place` and `content.object.hold.release`.

## Policies

| Policy | Gives |
|---|---|
| `moderation/read` | the list and the dashboard block: every object's name, class, status, hold, section and creator |
| `moderation/act` | the actions (each item's own content, section and state policies still decide) |
| `moderation/personaldata` | the *Submitted from* column: addresses and browsers are personal data |

New installations give the Editor role `moderation/read` and `moderation/act` (the editors' everyday policies,
`expEditorPolicies`); Administrators have every policy. `moderation/personaldata` is given to nobody by default. On an
existing installation the upgrade step `6.0.15-editor-policies` adds the two to the Editor role when it runs.

## Dashboard

The block **Recent content** (dashboard.ini `[DashboardBlock_moderation]`, for users with `moderation/read`) shows how
many objects are held for review and how many were created today, each linked to the list filtered on them.

## The website sidebar

The [website sidebar](https://github.com/se7enxweb/expwt) links *Recent content* for moderators, and its Page panel
releases a hold the same way (the hidden locations revealed).

## Settings

```ini
# content.ini
[ModerationSettings]
JobThreshold=100
SubmissionEvents[]=content.object.hold.auto
SubmissionEvents[]=content.object.publish
SubmissionEvents[]=content.object.create
```
