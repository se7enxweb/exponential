---
title: "Lesson 5.3: Changing shared content safely"
audience: trainer, editor, website-creator
category: training
order: 53
summary: Learners change a block of a shared layout through its own draft, check the reference count first, publish and verify several pages, and know how to retire a shared layout. 45 minutes.
keywords: exponential layouts, shared layout, edit linked layout, references, draft, publish, footer change, retire shared layout, caches, verification
type: help
---
# Lesson 5.3: Changing shared content safely

Changing a shared layout changes every page that links to it. That is the point, and the risk. This lesson teaches a
safe routine: check who uses it, change it in its own draft, publish, verify. The exercise uses a training shared
layout, never the site's real header.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 5: Shared layouts and zone linking](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders who maintain shared content |
| Prerequisites | [Lesson 5.2](linking-and-unlinking-zones.md) |

## Learning objectives

After this lesson, learners can:

- find a shared layout's references before changing it;
- open a shared layout from a linked zone with **Edit linked layout** and change it in its draft;
- publish it and check several linking pages;
- describe how to keep one page without the shared footer, and how to retire a shared layout.

## Trainer's notes

### How to present it

Sources: [Shared layouts: Changing and removing](../../../creators/website-creators/shared-header-and-footer.md#changing-and-removing)
and [Exponential Layouts: Shared layouts and linked zones](../../../../bc/6.0/LAYOUTS.md#shared-layouts-and-linked-zones).

1. **The routine** (5 minutes). Write it on the board:
   1. look up the references on **Shared layouts**;
   2. open the shared layout (from the list, or **Edit linked layout** in a linking layout) and work in its draft;
   3. change, then publish;
   4. load two or three linking pages, signed out, and check.
2. **Blocks cannot be edited from the linking layout** (2 minutes). The editor refuses changes to blocks that belong to
   a published shared layout when you are in a linking layout. Open the shared layout itself.
3. **Variations** (4 minutes):

   | You want | Do |
   |---|---|
   | Change the footer everywhere | draft the shared layout, edit, publish |
   | One page without the shared footer | unlink that zone in that page's layout |
   | Retire a shared layout | check its references first; unlink every zone, then delete |

4. **Caches** (2 minutes). After publishing, the editor may ask about clearing caches (module 6). Pages show the change
   when their cached copies are renewed.

### Common questions

**Who should change shared layouts?** Agree in the team (lesson 1.3); often one person.

**Can I test a change on one page first?** Copy the shared layout, mark the copy shared, link one page's zone to the
copy, and check; then change the real one.

**Why did some pages not change?** They link to another shared layout, or their cached copies are not renewed yet.

### Pitfalls

- **Practicing on the real header.** Use the training shared layout the trainer prepared.
- **Forgetting the reference count.** A shared layout with 36 references changes 36 zones.
- **Deleting a referenced shared layout.** Unlink every zone first.

## Hands-on exercise

Use the training installation. The trainer prepared a shared layout `Training footer` (type `layout_1`, with a rich
text block in `main`), published, and each learner linked their course layout's *FOOTER* zone to it in place of the
site footer (or does it now with lesson 5.2's steps).

1. Open **Layouts** > **Shared layouts** and read the references of `Training footer`.
2. Open your course layout with **Open**. On the linked *FOOTER* zone, find **Edit linked layout** and use it (or open
   `Training footer` from the **Shared layouts** list).
3. The trainer assigns one learner per round to change the rich text of `Training footer` (add a line with today's
   date) and publish it with **Publish layout**. The others watch on the projector.
4. Everybody opens their own course layout's **Preview** (or the editor) and checks the footer.
5. Discuss: how many layouts changed with one publish?
6. In your course layout, unlink *FOOTER* and link it again (lesson 5.2), to practice "one page without the shared
   footer".
7. Choose **Save draft and close**.

## Expected result

- The change made once in `Training footer` appears in every learner's course layout that links to it.
- Each learner can name the steps of the routine.
- The trainer checks by previewing two learners' layouts on the projector after the change.

## Quiz

1. What do you check before changing a shared layout?
2. Why can you not change a linked block in the linking layout?
3. How do you keep one page without the shared footer?
   A. Delete the shared layout B. Unlink that zone in that page's layout C. Hide the footer block D. Change the rule
4. Put the routine in order: publish, check references, verify pages, change in the draft.
5. What must happen before a shared layout is deleted?

<details>
<summary>Answers</summary>

1. Its references on the **Shared layouts** screen.
2. Blocks of a published shared layout are changed in that layout's own draft; the linking layout only borrows them.
3. **B.** ([Shared layouts](../../../creators/website-creators/shared-header-and-footer.md#changing-and-removing)).
4. Check references, change in the draft, publish, verify pages.
5. Every zone that links to it is unlinked.

</details>

## Further reading

- [Shared layouts: header, footer and prefooter](../../../creators/website-creators/shared-header-and-footer.md)
- [Exponential Layouts: Shared layouts and linked zones](../../../../bc/6.0/LAYOUTS.md#shared-layouts-and-linked-zones)

## Related pages

- [Module 5: Shared layouts and zone linking](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
