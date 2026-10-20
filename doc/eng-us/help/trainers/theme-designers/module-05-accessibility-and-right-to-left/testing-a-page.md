---
title: "Lesson 5.3: Testing a theme's accessibility"
audience: trainer, designer, developer
category: training
order: 53
summary: Learners choose the pages to test, run an automatic checker, do a keyboard pass, a zoom and color pass and a short screen reader pass, and record findings in a table that separates template work from content work. 45 minutes.
keywords: theme design, accessibility testing, automatic checker, axe, keyboard pass, screen reader, 200% zoom, 320 pixels, reflow, contrast, reduced motion, findings table, WCAG success criterion, template work, content work
type: help
---
# Lesson 5.3: Testing a theme's accessibility

An accessible theme is one that was tested. The tests are cheap and mostly need no special tools: the keyboard, the
zoom, the browser's developer tools and a screen reader the operating system already has. This lesson gives learners a
routine they can run on every release of their theme.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 5: Accessibility and right to left](README.md) |
| Duration | 45 minutes (10 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Designers, front-end developers and testers |
| Prerequisites | [Lesson 5.2](an-accessible-site-design.md) |

## Learning objectives

After this lesson, learners can:

- choose one page per template to test instead of every page;
- run an automatic checker and tell its errors from its warnings;
- do a keyboard pass and a zoom and color pass with a checklist;
- record findings with the page, the problem, the WCAG success criterion, the severity, the fix and the owner.

## Trainer's notes

### How to present it

Source: [Testing your site's accessibility](../../../creators/website-creators/testing-accessibility.md).

1. **Which pages** (2 minutes). One page of every template: the front page, one full view per class, a folder list
   with paging, search results with and without hits, every form, the error pages, each language siteaccess.
2. **Four passes** (5 minutes). Automatic checks (a browser extension or command-line tool based on axe; they find
   missing alternative texts, labels, contrast and wrong ARIA, not whether the order makes sense); keyboard (Tab,
   Shift+Tab, Enter, Space, Escape, the arrows); zoom, color and motion (200% and 320 CSS pixels wide, contrast, no
   information by color alone, reduced motion); screen reader (page title, landmarks and headings, images, form
   fields).
3. **Recording** (3 minutes). Show the findings table and the split into template work (fixed once for every page) and
   content work (editors, page by page).

### Common questions

**Which automatic checker should we use?** Any axe-based browser extension or command-line tool works; the course does
not prescribe one. Install it on the lab computers before the course.

**Is a screen reader pass needed every time?** A short one per release: page title, headings, landmarks, one form.

**Where do the administration's own tests come in?** The admin4 design tests run in CI; this routine is for the
public theme, which no test checks for you.

### Pitfalls

- **Testing only the front page.** Templates differ; the article and the form pages fail differently.
- **Treating warnings as passes.** Read every warning; some need a human decision.
- **Fixing content in the template.** A missing alternative text is content work; record it for the editors.

## Hands-on exercise

On your training installation, `<site>` drawn by `training_theme`, with an automatic accessibility checker installed in
the browser. Work in pairs, testing the partner's site.

1. Choose four pages: the front page, a folder list, an article, and the search result page for a word that finds
   nothing (`/content/search?SearchText=zzzz`).
2. Run the automatic checker on each page. Note every error.
3. Keyboard pass on the article page, mouse away: the skip link on the first Tab, every link reachable, the focus always
   visible, nothing traps the focus.
4. Zoom pass: set 200% zoom, then narrow the window to about 320 CSS pixels (the developer tools' device toolbar). Check
   for horizontal scrolling and cut-off text.
5. Color pass: check the contrast of the body text, links and the focus ring; check that nothing is said by color
   alone (the current menu item, from lesson 5.2, is also bold and underlined).
6. Motion pass: in the developer tools' rendering panel, emulate `prefers-reduced-motion: reduce` and reload.
7. Short screen reader pass, with the screen reader of your operating system: the page title, the list of headings and
   the list of landmarks of the article page.
8. Write every finding into the table:

   | Page | Problem | Success criterion | Severity | Fix | Owner |
   |---|---|---|---|---|---|

9. Mark each finding "template" or "content". Hand the table to your partner.

## Expected result

- Each pair has a findings table for the partner's site with at least the four pages tested.
- Template findings name the file of `training_theme` to change; content findings name the item and what the editor
  should do.
- The pages from lesson 5.2 pass the keyboard pass: skip link, visible focus, current item marked by more than color.
- The trainer collects the tables and lists the three most common template findings on the board.

![An item's page at 375 pixels wide, one of the widths to test a page at](../../../images/trainers/td-rtl-375.webp)

## Quiz

1. Why test one page per template rather than every page?
2. What can an automatic checker not tell you?
   A. A missing `alt` B. A missing label C. Whether the reading order makes sense D. Low contrast
3. At which zoom level and width do you check reflow?
4. A finding is "an image without alternative text in an article". Template or content?
5. Name three things the screen reader pass checks.

<details>
<summary>Answers</summary>

1. Pages drawn by the same template share the same markup; one page tests them all
   ([Testing your site's accessibility](../../../creators/website-creators/testing-accessibility.md#choose-the-pages)).
2. **C.**
3. 200% zoom and 320 CSS pixels wide, with no horizontal scrolling.
4. Content: the editor fills in the alternative text.
5. Any three: the page title, landmarks and headings, images described or skipped, form fields with label, required
   state and errors, language switches.

</details>

## Further reading

- [Testing your site's accessibility](../../../creators/website-creators/testing-accessibility.md): the full routine
  and what to do after launch.
- [Accessibility audit per release](../../../designers/lifecycle/accessibility-audit-per-release.md): the same routine
  as part of every theme release.
- [Accessible course materials](../../train-the-trainer/module-05-accessibility-and-inclusion/accessible-course-materials.md):
  the trainer's own material.

## Related pages

- [Module 5: Accessibility and right to left](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
