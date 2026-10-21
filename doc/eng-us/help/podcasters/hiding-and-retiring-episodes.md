---
title: Hiding and retiring episodes
audience: podcaster
category: podcasting-streaming
order: 28
summary: What happens to the site and the podcast feed when an episode is hidden, removed to the trash or set to disappear at a date - the hide.php and unpublish.php cronjobs, the trash, the effect on the feed's items and guids - and how to retire episodes without breaking subscribers.
keywords: hide episode, remove episode, unpublish, expire, take down, retire, trash, restore, hide.php, unpublish.php, HideSettings, UnpublishSettings, feed, guid, rights expiry
type: help
---
# Hiding and retiring episodes

This page is for producers who need an episode off the site: a rights window ends, a guest withdraws consent, a
mistake cannot be fixed by editing, or old material is retired. Exponential offers hiding, removing to the trash, and
both at a date. Each has a different effect on the feed.

## Contents

1. [The options](#the-options)
2. [Effect on the feed](#effect-on-the-feed)
3. [Hide or remove now](#hide-or-remove-now)
4. [Hide or remove at a date](#hide-or-remove-at-a-date)
5. [Bringing an episode back](#bringing-an-episode-back)
6. [Good practice](#good-practice)

## The options

| Option | On the site | In the administration | Reversible |
|---|---|---|---|
| **Hide** | gone for visitors, with everything below it | still there, marked hidden | yes: unhide |
| **Remove** | gone | in the trash | yes: **Restore** from the trash, until it is emptied |
| Delete from the trash | gone | gone | no |
| **Hide at a date** (`hide.php`) | gone after the date | marked hidden | yes |
| **Remove at a date** (`unpublish.php`) | gone after the date | in the trash | yes, from the trash |

Details: [Publishing, versions and the trash](../publishing-and-versions.md#hide-an-item).

## Effect on the feed

The podcast export reads the items below its sources at the time it writes the feed (`podcastItemList()` in
`kernel/classes/ezrssexport.php`):

| Action | Feed |
|---|---|
| Hidden | the item is skipped, unless the site is set to show hidden items |
| In the trash or removed | the item is no longer below the source and is not listed |
| Restored or unhidden | the item returns with its **same guid** (the object's remote id), so apps recognize it |

The change reaches the feed when the feed cache runs out (`CacheTime`, 1200 seconds as shipped). Apps that already
downloaded the episode keep their copy; Exponential cannot remove files from listeners' devices.

Hiding is about what the site shows; it is not a permission. If the file must certainly not be fetched any more, for
example for legal reasons, remove the item, or move it to a section visitors may not read
([Members-only episodes](members-only-episodes.md)), and test the old download address signed out.

## Hide or remove now

1. Open the episode.
2. **Hide**: in the tree, open the item's menu and choose **Advanced** > **Hide / unhide**
   ([Hide an item](../publishing-and-versions.md#hide-an-item)).
3. **Remove**: press **Remove** on the item's page and confirm; the item goes to the trash
   ([Remove and restore](../publishing-and-versions.md#remove-and-restore)).

## Hide or remove at a date

Both cronjobs run in the default part of `settings/cronjob.ini` and do nothing until an administrator fills in their
settings in `settings/content.ini`:

| Cronjob | Settings | What it needs in the class |
|---|---|---|
| `hide.php` | `[HideSettings] RootNodeList[]` and `HideDateAttributeList[<class>]=<date field>` | a date field, for example `hide_date` |
| `unpublish.php` | `[UnpublishSettings] RootNodeList[]` and `ClassList[]` | a field `unpublish_date` |

For a show with licensed material that may only be online for a period, add a date field to the episode class and
let `hide.php` hide episodes when the period ends. See [Scheduled publishing](../customers/day-to-day/scheduled-publishing.md#hiding-or-removing-at-a-date).

## Bringing an episode back

- Hidden: unhide it (**Advanced** > **Hide / unhide**). It returns to lists and the feed.
- In the trash: **Restore** it to its old place or a new one ([The trash](../publishing-and-versions.md#the-trash)).
- Deleted from the trash: it cannot come back. Creating it again makes a new item with a new `guid`; apps list it as a new
  episode.

## Good practice

- Prefer **hiding** when the episode may return; leave removed episodes in the trash for a while before deleting
  them for good.
- Write down why and when an episode was retired, for example in an internal note field or your team's log.
- If an episode goes because of a rights or consent issue, check that its audio is not linked from other pages,
  embeds or the show notes of other episodes ([Rights and licensing](rights-and-licensing.md)).
- Retiring a whole show is different: see [Moving, pausing or ending a show](moving-pausing-or-ending-a-show.md).

## Related pages

- [Archiving and the trash](../customers/day-to-day/archiving-and-the-trash.md)
- [Scheduling a release](scheduling-a-release.md)
- [Publishing, versions and the trash](../publishing-and-versions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
