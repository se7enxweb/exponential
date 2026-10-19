---
title: Why doesn't my change appear?
audience: editor, website-creator, designer, admin, developer
category: layouts
order: 310
summary: A check list for a change that does not show on the site - draft not published, linked zone, wrong layout mapping, wrong siteaccess or settings file, compiled template, page cache, Velocity - with the command or screen that proves each one.
keywords: change not showing, not updated, draft, publish layout, linked zone, layout mapping, rule, siteaccess, cache, exp:ini where, exp:layouts list, troubleshooting
type: help
---
# Why doesn't my change appear?

Work from the top: the first causes are the most common.

| # | Check | How to see it | Fix |
|---|---|---|---|
| 1 | The layout change is still a **draft** | The editor shows **Discard** and **Publish layout**; `exp:layouts list --drafts` lists drafts | **Publish layout** |
| 2 | You edited a zone that is **linked** | The page layout's zone links to a shared layout (`exp:layouts list`, column *Links to*) | Edit the shared layout, or unlink the zone ([Shared layouts and zone linking](shared-layouts-and-zone-linking.md)) |
| 3 | The page uses **another layout** | Layouts > Layout mappings: which mapping matches the page | Change the mapping, or edit the layout that matches |
| 4 | The change is not in a layout at all | The header and footer menus are `menu.ini` lists, rendered by template blocks | [Add a header menu item](add-a-header-menu-item.md), [Footer menu items](footer-menu-items.md) |
| 5 | A setting went to a **file that does not win** | `./console exp:ini where <file>/<Block>/<Variable> <siteaccess>`; in the administration *Set in N files* | Write the scope marked in effect |
| 6 | You changed **another siteaccess** | The siteaccess choice on Setup > Ini settings; the page's address | Change the siteaccess of the page |
| 7 | The **compiled template** holds an old value | Values read with `ezini()` are compiled into the template | Clear cache **Template & content** |
| 8 | The **page cache** answers | Response header `X-Exp-Cache: HIT` | Same clear; or `./console exp:cache httpcache clear` |
| 9 | **Velocity** workers kept old settings | The site runs on Exponential Velocity; `exp:ini` printed its hint | `./console exp:velocity restart` |
| 10 | The **browser** shows an old copy | Another browser or a private window shows the change | Reload without the browser cache |

## The proof for each layer

- **Database (layouts)**: `exp:layouts list` shows the published layouts and their links; `exp:layouts export
  --only=<identifier> --output=<file>.zip` writes one layout with its zones, blocks, parameters and collection items
  (`layouts.json` in the archive).
- **Settings**: `exp:ini get <file>/<Block>/<Variable> -s <siteaccess>` shows the value in effect.
- **Templates**: the site's HTML; menu items, blocks and zones carry IDs and classes such as
  `menu-item-main_menu-location-id-<id>`, `ngl-block ngl-<block type>` and `zone zone-<identifier>`.
- **Caches**: `./console exp:cache list` lists them; `X-Exp-Cache` tells whether a page came from the page cache.

## Related pages

- [Caches after a change](caches-after-a-change.md)
- [Header menu troubleshooting](header-menu-troubleshooting.md)
- [Shared layouts and zone linking](shared-layouts-and-zone-linking.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
