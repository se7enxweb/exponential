# The responsive admin design (admin3)

`admin3` is an administration design that works on a phone, a tablet and a wide
monitor. It is the classic admin interface (the `admin` and `admin2` designs)
with a sidebar that collapses, menus that stay usable by touch, and content
forms that stack instead of overflowing. It first shipped in August 2024
(version 6.0.4) and was reworked in October and November 2024 (6.0.6) after
testing on phones and tablets.

## Why it matters

A traditional admin design assumes a desktop browser. Editors who have to fix a
typo from a phone, or who work on a small laptop, could not reach the menus or
read the tables. `admin3` fixes that without changing how anything is called:
every module, permission and template name is the same as in `admin`.

## What is different

- **Collapsible sidebars.** The left menu (the content tree and shortcuts) and
  the right menu can be folded away with a toggle; the choice survives page
  loads. On narrow screens they open as a layer over the page, with a deeper
  shadow so they read as a layer.
- **Tables scroll instead of breaking the page.** `pagelayout.css` gives
  `.table-responsive` and any element that directly contains a table
  (`div:has(> table)`) `overflow-x: auto`, so a wide table scrolls inside its
  box. `ezadmin_menubar.js` also defines a helper `wrapTable()` that wraps tables
  in a `table-responsive` div, but at HEAD its call in the page-ready handler is
  commented out; the CSS rule does the work. Check:
  `grep -n wrapTable design/admin3/javascript/ezadmin_menubar.js`.
- **Header and dashboard follow the screen.** The dashboard height is computed
  from the real header height and stored in a CSS variable (`--header-height`).
- **Phone browsers behave.** A fix in November 2024 stopped iOS browsers from
  hiding the sidebars when the page was scrolled; menu layering (`z-index`) was
  corrected so every submenu item is clickable.
- **Edit forms are rebuilt for small screens.** `content/edit.tpl`,
  `content/edit_draft.tpl`, `content/edit_menu.tpl`, `content/history.tpl` and
  `content/view/versionview.tpl` are overridden in `design/admin3/templates/`.
- **Quick select of the current node when browsing.** In a browse dialog
  (choosing a parent location, a relation target, a swap target) the override
  `content/browse_current_node.tpl` (June 2025), included by
  `content/browse_mode_list.tpl` and `content/browse_mode_thumbnail.tpl`, shows the node you are standing in
  as a selectable row, so you no longer have to go up a level and back down to
  pick it.
- **The Design menu returns.** The `/design` menu item is available again under
  the new menu space (December 2024).

## Turn it on

`admin3` is a design that extends `admin`, so it is listed as an additional
design of an admin siteaccess. In the shipped `admin` siteaccess:

```ini
# settings/siteaccess/admin/site.ini.append.php
[DesignSettings]
SiteDesign=admin4
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=admin3
AdditionalSiteDesignList[]=admin2
AdditionalSiteDesignList[]=admin
```

Designs listed first win, so to run `admin3` itself put it in `SiteDesign`:

```ini
[DesignSettings]
SiteDesign=admin3
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=admin2
AdditionalSiteDesignList[]=admin
```

Then clear the caches:

```bash
php bin/php/ezcache.php --clear-all --allow-root-user
```

The responsive scripts are loaded by `settings/design.ini`
`[JavaScriptSettings] BackendJavaScriptList[]=ezadmin_menubar.js`. (In the first
release the script was called `main.js` and had to be enabled by hand; since
November 2024 it is `ezadmin_menubar.js` and on by default.)

## Settings and files

| File | Block | Key | Default | Scope | Meaning |
|---|---|---|---|---|---|
| `settings/siteaccess/<admin>/site.ini.append.php` | `[DesignSettings]` | `SiteDesign` | `admin4` in the shipped admin siteaccess | siteaccess | The design used first. |
| same | `[DesignSettings]` | `AdditionalSiteDesignList[]` | `admin3`, `admin2`, `admin` | siteaccess | Fallback designs, in order. |
| `settings/design.ini` | `[JavaScriptSettings]` | `BackendJavaScriptList[]` | includes `ezadmin_menubar.js` | global | Scripts of the backend. |

| Path | What it is |
|---|---|
| `design/admin3/stylesheets/pagelayout.css`, `responsive.css` | The layout and the small-screen rules. |
| `design/admin3/javascript/ezadmin_menubar.js` | Sidebar toggles, table wrapper, header height, sidebar width controls. |
| `design/admin3/templates/pagelayout.tpl`, `page_header.tpl`, `page_topmenu.tpl`, `page_leftmenu.tpl` | The frame of every admin page. |

## Related features

- [Left sidebar width and font size](admin3-sidebar-width-and-font-size.md)
- [Copy selected subitems](subitems-copy-selected.md)
- [Hide and unhide selected subitems](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [Paging and page sizes](admin-list-paging.md)
- [The yui removal](../../bc/6.0/yui-removal.md), which moved all admin designs to jQuery
- Chronicles: [August 2024](../../history/2024/2024-08.md),
  [October 2024](../../history/2024/2024-10.md),
  [November 2024](../../history/2024/2024-11.md)

## See also

Changelogs: [6.0.4](../../changelogs/6.0/6.0.4.md), [6.0.6](../../changelogs/6.0/6.0.6.md), [6.0.10](../../changelogs/6.0/6.0.10.md). The June 2026 additions are in [the June 2026 chronicle](../../history/2026/2026-06b.md).

## Related pages

- [The sub-items list: 129 columns, presets and CSV export](subitems-table-options.md)
- [The admin4 design](admin4-design.md)
- [June 2025](../../history/2025/2025-06.md)
- [January 2025](../../history/2025/2025-01.md)
