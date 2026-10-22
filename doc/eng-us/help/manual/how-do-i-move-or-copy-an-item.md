---
title: How do I move or copy an item?
audience: editor
category: manual
order: 415
summary: Move takes an item and everything below it to a new parent; Copy makes an independent duplicate of one item, and Copy subtree duplicates the item with everything below it; all three start on the item's page or in its tree menu.
keywords: move, copy, copy subtree, duplicate, new parent, choose a new location, versions to copy, node ID, object ID, section
context: content/copy
type: help
---
# How do I move or copy an item?

**Move** changes where an item lives; its address follows, and everything below it moves along. **Copy** makes a new,
separate item with the same content. Use a copy as the starting point for something similar; use an extra location
when the same item must appear in two places (see
[How do I show an item in more than one place?](how-do-i-show-an-item-in-more-than-one-place.md)).

## Who can do this

- Moving needs `content/edit` on the item and permission to create its class under the new parent.
- Copying needs `content/read` on the item and `content/create` under the new parent. **Copy subtree** needs
  `content/create` for every class in the subtree.

A button you may not use is grayed out; point at it to read why.

## Steps

1. Open the item.
2. Click **Move** or **Copy** next to **Edit**. For a whole branch, open the item's menu in the tree and choose
   **Copy subtree**.
   A browser opens with the places you may choose.
3. Open the parent you want by clicking its name, then select it with its radio button.
4. Click **Select**.
5. If the site asks which versions to copy, choose **Copy current version** or **Copy all versions** and click
   **Copy**. For **Copy subtree**, choose whether the copies keep their creators and dates, or get you and the current
   time, then click **Copy**.
   The item opens in its new place.
6. To move or copy several items, tick them in the **Sub items** list and choose **More actions** > **Move selected**
   or **Copy selected**.

## What can go wrong

- **The parent you want cannot be selected.** It is not a container, or your role may not create that class there.
- **The moved item now has other readers.** An item takes the section of its new parent when it moves. Content moved
  into a section that visitors may not read disappears from the site; content moved out of a protected section may
  become public. Check the **Details** tab after the move.
- **A large copy or move is offered in the background.** For a big subtree the page lets you run it now or as a
  content job that works in batches and shows its progress. Let it finish before you edit the items.
- **Copy subtree refuses.** A site can limit how many items one subtree copy may take. Copy smaller branches, or ask
  an administrator.
- **The copy is not the same item.** It has its own object ID and node ID, its own versions, and changes to one do not
  reach the other. The **Details** tab shows both numbers.

## Related pages

- [Creating and editing content](../editing-content.md#move-copy-and-remove)
- [How do I show an item in more than one place?](how-do-i-show-an-item-in-more-than-one-place.md)
- [How do I swap two items in the tree?](how-do-i-swap-two-items.md)
- [Objects, nodes and locations](../cms-developers/objects-nodes-and-locations.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
