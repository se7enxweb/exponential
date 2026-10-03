# Administration navigation: the top node, tabs and content browse

This page is for administrators and editors who find their way around the admin. Between 28 and 30 September 2026 the
content structure tree learned to start at the top of the whole content tree, the header tabs got clearer names, and
several small navigation faults were fixed. If a menu looks different after an upgrade, the answer is probably here.

## The content structure tree starts at the top node

Node 1 is the virtual top node of the content structure. Until now the menu started at node 2 (the content root), so a
new installation's other top-level folders (Users, Media, Setup, Design) were not in it. Now the tree starts at node 1,
shown as **Top Level Nodes**, which opens the dashboard.

To get the previous behaviour back, set the root to node 2:

```ini
# settings/override/contentstructuremenu.ini.append.php
[TreeMenu]
RootNodeID=2
```

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/contentstructuremenu.ini` | `TreeMenu` | `RootNodeID` | `1` | siteaccess | Node the tree starts at. Set `2` for the previous behaviour, or any other node id. |
| `settings/contentstructuremenu.ini` | `TreeMenu` | `MaxDepth` | `0` | siteaccess | Levels shown below the root of each tree; `0` means every level can be opened. A node at the last allowed level has no open/close control. The dynamic tree honours it too. It is read on every page outside the template block cache, so a change is immediate. |
| `settings/contentstructuremenu.ini` | `TreeMenu` | `AutoopenCurrentNode` | `enabled` | siteaccess | Opens and highlights the current node. The setting reached the page before but the tree script never read it. |

The Media and Users tabs keep their own trees, rooted at `[NodeSettings] MediaRootNode` and `UserRootNode` in
`content.ini`. On search pages restricted to a subtree, the tree root node is fetched along with its id, so the class
filter follows the subtree that is shown.

### Top Level Nodes has its own full view

`content/view/full/1` now shows what the top node has, instead of a placeholder object: its name, node ID, the time of
the latest change below it, the language the list is shown in, **View**, **Details** and **Ordering** tabs (the kernel
keeps the sorting of the top-level nodes on the top node) and the usual sub-items list with paging and sorting.

Before, the generic view showed the Unix epoch as the modification date, an empty object id, an unknown language flag,
and Edit, Move, Remove and version actions that cannot work on a node with no object.

### Editors with limited read access

The top node has no object of its own, so checking read access on it refused every editor without access to the whole
tree. Once the menu started at node 1, their content structure menu was empty. That check is gone; the children are
still filtered by the editor's read access when they are fetched.

## Clearer header tabs

| Before | Now | Where |
|---|---|---|
| Media library | **Media** | `Name=` in the tab's `menu.ini` block |
| User accounts | **Users** | same |
| Webshop | **Store** | same; the tab opens the [Store dashboard](store-dashboard.md) |
| Tab name and tooltip in English only | translated | `[TopAdminMenu]` blocks |

A tab listed twice in `[TopAdminMenu] Tabs[]` is shown once, at its first place. Extensions append their tab to that
list, and their settings are read after a siteaccess's. So a siteaccess that restated the whole list to put the tabs in
its own order used to get every extension tab a second time at the end.

The Setup menu entries Maintenance, Cronjobs, Preload Sites and oAuth admin are translated too (German: Wartung,
Cronjobs, Seiten vorladen, oAuth-Verwaltung).

## The link to the site and the logo

- **Site link.** The link beside the logo is worked out from the settings (`ezpSiteAccessURL::root()`):
  `DefaultAccess`; this request's scheme; this host when the siteaccess can be reached on it by URI, or else a host
  that `HostMatchMapItems` gives it, or its `SiteURL`; the visitor's port when the host is served by this
  installation; and the siteaccess path unless `RemoveSiteAccessIfDefaultAccess` leaves it out. Before, it was
  `https://` plus the host with `edit.` cut out, so it named neither the siteaccess nor the port (an installation
  served on 443 and 8080 always led to 443). The template operator is `siteaccess_url()`. The two admin3 header cache
  blocks carry the address in their keys, so a header rendered on one port is never served on the other.
- **Logo.** The logo links to the content root, `content.ini [NodeSettings] RootNode`, through `ezurl`. On an admin
  matched by URI it stays in the admin (`/admin/content/view/full/2`); on a host-matched admin it is
  `/content/view/full/2`.

## Content browse: select the current node

In a browse dialog, one row pre-selects the node you are in, so one click on **Select** picks it. That row had faults:

- it checked ignored nodes against a variable that did not exist, so a node that must not be chosen (the current
  parent when moving or adding a location) was offered and even pre-selected;
- it always posted the node id, even when the browse returns object ids (assigning a role to the Users group posted
  the wrong object);
- with a class constraint it wrote a bare input into the table.

It now lives in the template `content/browse_current_node.tpl` (shipped in the `admin3` and `admin4` designs) with the
same checks as listed items: permission, ignored nodes and subtrees, class constraints, containers for move, copy and
add location, and swap compatibility. It posts the id the browse asks for, is shown disabled when the node may not be
selected, and is left out for search results and the top level. The thumbnail display of the admin3 design offers the
current node as the first thumbnail, pre-selected, too.

## Smaller fixes in the same sweep

- The toggler left of the node view tabs hides the tabs' content again.
- **Edit selected** and **Create multiple new** in the sub-items list go to the admin, not the public siteaccess.
- The admin URL list counts and lists the URLs of published content in milliseconds (an `EXISTS` test instead of a
  join with `count(DISTINCT)` that took over two minutes on 4,615 URLs on SQLite).
- Setting the sorting of a node requires permission to edit it, and only a known sort field and order are stored.
- Admin error pages show their own error in the page title.
- A subtree count with a language finds a start node that has no translation in that language, and no longer leaves
  that language set when the node does not exist (content "looked missing" for the rest of the request).

## Related pages

- [Installer logs and seed data](../../specifications/6.0/installer-logs-and-seed-data.md) for the new top-level folders
- [Hidden admin tabs](hidden-admin-tabs.md), [Store dashboard](store-dashboard.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
