---
title: Archiving and the trash
audience: customer, editor, admin
category: customer-success
order: 24
summary: The owner's choices for content that is no longer current on an Exponential site - keep, hide, move to an archive area, remove to the trash or delete for good - what each does for visitors and addresses, who may restore, and a trash routine.
keywords: archive, trash, hide, remove, restore, empty trash, archived content, section, object states, retention, site owner
type: help
---
# Archiving and the trash

This page is for the owner and editor-in-chief who decide what happens to content that is no longer current. Exponential
offers several steps between "on the site" and "gone for good"; choosing the right one keeps history, addresses and
search results in order. The clicks are on [Publishing, versions and the trash](../../publishing-and-versions.md).

## Contents

1. [Five choices](#five-choices)
2. [Hiding](#hiding)
3. [An archive area](#an-archive-area)
4. [The trash](#the-trash)
5. [Deleting for good](#deleting-for-good)
6. [A trash routine](#a-trash-routine)

## Five choices

| Choice | Visitors see it? | Address works? | Can be undone? |
|---|---|---|---|
| Keep, marked as older (a note in the text) | yes | yes | yes |
| Hide | no, nor anything below it | no | yes, unhide |
| Move to an archive area | depends on the area's section | the old address redirects to the new place | yes, move back |
| Remove to the trash | no | no | yes, restore |
| Delete from the trash | no | no | **no** |

Sources: [Publishing, versions and the trash](../../publishing-and-versions.md),
[Redirects and moved content](../../creators/website-creators/redirects-and-moved-content.md).

## Hiding

A hidden item stays in the administration but is not shown on the site, and nor is anything below it. Use it for
content that is not ready, or not needed for a while ([Publishing, versions and the trash](../../publishing-and-versions.md#hide-an-item)).
Your administrator can also have items hidden automatically at a date stored in the item
([Scheduled publishing](scheduled-publishing.md)).

## An archive area

For content worth keeping in the open (old press releases, past events), an **archive folder** is often best:

- Moving content there keeps its old address working: the old path redirects.
- If the archive folder is in a **section** that anonymous visitors may not read, it becomes an internal archive;
  if visitors may read it, it is a public archive ([Sections](../../../guides/sections.md)).
- Object states can mark content as archived without moving it, for roles and templates to use
  ([Object states](../../../guides/object-states.md)).

Ask your site builder which of these fits your site; moving content between sections can change who may see it.

## The trash

**Remove** moves an item, with everything below it, to the trash. When an item has more than one location, removing
one keeps it in its other places; it goes to the trash only when its last location is removed
([Publishing, versions and the trash](../../publishing-and-versions.md#remove-and-restore)).

The trash lists each item with who removed it, when and where it was, with filters by user, type and date
([The trash view](../../../bc/6.0/trash.md)). **Restore** puts an item back: to its original place if that still
exists; if the parent is in the trash too, restore the parent first; otherwise you choose a new place.

> **Note**
> Your role decides whether you may restore or delete from the trash. Users never go to the trash: removing a user
> account is final ([Check user accounts](../../../features/6.0/user-hygiene.md)).

## Deleting for good

**Remove selected** in the trash and **Empty trash** delete items for good; neither can be undone. **Empty trash**
empties the whole trash, whatever filter is shown ([The trash view](../../../bc/6.0/trash.md)). Before you empty it:

- check that nothing still links to the items (broken relations, [Content audits](content-audits.md));
- check whether your organization must keep certain records for a period (ask whoever is responsible; this is not
  legal advice, see [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md));
- remember that the backups still hold the content for as long as they are kept.

## A trash routine

| When | What | Who |
|---|---|---|
| Weekly | look at what was trashed this week (filter by date); restore mistakes | editor-in-chief |
| Monthly | delete items trashed more than a period ago that nobody missed | editor-in-chief, with the agreed period |
| Yearly | agree the period with the owner and your retention rules | owner |

Write your period here: `Items stay in the trash for at least ____ before they are deleted.`

## Related pages

- [Publishing, versions and the trash](../../publishing-and-versions.md)
- [Content audits](content-audits.md)
- [The content finder](../../../features/6.0/content-finder.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
