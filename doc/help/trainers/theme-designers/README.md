---
title: "Theme design: course overview"
audience: trainer, designer, developer
category: training
order: 1
summary: The course for designers and front-end developers who build themes for Exponential, 8 modules and 31 lessons, about 24 hours of lessons, from the design fallback chain through the pagelayout, overrides, the ezjscore packer, the admin4 styling rules, accessibility, right to left and icons to packaging, licensing and releasing a theme extension.
keywords: theme design, theme course, designer training, design, fallback chain, pagelayout, override, ezjscore packer, admin4, tokens, dark mode, accessibility, right to left, icons, theme extension, ezpkg, Composer, license, release, course overview
type: help
---
# Theme design: course overview

Theme design is the course for people who change how Exponential looks. Learners start by reading how a page finds its
templates, then build a theme extension of their own, `training_theme`, and take it all the way: its pagelayout and
overrides, stylesheets and scripts through the packer, a block in the administration built to the admin4 styling
rules, accessibility and right-to-left support, icons, metadata, an `.ezpkg` export, a Git repository with a
`composer.json`, a license inventory and a first release in a local repository.

## Who it is for

- Designers and front-end developers who build or adapt themes for sites made with Exponential.
- Developers who build administration pages for extensions and must follow the admin4 styling rules (modules 4 and 6
  can be taught on their own to them).
- Agencies preparing to publish or sell themes (modules 7 and 8).

## Outcome

After the course, learners can read the design chain of any siteaccess and find the template behind any markup; create
and switch on a theme extension; write a pagelayout, overrides and safe cache blocks; deliver stylesheets and scripts
through the ezjscore packer and keep a theme ready for a Content Security Policy; build administration markup from the
shared components and tokens that work in dark mode and forced colors; make and test an accessible, right-to-left-safe
theme; ship icons; and package, license and release a theme without ever moving a published tag.

## Format and length

| | |
|---|---|
| Lessons | 31 lessons in 8 modules |
| Lesson time | 24 hours 15 minutes (presenting and exercises) |
| With breaks | about 25 hours 45 minutes, as in the modules' timing plans |
| Format | Classroom with a projector, or online with screen sharing; every learner at their own computer with a shell |
| Group size | Up to about 10 learners per trainer: most exercises change files, and the trainer checks screens and sources |
| Level | Beginner to advanced, rising through the modules |

## Prerequisites

- Learners: HTML and CSS; basic use of a shell and a text editor; Git basics for module 7. The template language is
  introduced where it is needed; the developers course (Developers, in this section of the help) teaches it in full.
- Trainer: experience building a theme extension on the version they teach, and the train-the-trainer course or
  equivalent experience ([Train the trainer: course overview](../train-the-trainer/README.md)).

## Lab setup

- **One installation per learner**, because learners create extensions, change settings and activate and deactivate
  extensions. Install each with `php bin/php/console exp:install`
  ([7. The console install](../../../install/07-console-install.md)) using a package with demo content, so folders,
  articles and images exist ([Choosing the site package for a class](../train-the-trainer/module-02-building-the-lab/choosing-the-site-package-for-class.md)).
  How to build, serve and reset a lab:
  [Module 2: Building the training lab](../train-the-trainer/module-02-building-the-lab/README.md).
- An administrator account per learner with `setup/setup` (for **Setup > RAD**) and `uiswitch/design`, its e-mail
  address at `example.invalid`. Never the shared administrator of a real site.
- The administration siteaccess in `admin4` (or `admin4l`) for modules 4 to 6.
- On every learner computer: a current Chromium-based browser with developer tools, an axe-based accessibility
  checker, Git, `xmllint`, and if possible Composer.
- For lesson 4.5, one development checkout with the development dependencies (`vendor/bin/phpunit`).
- No real mail and no remote repositories: nothing in the course is published.

## Modules

| Module | Lessons | Duration | Topics |
|---|---|---|---|
| [Module 1: Designs and the fallback chain](module-01-designs-and-the-fallback-chain/README.md) | 4 | 3 h 10 min | Design folders, the chain of a siteaccess, template path comments, site and administration designs |
| [Module 2: Templates, overrides and the pagelayout](module-02-templates-overrides-and-the-pagelayout/README.md) | 4 | 4 h 5 min | The design extension wizard, the pagelayout, `override.ini`, cache blocks |
| [Module 3: Stylesheets and scripts through the packer](module-03-stylesheets-and-scripts/README.md) | 4 | 2 h 55 min | `design.ini` lists, the ezjscore packer, the clear routine, Content Security Policy |
| [Module 4: Styling the administration](module-04-styling-the-administration/README.md) | 5 | 4 h 35 min | The styling rules, shared components, tokens and dark mode, forced colors, review and tests |
| [Module 5: Accessibility and right to left](module-05-accessibility-and-right-to-left/README.md) | 4 | 3 h 40 min | The project's status, an accessible frame, testing, right to left |
| [Module 6: Icons](module-06-icons/README.md) | 2 | 1 h 30 min | Icon themes in an extension, admin4 icons |
| [Module 7: Packaging a theme](module-07-packaging-a-theme/README.md) | 4 | 3 h 10 min | Metadata, activation and loading order, `.ezpkg` and Composer, Git |
| [Module 8: Licensing and releasing](module-08-licensing-and-releasing/README.md) | 3 | 2 h 40 min | Licenses, versions and permanent tags, changelog, release notes, checklists |

## Reading path

For a trainer preparing the course, in this order:

1. [Templates and design: change what a page looks like](../../../guides/templates-and-design.md): the whole technical
   base in one guide.
2. [Designs, siteaccesses and the design fallback chain](../../creators/website-creators/designs-and-the-fallback-chain.md),
   [The pagelayout](../../creators/website-creators/the-pagelayout.md),
   [Template overrides for site builders](../../creators/website-creators/template-overrides.md) and
   [View cache and cache blocks](../../creators/website-creators/view-cache-and-cache-blocks.md).
3. [Stylesheets and scripts for your site](../../creators/website-creators/stylesheets-and-scripts.md) and
   [Content Security Policy](../../../features/6.0/content-security-policy.md).
4. [Styling the administration](../../../bc/6.0/admin4-styling.md) and [The admin4 design](../../../features/6.0/admin4-design.md):
   read the first one completely; modules 4 and 6 depend on it.
5. [Accessibility standards](../../../standards/accessibility.md),
   [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md) and
   [Right-to-left languages in the administration](../../../features/6.0/right-to-left.md).
6. [Icon themes in extensions](../../../features/6.0/icon-themes-in-extensions.md).
7. The designers' guides: [Theme extensions: start here](../../designers/extensions/theme-extensions-start-here.md),
   [Distributing a theme: start here](../../designers/distribution/distribution-start-here.md) and
   [The life cycle of a theme](../../designers/lifecycle/the-life-cycle-of-a-theme.md).
8. Then the modules in order: each module's overview first (its preparation list and timing plan), then its lessons.
   Build `training_theme` yourself once on a lab installation, exactly as the exercises describe.

## Suggested schedules

Lesson times add up to 24 hours 15 minutes; the schedules add breaks and a short daily review.

### Four full days

| Day | Morning | Afternoon |
|---|---|---|
| 1 | Module 1 | Module 2 |
| 2 | Module 3 | Module 4 (lessons 4.1 to 4.3) |
| 3 | Lessons 4.4 and 4.5, Module 6 | Module 5 |
| 4 | Module 7 | Module 8, open questions |

Module 6 comes before module 5 on day 3 because its exercises continue the dashboard block of module 4.

### Eight half days (for example one a week)

| Half day | Modules |
|---|---|
| 1 | Module 1 |
| 2 | Module 2 |
| 3 | Module 3 |
| 4 | Module 4, lessons 4.1 to 4.3 |
| 5 | Lessons 4.4 and 4.5, Module 6 |
| 6 | Module 5 |
| 7 | Module 7 |
| 8 | Module 8 |

Between half days, learners keep their installation and `training_theme`; every module builds on the last.

### Two days for administration pages only

For developers who build admin pages for extensions: lessons 1.1, 1.4, 2.1, 3.1, 3.3, all of module 4, lesson 5.4 and
module 6 (10 hours 30 minutes of lessons).

## Assessing learners

- Each lesson's quiz and expected result check understanding during the course; the peer review of lesson 4.5 and the
  checklists of lesson 8.3 are practical checks.
- The finished `training_theme` is a natural final task: it should pass the review checklist of lesson 4.5 and the
  "theme extension is ready" checklist of lesson 8.3.
- The [Assessments course](../assessments/README.md) has further material. All assessments are the trainer's own
  assessment, not an official certification; the partner program's certifications are separate
  ([Certifications](../../partners/certifications.md)).

## Related pages

- [Trainers: start here](../trainers-start-here.md)
- [Course catalogue](../course-catalogue.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
