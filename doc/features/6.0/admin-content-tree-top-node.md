# Administration navigation: the top node, tabs and content browse

Between 28 and 30 September 2026 the administration's content structure tree
learned to start at the top of the whole content tree, the header tabs got
clearer names, and several small navigation faults were fixed.

## The content structure tree starts at the top node

Node 1 is the virtual top node of the content structure. Until now the menu
started at node 2 (the content root), so a new installation's other top-level
folders (Users, Media, Setup, Design) were not in it.

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `settings/contentstructuremenu.ini` | `[TreeMenu]` | `RootNodeID` | `1` | Node the tree starts at. Shown under **Top Level Nodes**, which opens the dashboard. Set `2` for the previous behaviour or any other node id. |
| `settings/contentstructuremenu.ini` | `[TreeMenu]` | `MaxDepth` | `0` | Levels shown below the root of each tree; `0` means every level can be opened. A node at the last allowed level has no open/close control. Honoured by the dynamic tree too; read on every page outside the template block cache, so a change is immediate. |
| `settings/contentstructuremenu.ini` | `[TreeMenu]` | `AutoopenCurrentNode` | `enabled` | Opens and highlights the current node. The setting reached the page before but was never read by the tree script. |

The Media and Users tabs keep their own trees, rooted at `[NodeSettings]
MediaRootNode` and `UserRootNode` in `content.ini`. On search pages restricted
to a subtree the tree root node is fetched along with its id, so the class
filter follows the subtree that is shown.

### Top Level Nodes has its own full view

`content/view/full/1` now shows what the top node has instead of a placeholder
object: its name, node ID, the time of the latest change below it, the language
the list is shown in, **View**, **Details** and **Ordering** tabs (the kernel
keeps the sorting of the top-level nodes on the top node) and the usual sub items
list with paging and sorting. Before, the generic view showed the Unix epoch as
its modification date, an empty object id, an unknown language flag and Edit,
Move, Remove and version actions that cannot work on a node with no object.

### Editors with limited read access

The top node has no object of its own, so checking read access on it refused every
editor without access to the whole tree and left their content structure menu
empty once the menu starts at node 1. Its children are still filtered by the
editor's read access when they are fetched.

## Clearer header tabs

| Before | Now | Where |
|---|---|---|
| Media library | **Media** | `Name=` in the tab's `menu.ini` block |
| User accounts | **Users** | same |
| Webshop | **Store** | same; the tab opens the [Store dashboard](store-dashboard.md) |
| Tab Name and Tooltip in English only | translated | `[TopAdminMenu]` blocks |

A tab listed twice in `[TopAdminMenu] Tabs[]` is shown once, at its first place.
Extensions append their tab to that list and their settings are read after a
siteaccess's, so a siteaccess that restates the whole list to put the tabs in its
own order used to get every extension tab a second time at the end. The Setup menu
entries Maintenance, Cronjobs, Preload Sites and oAuth admin are translated too
(German: Wartung, Cronjobs, Seiten vorladen, oAuth-Verwaltung).

## The link to the site and the logo

- The site link beside the logo is worked out from the settings
  (`ezpSiteAccessURL::root()`): `DefaultAccess`, this request's scheme, this host
  when the siteaccess can be reached on it by URI or else a host
  `HostMatchMapItems` gives it or its `SiteURL`, the visitor's port when the host is
  served by this installation, and the siteaccess path unless
  `RemoveSiteAccessIfDefaultAccess` leaves it out. Before, it was `https://` plus
  the host with `edit.` cut out, so it named neither the siteaccess nor the port
  (an installation served on 443 and 8080 always led to 443). The template
  operator is `siteaccess_url()`; the two admin3 header cache blocks carry the
  address in their keys so a header rendered on one port is never served on the
  other.
- The logo links to the content root `content.ini [NodeSettings] RootNode`
  through `ezurl`, so on an admin matched by URI it stays in the admin
  (`/admin/content/view/full/2`) and on a host-matched admin it is
  `/content/view/full/2`.

## Content browse: select the current node

The row that pre-selects the node being browsed (one click on **Select** picks it)
had faults: it checked ignored nodes against a variable that did not exist, so a
node that must not be chosen (the current parent when moving or adding a
location) was offered and even pre-selected; it always posted the node id even when
the browse returns object ids (assigning a role to the Users group posted the wrong
object); and with a class constraint it wrote a bare input into the table. It now
lives in the template `content/browse_current_node.tpl` (shipped in the `admin3` and `admin4` designs) with the same checks as listed items
(permission, ignored nodes and subtrees, class constraints, containers for move,
copy and add location, swap compatibility), posts the id the browse asks for, is
shown disabled when the node may not be selected, and is left out for search
results and the top level. The thumbnail display of the admin3 design offers the
current node as the first thumbnail, pre-selected, too.

## Smaller fixes in the same sweep

- The toggler left of the node view tabs hides the tabs' content again.
- **Edit selected** and **Create multiple new** in the sub items list go to the
  admin, not the public siteaccess.
- The admin URL list counts and lists the URLs of published content in
  milliseconds (an `EXISTS` test instead of a join with `count(DISTINCT)` that took
  over two minutes on 4,615 URLs on SQLite).
- Setting the sorting of a node requires permission to edit it, and only a known
  sort field and order are stored.
- Admin error pages show their own error in the page title.
- A subtree count with a language finds a start node that has no translation in
  that language and no longer leaves that language set when the node does not
  exist (content "looked missing" for the rest of the request).

## Related pages

- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md) for the new top-level folders
- [Hidden admin tabs](hidden-admin-tabs.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
