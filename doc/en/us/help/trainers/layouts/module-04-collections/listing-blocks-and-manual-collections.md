---
title: "Lesson 4.1: Listing blocks and manual collections"
audience: trainer, editor, website-creator
category: training
order: 41
summary: Learners add a List block, choose its view type, fill a manual collection with + Add items from the content browser, order and remove items, and override one item's view type. 45 minutes.
keywords: exponential layouts, list block, listing, view type, grid, grid featured, collection, manual collection, add items, content browser, set position, remove all manual items
type: help
---
# Lesson 4.1: Listing blocks and manual collections

Listing blocks show content from the site: a list of articles, a grid of products, a gallery. What they show is their
**collection**. This lesson starts with the simplest kind: a **manual** collection of hand-picked items.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 4: Collections](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Module 3](../module-03-blocks/README.md); some published articles on the training site |

## Learning objectives

After this lesson, learners can:

- name the listing blocks and the six view types of the **List** block;
- fill a manual collection with **+ Add items** through the content browser;
- change an item's position with **Set position** and remove items;
- override the view type of one item;
- choose between a manual and a dynamic collection.

## Trainer's notes

### How to present it

Sources: [Collections: giving a block its content](../../../../bc/6.0/LAYOUTS.md#collections-giving-a-block-its-content)
and [Block catalogue](../../../../bc/6.0/LAYOUTS.md#block-catalogue).

1. **Listing blocks** (3 minutes). In the **Listing** group: **List**, **Grid**, **Zig-Zag (List)**, **Accordion
   (List)**, **Sushi bar**, **Single content**; galleries in **Gallery**. **List** is the workhorse with six view
   types: `list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`, `grid_featured`. Change the view type
   before reaching for another block.
2. **Manual collections** (5 minutes). Each item chosen by hand through the content browser: right for specific
   content that will not change, such as three named products. In the block's collection, **+ Add items** opens the
   browser; picked items appear in order.
3. **Item actions** (4 minutes). Each item has actions such as **Set position** (move it; *New position must be larger
   than 0*), **Override item view type**, **Edit in CMS** (opens the content item) and **Remove**; **Remove all manual
   items** clears the list. An item hidden in the content tree is marked *Hidden in CMS*.
4. **Manual or dynamic** (3 minutes). Manual for curated sets; dynamic (lesson 4.2) for lists that should keep
   themselves current.

### Common questions

**Does a manual collection update when I edit the article?** Yes: it points at the item; changes to the item show.
It does not add new items by itself.

**Why is an item marked Hidden in CMS?** It is hidden in the content tree; visitors do not see it in the list.

**Where do I change the number of items shown?** **Limit** (lesson 4.3).

### Pitfalls

- **Picking from the wrong place.** The content browser starts at a location; navigate before picking.
- **Manual for news.** A manual news list is out of date next week. Use dynamic.
- **Removing all by accident.** **Remove all manual items** asks first; read it.

## Hands-on exercise

Use the training installation and your course layout. The trainer named three published articles to pick.

1. Open your course layout with **Open** (continue your draft if asked).
2. Press **Add a block**, open **Listing**, and drag a **List** block into *MAIN* below the Full view block.
3. Select it. In **Design**, set the view type to `grid`.
4. In its collection, choose a manual collection if the block asks, and press **+ Add items**.
5. In the content browser, find and pick the three articles; confirm.
6. Look at the three items in the block. Move the last one to position 1 with **Set position**.
7. Change the view type of one item with **Override item view type** if your installation offers a choice; then set it
   back.
8. Remove one item and add it again.
9. Set the block's view type to `list` and compare.
10. Give the block the **Name** `Hand-picked articles` and choose **Save draft and close**.

## Expected result

- The draft has a List block named *Hand-picked articles* with three items in the learner's order.
- The learner saw the block change between `grid` and `list`.
- The trainer checks by opening one learner's draft on the projector.

## Quiz

1. Name three view types of the **List** block.
2. What opens the content browser for a manual collection?
3. When is a manual collection the right choice?
   A. For the newest news B. For three named products C. For all children of a folder D. For search results
4. What does *Hidden in CMS* on an item mean?
5. True or false: a manual collection adds newly published articles by itself.

<details>
<summary>Answers</summary>

1. Any three of `list`, `grid`, `list_numbered`, `list_zigzag`, `list_accordion`, `grid_featured`
   ([Exponential Layouts](../../../../bc/6.0/LAYOUTS.md#block-catalogue)).
2. **+ Add items**.
3. **B.**
4. The item is hidden in the content tree; visitors do not see it.
5. **False.**

</details>

## Further reading

- [Exponential Layouts: Collections](../../../../bc/6.0/LAYOUTS.md#collections-giving-a-block-its-content)
- [Layout types, zones and blocks: Collections](../../../creators/website-creators/layout-types-zones-and-blocks.md#collections)

## Related pages

- [Module 4: Collections](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
