---
title: "Lesson 6.4: Going back, hiding and the trash"
audience: trainer, editor
category: training
order: 64
summary: Learners restore an earlier text with Make a new draft, hide and unhide an item, remove an item and get it back from the trash. 60 minutes.
keywords: editors 101, make a new draft, restore version, go back, hide, unhide, remove, trash, restore, empty trash, undo
type: help
---
# Lesson 6.4: Going back, hiding and the trash

Mistakes happen. Exponential has three safety nets for them: earlier versions can be brought back, items can be
hidden instead of removed, and removed items wait in the trash. This lesson practices all three, so learners know
there is almost always a way back.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 6: Publishing, versions and drafts](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Lesson 6.3](versions-and-comparing.md); a role that may remove and restore items in the course folders |

## Learning objectives

After this lesson, learners can:

- bring back an earlier text with **New draft from this version, in** and **Make a new draft**, and publish it;
- hide and unhide an item and explain what visitors see;
- remove an item and say what happens to the items below it;
- find a removed item in the **Trash** and **Restore** it;
- explain why **Remove selected** and **Empty trash** in the trash cannot be undone.

## Trainer's notes

### How to present it

Sources: [Go back to an earlier version](../../../publishing-and-versions.md#go-back-to-an-earlier-version),
[Hide an item](../../../publishing-and-versions.md#hide-an-item) and
[Remove and restore](../../../publishing-and-versions.md#remove-and-restore).

1. **Going back** (5 minutes). On the versions page, choose the translation under **New draft from this version,
   in** on the card of the version you want and press **Make a new draft**. A new draft is made from that version;
   the current version does not change until you publish. The box *Stay on this page after making a new draft*
   decides whether the page stays or opens the new draft in the editor at once. Check the draft and publish it as
   usual. The same works for someone else's draft: the new draft is yours.
2. **Hiding** (4 minutes). A hidden item stays in the administration but is not shown on the site, and nor is
   anything below it. In the tree: the item menu > **Advanced** > **Hide / unhide**. In a **Sub items** list: tick and
   **More actions** > **Hide selected** or **Unhide selected**. The badge next to the type shows the visibility.
3. **Removing** (4 minutes). **Remove** on an item's page asks for a confirmation and moves the item, with everything
   below it, to the trash. An item with more than one location goes to the trash only when its last location is
   removed.
4. **The trash** (5 minutes). **Trash** at the bottom of the tree. The page *Trash (n)* lists each item with its
   type, section, original placement and the date it was trashed; filters narrow the list by **Trashed by**, **Type**
   and the dates (**Trashed from** ... **to**) with **Filter**. **Restore** puts an item back: to its original place
   if that still exists, or to a place you choose. **Remove selected** and **Empty trash** delete for good and cannot
   be undone.
5. **Roles** (2 minutes). The role decides whether an editor may restore or delete from the trash. On many sites
   editors may restore but not empty the trash.

### Common questions

**Is hiding the same as unpublishing?** Close: visitors no longer see the item, but it stays published in the
administration and can be shown again in a second.

**I removed a folder. Are the articles in it gone?** They went to the trash with it. Restore the folder and they
come back with it.

**Why can I not restore an item?** If its original place is in the trash too, restore the parent first; otherwise
the page asks you to choose a new place. Or your role does not allow restoring.

**Do links to a restored item still work?** Restoring gives the item a new location. Links made by node number to
the old place may need to be re-made (lesson 4.2).

### Pitfalls

- **Emptying the trash on a shared training site.** Make sure learners' roles cannot use **Empty trash**, or every
  learner loses their safety net.
- **Making a new draft and forgetting to publish it.** The old text comes back only when the new draft is published.
  The draft otherwise waits in **My drafts**.
- **Hiding a folder by accident.** Everything below it disappears from the site. Teach learners to look for the
  visibility badge.

## Hands-on exercise

Use the training installation and your course folder.

1. Open *My first article* and press **Manage versions**.
2. On the card of the oldest version, keep the translation under **New draft from this version, in**, untick
   *Stay on this page after making a new draft*, and press **Make a new draft**.
3. The editor opens with the old text. Press **Send for publishing**. Check the title on the article's page: it is
   the original one again, with a new version number.
4. Open your course folder. In the **Sub items** list, tick *News one (updated)* and choose **More actions** > **Hide
   selected**.
5. Open *News one (updated)* and read its visibility badge. Try to open it on the site with **View on site**.
6. Unhide it with the item menu in the tree: **Advanced** > **Hide / unhide**.
7. Open *News two (updated)* and press **Remove**. Read the confirmation and confirm.
8. Open **Trash** at the bottom of the tree. Set **Trashed by** to yourself and press **Filter**.
9. Find *News two (updated)*, read its **Original Placement**, and **Restore** it.
10. Check your course folder: *News two (updated)* is back.

## Expected result

- *My first article* is published with its original title at a new version number; the versions page shows the
  version it came from as archived.
- While hidden, *News one (updated)* was marked hidden and was not shown on the site; after step 6 it is visible
  again.
- *News two (updated)* went to the trash and came back to the learner's folder.
- The trainer checks by opening one learner's folder and versions page on the projector.

![The trash: how many items it holds, and the filters by who removed them, type and date](../../../images/trainers/e101-trash.webp)

## Quiz

1. How do you bring back the text of an older version?
2. What do visitors see of a hidden folder and the articles below it?
   A. Everything B. Only the folder C. Only the articles D. Nothing
3. You remove a folder with five articles. Where are the articles now?
4. Which two trash actions cannot be undone?
5. The original place of an item in the trash was removed too. How do you restore the item?

<details>
<summary>Answers</summary>

1. On the versions page, **Make a new draft** from that version, then publish the draft
   ([Publishing, versions and the trash](../../../publishing-and-versions.md#go-back-to-an-earlier-version)).
2. **D.** A hidden item and everything below it is not shown on the site.
3. In the trash, together with the folder.
4. **Remove selected** and **Empty trash**.
5. Restore the parent first, or choose a new place when the page asks for one.

</details>

## Further reading

- [Publishing, versions and the trash](../../../publishing-and-versions.md): every safety net.
- [The trash view](../../../../bc/6.0/trash.md): the trash page's columns, filters and restoring.
- [Versions of an object](../../../../guides/content-history.md): making a draft from an older version.

## Related pages

- [Module 6: Publishing, versions and drafts](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
