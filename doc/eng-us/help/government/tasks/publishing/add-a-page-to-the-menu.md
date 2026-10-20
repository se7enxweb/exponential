---
title: Add a page to the menu
audience: government, editor
category: public-sector
order: 160
summary: For editors - make a page appear in the site's menu in Exponential, where menus come from the content tree - choose the right parent and a menu class, set its place in the order, and keep pages out of the menu when needed.
keywords: menu, navigation, top menu, left menu, content tree, menu classes, MenuContentSettings, priority, order, hide from menu
type: help
---
# Add a page to the menu

This page is for editors who want a page in the menu. On an Exponential site, menus are not a separate list: they are
made from the content tree. A page of a menu class placed directly below a folder that is in the menu becomes an
entry ([Menus and navigation](../../../creators/website-creators/menus-and-navigation.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Know which classes your site's menus show. Designs based on ezwebin read `menu.ini [MenuContentSettings]`; the kernel
  ships `folder`, `feedback_form`, `gallery` and `forum`, and ezwebin adds more (`documentation_page`, `event_calendar`,
  `link`, `blog` ...). Your site may differ; ask the web team.
- Know where in the tree the entry belongs: below the root for the top menu, below a top folder for its submenu.

## Steps

1. Open the parent: the root folder (for the top menu) or a top-level folder (for its submenu).
2. **Create new**, choose a menu class, usually **Folder**; give it the name to show in the menu (**Short name** if the
   name is long), and its content or children.
3. **Send for publishing**.
4. Set its place in the order: open the parent, tab **Ordering**: if **Sort by** is *Priority*, enter numbers in the
   **Priority** column of the **Sub items** list and press **Update priorities**, or drag rows by their grip
   ([Reorder pages in a folder](reorder-pages-in-a-folder.md)).

To put an existing page into a menu, either move it below a menu folder ([Move a page](move-a-page.md)) or give it an
extra location there ([Publish one item in several places](publish-one-item-in-several-places.md)). A link to another
site in the menu is a **Link** object, where the menu lists links.

## Check the result

- [ ] Signed out, the menu shows the new entry in the right place.
- [ ] The breadcrumb on the page shows the path.
- [ ] If it does not appear: the class may not be in the menu lists, the page may be hidden, or the menu is cached
  (it renews with its cache; [Refresh a page that shows old content](refresh-a-page-that-shows-old-content.md)).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| An Article placed at the top level expecting a menu entry | articles are usually not menu classes; use a folder |
| A very long menu | group pages below folders; aim for a short top menu |
| Menu names that differ from page titles | use **Short name** consistently |
| Keeping a page out of the menu | hide it, or use a class that is not in the menu lists |

## Related pages

- [Publishing tasks](README.md)
- [Structuring the content tree](../../../creators/website-creators/structuring-the-content-tree.md)
- [Classic menu settings](../../../../guides/classic-menu-settings.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
