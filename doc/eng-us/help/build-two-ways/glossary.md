---
title: Two ways to build, the words
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 40
summary: The terms of layouts and templates as these pages use them - layout, layout type, zone, block, block definition, view type, item view type, collection, shared layout, linked zone, template block, design, design extension, override, siteaccess, scope, node ID.
keywords: glossary, terms, layout, zone, block, block definition, view type, item view type, collection, manual, dynamic, shared layout, linked zone, template block, design extension, override, siteaccess, scope
type: help
---
# Two ways to build, the words

| Term | Means | Where you meet it |
|---|---|---|
| **Layout** | A page structure: a layout type plus the blocks placed in its zones | Layouts > Layouts; table `explayouts_layout` |
| **Layout type** | The set of zones a layout has (for example `layout_2`: header, post_header, main, pre_footer, footer) | The first choice when creating a layout; `[LayoutType_<id>]` in `explayouts.ini` |
| **Zone** | A named area of a layout that holds blocks | The dashed areas of the editor (HEADER, MAIN ...) |
| **Block** | One piece of a page: a title, a list, an image, a column | The editor's blocks |
| **Block definition** (block type) | What a block is: name, handler class, view types, category, whether it has a collection | `[BlockDefinition_<id>]` in `explayouts.ini` |
| **View type** | One look of a block type, chosen per block | List: list, grid, list_numbered, list_zigzag, list_accordion, grid_featured |
| **Item view type** | How a block shows each of its items (line, card, overlay ...) | Lists and grids; templates `content/views/<view>/` |
| **Container block** | A block with placeholders that hold other blocks | Column (placeholder `main`), Two columns (`left`, `right`) |
| **Collection** | The items of a block | A List block's items |
| **Manual collection** | Items picked by hand; each is a node ID | Prefooter's three articles |
| **Dynamic collection** | Items from a query on the content tree | A list of the newest articles of a section |
| **Shared layout** | A layout whose zones other layouts link to | Header / Footer, Prefooter |
| **Linked zone** | A zone that shows the blocks of a shared layout's zone | `header` of every Fit & Healthy layout |
| **Template block** | A block that renders a template named in it | The header, breadcrumb and footer of Header / Footer |
| **Layout mapping** (rule) | Which layout a page gets: by node, subtree, path or content class | Layouts > Layout mappings |
| **Draft / published** | A layout is changed in a draft; **Publish layout** makes it live | The editor's Discard and Publish layout buttons |
| **Design** | A named set of templates and stylesheets | `media` for Fit & Healthy |
| **Design extension** | An extension that carries a design under `design/<design>/templates/` | `sevenx_themes_media` |
| **Override** | A template in a design that replaces a template of the same path from a design further down the list | your extension's `pagelayout/header.tpl` |
| **Siteaccess** | One configuration of the installation: a site, a language version, the administration | `site`, `bold`, `admin` |
| **INI scope** | Where a setting is written: global, a siteaccess, an extension | `exp:ini scopes` |
| **Node ID** | A page's place in the content tree | `Node 192` in the administration |

## Related pages

- [Two ways to build](two-ways-to-build.md)
- [Two ways to build, every page](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
