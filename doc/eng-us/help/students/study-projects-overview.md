---
title: "Study projects: overview"
audience: student
category: learning
order: 23
summary: The ten graded study projects for learners - what each one builds with real Exponential features (content classes, site structure, template overrides, layouts, languages, search, the REST API, extensions, roles and a capstone), its level and time, what you hand in, how the projects are graded, and how they relate to the trainers' assessments.
keywords: study projects, practice projects, graded, exercises, assignments, portfolio, content class, template, layouts, multilingual, search, REST API, extension, roles, capstone, rubric
type: help
---
# Study projects: overview

The study projects turn reading into working results. Each one builds something small but real on your study
installation, uses only features the help documents, and ends with a set of things to hand in and a grading rubric.
Do them alone, in a study group, or as assignments from a teacher.

> **Note**
> These projects and their grades are practice for you, your study group or your teacher. They are not an official
> certification and are not issued or endorsed by 7x or the Exponential Foundation; official certifications are
> planned but not available yet ([Certifications and certificates](certifications-and-certificates.md)).

## Contents

1. [The ten projects](#the-ten-projects)
2. [How every project is built](#how-every-project-is-built)
3. [What you hand in](#what-you-hand-in)
4. [How projects are graded](#how-projects-are-graded)
5. [Rules for every project](#rules-for-every-project)
6. [Projects and the trainers' assessments](#projects-and-the-trainers-assessments)

## The ten projects

| # | Project | Level | Time | Main features |
|---|---|---|---|---|
| 1 | [A content class of your own](project-01-a-content-class-of-your-own.md) | Beginner | 2 h | Content classes, attributes, datatypes |
| 2 | [A small site structure](project-02-a-small-site-structure.md) | Beginner | 3 h | Folders, articles, media, versions, trash |
| 3 | [A template override](project-03-a-template-override.md) | Intermediate | 3 h | Designs, override rules, the template language |
| 4 | [A page with layouts](project-04-a-page-with-layouts.md) | Beginner | 2 h | Exponential Layouts: layouts, zones, blocks, mappings |
| 5 | [A multilingual site](project-05-a-multilingual-site.md) | Intermediate | 3 h | Content languages, translations, `SiteLanguageList` |
| 6 | [Search that finds the right things](project-06-search-that-finds-the-right-things.md) | Intermediate | 2 h | Searchable attributes, the search index |
| 7 | [Reading content over the REST API](project-07-reading-content-over-the-rest-api.md) | Intermediate | 3 h | `ezprestapi`, personal API keys, curl |
| 8 | [Your first extension](project-08-your-first-extension.md) | Intermediate | 4 h | Extensions, modules, views, templates |
| 9 | [Roles for a restricted editor](project-09-roles-for-a-restricted-editor.md) | Intermediate | 2 h | Users, groups, roles, policies, limitations |
| 10 | [Capstone: a complete small site](project-10-capstone-a-complete-small-site.md) | Advanced | 2 to 4 days | All of the above |

Times are for a learner who has finished the steps of the matching learning path. Which projects belong to which
path is in [Choosing a learning path](choosing-a-learning-path.md#study-projects-per-path).

## How every project is built

Each project page has the same parts:

| Part | What it gives you |
|---|---|
| At a glance | Level, time, prerequisites and the learning path |
| Goal | What you will have at the end |
| Before you start | What to read and prepare |
| Tasks | Numbered tasks, each with what you should see |
| Hand in | The list of things to deliver |
| Rubric | How the work is graded |
| Stretch goals | Optional extras for a higher mark or for fun |

## What you hand in

Unless a project says otherwise:

1. **A short report** (one or two pages, Markdown is fine): what you built, the pages you used, the problems you met
   and how you solved them. Your [study journal](keeping-a-study-journal.md) is the raw material.
2. **Screenshots** of the result, with no passwords, keys or personal data on screen.
3. **Files you wrote** (templates, settings, PHP, scripts), in a Git repository of your own
   ([Using Git as a student](using-git-as-a-student.md)). Never commit `settings/override/`, passwords or keys.
4. **The release** you worked on (the first line of `php bin/php/console --version`).

## How projects are graded

Every project uses the same four criteria, scored 0 to 3, plus project-specific checks. The full method is on
[Grading your own work](grading-your-own-work.md).

| Criterion | Asks |
|---|---|
| It works | Does the result do what the goal says, on the release named? |
| It is built the documented way | Overrides instead of edited core files, settings in the right place, roles instead of shared accounts |
| It is safe | Escaped output, no secrets in files, only invented data, access control where needed |
| It is explained | Can a reader follow the report and repeat the work? |

## Rules for every project

- Work on a **study installation**, never on a live site or a site with real data.
- Use **invented content** and test addresses at `example.invalid`.
- **Cite the pages** you used, by title and link.
- Do not copy another learner's work. Helping each other is fine; say who helped.
- If a step on a help page is wrong, report it ([Improving the help as a student](improving-the-help-as-a-student.md)).
  That counts in your favor.

## Projects and the trainers' assessments

The trainers have their own assessments: written tests, practical tasks with rubrics and capstones, written for
courses they teach ([Assessments and practical exercises](../trainers/assessments/README.md)). The study projects
here are written for self study and cover similar ground. If you take a course, your trainer's assessment counts for
that course; the projects are good preparation for it ([Taking a course with a trainer](taking-a-course-with-a-trainer.md)).

## Related pages

- [Grading your own work](grading-your-own-work.md)
- [Building a portfolio](building-a-portfolio.md)
- [Student checklists](student-checklists.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
