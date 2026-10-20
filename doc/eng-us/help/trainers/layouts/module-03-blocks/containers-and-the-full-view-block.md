---
title: "Lesson 3.4: Containers and the Full view block"
audience: trainer, editor, website-creator, designer
category: training
order: 34
summary: Learners split a zone into columns with container blocks, place blocks into their placeholders, and use the Full view block to show the page's own content inside a layout. 45 minutes.
keywords: exponential layouts, containers, two columns, three columns, four columns, column, placeholders, nesting, full view block, page shell, components
type: help
---
# Lesson 3.4: Containers and the Full view block

Two blocks are different from all others. **Containers** hold other blocks, so a zone can be split into columns
without a new layout type. The **Full view** block shows the page's own content item inside a layout, so a layout can
frame ordinary content pages. This lesson teaches both.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 3: Blocks](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors, site builders and designers |
| Prerequisites | [Lesson 3.3](buttons-images-and-the-design-tab.md) |

## Learning objectives

After this lesson, learners can:

- add a **Two columns**, **Three columns**, **Four columns** or **Column** container and fill its placeholders;
- change a two-column container's split with its view type;
- explain why nesting should stay shallow;
- add a **Full view** block and explain what it shows on each page the layout is used for;
- name the component blocks and where their use is listed.

## Trainer's notes

### How to present it

Sources: [Exponential Layouts: Containers and nesting](../../../../bc/6.0/LAYOUTS.md#containers-and-nesting) and
[Block catalogue](../../../../bc/6.0/LAYOUTS.md#block-catalogue).

1. **Containers** (4 minutes). Four container blocks, each with placeholders (named slots like small zones):

   | Container | Placeholders |
   |---|---|
   | Two columns | `left`, `right` |
   | Three columns | `col_1`, `col_2`, `col_3` |
   | Four columns | `col_1` to `col_4` |
   | Column | `main` |

   Two columns has the view types `two_columns_66_33` and `two_columns_33_66` for the width split; changing the view
   type reflows without moving blocks.
2. **Nesting** (2 minutes). Containers can nest; two levels are easy to edit, four are not.
3. **Full view** (4 minutes). In the **Placeholders** group. It renders the current content item in its normal full
   view: on an article it shows the article. Most page-shell layouts need exactly one. A layout with a Full view block
   mapped to a subtree frames every page in it.
4. **Components** (3 minutes). The **Components** group holds pre-designed sections (hero, features, about, logos,
   quote, lead) with several styles each. The **Components** screen of the Layouts tab lists where each is used.

### Common questions

**Why does my Full view block show nothing in the editor?** The editor has no current content item; the preview or
the site shows it with real content.

**Containers or a layout type with more zones?** Containers inside a simple shell are more flexible; a new layout type
is a developer's task.

**Are containers responsive?** The column templates carry the theme's responsive classes; check on a phone width.

### Pitfalls

- **Dropping into the container instead of a placeholder.** Aim for the drop area inside the container.
- **Two Full view blocks.** The content appears twice.
- **Deep nesting.** Stop at two levels.

## Hands-on exercise

Use the training installation and your course layout.

1. Open your course layout with **Open** (continue your draft if asked).
2. Press **Add a block**, open the **Containers** group and drag **Two columns** into *MAIN* above the Rich text block.
3. Drag the **Rich text** block into the container's left placeholder and the **Button / Link** block into the right
   placeholder.
4. Select the container and, in **Design**, change its view type between `two_columns_66_33` and `two_columns_33_66`.
   Watch the split change.
5. Open the **Placeholders** group and drag a **Full view** block into *MAIN* below the container.
6. Open the **Components** group and read the block types it offers. Do not add one.
7. Choose **Save draft and close**.
8. Open **Components** in the Layouts tab's left menu and find where one component is used on the training site.

## Expected result

- The draft has a two-column container with the rich text left and the button right, and a Full view block below.
- The learner saw the split change with the view type.
- The trainer checks by opening one learner's draft on the projector and naming the placeholders.

## Quiz

1. What is a placeholder?
2. Which view types does the **Two columns** container have?
3. What does a **Full view** block show?
   A. A list of articles B. The current content item in its full view C. The site's header D. A picture
4. How deep should containers nest?
5. Where do you see which pages use a component?

<details>
<summary>Answers</summary>

1. A named slot inside a container that holds blocks, like a small zone
   ([Exponential Layouts](../../../../bc/6.0/LAYOUTS.md#containers-and-nesting)).
2. `two_columns_66_33` and `two_columns_33_66`.
3. **B.**
4. Shallow: two levels are easy to edit.
5. On the **Components** screen of the Layouts tab.

</details>

## Further reading

- [Exponential Layouts: Containers and nesting](../../../../bc/6.0/LAYOUTS.md#containers-and-nesting)
- [Layout types, zones and blocks](../../../creators/website-creators/layout-types-zones-and-blocks.md#containers)

## Related pages

- [Module 3: Blocks](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
