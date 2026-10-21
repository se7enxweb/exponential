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
| `admin_full` | header, topmenu, main, footer | none |

The zones `main_top` and `main_bottom` are drawn only on a page that has the `#maincolumn` frame: not on a page admin4
draws wrapped in `#a4-main` (`main_wrapped`) and not when a view sets `hide_left_menu`. `admin_full` therefore does not
declare them. A block placed there in a layout stored before (installations seeded before this change have a
breadcrumb in `admin_full`'s `main_top`, see the [behaviour change](../../bc/6.0/admin4l.md)) stays stored but is never shown.

The layouts are edited in the **Layouts** tab, like site layouts. Admin layouts, rules and blocks are a group of their
own: the editor lists them only as admin layouts (`?group=admin`, or `all`), the block list of a site layout never
offers an admin block, and a site layout never reaches the admin.

Blocks (`[AdminBlockSettings]` in `explayouts.ini`, each `Group=admin`; the view template of a block is
`design:explayouts/block/<identifier>.tpl`):

| Part of the page | Blocks |
|---|---|
| Header | `admin_logo`, `admin_menu_button`, `admin_search`, `admin_theme_switch` (not only the theme button: it draws the whole group of tools admin4 puts after the search, the site link and site menu, the colour mode button, the context help and the user menu; the name stayed so stored layouts keep working), `admin_sidebar_controls` (the page draws the logo and the menu button before the menu panel and the rest of the header zone after it, so the Tab order is admin4's; `admin_sidebar_toggles`, the menu button and the sidebar controls in one block, still works in layouts stored before the split, and is drawn after the tools); `admin_tab_menu` for the **topmenu** zone: it draws only the list items, the page draws the one menu panel around them |
| Left sidebar | `admin_left_menu` (the menu of the current navigation part; optional parameter `part` forces one), `admin_content_tree` |
| Right sidebar | `admin_toolbar_right`, `admin_toolbar_developer` (the toolbars `admin_right` and `admin_developer` of `toolbar.ini`, as admin4 draws them, in admin4's order), or the single tools `admin_clear_cache`, `admin_bookmarks`, `admin_current_user`, `admin_preferences`, `admin_quick_settings` |
| Page | `admin_breadcrumb`, `admin_module_result`, `admin_footer`, `admin_popup_menu`, `admin_overlay`, `admin_debug_area` |
| Toolbar | `admin_ui_switch` (admin4's toolbar tool for the interface choices, `uiswitch`) |

Each block includes the admin4 part it is named after, with admin4's own conditions, so the markup stays the same. The
wrappers (`#navbar`, `#leftmenu`, `#footer-design` ...) are drawn by the page, not by the blocks. The popup menu, the
debug marker and the overlay are drawn where admin4 draws them (outside the footer), from the footer zone's blocks, and
**only** from there: placed in another zone they are not drawn (the page draws each of them once). Only blocks whose
definition identifier starts with `admin_` are drawn; the identifier names the template, so any other definition in an
admin layout is skipped and noted in the debug log.

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
roles (the popup menu shows entries by policy). The GET parameter `SectionID` enters the keys as a number, so odd
values do not create cache files.

## Differences from admin4

- The Tab order of the header is admin4's (logo, menu button, menu, search, tools, sidebar controls): the page draws the
  logo and the menu button of the header zone before the menu panel and the rest of the header zone after it. A layout
  stored with the old `admin_sidebar_toggles` block (menu button and controls in one) draws it after the tools.
- On a phone, admin4 adds the user's actions (`parts/a4_phone_user.tpl`) to the open phone menu and hides the header's
  user menu there. admin4l's tab menu block does not include it: the header's user menu stays, so the phone menu has the
  search, the tabs, "More" and the close button only.
- A block of `main_top`/`main_bottom` is not shown where the page has no `#maincolumn` (see the zone table above).
- `admin_left_menu` with the `part` parameter, or any navigation part whose `parts/<name>/menu.tpl` does not exist, draws
  that include as it stands: the template language cannot test whether a template exists, so there is no fallback. Name
  only parts that exist on the siteaccess.

## Checking it

```bash
php vendor/bin/phpunit --testsuite explayouts_admin
```

covers the layout types, patterns, resolution per module, the safety switch, separation from the site, the cache key and
generation, and the block definitions and templates (live settings and database, nothing written but the resolver's
own cache generation).

See [the behaviour change](../../bc/6.0/admin4l.md) for what is different from admin4, and
[admin4](admin4-design.md) for the design underneath.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../help/partners/partners-start-here.md) and [How to apply](../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
