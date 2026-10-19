---
title: "Lesson 3.1: Moving and copying"
audience: trainer, editor
category: training
order: 31
summary: Learners move items and subtrees, copy an item or a whole subtree, and work on several items at once with Move selected and Copy selected. 45 minutes.
keywords: editors 201, move, copy, copy subtree, move selected, copy selected, browse, new parent, reorganize, restructure
type: help
---
# Lesson 3.1: Moving and copying

Sites change shape: a section is split, an old campaign is archived, a page template is reused. This lesson teaches
the tools to reorganize: **Move**, **Copy**, **Copy subtree**, and their versions for several items at once.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 3: Organizing content](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors who organize a part of the site |
| Prerequisites | [Module 2](../module-02-relations-locations-and-embedding/README.md) |

## Learning objectives

After this lesson, learners can:

- move an item, with everything below it, with **Move** and the browser;
- copy one item with **Copy**, and an item with everything below it with **Copy subtree**;
- move or copy several items at once with **More actions** > **Move selected** and **Copy selected**;
- explain what a move does to the address and the section of the moved items.

## Trainer's notes

### How to present it

Sources: [Move, copy and remove](../../../editing-content.md#move-copy-and-remove),
[Copy from the full view](../../../../guides/content-model-and-editing.md#copy-from-the-full-view) and
[Moving and locations](../../../../guides/sections.md#82-moving-and-locations).

1. **Move** (4 minutes). On the item's page, **Move** opens a browser to choose the new parent. The item moves with
   everything below it. The address follows the new place on most sites. Sub items whose section was inherited from
   the old parent take the new parent's section (lesson 3.3).
2. **Copy** (4 minutes). **Copy** next to **Move** (and in the item menu) copies the item into a place you choose;
   if your site asks, you also choose which versions to copy. The copy is a new item with its own numbers. **Copy
   subtree** in the item menu copies the item and everything below it. The button is grayed out when you may not
   create in the item's parent, and you need create access where the copy goes.
3. **Many at once** (3 minutes). In a **Sub items** list: tick, then **More actions** > **Move selected** or **Copy
   selected**.
4. **Large operations** (2 minutes). Moves and copies of big subtrees can run in the background as content jobs;
   lesson 3.4 shows them.

### Common questions

**Move or second location?** Move when the item belongs elsewhere now; a second location when it belongs in both
places (lesson 2.2).

**Does a copy keep the relations?** The copy has the same fields, so its relation fields and links point at the same
items. Other items that pointed at the original still point at the original.

**Why is Copy grayed out?** You may not create content in the item's parent (pointing at the button says why).

### Pitfalls

- **Moving into its own subtree.** The browser does not let you move an item below itself; learners who try are
  confused. Explain why.
- **Copy subtree of a big folder.** It can create hundreds of items. Keep it to small training folders.
- **Broken expectations about addresses.** After a move, old addresses may redirect or not, depending on the site.
  Check the site's behavior before promising.

## Hands-on exercise

Use the training installation and your course folder.

1. In your course folder, create a folder `Archive` and publish it.
2. Open *Opening hours* (module 1) and press **Move**. In the browser, choose `Archive` and confirm.
3. Open `Archive`: *Opening hours* is there; it is no longer in your course folder. Note its new address with **View
   on site**.
4. Open *Our team, two places* and press **Copy**. Choose your course folder as the place of the copy and confirm
   (choose the versions if asked).
5. Compare the original and the copy: write down both **Object** numbers.
6. In the tree, open the item menu of the folder `Projects` (lesson 2.2) and choose **Copy subtree**. Copy it into
   `Archive`.
7. In your course folder's **Sub items**, tick two articles and choose **More actions** > **Move selected**; move them
   into `Archive`.
8. Move them back the same way.

## Expected result

- *Opening hours* lives in `Archive`, with an address that reflects the new place (on most sites).
- The copy of *Our team, two places* has a different object number from the original.
- `Archive` holds a copy of `Projects` with its contents.
- The two articles moved into `Archive` and back.
- The trainer checks by opening one learner's `Archive` on the projector.

## Quiz

1. What happens to the items below an item you move?
2. What is the difference between **Copy** and **Copy subtree**?
3. True or false: a copy has the same object number as the original.
4. Where do you find **Move selected**?
   A. On the dashboard B. In **More actions** of a **Sub items** list C. In the Details tab D. In the user menu
5. Why might **Copy** be grayed out?

<details>
<summary>Answers</summary>

1. They move with it.
2. **Copy** copies one item; **Copy subtree** copies the item with everything below it
   ([Creating and editing content](../../../editing-content.md#move-copy-and-remove)).
3. **False.** A copy is a new item.
4. **B.**
5. You may not create content in the item's parent.

</details>

## Further reading

- [Creating and editing content](../../../editing-content.md#move-copy-and-remove): moving, copying and removing.
- [The content model and editing content](../../../../guides/content-model-and-editing.md#copy-from-the-full-view):
  the Copy button.
- [Sections](../../../../guides/sections.md#82-moving-and-locations): what a move does to sections.

## Related pages

- [Module 3: Organizing content](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
