---
title: Find a node ID
audience: editor, website-creator, admin, designer, developer
category: build-two-ways
order: 400
summary: Menus, layouts and templates name pages by their node ID (location ID). Where the administration shows it, how to tell it from the object ID, and how to look a page up by ID, path or URL.
keywords: node ID, location ID, object ID, content ID, find node, content finder, MainMenuID, FooterMenuID, value_id, URL alias, content/view/full
type: help
---
# Find a node ID

Every page in the content tree has two numbers:

| Number | Means | Used by |
|---|---|---|
| **Node ID** (location ID) | the page's place in the tree | menus (`MainMenuID[]`, `FooterMenuID[]`), layout mappings, collection items of Layouts blocks, `fetch('content','node', ...)`, `/content/view/full/<node id>` |
| **Object ID** (content ID) | the content itself, in all its places | relations, `fetch('content','object', ...)`, `/content/edit/<object id>` |

A page with one place has one of each, and they differ: **Showcase** is node 192, object 190.

## In the administration

Open the page in **Content structure** (or **Media**). The line under the buttons reads
`Modified ... · Published ... · Version ... · <section> · Node 192 · Object 190 · Remote ID`.

![The view of Showcase in the administration; the line under the buttons shows Node 192 and Object 190](images/hm-02-find-node-id.webp)

Also:

- the address of the page in the administration ends with `/content/view/full/<node id>`;
- the **Locations** tab lists every place of a page with its node ID (a page with two places has two node IDs; menus
  use the one you choose);
- the **Content finder** box under the tree (placeholder "Id, path or URL", button **Find**) looks a page up by any of the three.

## On the site

The site's menu items carry the ID in their markup: `id="menu-item-main_menu-location-id-<id>"` and
`data-location-id="<id>"` (on this theme the first items show the reference site's IDs there, see
[How the header is built](how-the-header-is-built.md)).

## Related pages

- [Add a header menu item](add-a-header-menu-item.md)
- [Footer menu items](footer-menu-items.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
