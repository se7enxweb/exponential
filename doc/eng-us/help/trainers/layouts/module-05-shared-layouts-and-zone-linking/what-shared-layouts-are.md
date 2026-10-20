---
title: "Lesson 5.1: What shared layouts are"
audience: trainer, editor, website-creator
category: training
order: 51
summary: Learners explain why a header or footer is defined once in a shared layout, read the Shared layouts screen with its reference counts, and recognize a linked zone in the editor. 30 minutes.
keywords: exponential layouts, shared layout, linked zone, header, footer, prefooter, references, shared layouts screen, new shared layout
type: help
---
# Lesson 5.1: What shared layouts are

Every page has the same header and footer. Without shared layouts, every layout would carry its own copy, and a new
phone number in the footer would mean editing every layout. A **shared layout** holds such blocks once; other layouts
**link** a zone to it. This lesson explains the idea and how to recognize it.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 5: Shared layouts and zone linking](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Module 3](../module-03-blocks/README.md) |

## Learning objectives

After this lesson, learners can:

- explain what a shared layout is and why it is not mapped to pages by a rule;
- read the **Shared layouts** screen: each shared layout with its identifier, type, date and number of references;
- recognize a linked zone in the editor (red dashed border, hatched blocks);
- explain that linked blocks are changed in the shared layout, not in the linking one.

## Trainer's notes

### How to present it

Sources: [Shared layouts: header, footer and prefooter](../../../creators/website-creators/shared-header-and-footer.md)
and [Exponential Layouts: Shared layouts and linked zones](../../../../bc/6.0/LAYOUTS.md#shared-layouts-and-linked-zones).

1. **The idea** (4 minutes). A layout marked **shared** exists to be borrowed. Any zone of another layout can be linked
   to one of its zones and then shows that layout's blocks instead of its own. Change the shared layout, publish, and
   every page follows.
2. **The screen** (3 minutes). **Layouts** > **Shared layouts**: *Layouts whose zones other layouts link to.* Each
   card: the name, identifier, type, date and **references** (how many zones link to it), with **Preview**, and **New
   shared layout** at the top. A shared layout with zero references is valid; nobody has adopted it yet.
3. **In the editor** (4 minutes). A zone with a red dashed border and hatched blocks is linked: its blocks come from a
   shared layout. Linked blocks cannot be changed in the linking layout; **Edit linked layout** leads to the shared
   layout, where you make a draft and edit.
4. **Typical shared layouts** (2 minutes). A header and footer (type `layout_2`), a prefooter (type `layout_1`), a
   right column for articles.

### Common questions

**Can a shared layout also be used as a page layout?** It is not mapped to pages by a rule; it exists to be linked.

**Can a zone link to a draft?** No. Only published shared layouts can be linked, so a zone never points at someone's
unfinished work.

**What happens to the linking zone's own blocks?** They are ignored while the link is in place, and come back when the
zone is unlinked.

### Pitfalls

- **Editing a shared layout "just quickly".** It changes every page that links to it; check the reference count first.
- **Dropping blocks into a linked zone.** They do not show while the link is in place.
- **Confusing shared layouts and components.** Components are styled blocks; shared layouts are whole layouts whose
  zones are borrowed.

## Hands-on exercise

Use the training installation and your layouts training account.

1. Open **Layouts** > **Shared layouts**. Read the line under the title.
2. Write down each shared layout's name, type and number of references.
3. Press **Preview** on the shared layout with the most references.
4. Open **Layouts** > **Layouts**, find a page layout of type `layout_2` that is not shared, and press **Preview**:
   find the header and footer from the shared layout.
5. The trainer opens that page layout in the editor on the projector and points at the linked zones (red dashed border,
   hatched blocks) and at **Edit linked layout**.
6. Write down which zones of that page layout are linked and to which shared layout.

## Expected result

- The learner's notes list the shared layouts with their reference counts, and the linked zones of one page layout.
- The learner can say where the footer of the training site is edited.
- The trainer checks by asking a learner where they would change the phone number in the footer.

![Shared layouts, such as the header and footer other layouts link to](../../../images/trainers/ly-shared.webp)

## Quiz

1. Why does a shared layout make sense for a footer?
2. What does the number of references on the **Shared layouts** screen tell you?
3. How do you recognize a linked zone in the editor?
4. True or false: a zone can link to a draft of a shared layout.
5. Where do you change a block that a linked zone shows?
   A. In the linking layout B. In the shared layout C. In the content tree D. In the mappings

<details>
<summary>Answers</summary>

1. The footer is defined once; changing and publishing the shared layout changes every page
   ([Shared layouts](../../../creators/website-creators/shared-header-and-footer.md#why-shared-layouts)).
2. How many zones of other layouts link to it.
3. A red dashed border and hatched blocks ([A tour of the administration](../../../admin-tour.md#2-zones-and-blocks)).
4. **False.** Only published shared layouts can be linked.
5. **B.**

</details>

## Further reading

- [Shared layouts: header, footer and prefooter](../../../creators/website-creators/shared-header-and-footer.md)
- [A tour of the administration: linked and shared zones](../../../admin-tour.md#6-linked-and-shared-zones)

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
