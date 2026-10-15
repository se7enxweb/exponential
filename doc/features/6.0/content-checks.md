# The content checks: unreferenced objects and broken relations

Read this page if you tidy up a content tree (images and files nobody uses any more, pages nothing links to) or look
for published pages that link to or embed something a visitor cannot reach; and if you decide who may do that.

## In short

- **Unreferenced objects** (`/contentcheck/unreferenced/<node id>`): the published objects below a node that no
  other object refers to. Filter by class (for example only Image and File), sort, page through them, and move the
  ticked ones to the trash, after a confirmation and only those you may remove.
- **Broken relations** (`/contentcheck/brokenrelations/<node id>`): visible, published content that refers to an
  object or node that was deleted, is in the trash, is hidden or was never published, with the source, the field,
  the target and its state, and a link to edit the source. Read only.
- Both are in the Setup menu and on the Setup overview (group Content model), and in the context menu of the content
  tree and the sub-items list (**Advanced > Unreferenced objects in subtree**, **Broken relations in subtree**),
  which open them for that node.
- Three policies of the new module `contentcheck`: `unreferenced`, `trash` and `brokenrelations`. Every list shows
  only what the user's `content/read` allows. Only Administrator has them in a new installation.
- Administration siteaccesses only. Moving to the trash is the kernel's own removal and is recorded in the audit
  as any removal to the trash (`content.node.remove.trash`).

## What counts as a reference

Both checks read the relations the kernel keeps in `ezcontentobject_link`. Every relation type counts: object
relations (common), relation fields (attribute), and the embed and link relations the rich text editor registers
when content is published (embed, link).

An object is **referenced** when another object has a relation to it

- from the **published version**, a **draft**, an **internal draft** (an edit in progress), a **pending** or a
  **queued** version, and
- that other object is **published** or a **draft object** (never published yet).

These do **not** count:

- relations from an object that is **in the trash**: an object referred to only by trashed content is unreferenced;
- relations from **old (archived), rejected or repeat versions**: what a page referred to before it was edited;
- an object's relation **to itself**.

So an image that only a draft uses is not listed (the draft may still be published); an image that only a trashed
page used is listed. When the trashed page is restored, the image is referenced again; moving the image to the
trash first does not prevent restoring the page, but its embed then shows as a broken relation.

What the checks do not see, because it is not a relation: an object named in a template, an INI setting or a
`node_id` hard-coded elsewhere; a link in an `ezurl` field to the object's address; content of datatypes that do
not register relations; a link in rich text that was stored without its relation (content imported straight into
the database). Check such objects before you remove them. The broken relations check reads rich text itself (see
below), so a deleted target is found there as well.

## Unreferenced objects

Open **Setup > Unreferenced objects**, or **Advanced > Unreferenced objects in subtree** in the context menu of a
node. Without a node in the address the list starts at the content tree's top node (`content.ini [NodeSettings]
RootNode`).

- **Listed** are published objects whose **main location** is below the node (not the node itself), that nothing
  refers to and that you may read. An object with locations elsewhere as well is listed under its main location;
  the column Location says how many it has.
- **Objects with sub-items** (folders with content) are left out unless **Also objects with sub-items** is ticked:
  a container is rarely referred to and is not a leftover because of that.
- **Classes**: none chosen lists every class; choose one or several (Ctrl or Cmd) to see, for example, only images
  and files.
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

- it is still published and **nothing refers to it now** (a relation added in the meantime keeps it);
- you may read it and remove **every one of its locations** (`content/read`, `content/remove`);
- **none of its locations has sub-items**, hidden ones and ones you may not read included: the action moves single objects; remove a container from the content
  tree, where its sub-items are shown;
- its class allows the trash, it has no version waiting to be published, and no content job holds it.

The objects go to the **trash**, never deleted permanently, through the kernel's own removal (the operation
`content/delete` with `move_to_trash`, as **Remove** in the content tree), one object at a time: one that fails
leaves the others done. Each is recorded in the audit as `content.node.remove.trash` with who did it, and can be
restored from the trash.

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
  it was deleted, when its relation rows went with it.

A **target** is reported when it is

| State | Meaning |
|---|---|
| Deleted | there is no object (or node) with that id any more |
| In the trash | the object is in the trash, or the node is (a node link or embed) |
| Hidden | every location of the object is hidden or below a hidden node; or the linked node is |
| Never published | the object is a draft that was never published |
| Without location | the object is published but has no location in the tree |

A target that is fine is not listed, and neither is a source's relation to itself. A relation of the rich text and
the link in its text are one finding; the field is the one whose text holds the link.

Each finding shows the **source** (a link to its node, its class and object id), the **field** (the field's name,
the language for a link in rich text, and the way: link, embed, inline embed, object relation, relation field), the
**target** (object or node id; the name only of a target that is never published or without location and that you
may read: a deleted, trashed or hidden target is shown by its id and state only) and its **state**, and an **Edit** button when
you may edit the source. The filters:

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
SQL), and what may be moved by `content/remove`. What you may not read is never shown, only counted: the list of
unreferenced objects says how many more there are below the node, and a page of broken relations how many sources in
the range it checked were left out (to the end of the tree on its last page). Neither names, classes nor paths of
those objects are read for the page, written to a log or recorded. A start node you may not read answers as not found. A new
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
# At most this many objects moved to the trash in one confirmation (1 to 200)
MaxTrashBatch=50

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

## Performance

Measured on MariaDB with a copy of a real installation plus 200,000 generated articles below one folder (206,000
objects, 118,000 relations, 750,000 attributes):

| Request | Time |
|---|---|
| Unreferenced, 100,000 hits below one folder: count and a page of 50, by name or by date | 1.0 to 1.7 s |
| the same, page 1000 (offset 50,000) | 1.1 s |
| Broken relations, a page of 50 findings (about 1,600 sources checked) | 20 to 30 ms |
| Broken relations, a page that finds nothing: 5,000 sources checked | 50 to 350 ms |

How: the unreferenced list is one `COUNT` and one page query, each with the reference condition as a correlated
`NOT EXISTS` on the indexes `ezco_link_to_co_id` and `(contentobject_id, version)` of the versions, and the
permission condition of `content/read` in the same SQL; then a fixed number of queries for the page (locations,
sub-items, files, removable). The broken relations read one chunk of sources by primary key order (`id > last`),
then their relations, their XML fields and the state of every target in six queries per chunk, never one per row.
The sizes are the one per-row lookup (a file stat, or a query of the cluster table), on the rows of the page only.

## Databases

The queries are plain SQL with `NOT EXISTS`, `LIKE` on path strings and `LIMIT` through the database layer: MySQL
and MariaDB, PostgreSQL, SQLite and Oracle. On MongoDB the checks say they are not available.

## For developers

- Module `kernel/contentcheck/` (views `unreferenced`, `trash`, `brokenrelations`); the view code in
  `kernel/private/classes/views/contentcheck/`; the classes in `kernel/classes/contentcheck/`:
  `expContentCheck` (settings, access, tokens, the reference condition `referenceExistsSQL()`),
  `expContentCheckUnreferenced` (parameters, SQL, the page), `expContentCheckTrash` (plan and move),
  `expContentCheckBrokenRelations` (parameters, source query, `xmlReferences()`, target states, the scan).
- Templates `contentcheck/*.tpl` in `design/admin` and `design/admin4`; context menu entries in
  `popupmenu/popup_sub_advance_menu.tpl` of both.
- Tests: `tests/tests/kernel/contentcheck/expContentCheckQueryTest.php` needs no database (input, SQL, rich text,
  states). `expContentCheckSandboxTest.php` writes content, users and roles and runs only in a throwaway
  installation: `EXP_CONTENTCHECK_SANDBOX=$PWD EXP_CONTENTCHECK_SANDBOX_DATABASE=<database>
  EXP_CONTENTCHECK_SANDBOX_SITEACCESS=<admin siteaccess> php vendor/bin/phpunit tests/tests/kernel/contentcheck/`.
