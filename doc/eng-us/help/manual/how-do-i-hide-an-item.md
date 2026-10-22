---
title: How do I hide an item without removing it?
audience: editor
category: manual
order: 425
summary: Choose Advanced > Hide / unhide in the item's tree menu, or Hide on its Locations tab; the location and everything below it leave the public site but stay in the administration, and Reveal brings them back.
keywords: hide, unhide, reveal, visibility, hidden, hidden by superior, invisible, Locations tab, visibility after publishing, ShowHiddenNodes
context: content/hide
type: help
---
# How do I hide an item without removing it?

Hiding takes a location off the public site for everyone, without touching its content, its versions or its rights.
Everything below it is hidden too. The administration still shows hidden items, marked as such, so you can keep
working on them and reveal them later. Use it for content that is not ready yet, or not wanted for a while.

## Who can do this

Editors whose role has `content/hide` for the item.

## Steps

1. Open the item's menu in the tree and choose **Advanced** > **Hide / unhide**. Or open the item, click the
   **Locations** tab and click **Hide** in the row of the location.
2. If a confirmation page appears, read it and click **OK**.
   It appears when other items use this one, or when the item has more locations; those other locations stay
   visible.
3. To show the item again, use the same menu entry, or click **Reveal** on the **Locations** tab.
4. For several items, tick them in the **Sub items** list and choose **More actions** > **Hide selected** or
   **Unhide selected**.

The **Visibility** column of the **Locations** tab, and the badge on the item's page, show one of three states:

| State | Meaning |
|---|---|
| **Visible** | shown on the site (if the reader may read it) |
| **Hidden** | hidden on its own |
| **Hidden by superior** | not hidden itself, but a location above it is |

## Hide a new item from the start

1. In **User preferences** in the right column, switch **Locations** on.
2. Create the item. The edit form now has a **Locations** box.
3. Set **Visibility after publishing** to **Hidden** and publish.

This works only for the first version: once the item exists, use the steps above.

## What can go wrong

- **An item stays hidden after Reveal.** A location above it is hidden (**Hidden by superior**). Reveal that one first.
- **Revealing a parent does not show a child.** A child that was hidden on its own stays hidden when its parent is
  revealed. Reveal it separately.
- **The item is still on the site.** Hiding acts on one location. If the item has other locations, they stay visible.
  Hide each one, or check the **Locations** tab.
- **Links to the item stop working on the site.** Other items that link to or embed it now point at something
  visitors cannot see. The confirmation page lists them.
- **Hiding is not a permission.** It hides the location for everyone on the public siteaccesses. To show content only
  to some readers, use sections and roles instead.

## Related pages

- [Publishing, versions and the trash](../publishing-and-versions.md#hide-an-item)
- [Removing and hiding: the check of reverse relations](../../features/6.0/reverse-relation-check.md)
- [How do I show an item in more than one place?](how-do-i-show-an-item-in-more-than-one-place.md)
- [Sections](../../guides/sections.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
