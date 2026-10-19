---
title: "Lesson 3.1: Adding, moving and removing blocks"
audience: trainer, editor, website-creator
category: training
order: 31
summary: Learners add blocks from the Add a block panel, select a block, drag it within and between zones, duplicate it, revert it and delete it. 45 minutes.
keywords: exponential layouts, add a block, block types, basic, components, listing, gallery, containers, placeholders, drag, move block, duplicate block, delete block, revert block
type: help
---
# Lesson 3.1: Adding, moving and removing blocks

Blocks are the building pieces of a layout. This lesson teaches the hand movements of the editor: add a block, select
it, drag it, duplicate it and delete it. The next lessons cover what goes into each block.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 3: Blocks](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Editors and site builders |
| Prerequisites | [Module 2](../module-02-layouts-and-layout-types/README.md): each learner has a course layout |

## Learning objectives

After this lesson, learners can:

- open **Add a block** and find a block type in its group;
- drag a block type into a zone at the right place;
- select a block and see its properties in the **Content** and **Design** tabs;
- move a block within a zone and to another zone;
- duplicate, revert and delete a block from its menu.

## Trainer's notes

### How to present it

Sources: [The layouts editor](../../../layouts-editor.md#add-a-block) and
[A tour of the administration: the layouts editor](../../../admin-tour.md#in-action-the-layouts-editor).

1. **Add a block** (4 minutes). The plus button at the top left, **Add a block**, opens **Block types** in groups:
   **Basic** (Title, Text, Button / Link, Rich text, Image, Markdown ...), **Components**, **Listing**, **Gallery**,
   **Containers** and **Placeholders**. Drag a type into a zone, to the place where it should go.
2. **Selecting** (2 minutes). Click a block: it is outlined in blue, and its properties appear on the right in two
   tabs, **Content** (what it shows) and **Design** (how it looks). Changes show in the block at once.
3. **Moving** (3 minutes). Drag a block by its header bar to another place, in the same zone or another one.
4. **The block menu** (4 minutes). The menu with three dots on a block has **Duplicate block** (the copy is inserted
   after the original, with its settings: *Block successfully duplicated.*) and **Delete block** (*Are you sure you
   want to delete the block? This cannot be undone.*). Some blocks also offer **Revert block**, which returns a block
   to its published version.
5. **Drafts** (2 minutes). All of this happens in the draft; the site is unchanged until the layout is published.

### Common questions

**Can I undo a deleted block?** Not inside the draft. If the layout was published before, **Discard** throws away the
whole draft, or **Revert block** returns a changed block to its published version where offered.

**Why can I not drop a block into a zone?** The zone may be linked to a shared layout (red dashed border, hatched
blocks): its blocks come from elsewhere (module 5).

**Is there a limit on blocks per zone?** Not a documented one; keep pages readable.

### Pitfalls

- **Dropping in the wrong place.** Watch the drop marker before releasing the mouse.
- **Deleting instead of duplicating.** The two entries are next to each other; the confirmation helps.
- **Working in a shared layout by mistake.** Make sure the name at the top is your own layout.

## Hands-on exercise

Use the training installation and your course layout `Training - <your first name>`.

1. Open **Layouts** > **Layouts** and press **Open** on your course layout.
2. Press **Add a block**. Read the groups of **Block types**.
3. Drag a **Title** block into the zone *MAIN*.
4. Drag a **Text** block into *MAIN*, below the title.
5. Click the **Title** block and read its **Content** and **Design** tabs. Type a title in the content field.
6. Drag the **Text** block above the **Title** block, then back below it.
7. Drag the **Title** block into the zone *POST_HEADER*.
8. Open the menu with three dots on the **Text** block and choose **Duplicate block**. Read the message.
9. Delete the duplicate with **Delete block**; read the question and confirm.
10. Open the menu with three dots at the top and choose **Save draft and close**.

## Expected result

- The draft of the learner's layout has a **Title** block in *POST_HEADER* and a **Text** block in *MAIN*.
- The learner saw *Block successfully duplicated.* and the delete question.
- The site is unchanged (nothing was published).
- The trainer checks by opening one learner's layout on the projector.

## Quiz

1. Name four groups of **Add a block**.
2. What do the **Content** and **Design** tabs of a selected block hold?
3. How do you move a block to another zone?
4. True or false: a deleted block can be brought back with undo inside the draft.
5. What does **Revert block** do?

<details>
<summary>Answers</summary>

1. Any four of **Basic**, **Components**, **Listing**, **Gallery**, **Containers**, **Placeholders**
   ([The layouts editor](../../../layouts-editor.md#add-a-block)).
2. **Content**: what the block shows; **Design**: how it looks.
3. Drag it by its header bar into the other zone.
4. **False.** The delete question says it cannot be undone.
5. It returns the block to its published version.

</details>

## Further reading

- [The layouts editor](../../../layouts-editor.md): adding and changing blocks.
- [A tour of the administration](../../../admin-tour.md#in-action-the-layouts-editor): the same steps with pictures.

## Related pages

- [Module 3: Blocks](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
