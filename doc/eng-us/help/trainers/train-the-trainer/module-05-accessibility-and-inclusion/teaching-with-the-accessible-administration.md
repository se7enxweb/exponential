---
title: "Lesson 5.2: Teaching with the accessible administration"
audience: trainer, editor, admin
category: training
order: 52
summary: Show learners the accessibility features of the administration, keyboard use, the skip link, color mode, column widths and browser zoom, so every learner can work in the way that suits them. 40 minutes.
keywords: accessibility, keyboard, skip link, color mode, dark mode, zoom, column width, admin4, screen reader, tree navigation
type: help
---
# Lesson 5.2: Teaching with the accessible administration

Learners differ: some use only the keyboard, some need dark mode or a large zoom, some use a screen reader. The
Admin 4 design of the administration supports all of these. A trainer who knows the features can show each learner
how to work comfortably from the first minute.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 5: Accessibility and inclusion](README.md) |
| Duration | 40 minutes (10 presenting, 30 exercise) |
| Level | Beginner |
| Audience | Trainers; useful for every learner |
| Prerequisites | A training installation in the Admin 4 design |

## Learning objectives

After this lesson, learners can:

- move through the administration with the keyboard alone, including the content tree;
- use the skip link, the search key `/` and `Esc`;
- switch the color mode and change the width of the left column;
- work at 200 percent browser zoom.

## Trainer's notes

### How to present it

1. Show the keyboard (5 minutes): **Tab** and **Shift+Tab**; the first **Tab** shows **Skip to main content**; `/`
   for the search; `Esc` to close menus; in the content tree the arrow keys, **Home**, **End** and letters
   ([Keyboard shortcuts](../../../keyboard-shortcuts.md)).
2. Show the display choices (5 minutes): the sun button in the header (**Color mode**) moves between light, dark and
   the system's mode, remembered per account; the buttons at the bottom of the left column set a small, medium or
   large width; the edge tabs hide or show the columns.

### Common questions

**Does it work with screen readers?** The Admin 4 work follows WAI-ARIA patterns for landmarks, tabs, disclosures and
menus; the standards page lists the status and the known gaps.

**What about the classic admin?** It has most of the keys, but teach accessibility in Admin 4.

### Pitfalls

- **Mouse-only demos.** Do one demo per module with the keyboard only.
- **Fixed zoom on the projector.** Zoom the browser for the room; the layout adapts.

## Hands-on exercise

1. Sign in to the administration of your training installation.
2. Press **Tab** once and then **Enter** on **Skip to main content**.
3. Press `/`, type a word, then `Esc`.
4. Move into the content tree with **Tab**; open a folder with the right arrow key, move with the down arrow, and
   open an item with **Enter**.
5. On an item's page, move between the tabs (**View**, **Details** ...) with the left and right arrow keys.
6. Press the sun button (**Color mode**) until the administration is dark; check one page; set it back.
7. Use the buttons at the bottom of the left column to set a large width, then a small one.
8. Zoom the browser to 200 percent and open **Content structure**. Check that nothing is cut off.

## Expected result

- The learner reached an item and its tabs without the mouse.
- Color mode and column width changed and were set back.
- At 200 percent zoom the page still works without horizontal scrolling.

## Quiz

1. Which key opens a closed folder in the content tree?
2. How many color modes does the sun button cycle through, and which?
3. Where are the buttons for the left column's width?
4. What does the first **Tab** on a page show?

<details>
<summary>Answers</summary>

1. The right arrow key ([Keyboard shortcuts](../../../keyboard-shortcuts.md)).
2. Three: light, dark and the computer's system mode ([A tour of the administration](../../../admin-tour.md)).
3. At the bottom of the left column (small, medium, large).
4. **Skip to main content**.

</details>

## Further reading

- [Keyboard shortcuts](../../../keyboard-shortcuts.md)
- [A tour of the administration](../../../admin-tour.md)
- [Accessibility standards](../../../../standards/accessibility.md)

## Related pages

- [Module 5: Accessibility and inclusion](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
