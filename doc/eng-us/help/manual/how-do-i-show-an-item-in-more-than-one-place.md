---
title: How do I show an item in more than one place?
audience: editor
category: manual
order: 420
summary: Give the item an extra location on its Locations tab with Add locations; one location is the main one, extra locations can be removed again, and every location shows the same content, so an edit appears everywhere at once.
keywords: locations, add locations, main location, set main, secondary location, remove location, node, multiple locations, cross-publishing
type: help
---
# How do I show an item in more than one place?

An item can have several **locations** in the tree. Each location is a node with its own address and its own sub
items, but all of them show the same content. Edit the item once and every location shows the change. One location is
the **main location**: it decides the item's section and its main address.

## Who can do this

Editors whose role has `content/manage_locations` for the item and may create its class under the new parent.

## Steps

1. Open the item and click the **Locations** tab.
   The tab lists every location with its path, its number of sub items, its visibility and which one is **Main**.
2. Click **Add locations**.
3. Open the parent you want, select it and click **Select**.
   The new location appears in the list.
4. To change the main location, choose it with the radio button in the **Main** column and click **Set main**.
5. To take away an extra location, tick it and click **Remove selected**, then confirm.
6. To add a location to several items at once, tick them in the **Sub items** list and choose **More actions** >
   **Add a location**.

You can also choose locations before the first publish: switch **Locations** on in **User preferences** in the right
column, and the edit form of a new item gets a **Locations** box with **Add locations**.

## What can go wrong

- **Add locations is grayed out.** The item belongs to a top node (such as the content or media root), or your role
  lacks `content/manage_locations`.
- **The main location cannot be removed.** Make another location main first, then remove the old one.
- **Sub items do not follow.** A new location shows only the item itself. The sub items under the main location are
  not added below the new one.
- **The tab never removes the item itself.** The main location always stays. To take the item away completely, use
  **Remove**: see [How do I remove an item?](how-do-i-remove-an-item.md)
- **Hiding is per location.** Hiding one location leaves the others visible. See
  [How do I hide an item?](how-do-i-hide-an-item.md)

## Related pages

- [How do I move or copy an item?](how-do-i-move-or-copy-an-item.md), when you need a separate item rather than a
  second place for the same one
- [Objects, nodes and locations](../cms-developers/objects-nodes-and-locations.md)
- [Creating and editing content](../editing-content.md#many-items-at-once)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
