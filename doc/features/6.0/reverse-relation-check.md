# Removing and hiding: the check of reverse relations

Before content is removed or hidden, the administration interface can list the other objects that point at it:
articles that embed an image, pages that link to a file in their rich text, objects whose relation field names it.
Removing such content leaves those references pointing at nothing; hiding it leaves them pointing at content the
site no longer shows. The check makes this visible before it happens, and on request refuses the removal.

## Settings

Both settings are in `content.ini`.

| Setting | Values | Default | What it does |
|---|---|---|---|
| `[RemoveSettings] ReverseRelationCheck` | `disabled`, `warn`, `block` | `warn` | content/removeobject, also where it is reached as content/removeuserobject and content/removemediaobject |
| `[HideSettings] ReverseRelationCheck` | `disabled`, `warn` | `warn` | content/hide and "Hide selected" of the sub items list |
| `[ReverseRelationSettings] ProviderClassList[]` | class names | none in the kernel; explayouts adds its own | references kept outside `ezcontentobject_link` (Layouts blocks) |

A missing setting takes the default; an unknown value takes the default too and writes a warning to the debug log.
`block` under `[HideSettings]` is taken as `warn`: a hide is never refused.

### Removal

- `disabled`: no check. The confirmation page is the one of earlier releases, with its general hint to look at
  Advanced > Reverse related for subtree.
- `warn`: the confirmation page lists, per selected item, the objects that point at what goes, in place of that
  hint. OK removes as before. One page is new: selecting every location of an object without sub items removed it
  at once before; when other objects point at it, the page with the list is shown first. With
  `HideRemoveConfirmation=true` there is no confirmation page, and so no list.
- `block`: as `warn`, and the removal is refused while such references exist. The OK button is disabled and says
  why. The view checks again when the confirmation is posted, so a form posted without the page (or a page left
  open while someone else added a reference) removes nothing either; the page then says "Nothing was removed".
  `HideRemoveConfirmation=true` does not get past it: the page is shown instead. A large removal that would run as
  a content job is refused the same way, before the job starts. A check that cannot be made (a database error)
  counts as a reference.

`warn` is the default because it adds information (and, in the one case above, a page) before content and its
references are lost, and never refuses anything; `block` changes what an editor can do and is a decision for each
site. The block itself is on the server, so it holds in every design; the list and the disabled OK button are in
the admin4, admin and standard templates of `node/removeobject.tpl`.

The refusal (`block`, off by default) holds on every way the kernel removes content from a request: the
confirmation of content/removeobject (also as content/removeuserobject and content/removemediaobject),
`HideRemoveConfirmation=true`, a removal as a content job, the move to the trash (it is the same removal), the
removal of the last locations of an object on the Locations tab (content/action sends a removal with findings to
content/removeobject, which lists or refuses it) and a delete over WebDAV (refused as forbidden). ezjscore and the
REST interface of the kernel have no removal. Scripts, cronjobs and the content API called directly are not checked.

### Hiding

- `disabled`: no check; the node is hidden at once, as before.
- `warn`: when the node about to be hidden has references, or its object has further locations, a confirmation
  page lists them first. This applies to every way of hiding in the administration interface: Hide / unhide in the
  context menu, Hide on the Locations tab of the node view, a link to content/hide, and "Hide selected" in the sub
  items list (one list per selected item). OK posts the hide again with `ReverseRelationCheckConfirmed`, and the
  hide is done. A large hide also offers the choice between now and a content job on the same page. Revealing a
  hidden node is never checked.

`warn` is the default: a hide with neither references nor further locations is done as before, and one that has
them shows what it affects first (from the context menu a hide is a POST without a page of its own). Set
`disabled` for the behaviour of earlier releases.

## What counts as a reference

A row of `ezcontentobject_link` counts when

- its source object is published (`status = 1`) and the row belongs to the source's current version
  (`from_contentobject_version = current_version`): references from drafts, from archived versions and from objects
  in the trash do not count;
- its kind is any of object relation (`common`, 1), embed in rich text (`embed`, 2), link in rich text (`link`, 4)
  and relation field (`attribute`, 8). A row whose kinds were all taken back (`relation_type = 0`) does not count;
- its target is an object that the operation takes away, and its source is not.

An object is taken away when every one of its locations lies inside the selected subtrees (a node with everything
below it). An object that keeps a location elsewhere stays, and the references to it stay valid; removing only
some locations of an object therefore never shows the list. For the same reason a reference from an object that
goes as well is not listed.

### Layouts blocks and other references outside ezcontentobject_link

Classes listed in `[ReverseRelationSettings] ProviderClassList[]` add references the kernel does not store as
relations. Each has a static method `reverseRelations( array $paths, int $limit )` that receives the path strings
of the selected subtrees and answers `array( 'total' => int, 'rows' => array( ... ) )` (each row: `to_node_id`,
`to_object_id`, `to_name`, `to_path`, `label`, `type`, `url`, `readable`), or `false` when it could not check (the
check then counts as failed, so `block` refuses). A class that does not exist is skipped, so a process that
started before an extension added its class keeps working.

explayouts registers `expLayoutsReverseRelationProvider` in its `content.ini.append.php`: every block of a
published layout whose collection names a node inside the subtrees (collection items hold node ids, whatever their
value type). Unlike a relation, this counts a location, not an object: the block loses its item when that node
goes, even when the object keeps a location elsewhere. The list shows "Layout: block" with a link to the layout
editor, the type "Layouts block"; a user without `explayouts/read` sees it only counted.

### The list

The list shows for each reference the referring object (a link to its main location), its class, the kind of
reference and, for a relation field, the name of the field, and which object it points at ("This item" for the
selected one, else the name of the object below it).

Only what the current user may read is described. A reference from an object the user may not read (by section,
state, subtree or class; read access is checked once for all objects of the list) is not listed; the item counts
them: "1 reference you cannot see", "3 references you cannot see". The same goes for further locations the user
may not read ("2 further locations you cannot see"). A target the user may not read is named "An object you may
not read". No name, class or ID of such objects reaches the page, a log or an audit record; the check has no JSON
or AJAX output.

The count cannot be avoided. The checks count every reference, so `block` refuses a removal even when the user
sees none of them, and a hide asks first even when every reference comes from an object the user may not read;
without the sentence the page could not explain why, and with the number the editor knows how much there is to
ask an administrator about. It tells the user how many such references exist per selected item, never which.

At most 100 references are read per page; the total is counted in full, and when more exist than are read the
page says so in a fixed sentence, without a number.

Not covered: links to a location (`eznode://`) in rich text are stored as a relation to the location's object and
count only when that object goes. URL aliases and URL wildcards that name a node are not references (the system
removes or redirects them with the node). Block parameters of Layouts other than collections are not read.
Not checked: the content API and operations called directly, scripts and cronjobs, and the trash purge (the
content was checked when it went to the trash).

Performance: two statements over `ezcontentobject_link` (a count and at most 100 rows), one over the target
locations, one per provider (a count and at most 100 rows), the selected nodes in one fetch, read access of every
named object in one fetch, class and field names once per request. A removal of many items or of a subtree costs
the same number of statements; the subtree is matched with `path_string LIKE`, which the index of
`ezcontentobject_tree` serves.

## Implementation

- `Exponential\Service\ReverseRelationCheck` (`kernel/private/classes/services/reverserelationcheck.php`): the
  modes, the query (two SQL statements over `ezcontentobject_link`, `ezcontentobject` and
  `ezcontentobject_tree`, any database engine of the kernel) and the description for the page.
- `content/removeobject`, `content/hide` and the HideButton action of `content/action` call it. The removal
  refuses in mode `block` before each way it removes (the content job, the confirmation and the direct removal of
  locations). Nested selections are queried once, through the outer subtree.
- Templates: `design/standard/templates/content/reverse_relation_check.tpl` (the list), included by
  `node/removeobject.tpl` (admin4, admin and standard) and `content/job_confirm.tpl` (admin4, admin, standard). Strings in
  the translation context `design/admin/content/reverserelations`.
- Permissions, form tokens and audit records are those of the operations themselves: the confirmation pages post
  with the form token where `ezformtoken` is active, and nothing is written before the operation runs.
- Tests: `expReverseRelationCheckTest` (modes, decisions, the query on SQLite, the order of the checks in the
  views, the providers, the Locations tab and WebDAV) and `AdminReverseRelationCheckTest` (the pages in admin4,
  the counts, the Layouts row, the translations); explayouts: `tests/explayoutsreverserelationprovidertest.php`.
