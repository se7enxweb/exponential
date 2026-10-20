---
title: "Lesson 2.1: Layout types and their zones"
audience: trainer, editor, website-creator, designer
category: training
order: 21
summary: Learners name the twelve layout types and their zones, choose a type for a page shell or a content area, and explain why the type cannot be changed later. 30 minutes.
keywords: exponential layouts, layout type, zones, 1 column, 2 columns, hero, sidebar, featured, mosaic, single zone, header main footer, layout_2, layout_4, page shell
type: help
---
# Lesson 2.1: Layout types and their zones

Every layout starts with a **layout type**, and the type fixes the zones for good. Choosing it well is the first
design decision of a page. This lesson presents the twelve types that ship and how to choose.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 2: Layouts and layout types](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Beginner |
| Audience | Editors, site builders and designers |
| Prerequisites | [Module 1](../module-01-introducing-layouts/README.md) |

## Learning objectives

After this lesson, learners can:

- name the twelve layout types and the zones of the most used ones;
- choose a page-shell type (with header and footer zones) or a content-area type for a given page;
- explain why the type of a layout cannot be changed later;
- read a layout's type in the **Layouts** list.

## Trainer's notes

### How to present it

Sources: [Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md#layout-types)
and [Exponential Layouts: The layout types](../../../../bc/6.0/LAYOUTS.md#the-layout-types).

1. **The table** (6 minutes). Project it:

   | Identifier | Name | Zones |
   |---|---|---|
   | `1_column` | 1 column | `main` |
   | `2_column` | 2 columns | `left`, `right` |
   | `3_column` | 3 columns | `left`, `main`, `right` |
   | `4_column` | 4 columns | `col1`, `col2`, `col3`, `col4` |
   | `hero` | Hero + 3 columns | `top`, `left`, `main`, `right` |
   | `sidebar_left` | Sidebar left | `sidebar`, `main` |
   | `sidebar_right` | Sidebar right | `main`, `sidebar` |
   | `featured` | Featured | `hero`, `feature1`, `feature2`, `feature3`, `bottom` |
   | `mosaic` | Mosaic | `a`, `b`, `c`, `d`, `e` |
   | `layout_1` | Single zone | `main` |
   | `layout_2` | Header / Main / Footer | `header`, `post_header`, `main`, `pre_footer`, `footer` |
   | `layout_4` | Header / Left / Right / Footer | `header`, `post_header`, `left`, `right`, `pre_footer`, `footer` |

2. **Shell or area** (3 minutes). `layout_1`, `layout_2` and `layout_4` build a whole page shell (with header and
   footer zones); the column types suit a content area.
3. **Permanent** (2 minutes). Blocks belong to zones, so the type cannot change. A wrong choice means a new layout
   (copy the blocks over by hand or rebuild).
4. **Containers** (2 minutes). Inside a zone, container blocks split the area further (module 3), so a few types cover
   most pages.

### Common questions

**Can a site have its own layout types?** Yes, developers add them in the settings (`[LayoutType_*]` blocks of the
layouts extension's settings) with a template that draws the zones.

**Which type do most front pages use?** On the sample data of the multisite package, many page layouts use
`layout_2` (Header / Main / Footer); look at your own site's **Layouts** list.

**What is `post_header` for?** A strip right under the header, for example a hero banner or a breadcrumb.

### Pitfalls

- **Choosing columns for a whole page.** The page then has no header and footer zones to link to the shared header and
  footer (module 5).
- **Over-planning.** Learners compare all twelve types for every page. Start with `layout_2` and containers.

## Hands-on exercise

Use the training installation and paper.

1. Open **Layouts** > **Layouts** and set **Sort by** to **Layout type**.
2. Write down which layout types the training site uses and how many layouts use each.
3. Press **Preview** on a `layout_2` layout and identify its five zones on the screen.
4. For each of these pages, choose a layout type and write down why:
   - a front page with header, a big banner, a news list and a footer;
   - a contact page with a form on the left and the address on the right;
   - a product launch page with one large hero and three feature boxes.
5. For your choice for the front page, list which zone each part goes into.
6. Compare with a neighbor.

## Expected result

- The learner's notes list the types in use with their counts.
- Reasonable choices: `layout_2` for the front page (banner in `post_header`, news in `main`), `layout_4` or
  `2_column` for the contact page, `featured` or `hero` (or `layout_2` with containers) for the launch page.
- The trainer checks two learners' choices and their reasons.

## Quiz

1. Which zones does `layout_2` have?
2. Which layout types include header and footer zones?
3. True or false: you can change a layout's type after creating it.
4. Which type has the zones `top`, `left`, `main`, `right`?
   A. `3_column` B. `hero` C. `featured` D. `mosaic`
5. How do you split a zone into columns without a different layout type?

<details>
<summary>Answers</summary>

1. `header`, `post_header`, `main`, `pre_footer`, `footer`.
2. `layout_2` and `layout_4` (and `layout_1` is a single-zone shell).
3. **False.** Blocks belong to zones, so the type is permanent.
4. **B.**
5. With a container block (two, three or four columns) inside the zone.

</details>

## Further reading

- [Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md): types, blocks
  and a planning sheet.
- [Exponential Layouts: Zones](../../../../bc/6.0/LAYOUTS.md#zones): what a zone does.

## Related pages

- [Module 2: Layouts and layout types](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
