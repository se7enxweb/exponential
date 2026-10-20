# The responsive admin design (admin3)

This page is for administrators who want editors to work from a phone, a tablet or a wide monitor. `admin3` is the
classic admin interface (the `admin` and `admin2` designs) with a sidebar that collapses, menus that work by touch,
and content forms that stack instead of overflowing. Every module, permission and template name is the same as in
`admin`. It first shipped in August 2024 (6.0.4) and was reworked in October and November 2024 (6.0.6) after testing
on phones and tablets.

## Turn it on

`admin3` extends `admin`, so it is listed as a design of an admin siteaccess. The shipped `admin` siteaccess already
lists it as a fallback behind [admin4](admin4-design.md):

```ini
# settings/siteaccess/admin/site.ini.append.php
[DesignSettings]
SiteDesign=admin4
AdditionalSiteDesignList[]
AdditionalSiteDesignList[]=admin3
AdditionalSiteDesignList[]=admin2
AdditionalSiteDesignList[]=admin
```

Designs listed first win. To run `admin3` itself, put it in `SiteDesign`:

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

The responsive script is loaded by `settings/design.ini`, `[JavaScriptSettings] BackendJavaScriptList[]=ezadmin_menubar.js`.
In the first release the script was called `main.js` and had to be enabled by hand; since November 2024 it is
`ezadmin_menubar.js` and on by default.

## What is different

- **Sidebars collapse.** The left menu (content tree and shortcuts) and the right menu fold away with a toggle; the
  choice survives page loads. On narrow screens they open as a layer over the page, with a deeper shadow.
- **Tables scroll instead of breaking the page.** `pagelayout.css` gives `.table-responsive` and any element that
  directly contains a table (`div:has(> table)`) `overflow-x: auto`, so a wide table scrolls inside its box.
  `ezadmin_menubar.js` also defines a helper `wrapTable()`, but its call in the page-ready handler is commented out;
  the CSS rule does the work. Check with
  `grep -n wrapTable design/admin3/javascript/ezadmin_menubar.js`.
- **Header and dashboard follow the screen.** The dashboard height is computed from the real header height and stored
  in a CSS variable (`--header-height`).
- **Phone browsers behave.** A fix in November 2024 stopped iOS browsers from hiding the sidebars when the page was
  scrolled. Menu layering (`z-index`) was corrected so every submenu item is clickable.
- **Edit forms fit small screens.** `content/edit.tpl`, `content/edit_draft.tpl`, `content/edit_menu.tpl`,
  `content/history.tpl` and `content/view/versionview.tpl` are overridden in `design/admin3/templates/`.
- **Pick the current node when browsing.** In a browse dialog (choosing a parent location, a relation target, a swap
  target) the override `content/browse_current_node.tpl` (June 2025), included by `content/browse_mode_list.tpl` and
  `content/browse_mode_thumbnail.tpl`, shows the node you are in as a selectable row. You no longer go up a level and
  back down to pick it.
- **The Design menu is back.** The `/design` menu item is available again under the new menu space (December 2024).

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/siteaccess/<admin>/site.ini.append.php` | `DesignSettings` | `SiteDesign` (the design used first) | `admin4` in the shipped admin siteaccess | siteaccess |
| `settings/siteaccess/<admin>/site.ini.append.php` | `DesignSettings` | `AdditionalSiteDesignList[]` (fallback designs, in order) | `admin3`, `admin2`, `admin` | siteaccess |
| `settings/design.ini` | `JavaScriptSettings` | `BackendJavaScriptList[]` (backend scripts) | includes `ezadmin_menubar.js` | global |

## Files

| Path | What it is |
|---|---|
| `design/admin3/stylesheets/pagelayout.css`, `responsive.css` | The layout and the small-screen rules |
| `design/admin3/javascript/ezadmin_menubar.js` | Sidebar toggles, table wrapper, header height, sidebar width controls |
| `design/admin3/templates/pagelayout.tpl`, `page_header.tpl`, `page_topmenu.tpl`, `page_leftmenu.tpl` | The frame of every admin page |

## Related pages

- [Left sidebar width and font size](admin3-sidebar-width-and-font-size.md), [the admin4 design](admin4-design.md)
- [Copy selected sub-items](subitems-copy-selected.md), [hide and unhide selected sub-items](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md), [sub-items list: columns, presets and CSV export](subitems-table-options.md)
- [Paging and page sizes](admin-list-paging.md)
- [The YUI removal](../../bc/6.0/yui-removal.md), which moved all admin designs to jQuery
- Changelogs: [6.0.4](../../changelogs/6.0/6.0.4.md), [6.0.6](../../changelogs/6.0/6.0.6.md), [6.0.10](../../changelogs/6.0/6.0.10.md)
- History: [August 2024](../../history/2024/2024-08.md), [October 2024](../../history/2024/2024-10.md), [November 2024](../../history/2024/2024-11.md), [January 2025](../../history/2025/2025-01.md), [June 2025](../../history/2025/2025-06.md), [June 2026, second half](../../history/2026/2026-06b.md)
