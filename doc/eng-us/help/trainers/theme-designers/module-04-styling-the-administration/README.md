---
title: "Module 4: Styling the administration"
audience: trainer, designer, developer
category: training
order: 40
summary: The fourth module of Theme design. Learners learn the administration styling rules, build a dashboard block from the shared admin4 components, style it with tokens that work in dark mode, handle forced colors and right to left, and review each other's work with the checklist. 4 hours 35 minutes.
keywords: theme design, module 4, admin4, administration styling rules, components.css, tokens, dark mode, BackendCSSFileList, forced colors, logical properties, code review checklist, AdminStylingRulesTest
type: help
---
# Module 4: Styling the administration

The administration is shared by every extension and every editor, so it is styled by common rules: no inline CSS,
shared components first, tokens for every value, a dark value for every color token. In this module learners add a
block to the admin4 dashboard from their own extension, build it from the shared components, give it a stylesheet
written only with tokens, make its state visible in forced-colors mode, and finish with a peer review against the
project's code review checklist.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Duration | 4 hours 35 minutes (with two 10-minute breaks) |
| Lessons | 5 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 4.1: The administration styling rules](the-administration-styling-rules.md) | 45 minutes | Learn the seven rules and their exceptions; find the rule breaks in a sample template |
| [Lesson 4.2: Building with the shared components](shared-components.md) | 60 minutes | Add a dashboard block from the extension, built from `.a4-table`, `.a4-badge`, `.a4-empty` and `.a4-btn` |
| [Lesson 4.3: Tokens and dark mode](tokens-and-dark-mode.md) | 60 minutes | Read tokens in the browser, add `training_admin.css`, fix a hard-coded color with tokens |
| [Lesson 4.4: Module and extension stylesheets](module-and-extension-stylesheets.md) | 45 minutes | Add a current-row state with a forced-colors outline, logical properties only |
| [Lesson 4.5: The review checklist and the design tests](review-checklist-and-design-tests.md) | 45 minutes | Review a partner's block with the checklist at two sizes and both modes; run the design test |

## Preparing this module

- The administration siteaccess of every learner installation in `admin4` (or `admin4l`); check with
  `php bin/php/console exp:ini where site/DesignSettings/SiteDesign admin`.
- Printed or projected copies of the sample template of lesson 4.1, and of the code review checklist from
  [Styling the administration](../../../../bc/6.0/admin4-styling.md#code-review-checklist).
- A Chromium-based browser on each learner computer for the forced-colors emulation of lesson 4.4.
- For lesson 4.5, step 9: one development checkout with the development dependencies installed
  (`vendor/bin/phpunit`), on the trainer's machine if the learners' installations do not have it.
- Pair learners for lessons 4.1 and 4.5 before the module starts.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 4.1 | 45 |
| Lesson 4.2 | 60 |
| Break | 10 |
| Lesson 4.3 | 60 |
| Break | 10 |
| Lesson 4.4 | 45 |
| Lesson 4.5 | 45 |
| **Total** | **275** |

## Related pages

- [Module 3: Stylesheets and scripts through the packer](../module-03-stylesheets-and-scripts/README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
