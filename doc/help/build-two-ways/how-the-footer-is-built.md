---
title: How the footer is built
audience: designer, developer, website-creator, admin
category: layouts
order: 220
summary: From the footer zone of the shared layout Header / Footer to pagelayout/footer.tpl and its three data sources - FooterMenuID, the site info object and the Footer links folder - with the real file paths.
keywords: footer, shared layout, Header / Footer, zone footer, template block, block 14, pagelayout/footer.tpl, FooterMenuID, NexusFooterMenuID, FooterLinksRemoteID, site info, ng_menu_item
type: help
---
# How the footer is built

The footer follows the same road as the header ([How the header is built](how-the-header-is-built.md)). Paths are
relative to `extension/sevenx_themes_media/design/media/templates/`.

## Contents

1. [From layout to template](#from-layout-to-template)
2. [Inside footer.tpl](#inside-footertpl)
3. [What to change where](#what-to-change-where)

## From layout to template

| Step | What |
|---|---|
| 1 | The page's layout links its zone `footer` to zone `footer` of the shared layout **Header / Footer** |
| 2 | That zone holds one **Template block** with the template name `footer` (block 14 on this site) |
| 3 | `explayouts/layout.tpl` places the `footer` zone after `<main>`, `explayouts/zone.tpl` renders its blocks |
| 4 | `explayouts/block/tpl_block.tpl` asks `tpl_block_template()`, which answers `pagelayout/footer.tpl` (by block ID, else because it is a root block in zone `footer`) |

A page without a layout includes `pagelayout/footer.tpl` from `pagelayout.tpl` directly.

## Inside footer.tpl

`pagelayout/footer.tpl` first fetches the **site info object**, the object whose remote ID is
`menu.ini [SiteInfo] RemoteID` (`media-o-site-info` on this site). Without that object it renders nothing. Then:

| Part | Data | Template code |
|---|---|---|
| Logo | the design's logo | `{include uri='design:content/parts/site_logo.tpl'}` |
| First link line | `ezini('SiteInfo','FooterMenuID','menu.ini')`, one `fetch('content','node', ...)` per ID | `<li id="menu-item-additional_menu-location-id-...">` |
| Social icons | fields `facebook`, `twitter`, `instagram`, `linkedin` of the site info object, shown when they have content | `$ft_site_info.data_map.facebook.has_content` |
| Second link line | `fetch('content','list', ...)` of the children of the object with remote ID `[SiteInfo] FooterLinksRemoteID` (default `media-o-footer-links`), class `ng_menu_item`, in the folder's sort order | `<div class="footer-menu footer-links">` |
| Cookie settings | `parts/cookie_settings_link.tpl` | |
| Info texts | translatable strings, context `design/media/pagelayout` | `'...'|i18n('design/media/pagelayout')` |

`NexusFooterMenuID[]` plays the same part as `NexusMainMenuID[]` in the header: it gives the `id` and
`data-location-id` attributes of the first items the IDs of the reference site. A link whose URL alias starts with
`fit-healthy/` is written with `ezroot` instead of `ezurl`.

## What to change where

| Change | Way | Page |
|---|---|---|
| Add a page to the first line | settings (`FooterMenuID[]`) | [Footer menu items](footer-menu-items.md) |
| Add a link to the second line | content (Menu item in Footer links) | [Footer links from the content tree](footer-links-from-the-content-tree.md) |
| Social profile addresses | content (edit the site info object) | |
| Blocks above the footer on article and recipe pages | Layouts editor (shared layout Prefooter) | [Shared layouts and zone linking](shared-layouts-and-zone-linking.md) |
| Footer markup | template override in your own design extension | [Two ways to build](two-ways-to-build.md) |

## Related pages

- [How the header is built](how-the-header-is-built.md)
- [Footer menu items](footer-menu-items.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
