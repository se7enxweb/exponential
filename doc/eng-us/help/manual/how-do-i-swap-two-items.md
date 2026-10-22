---
title: How do I swap two items in the tree?
audience: editor
category: manual
order: 430
summary: Advanced > Swap with another node exchanges the content of two locations while their places, addresses and sub items stay put; use it to put a prepared replacement at the head of a branch without moving everything below it.
keywords: swap, swap with another node, exchange, replace a container, change class, sitemap for subtree, two-level index, node ID, object ID
type: help
---
# How do I swap two items in the tree?

A swap exchanges what two locations show. Each location keeps its place in the tree, its sub items and its parent;
only the objects behind them change places. It is the quick way to put a new object at the head of an existing branch,
for example a reworked start page of a section that you prepared out of sight
and now want to put in front of everything below it.

## Who can do this

Editors who may edit both items, and who may create each item's class under the other item's parent. Without these
rights the swap is refused.

## Steps

1. Prepare the replacement: create it somewhere else, for example in a hidden folder, and publish it.
2. Check the branch you are about to change: open the tree menu of the item and choose **Advanced** >
   **Sitemap for subtree**. A two-level index of everything below it opens.
3. Open the tree menu of the item to replace and choose **Advanced** > **Swap with another node**.
   A browser opens: *Choose the node to exchange for* the item.
4. Find the replacement, select it with its radio button and click **Select**.
   The two objects change places. The sub items stay where they were and now hang under the replacement.
5. Open the old object in its new place (where the replacement was) and remove or archive it.

## What can go wrong

- **The swap is refused because of sub items.** An object that cannot hold sub items (its class is not a container)
  cannot take a place that has sub items. Choose a container class for the replacement.
- **The section changes.** When a main location changes owner, the object takes the section of its new parent. Check
  the **Details** tab of both items afterwards.
- **The old object is now visible.** It took the replacement's place; if that place was visible, so is the old object.
  Remove or hide it right away.
- **Node and object numbers.** The locations keep their node IDs; the object IDs move. Links made to a node now lead
  to the other object. The **Details** tab shows both numbers.

## Related pages

- [How do I move or copy an item?](how-do-i-move-or-copy-an-item.md)
- [How do I change the content class of an object?](how-do-i-change-the-content-class-of-an-object.md), the
  alternative when the content can be converted field by field
- [How do I hide an item?](how-do-i-hide-an-item.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
