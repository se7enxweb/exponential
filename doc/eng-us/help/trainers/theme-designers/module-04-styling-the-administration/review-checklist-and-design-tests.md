---
title: "Lesson 4.5: The review checklist and the design tests"
audience: trainer, designer, developer
category: training
order: 45
summary: Learners review a partner's administration template and stylesheet with the code review checklist, check the page in light and dark mode at 1440 px and at 960 px with 200% zoom, and run the DB-less design tests. 45 minutes.
keywords: theme design, admin4, code review checklist, AdminStylingRulesTest, design test suite, phpunit, testsuite design, light and dark mode, 200% zoom, 960 px, 1440 px, contrast, horizontal overflow, console errors, allowlist
type: help
---
# Lesson 4.5: The review checklist and the design tests

Rules only help when someone checks them. For the administration designs, two things do: a test suite that reads
templates and stylesheets without a database, and a checklist that a reviewer works through for every change. This
lesson closes the module with a peer review of the learners' own dashboard block.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 4: Styling the administration](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Designers and developers who build or review administration pages |
| Prerequisites | [Lesson 4.4](module-and-extension-stylesheets.md); a development checkout with the development dependencies (for step 9) |

## Learning objectives

After this lesson, learners can:

- review an administration template and stylesheet with the code review checklist;
- check a page in light and dark mode at 1440 px and at 960 px with 200% zoom;
- run the design test suite and read a failure;
- explain why the test's allowlist only shrinks.

## Trainer's notes

### How to present it

Source: [Styling the administration, code review checklist](../../../../bc/6.0/admin4-styling.md#code-review-checklist)
and [Checked in CI](../../../../bc/6.0/admin4-styling.md#checked-in-ci).

1. **The checklist** (6 minutes). Project it and group it: markup (no inline CSS, shared components, icons as
   `.a4-icon` with `.a4-bi-*`, popups through the shared context menu), names (every control and field has an accessible
   name, header cells have `scope`), CSS (tokens only, new tokens with a dark value, `--a4-z-*`, forced-colors outlines),
   tables and notices (`table.list` or `.a4-table`; notices with `role="alert"` or `role="status"`), and the final checks
   (light and dark at 1440 px and 960 px with scale 2, the design test, a deploy with `--packer`).
2. **The visual check** (3 minutes). At 1440 px and at 960 px with 200% zoom, in light and dark mode: no light card on
   the dark page, text contrast at least 4.5:1, no horizontal overflow, no console errors.
3. **The tests** (4 minutes). `tests/tests/design/AdminStylingRulesTest.php` (test suite `design`, no database) fails
   on a `<style>` block or `style=""` in the templates of `admin4`, `admin4l`, `admin3` and `admin`, on literal colors
   and radii in the module stylesheets, on a color token without a dark value, and more. Run it, or the whole suite:

   ```bash
   php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php
   php vendor/bin/phpunit --testsuite design
   ```

4. **The allowlist** (2 minutes). Templates still being migrated are listed in the test; the list only shrinks. Nobody
   weakens the test or adds an entry to make a change pass: the CSS is moved instead.

### Common questions

**Do the tests check my extension?** They check the core designs. The checklist covers extensions; a reviewer applies
it to `extension/*/design/admin*` the same way.

**The training installation has no `vendor/bin/phpunit`.** The tests need a development checkout with the development
dependencies. Run step 9 on the trainer's machine and show the result, or skip it.

**How do I get 960 px at 200%?** Make the browser window 1920 px wide and zoom to 200%, or use the developer tools'
device toolbar with a width of 960 and a device pixel ratio of 2.

### Pitfalls

- **Reviewing only the template.** The stylesheet and the dark mode are half of the checklist.
- **Approving "because it looks fine".** Look for the hidden ones: a missing `scope`, a `title` used as the only name,
  a `#fff` in a rule that is not shown on the page you tested.
- **Running the full suite on a busy shared server.** Run one test file, with `nice -n 15`.

## Hands-on exercise

In pairs. Each learner reviews the partner's `training_notes.tpl`, `dashboard.ini.append.php` and `training_admin.css`
from lessons 4.2 to 4.4, on the partner's installation or from the shared files.

1. Open the checklist in [Styling the administration](../../../../bc/6.0/admin4-styling.md#code-review-checklist).
2. Template: check for a `<style>` block, a `style=""` attribute, inline handlers (`onclick` and the like).
3. Template: check that every table header cell has `scope="col"`, that the empty list uses `.a4-empty`, and that links
   have visible text.
4. Stylesheet: check for literal colors, radii and lengths (other than edges of 1 to 3 px), local custom properties,
   numeric `z-index` and `left`/`right`.
5. Stylesheet: check that the current state has its outline in `@media (forced-colors: active)`.
6. Set the browser to 1440 px wide at 100%, open the partner's dashboard, check the block in light and dark mode.
7. Set 960 px with 200% zoom (or a device pixel ratio of 2) and check again: no horizontal scroll bar, text readable.
8. Open the console and note any error.
9. If a development checkout is available, run
   `nice -n 15 php vendor/bin/phpunit tests/tests/design/AdminStylingRulesTest.php` and read the result line.
10. Write your findings as a short review: each finding with the checklist item and a proposed fix. Hand it to your
    partner, who fixes what is found and asks you to check again.

## Expected result

- Every pair has a written review with zero or more findings, each naming a checklist item.
- After the fixes, both blocks pass every checklist item that applies, in both modes and both sizes.
- Step 9 (where run): the test ends with `OK`.
- The trainer collects one review per pair and discusses the most common finding with the group.

## Quiz

1. Name the four things the visual check looks for at each size and mode.
2. Which test fails when a template of admin4 gets a `style=""` attribute?
   A. `AdminKeyboardTest` B. `AdminStylingRulesTest` C. `AdminLandmarksTest` D. `AdminLogoTest`
3. Why does the allowlist of that test only shrink?
4. Does the design test suite need a database?
5. Which deploy option belongs to the last item of the checklist?

<details>
<summary>Answers</summary>

1. No light card on the dark page, text contrast at least 4.5:1, no horizontal overflow, no console errors
   ([Styling the administration](../../../../bc/6.0/admin4-styling.md#the-rules)).
2. **B.**
3. It lists templates still being migrated; adding to it would hide new inline CSS instead of moving it.
4. No; it reads template and stylesheet sources.
5. `--packer` (`exp:velocity deploy --packer`), and no version query added to a link.

</details>

## Further reading

- [Styling the administration](../../../../bc/6.0/admin4-styling.md#code-review-checklist): the checklist in full.
- [Accessibility standards](../../../../standards/accessibility.md): which design tests hold which WCAG criteria.
- [Visual regression checks](../../../designers/lifecycle/visual-regression-checks.md): screenshots over time.
- [Testing and CI standards](../../../../standards/testing-and-ci.md): where the design suite runs.

## Related pages

- [Module 4: Styling the administration](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
