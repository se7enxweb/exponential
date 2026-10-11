# The content finder

Read this page if you need to find a content object when all you have is an id or a path (an object id from a log,
a node id from a URL, the attribute id of an error message, the path of an image file on disk), to see every version,
location and trash entry of it, or to remove it from there; and if you decide who may do that.

## In short

- **Setup > Content finder** (`/contentfinder/search`), and a small search box below the content structure in the
  Content tab. One search field, and an optional selector for what the text is.
- It finds the object behind an object, node, attribute or version id, a remote id, a URL alias, a page URL, or the
  path or URL of an image, binary or media file below `var/`.
- Each result shows the object's class, name, owner, section, languages, states, remote id and dates; every version
  (id, number, status, language, creator, modified, with a link to the version view); every location (node id, path,
  URL alias, main, hidden); and, when it is in the trash, where it was, when and by whom it was trashed.
- From the result: **Move to trash**, **Delete permanently**, **Purge from the trash**, **Restore** and **Remove
  version**. Each goes through a confirmation page that shows exactly what will happen; what cannot be undone needs
  an extra tick.
- Two policies: `contentfinder/search` to use it at all, `contentfinder/delete` for its actions. Each result shows
  and offers only what the user's content policies allow; something the user may not see is answered exactly like a
  miss.
- Administration siteaccesses only. Every search and every action is recorded in the audit.

## Finding something

Type the id, path or URL and press **Find**. With **Search for: Recognise automatically** (the default) the text
decides:

| You type | It is looked up as |
|---|---|
| `4242` | an object id, a node id (in the tree and in the trash), an attribute id and a version id: every hit is shown |
| `node:4242`, `object:4242`, `attribute:…`, `version:…`, `remote:…`, `alias:…`, `file:…` (or `n:`, `o:`, `a:`, `v:`, `r:`, `f:`) | that type only |
| `https://www.example.com/admin/content/view/full/4242` | node 4242 (any view mode, with or without a siteaccess) |
| `https://www.example.com/content/download/12/345/file/report.pdf` | attribute 345 |
| `var/site/storage/images/news/summer/1234-2-eng-GB/summer_large.jpg`, or the full URL of it | the image file: the attribute, the object and its nodes |
| `var/site/storage/original/application/4c1b…e9.pdf` | the binary or media file of that name (and of that mime group) |
| `var/site/storage/images/news/summer/` or `…/original/application/*` | every file below that prefix (up to the row limit) |
| `d41d8cd98f00b204e9800998ecf8427e` (32 hexadecimal digits) | a remote id |
| `News/Summer-party`, `/News/Summer-party/`, `https://www.example.com/News/Summer-party?x=1` | a URL alias or a page address (exactly; see "URL aliases and siteaccesses" below) |
| any other word | a remote id and a URL alias |

Choose a type in the selector to look the text up as that only: **Object id**, **Node id**, **Attribute id**,
**Version id**, **Remote id**, **URL alias**, **File path**.

Each result block starts with **Found by**: what matched. For an attribute or a file it names the attribute (id,
identifier, datatype), the version and the language the match was in, and the file path.

An image is stored once per alias (`…_large.jpg`, `…_small.jpg`) and in every version that kept it; any of those
paths finds it. A path to a file that is not referenced by an attribute (an orphan left in `var/storage`) finds
nothing.

### URL aliases and siteaccesses

A siteaccess with `site.ini [SiteAccessSettings] PathPrefix` shows its pages without the prefix: the alias stored as
`fit-healthy/showcase/test-product` is the page `/site/showcase/test-product` of the siteaccess `site` with
`PathPrefix=fit-healthy`. The finder takes all three: a URL alias or a page address is tried, exactly and in this
order, until one resolves:

1. the path as typed;
2. the path as a page address: the siteaccess the kernel matches for it (`eZSiteAccess::match()`: by its first
   segment with URI matching, by the host of a full URL with host or host and URI matching), its segment taken off,
   and that siteaccess's own `PathPrefix` put in front, as `eZURLAliasML` does (not when the path already starts
   with it, nor when its first element is in `PathPrefixExclude[]`); then the same without the prefix;
3. a first segment that names a siteaccess taken off, with that siteaccess's prefix in front, then without;
4. the path with each distinct `PathPrefix` of the siteaccesses (`AvailableSiteAccessList[]` and
   `RelatedSiteAccessList[]`) in front.

Each siteaccess's prefix is read from its own `site.ini`. **Found by** names the alias that resolved and, when a
siteaccess or a prefix made the difference, which. These are exact lookups like the others, never a `LIKE`, and
the rights decide per result as for every lookup.

### What is accepted

- Ids are whole numbers from 1 to 2147483647 (a leading `#` is allowed). Anything else is not an id: `1 OR 1=1`
  is looked up as a remote id and a URL alias, as text.
- A URL is reduced to its path: scheme, host, query and fragment are dropped; the path is percent-decoded once and
  repeated slashes are made one. Only `http`, `https` and `ftp` URLs are taken.
- A file path must be below `var/`. A path with a `.` or `..` segment (also percent-encoded), a backslash, a NUL
  byte or another control character is refused. A prefix (a trailing `/` or `*`) must name at least
  `var/<dir>/storage`. A `*` anywhere else is refused.
- Texts longer than 1024 bytes and texts that are not valid UTF-8 are refused.

### How it looks things up

Every lookup is one query on an indexed column (or an exact match on a short one), with a hard row limit:
`ezcontentobject.id`, `ezcontentobject_tree.node_id`, `ezcontentobject_trash.node_id`, `ezcontentobject_attribute.id`,
`ezcontentobject_version.id`, the `remote_id` of objects, nodes and trashed nodes, `eZURLAliasML::fetchNodeIDByPath()`,
`ezimagefile.filepath`, and `filename` and `mime_type` of `ezbinaryfile` and `ezmedia`. Integers are cast, texts are
escaped by the database; a prefix is `LIKE 'prefix%' ESCAPE '!'` with `%`, `_` and `!` in the text escaped. There
is never a `LIKE '%text%'`.

At most `ResultLimit` objects (25) are described for one search, and at most `FileRowLimit` file rows (50) are read
for one path or prefix; the page says when there were more.

## Who may use it

| Policy | What it allows |
|---|---|
| `contentfinder/search` | open the finder (the Setup entry and the box show only with it) and search |
| `contentfinder/delete` | the actions of the finder; each needs its content policies as well (below) |

No role but Administrator (which holds every policy) has them in a new installation. Give `contentfinder/search` to
a support role; it shows that role nothing it could not see in the content structure, the trash or the version
history. Give `contentfinder/delete` only to the roles that also remove content.

### What each result shows

The finder never shows more than the user may see elsewhere. It asks the kernel's own checks:

| Part | Shown when |
|---|---|
| the object (name, class, owner, section, languages, states, remote id, dates) | `content/read` on the object (`eZContentObject::canRead()`), or a trash entry of it the user may see |
| a location | `content/read` on that node (`eZContentObjectTreeNode::canRead()`) |
| a version | `content/versionread` on that version (`eZContentObjectVersion::canVersionRead()`); the published version also for who may read the object |
| a trash entry | `content/restore` or `content/cleantrash`, and the trash list's own permission SQL (`content/read` where the node was) |
| a match by node, URL alias or node remote id | that node is one the user may read |
| a match by attribute, file path or version id | the version the match is in is one the user may see |

A hit the user may not see is dropped. When nothing is left, the page says **Nothing found, or you have no access to
what was found.**, exactly as for an id that does not exist, so ids cannot be probed. (The audit record of the search
counts the objects found but not shown; only `audit/read` sees it.)

### The actions

| Action | Needs (besides `contentfinder/search` and `contentfinder/delete`) | Done by |
|---|---|---|
| Move to trash | `content/read` and `content/remove` on every location of the object and on everything below them; the object can go to the trash (no user below it) | the `content/delete` operation with `move_to_trash` (as **Remove** in the content structure) |
| Delete permanently | as Move to trash | the `content/delete` operation without the trash |
| Purge from the trash | the trash entry visible; `content/cleantrash` and `content/remove` | the trash service's purge (as **Empty trash** for one object) |
| Restore | `content/restore` | the kernel's own **content/restore** page, which the button opens |
| Remove version | who may edit the object; `content/versionremove` on the version; a draft, archived, rejected or internal draft version (never the published one or one a workflow holds) | `eZContentObjectVersion::removeThis()`, as the version history does |

- Every action is a POST from the result page and opens a **confirmation page** that lists exactly what will
  happen: each location with the number of nodes below it, the number of versions, other objects that relate to it.
  Nothing is done until **Confirm** is pressed there.
- **Delete permanently**, **Purge** and **Remove version** cannot be undone: the confirmation needs the tick
  **I understand that this cannot be undone.** as well.
- The confirmation is bound to the user's session, the action, the object and what the page showed: when anything
  changed in between (a node added below, the object moved to the trash by someone else), the action is refused with
  **The object changed after the confirmation page was shown.**
- A removal with more nodes below it than `content.ini [RemoveSettings] MaxNodesRemoveSubtree` (100) is refused
  here: remove it from the content structure, where a large removal runs as a background job. A subtree a content job
  is working on, and one with something waiting for approval, is refused too.
- After the action the finder shows the outcome and the object as it is now.

### Requests that are refused

- `contentfinder/action` answers **405** to anything but POST, and **403** to a POST without the form tokens: the
  ezformtoken token (checked for every POST, and again by the view) and the finder's own token, bound to the
  session and the user.
- On a siteaccess that is not an administration one the finder answers **404** (a user without
  `contentfinder/search` gets the access denied page first).

## Settings

`settings/contentfinder.ini`, overridden in `settings/override/contentfinder.ini.append.php` or a siteaccess:

```ini
[ContentFinderSettings]
# true | false: with false the finder answers 404 everywhere
Enabled=true
# The siteaccesses it answers on; empty: every siteaccess whose design list has an
# administration design (admin, admin2, admin3, admin4, admin4l, adminui, ...)
SiteAccessList[]
# At most this many objects per search (1 to 100)
ResultLimit=25
# At most this many file rows per path or prefix (1 to 500)
FileRowLimit=50
```

The Setup entry is `menu.ini [Leftmenu_setup] Links[contentfinder]` (with `PolicyList_contentfinder[]=contentfinder/search`);
the box is `design:contentfinder/box.tpl`, included by `parts/content/menu.tpl` of the admin and admin4 designs (and
so of admin2, admin3, admin4l and the editor siteaccess, which use them). A design with its own
`parts/content/menu.tpl` includes the box with

```smarty
{include uri='design:contentfinder/box.tpl'}
```

## The audit

| Event | Recorded | Data |
|---|---|---|
| `content.finder.search` | every search (channel `content`, info) | the type chosen, the types tried, the text (up to 128 characters), the objects shown and the objects found but not shown; `refused` with the reason for a text that is not taken |
| `content.finder.action` | every action done (channel `content`, notice), and every refusal of the action view | the object, the target (the nodes, the trash entry or the version), the action, the locations and the nodes below; the kernel's own removal events (`content.node.remove.trash`, `content.object.remove`, `content.object.purge`, `content.version.remove`) are recorded as its children |

The actor is the signed-in user, as for every audit event. See [the audit trail](audit-trail.md).

## For developers

- `expContentFinderInput::parse( $text, $type )`: the recognition, without a database.
- `( new expContentFinder() )->search( $text, $type )`: the results for the current user;
  `expContentFinder::isAvailableHere()`, `expContentFinder::hasPolicy( $module, $function )`.
- `expContentFinderAction::plan( $action, $objectID, $versionID )` and `::execute( ..., $confirmToken )`.
- The views are `Exponential\View\Kernel\Contentfinder\Search` and `\Action` (`Action::decide()` is the request
  check, without side effects).
- Tests: `tests/tests/kernel/contentfinder/` (`--testsuite kernel-contentfinder`). `expContentFinderInputTest` needs
  nothing; `expContentFinderSandboxTest` writes content, users and roles (`fixtures/expcontentfindertestfixtures.php`),
  so it runs only in a throwaway copy of an installation named by `EXP_CONTENTFINDER_SANDBOX` and is skipped
  everywhere else.
