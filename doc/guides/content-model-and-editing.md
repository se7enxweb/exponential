# The content model and editing content

This guide is for editors and site builders. It shows how content is shaped in Exponential and how to work with it
every day. You define a content class, create content from it, and edit it with the online editor (including the
TinyMCE 8 engine). Then you work on many items at once, use the sub-items table, recover an item from the trash and
run a large operation as a background job.

Allow about 30 minutes, with a browser and one terminal. You need a running Exponential 6 installation
([Getting started](getting-started.md)) and an administrator login. The administration interface is on your admin
siteaccess, for example `https://edit.example.com/` or `http://localhost:8087/admin/`. Commands run from the
installation root.

## 1. How the content model fits together

| Term | Meaning |
|---|---|
| Content class | a type of content: a name, an identifier and a list of attributes (for example Article: Title, Intro, Body, Image) |
| Class group | a folder of classes in the class editor (Content, Users, Media, Setup) |
| Datatype | the kind of one attribute: `ezstring` (one line), `eztext` (several lines), `ezxmltext` (rich text), `ezimage`, `ezobjectrelationlist` and so on |
| Content object | one piece of content, an instance of a class; has versions (draft, published, archived) and languages |
| Node / location | a place of an object in the content tree; one object can have several |

The datatypes available for new attributes are the `AvailableDataTypes[]` list of `settings/content.ini`; the shipped
list has 35 entries. Check: `grep -c 'AvailableDataTypes\[\]=' settings/content.ini` prints `35`. Other extensions add
their own (for example `ezkeyword`, `ezmatrix` and `ezselection` are in the list; third-party ones are registered in
their `settings/`).

## 2. Create a class

1. Open `/class/grouplist` in the administration interface. This lists the class groups.
2. Click the group **Content**, then **New class**. (`/class/classlist/<group id>` is the list of its classes.)
3. Give it a name, for example `Note`, and an identifier `note`. Tick **Object name pattern** with `<title>`.
4. In **Add attribute**, choose the datatype `Text line` and click **Add**; name it `Title`, identifier `title`, tick
   **Required**. Add a second attribute `Text block`, name `Body`, identifier `body`.
5. Click **OK** (apply and close). Expected: the class appears in the list.

Expected result: **Setup > Classes** (or `/class/view/<id>`) shows `Note` with two attributes. The class can now be
created anywhere its parent's policies allow.

## 3. Create and edit an object

1. Open the content structure (`/content/view/full/2`), select a folder, choose **Note** in the create menu and click
   **Create here**.
2. Fill the Title, press **Send for publishing**. Expected: the page of the new object is shown, and it is in the
   sub-items list of the folder.
3. Edit it again with **Edit**. Each save creates a version; an unsaved edit leaves a draft you find again on
   `/content/draft`. The number of archived versions kept per class is limited by
   `[VersionManagement] DefaultVersionHistoryLimit` (default 10) in `settings/content.ini`; the cleanup is
   `./console exp:cleanupversions --help --allow-root-user` (read the help first; without `--help` it deletes old versions).

Autosave stores a draft every 180 seconds when the `ezautosave` extension is active
(`autosave.ini`, `[AutosaveSettings] Interval`).

## 4. The online editor and TinyMCE 8

The rich text attribute (`ezxmltext`) is edited with the online editor. Two engines exist: TinyMCE 3.5.12, the default,
and TinyMCE 8.9.2, opt-in since 6.0.15. Both write the same stored XML, so you can switch at any time.

Try TinyMCE 8 in one minute, for your user only:

1. Open `/user/preferences/set/ezoe_engine/tinymce8`.
2. Edit any object with a rich text attribute (for example an Article). The editor toolbar is the new engine; the
   status bar shows the tag path such as "Path: paragraph » embed".
3. Go back with `/user/preferences/set/ezoe_engine/tinymce3`.

For everybody on a siteaccess, put into `settings/siteaccess/<name>/ezoe.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[EditorSettings]
EditorEngine=tinymce8
EngineSwitch=enabled

*/ ?>
```

`EngineSwitch=enabled` shows a button below the editor to switch. Then clear the INI cache:
`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`. Check the setting is read:
`grep -n 'EditorEngine\|EngineSwitch' extension/ezoe/settings/ezoe.ini`.

Inside the editor: the toolbar embeds objects (search tab, Upload tab), links to nodes or objects, inserts custom tags,
tables and lists; double click an embed to edit its view, class, size and alignment. Uploads accept only the types in
`UploadFileExtensions[]` and refuse names such as `shell.php.jpg`. Depth:
[online editor TinyMCE 8](../features/6.0/online-editor-tinymce8.md),
[ezoe upgrade notes](../bc/6.0/ezoe-tinymce8.md).

## 5. The sub-items table

Open a folder that has children. The table below the node's view is the sub-items list.

1. Click **Table options**.
2. Tick columns (129 are available, grouped Basic, Node, Object, Dates, SEO and so on) and drag to reorder; choose a
   preset (SEO, Editorial, Technical) or a page size.
3. Under items per page pick **Custom** and type a number from 1 to 10000, press Enter.
4. Use the CSV export to download the visible columns. Your choice is stored per user.

Expected: the table reloads at once with the chosen columns and rows. Depth:
[sub-items table](../features/6.0/subitems-table-options.md), [catalogue and how to add a column](../bc/6.0/subitems-table-options.md),
[paging settings](../bc/6.0/pagination-settings.md).

## 6. Edit many items in one form (multi-node edit)

1. In the sub-items list tick several items, then **More actions > Edit selected**. Or search (`/content/search`), tick
   results across the whole site and click **Edit selected**.
2. The form shows one section per content class and a collapsible panel per object. **Expand all** and **Collapse all**
   are above it.
3. Press **Save drafts** to keep the work without publishing, **Publish all** to publish each object, or **Discard** to
   throw the drafts away. The report lists which objects were published, waiting for approval or refused.

To make several new items at once use **Create multiple new** next to **Create new** (up to 50 at a time). After
creating, check the new items appear under their parent. Depth: [multi-node edit](../bc/6.0/multi-node-edit.md).

## 7. The trash

1. Select an item, **Remove**, confirm. The item moves to the trash (unless you choose delete).
2. Open `/content/trash`. Each row shows who trashed the item, where it was, how many nodes were below it and its
   languages. Filter by user, type or date; **Restore** puts an item back (`/content/restore/<object id>`).
3. **Empty trash** deletes everything for good, whatever the filter. From the shell the same is
   `./console exp:trashpurge --allow-root-user`: it is permanent, so do not run it on a site you care about.

Depth: [trash: who and where](../features/6.0/trash-who-and-where.md), [trash upgrade notes](../bc/6.0/trash.md).

## 8. Large operations as content jobs

Removing, copying or moving a big subtree can run as a background job that works in batches and resumes after a crash.

1. Start a remove, copy or move on a subtree. The confirmation page offers **run now** or **in the background**; from 50
   nodes on, "background" is offered first (`[ContentJobSettings] SynchronousLimit=50` in `settings/content.ini`).
2. The browser follows `/content/job/<id>`: progress, batches, buttons to cancel or resume. All jobs are listed on
   `/content/jobs`.
3. From the shell:

```bash
./console exp:expcontentjob list --allow-root-user
./console exp:expcontentjob show <id> --allow-root-user
./console exp:expcontentjob resume <id> --allow-root-user
./console exp:expcontentjob cancel <id> --allow-root-user
```

On an installation without jobs `list` prints `No active or failed content jobs (--all lists every job).`. The
`contentjobs` cronjob part restarts a worker whose process died. Depth: [content jobs](../features/6.0/content-jobs.md),
[content jobs guide](../bc/6.0/content-jobs.md), [audit trail](../features/6.0/audit-trail.md) (every job is recorded).

## Settings used in this guide

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/content.ini` | `[DataTypeSettings]` | `AvailableDataTypes[]` | 35 datatypes | installation, siteaccess |
| `settings/content.ini` | `[VersionManagement]` | `DefaultVersionHistoryLimit` | `10` | installation, siteaccess |
| `settings/content.ini` | `[ContentJobSettings]` | `SynchronousLimit` | `50` nodes | installation, siteaccess |
| `extension/ezautosave/settings/autosave.ini.append.php` | `[AutosaveSettings]` | `Interval` | `180` seconds | installation, siteaccess |
| `extension/ezoe/settings/ezoe.ini` | `[EditorSettings]` | `EditorEngine` | `tinymce3` | installation, siteaccess, user preference `ezoe_engine` |
| `extension/ezoe/settings/ezoe.ini` | `[EditorSettings]` | `EngineSwitch` | `disabled` | installation, siteaccess |

Change a value in `settings/override/` or `settings/siteaccess/<name>/`, never in the shipped file, then clear the
INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

## Related pages

- Change how content is shown: [Templates and design](templates-and-design.md).
- What happened when: [October 2026 chronicle](../history/2026/2026-10.md), [6.0.15 changelog](../changelogs/6.0/6.0.15.md).
- Hardening of what editors can type: [datatype input hardening](../specifications/6.0/datatype-input-hardening.md).
- Running background and scheduled work: [cronjobs console](../features/6.0/cronjobs-console.md).
