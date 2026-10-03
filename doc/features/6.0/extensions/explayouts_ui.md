# explayouts_ui: the Layouts admin screens

`explayouts_ui` adds the **Layouts** tab to the admin. It is the management side of
Exponential Layouts: lists of layouts, shared layouts and components, the **layout
mappings** that decide which layout a page gets, imports, a template editor and a
layout preview. Each list links into the visual editor served by
[explayouts_ui_api](explayouts_ui_api.md). The extension has no domain classes of
its own, only the thin view classes under `classes/runnable/views/explayouts_ui/`; its module views are controllers over the `explayouts_core` services, so the
screens and the editor always show the same data.

Imported 30 July 2026 (1.0.0); 1.3.8 followed on 2 October 2026.

Since 1.3.7 (1 October) the pages run on the admin's own jQuery 4 from `ezjscore`, and their files carry Exponential names: `design/admin/javascript/netgen/layouts-exponential.js`,
`design/admin/stylesheets/netgen/layouts-exponential.css` and `design/admin/stylesheets/explayouts-ui.css` (formerly `layouts-ibexa.*` and `nglayouts-ui.css`); `layouts-admin.js` is no longer
loaded. 1.3.7 also ships the Roboto fonts (no more 404s) and fixes the rule list's action condition; 1.3.8 stops the stylesheet's page-wide rules from resetting node views.
Check the files with `ls extension/explayouts_ui/design/admin/stylesheets extension/explayouts_ui/design/admin/stylesheets/netgen`.

## Where things are

Open **Layouts** in the top admin menu (`/explayouts_ui/dashboard`). The left menu
holds:

| Link | View | What you do there |
|---|---|---|
| Layouts | `layout_list` | Browse layouts as cards, sort, create (**New layout**), open a layout in the editor |
| Shared layouts | `shared_layouts_list` | The layouts other layouts inherit zones from (site header, footer). A shared layout nothing links to yet is listed too (1.2.1), with how many zones link to it |
| Layout mappings | `rule_list` | Decide which layout renders for which page; see below |
| Components | `components` | The component blocks and where they are used, with edit links; names link to the content item's URL alias |
| Import | `transfer_import` | Import layouts and rules from transfer files |
| Template editor | `template_editor` | Edit layout templates (roots restricted, see Settings) |
| Setup | `setup` | Setup and status of the Layouts suite |

Other views: `layout_create`, `layout_edit`, `block_edit`, `rule_edit` and
`layout_preview` (`/explayouts_ui/layout_preview/<layout id>/<status>`).

The top-menu tab is named **Layouts** (renamed from "Exponential Layouts UI" on 28 August 2026, before
1.1.0) and requires the `explayouts/read` policy.

## Layout mappings

The screen formerly called "rules" is **Layout mappings** from 1.1.0. A mapping links a
**layout** to one or more **targets** and **conditions** with a priority:

* **Targets**: a node, a subtree, and other target types. The old `content_node`
  target type is shown as `node`.
* **Conditions**: class, siteaccess, query parameter, route parameter, time and others.
  The legacy `ibexa_*` condition names are no longer offered; an existing mapping that
  uses one keeps working and is shown under its canonical name, and the class condition
  is called **class** (the Exponential term). Class and siteaccess are dropdowns, not
  free text.
* Each mapping row has quick actions: enable, disable, unlink layout, copy, delete, a
  details panel with the mapped layout, priority, enabled switch and target and
  condition tables, and a per-mapping **cache clear**.
* **New rule** opens an inline panel; a new mapping starts with a priority that actually
  resolves.
* The list can be preloaded from a **Map layout** or **Edit mapping** link with the
  query parameters `TargetType`, `TargetValue` and `RuleID`, for example
  `/explayouts_ui/rule_list?TargetType=node&TargetValue=2`.
* **View in CMS** for a node target opens the node's system URL.

Mapping changes affect which layout renders; after a bulk change the resolver cache
under `var/site/cache/explayouts/resolver/` may need clearing (the per-mapping cache
clear does this for one mapping).

## Preview

`layout_preview` renders a layout against a representative content node, resolved from
the mapping's own target, so content-driven blocks show real content instead of
empty. The preview sets the `content_info` view mode to `layout_preview` so the
public page layout can omit header and footer when the layout itself contains header and
footer zones. A dedicated full-view template breaks the circular reference the module
result would otherwise produce. **Load more** works in the preview (1.3.1).

## Lists are paged

Layout, shared layout, component and mapping lists are paged (1.2.0), 25 per page by
default. Set the page size in `admininterface.ini`:

```ini
[PaginationSettings]
ItemsPerPage[explayouts_ui/layout_list]=25
ItemsPerPage[explayouts_ui/shared_layouts_list]=25
ItemsPerPage[explayouts_ui/components]=25
ItemsPerPage[explayouts_ui/rule_list]=25
```

The extension ships these defaults itself. See
[Admin list paging](../admin-list-paging.md) and
[Pagination settings](../../../bc/6.0/pagination-settings.md).

## Settings

| File | Block and key | Default | Meaning |
|---|---|---|---|
| `admininterface.ini` | `[PaginationSettings] ItemsPerPage[explayouts_ui/<list>]` | `25` | Page size of the four lists |
| `explayouts.ini` (extension `explayouts`) | `[TemplateEditorSettings] AllowedTemplateRoots[]` | `design`, `extension` | Folders the template editor may edit |
| `menu.ini` | `[TopAdminMenu] Tabs[]=explayouts_ui_dashboard`, `[Topmenu_explayouts_ui_dashboard]`, `[Leftmenu_explayouts_ui_dashboard]` | shipped | Tab (policy `explayouts/read`) and left menu links |
| `module.ini` | module `explayouts_ui`, functions `read`, `edit` | shipped | Policies |

Policies (from `module.php`): `layout_list`, `shared_layouts_list`, `rule_list`, `components`, `dashboard` and `layout_preview` use `read`;
`layout_create`, `layout_edit`, `rule_edit`, `block_edit`, `setup` and `transfer_import` use `edit`; `template_editor` lists both.
The sidebar is drawn by `design/admin/templates/parts/explayouts_ui/menu.tpl` and has five links: Layout mappings, Layouts, Shared layouts, Components and Import. **Template
editor** and **Setup** are not in that sidebar; reach them by their URL (`/explayouts_ui/template_editor`, `/explayouts_ui/setup`) or through the `[Leftmenu_explayouts_ui_dashboard]`
block of the shipped `menu.ini`, which lists Layout mappings, Layouts, Shared layouts (pointing at `layout_list`), Import, Template editor and Setup. Open `/explayouts_ui/dashboard` to check which one your admin design uses.

## Mobile and narrow screens

The layout list header wraps below 900 px, takes a line of its own for the sort controls,
and fills the width below 480 px, so **New layout** is never pushed off a phone screen
(1.2.3). The layout grid uses the smaller of 260 px and the available width.

## Languages

Every visible text is a translation string (English and German ship). The tab, its tooltip,
the navigation part name and the left menu names are translated in the contexts
`design/admin/pagelayout`, `kernel/navigationpart` and `design/admin/parts/<menu>/menu`.

## MongoDB

The component usage and shared layout listings read on MongoDB too (1.2.2): the join and
the aggregate the driver cannot translate were replaced by reads matched in PHP, so
screens that used to show nothing now show the 49 components and the real reference
counts.

## Related

* [explayouts_ui_api: the editor](explayouts_ui_api.md)
* [Chronicle](../../../history/extensions/explayouts_ui.md) and [release notes](../../../changelogs/extensions/explayouts_ui.md)
* [Layouts bc note](../../../bc/6.0/LAYOUTS.md)
