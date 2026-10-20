---
title: "Lesson 5.2: Linking and unlinking zones"
audience: trainer, editor, website-creator
category: training
order: 52
summary: Learners link the header and footer zones of their course layout to a shared layout with Link zone with shared layout zone, change a link, and unlink a zone. 45 minutes.
keywords: exponential layouts, link zone, link zone with shared layout zone, choose a zone you wish to link, update link, choose different zone, unlink zone, linked zone, shared header, shared footer
type: help
---
# Lesson 5.2: Linking and unlinking zones

Linking is a short sequence in the editor, but it has two steps that confuse beginners: first choose your own zone,
then the shared layout and its zone. This lesson practices linking, changing and removing a link on the learners'
course layouts.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 5: Shared layouts and zone linking](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Lesson 5.1](what-shared-layouts-are.md); a published shared header/footer layout on the training site |

## Learning objectives

After this lesson, learners can:

- open the linking mode with **Link zone with shared layout zone**;
- choose a zone to link (**Link zone**), then the shared layout and its target zone;
- read the link of a linked zone (**Layout:**, **Zone:**) and change it with **Update link** or **Choose different
  zone**;
- remove a link with **Unlink** and confirm it.

## Trainer's notes

### How to present it

Sources: [A tour of the administration: linked and shared zones](../../../admin-tour.md#6-linked-and-shared-zones)
and [Shared layouts: Build it](../../../creators/website-creators/shared-header-and-footer.md#build-it).

1. **Open the mode** (2 minutes). The button at the bottom left of the editor, **Link zone with shared layout zone**.
   A note reminds: *Multiple zones in this layout can be linked to zones from different shared layouts.*
2. **Step 1** (3 minutes). *Choose a zone you wish to link*: each zone has **Link zone**; a linked zone shows a red
   link button.
3. **Step 2** (3 minutes). *Choose one of the shared layouts and then select a target zone*: pick the shared layout in
   the list, then the zone (for example its `header`). The zone now shows the shared blocks.
4. **An existing link** (3 minutes). A linked zone says *This zone is already linked with* and names the **Layout:**
   and **Zone:**; **Update link** or **Choose different zone** change it, **Unlink** removes it after *Are you sure you
   want to unlink this zone?*
5. **Back and publish** (2 minutes). **Back** returns to the editor. The link is part of the draft: it shows on the site
   after the layout is published (module 6).

### Common questions

**Do the zone names have to match?** No, a zone can link to any zone of a shared layout; matching names (header to
header) keep things understandable.

**Why is my shared layout not in the list?** It is not published, or not marked shared.

**What happens to the blocks I had in the zone?** They are ignored while linked and return when you unlink.

### Pitfalls

- **Skipping step 1.** Learners pick a shared layout without choosing their own zone first.
- **Linking the wrong zone.** Read the zone name before **Link zone**.
- **Expecting the site to change.** Nothing changes until the layout is published.

## Hands-on exercise

Use the training installation and your course layout. The trainer named the shared header/footer layout.

1. Open your course layout with **Open** (continue your draft if asked).
2. Press **Link zone with shared layout zone** at the bottom left.
3. Read the instruction. Press **Link zone** on your zone *HEADER*.
4. Choose the shared header/footer layout and then its zone `header`.
5. Press **Back**. Your *HEADER* zone now shows the shared header, with a red dashed border and hatched blocks.
6. Link your *FOOTER* zone to the shared layout's `footer` zone the same way.
7. Open the linking mode again and look at *HEADER*: read *This zone is already linked with*, the **Layout:** and the
   **Zone:**.
8. Link *PRE_FOOTER* to the shared layout's `pre_footer` zone (or to a prefooter shared layout if the site has one).
9. Unlink *PRE_FOOTER* with **Unlink** and confirm *Are you sure you want to unlink this zone?*
10. Press **Back** and choose **Save draft and close**.

## Expected result

- The learner's draft has *HEADER* and *FOOTER* linked to the shared header/footer layout, and *PRE_FOOTER* unlinked.
- The linked zones show the shared blocks with the linked look.
- The trainer checks by opening one learner's draft in the linking mode on the projector.

## Quiz

1. Which button opens the linking mode?
2. What are the two steps of linking a zone?
3. What does **Unlink** do to the zone's own blocks?
4. True or false: a new link is visible on the site as soon as it is made.
5. A linked zone says *This zone is already linked with*. Which two things does it name?

<details>
<summary>Answers</summary>

1. **Link zone with shared layout zone** ([A tour of the administration](../../../admin-tour.md#6-linked-and-shared-zones)).
2. Choose your zone (**Link zone**), then the shared layout and its target zone.
3. They show again; they were ignored while the link was in place.
4. **False.** The link is part of the draft until the layout is published.
5. The **Layout:** and the **Zone:** it is linked with.

</details>

## Further reading

- [A tour of the administration: linked and shared zones](../../../admin-tour.md#6-linked-and-shared-zones)
- [Shared layouts: Rules to know](../../../creators/website-creators/shared-header-and-footer.md#rules-to-know)

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
