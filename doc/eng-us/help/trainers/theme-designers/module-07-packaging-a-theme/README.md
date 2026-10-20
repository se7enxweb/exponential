---
title: "Module 7: Packaging a theme"
audience: trainer, designer, developer
category: training
order: 70
summary: The seventh module of Theme design. Learners complete their theme's metadata, activate and order it safely, export it as an .ezpkg, and put it under Git with a composer.json. 3 hours 10 minutes.
keywords: theme design, module 7, theme extension, ezinfo.php, extension.xml, Setup Extensions, ActiveExtensions, loading order, extends, ezpkg, ezpm, Composer, composer.json, git
type: help
---
# Module 7: Packaging a theme

A theme is only useful to others when it can be installed, identified and updated. In this module learners turn
`training_theme` into something they could hand over: metadata that agrees in every file, a README that tells an
installer how to switch it on, a clear split between the theme's settings and the installation's, an `.ezpkg` export,
and a Git repository of its own with a `composer.json`.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Duration | 3 hours 10 minutes (with one 10-minute break) |
| Lessons | 4 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 7.1: The theme's metadata](theme-extension-metadata.md) | 45 minutes | Make `ezinfo.php`, `extension.xml` and `LICENSE` agree; find the theme on the About page |
| [Lesson 7.2: Activating a theme and the loading order](activating-and-loading-order.md) | 45 minutes | Deactivate and activate on Setup > Extensions with a review; document `SiteDesign`; note an `<extends>` block |
| [Lesson 7.3: A Composer package or an .ezpkg](composer-or-ezpkg.md) | 45 minutes | Choose how to ship, export the theme with `ezpm.php`, inspect the archive, switch export off |
| [Lesson 7.4: A theme under Git, with a composer.json](a-theme-under-git.md) | 45 minutes | Start a repository in the extension folder, commit with prefixes, write and validate `composer.json` |

## Preparing this module

- Every learner works on their own installation: lessons 7.2 and 7.3 change `ActiveExtensions[]` and
  `package.ini`.
- `xmllint` (libxml2 tools), Git and, if possible, Composer installed on the learner machines or the training server.
- `ezpm.php` in the installation root of the lab; run `php ezpm.php help` once on a copy of the lab.
- Agree with the group on a made-up Composer vendor name for the course (lesson 7.4) and on a course e-mail address at
  `example.invalid` for Git commits.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 7.1 | 45 |
| Lesson 7.2 | 45 |
| Break | 10 |
| Lesson 7.3 | 45 |
| Lesson 7.4 | 45 |
| **Total** | **190** |

## Related pages

- [Module 6: Icons](../module-06-icons/README.md)
- [Theme extensions: start here](../../../designers/extensions/theme-extensions-start-here.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
