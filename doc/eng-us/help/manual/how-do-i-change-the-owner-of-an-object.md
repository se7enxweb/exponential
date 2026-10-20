---
title: How do I change the owner of an object?
audience: admin
category: manual
order: 345
summary: Choose Change owner in the object's context menu, pick the new owner and confirm; owner limitations of roles then apply to the new owner.
keywords: owner, change owner, ezownerchange, owner/change, author, handover, offboarding
type: help
---
# How do I change the owner of an object?

The owner is the user an object belongs to. Policies limited to "own" content (for example "edit your own articles")
follow the owner, so change it when someone hands their work over.

## Who can do this

Users whose role has `owner/change` (the extension ezownerchange, in a new installation).

## Steps

1. In **Content structure**, open the object's context menu (or the menu of a row in its parent's **Sub items**) and
   choose **Change owner**.
2. On the page **Choose the new owner of** *name*, find and select the user.
3. Confirm.
   The object's **Details** show the new owner.

## What can go wrong

- **The menu entry is missing.** Your role lacks `owner/change`.
- **The new owner still cannot edit it.** Their role does not allow editing in that section or class. Ownership does
  not add rights; it only matters for "Owner" limitations.
- **You need to change many objects at once.** The changing owners guide has the command for a whole subtree.

## Related pages

- [Changing owners](../../guides/changing-owners.md)
- [Offboarding staff](../customers/day-to-day/offboarding-staff.md)
- [User manual](README.md)
