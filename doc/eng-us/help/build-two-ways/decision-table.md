---
title: "Which way: the decision table"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 20
summary: For each kind of change on a site - layout, block look, menu, chrome, content view, new block type - which way to build it (Layouts editor, template override, settings, content) and why, with the Fit & Healthy example for each row.
keywords: decision table, when to use layouts, when to use templates, template override, Layouts editor, settings, content, block types, menus, header, footer
type: help
---
# Which way: the decision table

Each row is a kind of change. **Way** is what to use; **Also** is the second way when the first is not enough.

## Page composition

| Change | Way | Also | Example on Fit & Healthy |
|---|---|---|---|
| Arrange blocks on a landing page | Layouts editor | | Landing Page / Home (28 blocks) |
| Add a block above the footer on article pages | Layouts editor, shared layout Prefooter | | Prefooter: "Most popular articles" |
| Show a block on every page | Layouts editor, shared layout Header / Footer, zone `post_header` or `pre_footer` | | Zone `header`: breadcrumb template block |
| Give one page type its own layout | Layouts > Layout mappings | | Full / Article for articles |
| Hand-pick the items of a list | Layouts editor, List block, manual collection | | Prefooter list: three articles |
| A list that updates itself | Layouts editor, List block, dynamic collection | Template with `fetch('content','list', ...)` | |

## Look and markup

| Change | Way | Also | Example |
|---|---|---|---|
| How every block of one type looks | Template override `explayouts/block/<type>.tpl` | Block options (Design tab) per block | `explayouts/block/list.tpl` |
| A new look for one block type, chosen per block | New **view type** in `explayouts.ini` plus its template | | List: list, grid, list_numbered, list_zigzag, list_accordion, grid_featured |
| How a content class shows in a list | Template `content/views/<view>/<class>.tpl` | The block's item view type | `content/views/line/...` |
| The HTML page around everything | Template `pagelayout.tpl` | | `extension/sevenx_themes_media/design/media/templates/pagelayout.tpl` |
| Header or footer markup | Template `pagelayout/header.tpl`, `pagelayout/footer.tpl` | Placed by template blocks | |
| Colors, fonts | Stylesheets of the design | Block option background color | `background_color:color = primary` on the header column |

## Menus and navigation

| Change | Way | Also |
|---|---|---|
| Add, remove, reorder header menu items | Settings: `menu.ini [SiteInfo] MainMenuID[]` | [Add a header menu item](add-a-header-menu-item.md) |
| Footer first line | Settings: `FooterMenuID[]` | [Footer menu items](footer-menu-items.md) |
| Footer second line | Content: Menu items in Footer links | [Footer links from the content tree](footer-links-from-the-content-tree.md) |
| Menu item text | Content: rename the page | |
| A menu that follows the tree by itself | Template override of the menu template | |

## New capabilities

| Change | Way |
|---|---|
| A new kind of block | `[BlockDefinition_<id>]` in `explayouts.ini` (name, handler, view types, category), a handler class, a template `explayouts/block/<id>.tpl` |
| A new layout type (other zones) | `[LayoutType_<id>]` in `explayouts.ini` plus its template |
| A template the editor's Template block can name | `explayouts/tpl_block/<name>.tpl` in a design |

## Rules of thumb

1. **Editors change it often?** Layouts or content. Templates need a deploy.
2. **Must look the same everywhere?** Template. A layout can be changed by anyone with the editor.
3. **A list of pages someone picks by hand?** A manual collection in a block, or a settings list like the menus.
4. **Not sure?** Build the block's template once (way 1), let editors place it (way 2).

## Related pages

- [Two ways to build](two-ways-to-build.md)
- [Shared layouts and zone linking](shared-layouts-and-zone-linking.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
