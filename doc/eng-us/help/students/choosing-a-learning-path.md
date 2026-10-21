---
title: Choosing a learning path
audience: student
category: learning
order: 4
summary: The four learning paths for students - editor, administrator, developer and designer - who each one suits, what you need before you start, how long each takes, how they build on each other, and which study projects belong to which path.
keywords: learning path, track, editor, administrator, developer, designer, choose, career, prerequisites, order, curriculum, roadmap
type: help
---
# Choosing a learning path

This page helps you choose where to begin. Exponential is used by people in very different roles, and the help has
pages for each of them. The four learning paths of this folder put those pages in an order that works for a learner.

## Contents

1. [The four paths at a glance](#the-four-paths-at-a-glance)
2. [Which path suits you](#which-path-suits-you)
3. [How the paths build on each other](#how-the-paths-build-on-each-other)
4. [What each path takes](#what-each-path-takes)
5. [Study projects per path](#study-projects-per-path)
6. [Courses that match the paths](#courses-that-match-the-paths)

## The four paths at a glance

| Path | You learn to | Start page |
|---|---|---|
| Editor | Write, structure, translate and publish content; build pages with blocks | [Learning path: editor](learning-path-editor.md) |
| Administrator | Install a site, manage users, roles, sections, languages, caches, logs and updates | [Learning path: administrator](learning-path-administrator.md) |
| Developer | Work with Git, templates, content classes, settings, extensions and the REST API | [Learning path: developer](learning-path-developer.md) |
| Designer | Change how a site looks: designs, templates, overrides, stylesheets, themes as extensions | [Learning path: designer](learning-path-designer.md) |

## Which path suits you

| If you ... | Start with |
|---|---|
| study communication, journalism, marketing or library science | Editor |
| want to run websites for an organization, or study IT operations | Editor, then Administrator |
| study computer science or web development and know some PHP | Editor (short), then Developer |
| study design or front-end development and know HTML and CSS | Editor (short), then Designer |
| are not sure | Editor: every other path assumes you can find and publish content |

The help itself describes this growth "from user to developer": first you use the administration, then you change
templates, then you work with the code ([Develop with Git](../develop-with-git.md),
[The ladder: user, builder, extension developer, theme developer](../extension-development/part-1-from-user-to-developer/the-ladder-from-user-to-developer.md)).

## How the paths build on each other

```text
                      Editor
                        |
          +-------------+-------------+
          |                           |
    Administrator                 Designer
          |                           |
          +-------------+-------------+
                        |
                    Developer
```

- **Editor** is the base for everyone. Its first steps take one or two days.
- **Administrator** and **Designer** can be studied in either order.
- **Developer** uses parts of the other three: content classes from the administrator path, templates from the
  designer path. You can start it straight after the editor path if you know PHP; you will meet the other parts on
  the way.

The trainers' [Course catalogue](../trainers/course-catalogue.md#suggested-order) shows the same order for the
courses.

## What each path takes

| Path | Prerequisites | Suggested length for self study | Matching trainer course length |
|---|---|---|---|
| Editor | A browser | 2 to 3 weeks, a few hours a week | Editors 101: about 19.5 hours |
| Administrator | Editor path; a terminal | 4 to 6 weeks | Site administration: about 30 hours |
| Developer | Editor path; PHP and Git basics | 6 to 10 weeks | Developers: about 44 hours |
| Designer | Editor path; HTML and CSS | 4 to 6 weeks | Theme design: about 24 hours |

The self-study lengths are suggestions for a learner with a few hours a week; the course lengths are those of the
[Course catalogue](../trainers/course-catalogue.md#the-courses).

## Study projects per path

The [study projects](study-projects-overview.md) are graded practice projects on your own installation. Each path
uses some of them:

| Project | Editor | Administrator | Developer | Designer |
|---|---|---|---|---|
| [1. A content class of your own](project-01-a-content-class-of-your-own.md) | | yes | yes | |
| [2. A small site structure](project-02-a-small-site-structure.md) | yes | yes | | yes |
| [3. A template override](project-03-a-template-override.md) | | | yes | yes |
| [4. A page with layouts](project-04-a-page-with-layouts.md) | yes | | | yes |
| [5. A multilingual site](project-05-a-multilingual-site.md) | yes | yes | | |
| [6. Search that finds the right things](project-06-search-that-finds-the-right-things.md) | | yes | yes | |
| [7. Reading content over the REST API](project-07-reading-content-over-the-rest-api.md) | | | yes | |
| [8. Your first extension](project-08-your-first-extension.md) | | | yes | |
| [9. Roles for a restricted editor](project-09-roles-for-a-restricted-editor.md) | | yes | | |
| [10. Capstone: a complete small site](project-10-capstone-a-complete-small-site.md) | yes | yes | yes | yes |

## Courses that match the paths

If you study with a trainer, these courses cover the same ground ([Taking a course with a trainer](taking-a-course-with-a-trainer.md)):

| Path | Courses |
|---|---|
| Editor | [Editors 101](../trainers/editors-101/README.md), [Editors 201](../trainers/editors-201/README.md), [Exponential Layouts](../trainers/layouts/README.md) |
| Administrator | [Site administration](../trainers/site-administrators/README.md), [Search](../trainers/search/README.md), [Multisite and site packages](../trainers/multisite-and-packages/README.md) |
| Developer | [Developers](../trainers/developers/README.md) |
| Designer | [Theme design](../trainers/theme-designers/README.md) |

## Related pages

- [Student reading path](student-reading-path.md)
- [Study projects: overview](study-projects-overview.md)
- [Students and learners: start here](students-start-here.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
