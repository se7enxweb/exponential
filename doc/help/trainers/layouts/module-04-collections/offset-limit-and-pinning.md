---
title: "Lesson 4.3: Offset, limit and pinning"
audience: trainer, editor, website-creator
category: training
order: 43
summary: Learners cut a collection with offset and limit to build a lead-plus-list from one query, and pin a hand-picked item at a fixed position of a dynamic list. 45 minutes.
keywords: exponential layouts, offset, limit, pinning, pinned item, lead plus list, featured, dynamic collection, manual item, position
type: help
---
# Lesson 4.3: Offset, limit and pinning

Two small settings and one trick turn dynamic lists into editorial tools. **Offset** and **limit** cut one query
into a lead story and a list below it. **Pinning** puts a hand-picked item at a fixed place in a list that otherwise
looks after itself.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 4: Collections](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Lesson 4.2](dynamic-collections-and-queries.md) |

## Learning objectives

After this lesson, learners can:

- set **offset** and **limit** on a collection;
- build a lead-plus-list from two blocks with the same query;
- pin a manual item at a position of a dynamic collection;
- note the reason for a pin in the block's name.

## Trainer's notes

### How to present it

Sources: [Collections](../../../../bc/6.0/LAYOUTS.md#collections-giving-a-block-its-content) and
[Pinning: mixing hand-picked and dynamic content](../../../../bc/6.0/LAYOUTS.md#pinning-mixing-hand-picked-and-dynamic-content).

1. **Offset and limit** (4 minutes). They apply after the query has run: offset skips items from the start, limit shows
   at most that many. A "featured" block with limit 1 and a "more stories" block with offset 1 and limit 6 make the
   familiar lead-plus-list from one ordering.
2. **Pinning** (5 minutes). Manual items added to a dynamic collection become **pinned overrides**: each keeps its
   stored position, and the query fills the slots around it. A pin at position 0 is first, whatever the query returns.
   Pinned positions are absolute within the collection.
3. **Advice** (2 minutes). Pin as little as possible, and write why in the block's **Name**, or the next editor will not
   understand why position 3 never changes.
4. **Messages** (2 minutes). The editor may report items skipped or out of range (for example *2 manual items
   out of range*) when a pin sits beyond the limit.

### Common questions

**Why does my pinned item not show?** Its position is beyond the limit.

**Can the same article appear in the lead and the list?** Not when the list's offset skips the lead's items and both
use the same query and order.

**How do I unpin?** Remove the manual item from the collection.

### Pitfalls

- **Different sorting in the two blocks.** Offset only works when both blocks use the same query and order.
- **Forgotten pins.** A pin stays until removed. The name note helps.
- **Positions counted from zero or one.** Check how your installation numbers positions before the exercise.

## Hands-on exercise

Use the training installation and your course layout with the *Newest articles* block from lesson 4.2.

1. Open your course layout with **Open** (continue your draft if asked).
2. Duplicate *Newest articles* with **Duplicate block**. Rename the first `Lead article` and the second `More articles`.
3. In *Lead article*, set the limit to 1 and the view type to `grid_featured` (or another view type that stands out).
4. In *More articles*, set the offset to 1 and the limit to 4.
5. Check in the editor: the lead shows the newest article, the list the next four.
6. In *More articles*, press **+ Add items** and pick one of your own articles; put it at the first position with
   **Set position**.
7. Rename the block `More articles (pinned: course article, until end of course)`.
8. Set the limit of *More articles* to 2 and watch what happens to the pinned item and the others; set it back to 4.
9. Choose **Save draft and close**.

## Expected result

- The draft has a lead block with one article and a list of four below it, without duplicates.
- One pinned item sits at the first place of *More articles*, with the reason in the block's name.
- The trainer checks by opening one learner's draft on the projector.

## Quiz

1. What do offset 1 and limit 6 do?
2. How do you build a lead-plus-list from one query?
3. What is a pinned item?
   A. A hidden item B. A manual item at a fixed position of a dynamic collection C. A deleted item D. A shared block
4. Why write the reason for a pin in the block's name?
5. A pinned item does not appear. What is the most likely reason?

<details>
<summary>Answers</summary>

1. Skip the first item and show at most six after it.
2. Two blocks with the same query and order: the first with limit 1, the second with offset 1
   ([Exponential Layouts](../../../../bc/6.0/LAYOUTS.md#collections-giving-a-block-its-content)).
3. **B.**
4. So the next editor knows why that position never changes.
5. Its position is beyond the collection's limit.

</details>

## Further reading

- [Exponential Layouts: Pinning](../../../../bc/6.0/LAYOUTS.md#pinning-mixing-hand-picked-and-dynamic-content)
- [Layout types, zones and blocks: Collections](../../../creators/website-creators/layout-types-zones-and-blocks.md#collections)

## Related pages

- [Module 4: Collections](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
