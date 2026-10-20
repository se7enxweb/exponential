---
title: Remove an outdated page and restore it
audience: government, editor
category: public-sector
order: 190
summary: For editors - remove an outdated page with Remove (it goes to the trash), give its visitors somewhere to go, and restore it from the Trash if it was removed by mistake; and why emptying the trash is final.
keywords: remove page, trash, restore, Empty trash, outdated content, redirect, reverse relations, removed by mistake, content/restore
type: help
---
# Remove an outdated page and restore it

This page is for editors who retire pages that are out of date. **Remove** moves an item to the trash, from where it
can be restored until the trash is emptied ([Content model and editing, 7](../../../../guides/content-model-and-editing.md#7-the-trash)).
Removing is different from hiding: the address stops working.

## Contents

1. [Before you start](#before-you-start)
2. [Steps to remove](#steps-to-remove)
3. [Steps to restore](#steps-to-restore)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- Confirm the page is not needed for the record (ask your records officer for formal documents); if it is, hide it
  instead ([Hide a page temporarily](hide-a-page-temporarily.md)).
- Decide where its visitors should go instead (a newer page, the parent).

## Steps to remove

1. Open the page, press **Remove** (or tick several in a **Sub items** list, **More actions** > **Remove selected**).
2. Read the confirmation: it lists other content that points to the page (embeds, links, relation fields). Fix those
   pages first, or accept that they will point to nothing.
3. Confirm. The item and everything below it go to the trash.
4. Give the old address somewhere to go: an alias on the replacement page or a URL wildcard
   ([Redirect old addresses after a reorganization](redirect-old-addresses-after-a-reorganization.md)). A removed page's
   address otherwise answers "not found".

## Steps to restore

1. Open **Trash** (in the left column of the content structure, or `/content/trash`).
2. Find the item: filter by **Trashed by**, **Type** or date. Each row shows who trashed it and where it was.
3. Tick it and press **Restore** (or open its restore page) and confirm the place it goes back to.
4. The item and what was below it come back.

## Check the result

- [ ] After removal: the old address redirects to the replacement (signed out).
- [ ] After restoring: the page opens at its place; menus show it.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| **Empty trash** | deletes everything in the trash for good, whatever the filter; leave it to administrators |
| User accounts | user objects never go to the trash; removing a user is final |
| Removing a folder | everything below goes with it; check first |
| Records | removing a formal record may break your records rules |
| Big removals | may run as a background job; follow it at `/content/jobs` |

## Related pages

- [Publishing tasks](README.md)
- [Publishing, versions and the trash](../../../publishing-and-versions.md#remove-and-restore)
- [Trash: who and where](../../../../features/6.0/trash-who-and-where.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
