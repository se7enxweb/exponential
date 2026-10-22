---
title: How do I remove an item?
audience: editor
category: manual
order: 435
summary: Click Remove on the item's page, check what the confirmation lists (sub items, other items that use it), keep Move to trash ticked and click OK; the item and everything below it go to the trash, from where they can be restored.
keywords: remove, delete, move to trash, trash, sub items, reverse relations, remove selected, confirm location removal, user accounts, disable instead
type: help
---
# How do I remove an item?

Removing takes an item, and everything below it, off the site and out of the tree. By default it goes to the trash
first, so a mistake can be undone.

## Who can do this

Editors whose role has `content/remove` for the item and for everything below it. Restoring from the trash needs
`content/restore`.

## Steps

1. Open the item and click **Remove**. Or choose **Remove** in its tree menu. For several items, tick them in the
   **Sub items** list and choose **More actions** > **Remove selected**.
   The page **Confirm location removal** opens.
2. Read the list: each item with its type and the number of its **Sub items**, which are removed as well.
3. Read the warnings. If other items use what you remove (they embed, link to or relate to it), they are listed.
4. Leave **Move to trash** ticked, unless you are sure the items must be deleted for good.
5. Click **OK**.
   Small removals happen at once; a large one runs in the background in batches and shows its progress.

To get an item back, open **Trash** at the bottom of the left column and click **Restore** in its row. See
[How do I empty the trash or remove single items from it?](how-do-i-empty-the-trash.md)

## What can go wrong

- **Attention: without Move to trash the removal cannot be undone.** The items, their versions and their files are
  deleted at once.
- **Red lines and a disabled OK button.** You may not remove some of the items in the list, or (on sites that block
  it) other items still use them. Cancel and remove only what you may, or fix the references first.
- **Other pages now show broken links or missing images.** The items that used the removed one still point at it.
  Edit them, or restore the item.
- **Only one location disappeared.** The item has other locations; it stays in those places. It goes to the trash only
  when its last location is removed.
- **User accounts are not trashed.** Objects with a user account field are deleted for good. Disable the account
  instead: see [How do I disable or unlock a user account?](how-do-i-disable-or-unlock-a-user-account.md)
- **You only want it off the site for a while.** Hide it instead: see [How do I hide an item?](how-do-i-hide-an-item.md)

## Related pages

- [Publishing, versions and the trash](../publishing-and-versions.md#remove-and-restore)
- [Removing and hiding: the check of reverse relations](../../features/6.0/reverse-relation-check.md)
- [Archiving and the trash](../customers/day-to-day/archiving-and-the-trash.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
