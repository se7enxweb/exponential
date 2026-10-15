# The content checks: unreferenced objects and broken relations

Read this page if you tidy up a content tree (images and files nobody uses any more) or look for published pages
that link to or embed something a visitor cannot reach; and if you decide who may do that.

## In short

- **Unreferenced objects** (`/contentcheck/unreferenced/<node id>`): the published **assets** (images, files,
  media) below a node that **nothing uses**: no other object, no layout, no setting, no template, no role, no shop or
  newsletter entry, no dynamic collection, and no parent that shows them. Filter by class, sort, page through them,
  and move the ticked ones to the trash, after a confirmation and only those you may remove. Pages, folders, users
  and other content are never listed: they are reached through the tree.
- **Broken relations** (`/contentcheck/brokenrelations/<node id>`): visible, published content that refers to an
  object or node that was deleted, is in the trash, is hidden or was never published, with the source, the field,
  the target, its state and what to do about it, and a link to edit the source; and on the first page the layouts
  whose blocks, collections or rules name such content. Read only.
- Both are in the Setup menu and on the Setup overview (group Content model), and in the context menu of the content
  tree and the sub-items list (**Advanced > Unreferenced objects in subtree**, **Broken relations in subtree**),
  which open them for that node.
- Three policies of the new module `contentcheck`: `unreferenced`, `trash` and `brokenrelations`. Every list shows
  only what the user's `content/read` allows. Only Administrator has them in a new installation.
- Administration siteaccesses only. Moving to the trash is the kernel's own removal and is recorded in the audit
  as any removal to the trash (`content.node.remove.trash`).
- On the command line, report only: `./console exp:content-check` (`bin/php/contentcheck.php`), see
  [On the command line](#on-the-command-line).

## What "unreferenced" means

A published page is reached by navigation, menus, search and its address whether or not anything links to it, so
"nothing links to it" says nothing about whether it is needed. The list is therefore limited to **assets**: objects
of the classes in `contentcheck.ini [UnreferencedSettings] AssetClassList[]` (by default `image`, `file`, `video`,
`audio`, `flash`, `quicktime`, `windows_media`, `real_video`), which matter only when something uses them.

An asset is **unreferenced** when all of these hold:

1. it is published, its main location is below the node, and you may read it;
2. **every location's parent only stores it**: the parent's class is in `StorageParentClassList[]` (by default
   `folder`). An image below a gallery, an article, an event or a landing page is shown by that parent's template,
   so it counts as used ("shown by its parent");
3. no **relation row** refers to it (see below), and
4. nothing in the **reference index** names it: by object id, by the id of any of its locations, by its or its
   locations' remote id, by its path (URL alias), or by the path of one of its files; and no **dynamic collection**
   lists it.

Objects with sub-items are left out unless asked for. Published objects without any location are counted (for a user
who may read everything) but not listed: nothing can reach them, and the trash needs a location.

## What counts as a reference

### Relation rows

The relations the kernel keeps in `ezcontentobject_link`, of every type: object relations (common), relation fields
(attribute), and the embed and link relations the rich text editor registers. A relation counts when it comes

- from the **published version**, a **draft**, an **internal draft** (an edit in progress), a **pending** or a
  **queued** version, and
- from an object that is **published** or a **draft object** (never published yet).

These do **not** count: relations from an object **in the trash**, from **old (archived), rejected or repeat
versions**, and an object's relation **to itself**. So an image that only a draft uses is not listed (the draft may
still be published); an image that only a trashed page used is listed. When the trashed page is restored, its embed
shows as a broken relation.

### The reference index

Many references never make a relation row: content imported straight into the database, datatypes that do not
register relations, and everything outside the fields. The checks read them into a **reference index**
(`expContentCheckReferences`), from the same counting versions and objects as above:

| Source | What is read |
|---|---|
| Fields | `ezobjectrelation` (`data_int`), `ezobjectrelationlist` (`contentobject-id`, `node-id`), `ngenhancedlink` (an internal link's id, which may be an object or a node and writes no relation row: both count), and in the text of any field: `object_id`/`node_id` of links and embeds, `ezobject://`, `eznode://`, `ezcontent://`, `ezlocation://`, `exp-remote-location://`, `content/view/<mode>/<node>`, `content/download/<object>`, storage file paths (`var/.../storage/...`, looked up in `ezimagefile`, `ezbinaryfile`, `ezmedia`) and internal paths of links (`href="/media/images/logo"`, compared with the URL alias with or without a site access prefix or host) |
| Links | the `ezurl` table (links of rich text and URL fields), custom URL aliases (`ezurlalias_ml`, `is_alias`), URL wildcards |
| Layouts | collection items (the `value_id` of a location item is a **node** id, of a content item an object id), dynamic collections (their parent node and topic content; the children or the subtree of the parent, of the classes the collection lets through; a collection of the current location lists below the nodes its layout's rules target: the children of a node target, everything below a subtree target, and, for a rule without such targets, a shared layout or a layout without rules, anywhere, limited to parents of the classes of the rule's class conditions), block parameters (links as `exp-remote-location://<remote id>`, `ezlocation://`, `ibexa-location://` through `explayouts.ini [NexusNodeMap]`; ids in parameters named for content, images or nodes; HTML with storage paths or links), rule targets (node, subtree, content, paths). Every status, so a draft's blocks count; a shared layout's blocks are rows like any other |
| Settings | every settings file (`settings/`, its siteaccess and override directories, every extension's `settings/`): the whole numbers in a setting whose block or name speaks of nodes, objects, roots, parents, subtrees, containers or folders (`RootNode`, `MediaRootNode`, `TopNodeID`, `NexusFooterMenuID[]` ...), remote ids there, and `content/view/...` paths in any setting (`IndexPage`) |
| Templates | literal ids in the templates of every design: `hash( 'node_id', 58 )`, `parent_node_id`, `object_id`, `content/view/full/77` |
| Roles | role assignments limited to a subtree, policies limited by node, parent node or subtree |
| Tables | the columns of `ObjectColumns[]`, `NodeColumns[]` and `NodeListColumns[]`: by default the shop (product collections of baskets, wish lists and orders), collected information, pending actions, role assignments, subtree notifications, bookmarks, where drafts will be placed (`eznode_assignment`), and the newsletter (edition articles, sends, tracked links, schedules, approvals, lists). A table that does not exist is skipped, so an extension's tables can be added |
| Newsletter | the article pools: their parent nodes, and the children of those of the pool's classes |

`eztags` was checked and adds nothing: a tag link goes from an object to a tag, never to an object.

### What cannot be known

- A template that **computes** an id (`fetch( 'content', 'node', hash( 'node_id', $id ) )`), PHP code with an id,
  and anything outside this installation (another site, a newsletter already sent, a bookmark in a browser). Check
  an asset before you remove it when such code exists; the trash keeps it restorable.
- Settings are read by the names of their settings: a node id in a setting named otherwise is not seen.
- An index older than `IndexTTL` (see [Settings](#settings)) may miss what was added since; the move to the trash
  always builds it again.

If a source cannot be read, the list shows nothing and says so: an incomplete index would call used content unused.

## Unreferenced objects

Open **Setup > Unreferenced objects**, or **Advanced > Unreferenced objects in subtree** in the context menu of a
node. Without a node in the address the list starts at the content tree's top node (`content.ini [NodeSettings]
RootNode`).

- **Listed** are the unreferenced assets whose **main location** is below the node (not the node itself). An object
  with locations elsewhere as well is listed under its main location; the column Location says how many it has.
- **Objects with sub-items** are left out unless **Also objects with sub-items** is ticked.
- **Classes**: the asset classes; none chosen lists every one of them.
- **Order by** name, class, last modified, published or object id, ascending or descending. The order is stable
  across pages (the object id breaks ties).
- **Columns**: name (a link to the node, with a Hidden tag for a hidden location), class, size, last modified and
  location (the path of the main location, the number of locations, and the number of sub-items when they are shown).
  The **size** is that of the files of the current version (image, binary file, media), read from the storage or
  the cluster for the rows of the page only; `–` for an object without files or whose files are missing.
- **Per page**: the sizes of `admininterface.ini [PaginationSettings]` (10, 25, 50 unless configured), kept as the
  preference `admin_contentcheck_list_limit`.
### Moving to the trash

Tick objects and press **Move ticked objects to the trash**. A row has a box only when you may move it: you hold
`contentcheck/trash`, you may remove its main location, and it has no sub-items. The next page lists every ticked
object with what will happen to it, and asks you to confirm. Only on **Move … objects to the trash** does anything
happen.

At the confirmation and again when it is done, each object is checked anew. It is moved only when

- it is still a published asset and **nothing uses it now**, judged against a reference index built at that
  moment (a relation, a layout block or anything else added in the meantime keeps it);
- you may read it and remove **every one of its locations** (`content/read`, `content/remove`);
- **none of its locations has sub-items**, hidden ones and ones you may not read included: the action moves single objects; remove a container from the content
  tree, where its sub-items are shown;
- its class allows the trash, it has no version waiting to be published, and no content job holds it.

The objects go to the **trash**, never deleted permanently, through the kernel's own removal (the operation
`content/delete` with `move_to_trash`, as **Remove** in the content tree), one object at a time: one that fails
leaves the others done. Workflows, caches and the search index are handled by that operation. Each is recorded
in the audit as `content.node.remove.trash` with who did it, and can be restored from the trash. The form posts with
the session's form token (and ezformtoken's); another method than POST is refused.

At most `MaxTrashBatch` objects (50) are taken from one confirmation; the page says so when more were ticked. The
confirmation is bound to exactly the objects it shows and to your session: when the objects or your access changed
in between, nothing is moved and the list says so.

## Broken relations

Open **Setup > Broken relations**, or **Advanced > Broken relations in subtree** for a node. Without a node the content
tree's top node is checked (`content.ini [NodeSettings] RootNode`); enter 1 to check the whole tree (Media, Users and
the other top nodes as well), which needs an unlimited `content/read`.

**Sources** are published objects whose main location is visible (not hidden, not below a hidden node), below the
node and not below an excluded one, that you may read. Of each source's **published version** the check reads

- its relations in `ezcontentobject_link` (object relations, relation fields, and the embed and link relations of its
  rich text), and
- the links and embeds in the text of its **XML fields** (`XmlDatatypes[]`, `ezxmltext` by default): `object_id` and
  `node_id` of `<link>`, `<embed>` and `<embed-inline>`, in every language. These still name an object or node after
  it was deleted, when its relation rows went with it;
- the data of its **relation fields** (`ezobjectrelation`, `ezobjectrelationlist`, `ngenhancedlink`), which name a
  target also where no relation row was written (an import; an enhanced link never writes one). An enhanced link's
  id is judged as the object, or as the node when no object has that id.

With the first page, for a user who may read all content, the **Layouts** section lists what the layouts name that
a visitor cannot reach: manual collection items (by node or object), the parent node of a dynamic collection (mapped
through `[NexusNodeMap]` as the layouts do), block links (`exp-remote-location://`, `ezlocation://`) and rule
targets by node or subtree, with the layout, the block or rule, and the target's state. Fix them in the layout
editor.

A **target** is reported when it is

| State | Meaning |
|---|---|
| Deleted | there is no object (or node) with that id any more |
| In the trash | the object is in the trash, or the node is (a node link or embed) |
| Hidden | every location of the object is hidden or below a hidden node; or the linked node is |
| Never published | the object is a draft that was never published |
| Without location | the object is published but has no location in the tree |
| Not available to you | with a limited `content/read`: the target has no location you may read, whatever its state |

A target that is fine is not listed, and neither is a source's relation to itself. A relation of the rich text and
the link in its text are one finding; the field is the one whose text holds the link.

Each finding shows the **source** (a link to its node, its class and object id), the **field** (the field's name,
the language for a link in rich text, and the way: link, embed, inline embed, object relation, relation field), the
**target** (object or node id; the name only of a target that is never published or without location and that you
may read: a deleted, trashed or hidden target is shown by its id and state only; a target you may not read as "not
available", see [What you may not read](#what-you-may-not-read)), its **state** with **what to do** (remove the
reference or choose another target; restore it from the trash; reveal it; publish it; give it a location; ask
someone who may read it), and an **Edit** button when you may edit the source. The filters:

- **Below node**: the subtree to check (1 checks the whole tree).
- **Leave out the nodes below**: node ids, separated by commas (up to 20); the sources below them are not checked,
  for example the Users and the Design subtree.
- **Targets**: the states to report.
- **References**: links and embeds in rich text, object relations and relation fields, or both.

### Pages

The check walks the sources by object id, `ChunkSize` objects (200) with one set of queries, and stops when a page
of findings is full or `ScanLimit` objects (5000) were checked. **Next page** goes on after the last object checked;
the page header says how many objects were checked and after which object. A page always ends after the whole of
one source, so it can show a few findings more than its size. There is no total: counting would mean reading the
whole tree, which is what the pages avoid. **From the start** begins again.

## Who may use them

| Policy | Opens |
|---|---|
| `contentcheck/unreferenced` | the list of unreferenced objects |
| `contentcheck/trash` | with `unreferenced`: moving them to the trash; each object needs `content/remove` as well |
| `contentcheck/brokenrelations` | the list of broken relations |

The policies have no limitations; what each list shows is limited by `content/read` (as a subtree fetch is, in its
SQL), and what may be moved by `content/remove`.

### What you may not read

What you may not read is left out completely, with the permission condition in the SQL of every query, not only in
what is drawn: from the lists, the counts, the pages and **Next page**, the number of objects checked, the locations
and sub-items a row shows, and the excluded subtrees (a node id you may not read is ignored like an unknown one).
Nothing of it is counted, named or logged. Adding or removing content you may not read changes nothing you see.

- **Broken relations**: a source you may not read is not checked. A target you may not read is shown as **Target
  not available** with the state **Not available to you**, without its id and without telling whether it is
  deleted, in the trash, hidden or fine: all look the same, so the list says nothing about content you may not see.
  With an unlimited `content/read` every target shows its real state and id.
- **Unreferenced objects**: whether an object you may read is unreferenced is decided against **every** reference,
  also from objects you may not read, and an object with sub-items you may not read is not offered for the trash;
  otherwise content in use would go to the trash. That an object you may read is missing from the list is
  therefore a weak hint that something refers to it (or lies below it). This cannot be avoided without offering
  referenced content for the trash; nothing about that something (number, name, id, class) is shown anywhere, and
  there is no view that says why an object is not listed. A start node you may not read answers as not found. A new
installation gives them to Administrator only (which holds every policy). Give them to a role for the people who
tidy up the content, not to every editor: the lists show the content of the whole subtree at once.

The context menu entries appear only for users with the matching policy. The Setup links use the same policies
(`PolicyList_unreferenced_objects[]`, `PolicyList_broken_relations[]` in `menu.ini`).

### Requests that are refused

`contentcheck/trash` takes only POST (405 otherwise), with the form token of the checks (a session secret) and, when
the extension is active, the token of `ezformtoken` (403 otherwise). The move itself needs the confirmation token
of the plan shown. Ids are read as whole numbers only; everything else in a request is left out. The checks answer
404 outside the administration siteaccesses and when `Enabled=false`.

## Settings

`settings/contentcheck.ini`; override it in `settings/override/contentcheck.ini.append.php`.

```ini
[ContentCheckSettings]
# true | false: with false the checks answer 404 everywhere
Enabled=true
# The siteaccesses they answer on; empty: every siteaccess with an administration design
SiteAccessList[]

[UnreferencedSettings]
# The classes the list covers (assets); pages, folders and users are never listed
AssetClassList[]=image
AssetClassList[]=file
AssetClassList[]=video
...
# An asset is listed only when every location's parent is of one of these classes
StorageParentClassList[]=folder
# At most this many objects moved to the trash in one confirmation (1 to 200)
MaxTrashBatch=50
# How long the reference index is kept, in seconds
IndexTTL=600
# false on very large installations: only the CLI script or the cronjob part builds the index
BuildOnRequest=true
# Fields read per query while building the index; candidates judged for one list at most
ScanChunkSize=2000
CandidateLimit=20000
# Table columns whose ids keep an object or node from being unreferenced (a missing table is skipped)
ObjectColumns[]=ezproductcollection_item.contentobject_id
ObjectColumns[]=cjwnl_edition_article.contentobject_id
...
NodeColumns[]=ezsubtree_notification_rule.node_id
NodeColumns[]=ezcontentbrowsebookmark.node_id
NodeColumns[]=eznode_assignment.parent_node
NodeListColumns[]

[BrokenRelationsSettings]
# The datatypes whose text is read for links and embeds
XmlDatatypes[]
XmlDatatypes[]=ezxmltext
# Objects checked with one set of queries (10 to 1000)
ChunkSize=200
# At most this many objects checked for one page (ChunkSize to 100000)
ScanLimit=5000
```

Page sizes: `admininterface.ini [PaginationSettings] ItemsPerPageList_contentcheck_unreferenced[]` and
`ItemsPerPageList_contentcheck_brokenrelations[]`.

## On the command line

Report only; nothing is changed or moved:

```
./console exp:content-check --build-index            # build the reference index (as the cronjob part does)
./console exp:content-check --unreferenced --node=2  # every unused asset below node 2
./console exp:content-check --broken --node=2        # every broken relation below node 2, and the layouts'
./console exp:content-check --why=123,456            # what uses objects 123 and 456 (the first reference found)
./console exp:content-check --unreferenced --json    # the same as JSON
```

The lists are those of the user given by `--login` (default `admin`). Exit code 0 when nothing is found, 1 when
something is, 2 on an error. On a very large installation set `BuildOnRequest=false` and run the cronjob part
`contentcheck` (`php runcronjobs.php contentcheck`) more often than `IndexTTL`.

## Performance

**The reference index** reads, once: every field of the counting versions whose text looks like a reference, in
chunks by attribute id (`ScanChunkSize`), the `ezurl` table, the layout tables, the settings files and the templates.
It is kept in the cache for `IndexTTL` seconds. On a demo installation on SQLite (1,100 objects, 12,000 fields, 650
layout blocks, 4,700 templates, 800 settings files) it is built in 2.5 to 4.5 s; the list then takes a few hundred
milliseconds. Where building it on a request is too slow, set `BuildOnRequest=false` and build it with the cronjob
part `contentcheck`.

**The unreferenced list**: the candidates (assets below the node with no relation row, at most `CandidateLimit`)
come from one query in the chosen order, with the reference condition as a correlated `NOT EXISTS` on the indexes
`ezco_link_to_co_id` and `(contentobject_id, version)` of the versions and the permission condition of
`content/read` in the same SQL. They are judged in PHP against the index, with one query per 500 objects for their
locations and parents; the count is of what is left. Then a fixed number of queries for the page (locations,
sub-items, files, removable). The sizes are the one per-row lookup (a file stat, or a query of the cluster table),
on the rows of the page only.

**The broken relations** read one chunk of sources by primary key order (`id > last`), then their relations, their
XML fields, their relation fields and the state of every target in seven queries per chunk (plus one per enhanced
link, to tell an object from a node). Measured on MariaDB with a copy of a real installation plus 200,000 generated
articles below one folder (206,000 objects, 118,000 relations, 750,000 attributes), before the relation fields were
added:

| Request | Time |
|---|---|
| Broken relations, a page of 50 findings (about 1,600 sources checked) | 20 to 30 ms |
| Broken relations, a page that finds nothing: 5,000 sources checked | 50 to 350 ms |

The layouts section reads the layout tables once, with one query per target it judges.

## Databases

The queries are plain SQL with `NOT EXISTS`, `LIKE` on path strings and `LIMIT` through the database layer: MySQL
and MariaDB, PostgreSQL, SQLite and Oracle. On MongoDB the checks say they are not available.

## For developers

- Module `kernel/contentcheck/` (views `unreferenced`, `trash`, `brokenrelations`); the view code in
  `kernel/private/classes/views/contentcheck/`; the classes in `kernel/classes/contentcheck/`:
  `expContentCheck` (settings, access, tokens, the reference condition `referenceExistsSQL()`),
  `expContentCheckUnreferenced` (asset classes, parameters, SQL, judging, the page, `reasons()`),
  `expContentCheckReferences` (the reference index: one extractor per source, `build()`, the cache, `reason()`),
  `expContentCheckTrash` (plan and move), `expContentCheckBrokenRelations` (parameters, source query,
  `xmlReferences()`, `attributeTargets()`, target states, `hint()`, the scan, `layoutFindings()`).
- Command `exp:content-check` (`bin/php/contentcheck.php`, `kernel/private/classes/commands/contentcheck.php`) and
  the cronjob part `contentcheck` (`cronjobs/contentcheck.php`).
- To add a reference source of an extension's table, list its column in `ObjectColumns[]`, `NodeColumns[]` or
  `NodeListColumns[]`; for anything else, extend `expContentCheckReferences::build()` with a `from...()` method and an
  extractor with a test in `expContentCheckReferencesTest`.
- Templates `contentcheck/*.tpl` in `design/admin` and `design/admin4`; context menu entries in
  `popupmenu/popup_sub_advance_menu.tpl` of both.
- Tests: `tests/tests/kernel/contentcheck/expContentCheckQueryTest.php` needs no database (input, SQL, rich text,
  states); `expContentCheckReferencesTest.php` neither (every reference source's extractor, judging, the asset
  classes, relation fields and hints). `expContentCheckSandboxTest.php` writes content, users and roles and runs only in a throwaway
  installation: `EXP_CONTENTCHECK_SANDBOX=$PWD EXP_CONTENTCHECK_SANDBOX_DATABASE=<database>
  EXP_CONTENTCHECK_SANDBOX_SITEACCESS=<admin siteaccess> php vendor/bin/phpunit tests/tests/kernel/contentcheck/`.
