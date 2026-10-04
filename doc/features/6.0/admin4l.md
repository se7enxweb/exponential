# The admin4l design: the admin page built from layouts

This page is for administrators who want to arrange the administration interface, and for developers who add admin
blocks. `admin4l` is admin4 whose page is assembled by Exponential Layouts: the header, the left and right sidebars, the
main area and the footer are zones of an **admin layout**, and what each zone shows are **blocks**. With the stock
layouts the page looks exactly like admin4: same markup, same classes, same stylesheets.

## Switch to it

1. In the admin siteaccess's `site.ini.append.php`:

   ```ini
   [DesignSettings]
   SiteDesign=admin4l
   AdditionalSiteDesignList[]
   AdditionalSiteDesignList[]=admin4
   AdditionalSiteDesignList[]=admin3
   AdditionalSiteDesignList[]=admin2
   AdditionalSiteDesignList[]=admin
   ```

   `admin4l` holds only what differs from admin4 (the page layout, the zone template and the block templates); every
   other template and all stylesheets come from `admin4`.

2. Clear the caches: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, then
   `--clear-id=template,template-override,template-block,content`.

## The safety switch

One setting returns every admin page to plain admin4, whatever the layouts and rules say. In
`settings/siteaccess/<admin siteaccess>/explayouts.ini.append.php`:

```ini
[AdminLayoutSettings]
Enabled=disabled
```

`Enabled=enabled` turns the layouts on. Two more cases fall back to admin4's own template without any setting:

- the page when no admin layout resolves, or the resolved layout has no blocks;
- a **zone** that renders nothing (it is drawn from admin4's own template instead). The main zone always shows the
  module result, even when no module result block is placed in it.

A block that fails halfway leaves what it had printed; only a zone that comes out empty falls back.

## Layouts, zones and blocks

| Layout type | Zones | Side columns |
|---|---|---|
| `admin_3col` | header, topmenu, left, right, main_top, main, main_bottom, footer | left and right |
| `admin_2col` | header, topmenu, left, main_top, main, main_bottom, footer | left |
| `admin_full` | header, topmenu, main_top, main, main_bottom, footer | none |

The layouts are edited in the **Layouts** tab, like site layouts. Admin layouts, rules and blocks are a group of their
own: the editor lists them only as admin layouts (`?group=admin`, or `all`), the block list of a site layout never
offers an admin block, and a site layout never reaches the admin.

Blocks (`[AdminBlockSettings]` in `explayouts.ini`, each `Group=admin`; the view template of a block is
`design:explayouts/block/<identifier>.tpl`):

| Part of the page | Blocks |
|---|---|
| Header | `admin_logo`, `admin_search`, `admin_theme_switch`, `admin_sidebar_toggles`, `admin_tab_menu` |
| Left sidebar | `admin_left_menu` (the menu of the current navigation part; optional parameter `part` forces one), `admin_content_tree` |
| Right sidebar | `admin_clear_cache`, `admin_bookmarks`, `admin_current_user`, `admin_preferences`, `admin_quick_settings` |
| Page | `admin_breadcrumb`, `admin_module_result`, `admin_footer`, `admin_popup_menu`, `admin_overlay`, `admin_debug_area` |

Each block includes the admin4 part it is named after, with admin4's own conditions, so the markup stays the same. The
wrappers (`#navbar`, `#leftmenu`, `#footer-design` ...) are drawn by the page, not by the blocks. The popup menu, the
debug marker and the overlay are drawn where admin4 draws them (outside the footer), from the footer zone's blocks.

`admin_left_menu` of the my, content, media and user parts already contains the content structure. Put
`admin_content_tree` in a zone only when you want the tree on a page whose own menu has none; in the stock layouts it
is not placed.

## Which layout a page gets

Rules choose by **module and view** (targets `module` and `module_view`; patterns `content`, `content/*`,
`content/view`, `*`). The first enabled rule by priority whose layout is an admin layout wins; then
`[AdminLayoutSettings] DefaultLayout` (default `admin_3col`). Rules only apply on the siteaccesses of
`SiteAccessMatch[]` (`admin`, `admin_*`, `admintest_*`). Stock rules:

| Priority | Targets | Layout |
|---|---|---|
| 200 | every view whose module definition says `ui_context=edit` (content/edit, ...) | `admin_full` |
| 100 | content/view, content/dashboard | `admin_3col` |
| 90 | modules setup, user, shop | `admin_3col` |
| 10 | every module | `admin_3col` |

## Caching

The resolved layout is remembered for `CacheTTL` seconds per module, view and siteaccess. The page's cache blocks are
keyed by the layout id and an **admin generation** that changes whenever an admin layout is published or deleted or a
rule changes, so an edit shows at once and nothing needs clearing. Header and footer are also keyed by the user's
roles (the popup menu shows entries by policy).

## Checking it

```bash
php vendor/bin/phpunit --testsuite explayouts_admin
```

covers the layout types, patterns, resolution per module, the safety switch, separation from the site, the cache key and
generation, and the block definitions and templates (live settings and database, nothing written but the resolver's
own cache generation).

See [the behaviour change](../../bc/6.0/admin4l.md) for what is different from admin4, and
[admin4](admin4-design.md) for the design underneath.
