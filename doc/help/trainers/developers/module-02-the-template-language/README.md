---
title: "Module 2: The template language"
audience: trainer, developer
category: training
order: 20
summary: The second module of the Developers course. Learners build a template playground and learn variables, conditions, loops, operators, wash, translations, the URL operators, include and the view functions. 5 hours.
keywords: developers course, module 2, template language, tpl, def, set, if, foreach, operators, wash, i18n, ezurl, ezdesign, include, node_view_gui, attribute_view_gui
type: help
---
# Module 2: The template language

Everything a visitor sees passes through templates. This module teaches the template language from the first
variable to the functions that assemble a page. Learners try every expression in a playground that renders a
template on the command line, so nothing on a site changes until module 3 puts templates into a design.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 5 hours (with two 10-minute breaks) |
| Lessons | 6 |
| Level | Beginner to intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 2.1: Variables and output](variables-and-output.md) | 50 minutes | Build the playground; `def`, `set`, `undef`, arrays, hashes, `wash` |
| [Lesson 2.2: Conditions and logic](conditions-and-logic.md) | 45 minutes | `if`, comparisons, `and`/`or`/`not`, `cond`, `first_set`, `switch` |
| [Lesson 2.3: Loops with foreach](loops.md) | 45 minutes | Keys, delimiters, sequences, offset, max, reverse, break |
| [Lesson 2.4: Operators, wash and translations](operators-wash-and-translations.md) | 50 minutes | Chain operators, wash types, dates, `i18n`, `attribute( 'show' )` |
| [Lesson 2.5: Links and design files](links-and-design-files.md) | 40 minutes | `ezurl` forms, `ezroot`, `ezdesign`, `ezimage` |
| [Lesson 2.6: include, node_view_gui and attribute_view_gui](include-and-view-functions.md) | 50 minutes | Include with parameters, view modes, fields with their datatype templates |

## Preparing this module

- Every learner has `extension/mysite_hello` from lesson 1.4 and a working `ezexec.php` run.
- The training installation has content below node 2: a few folders and articles, so that `$node.children` and
  `data_map` show something. A fresh `exp:install` with the default package is enough.
- Prepare a printed or projected cheat sheet of the syntax in
  [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes).
- Rehearse each exercise in the playground; outputs depend on the content of your training installation, so note
  the expected names and IDs for your own installation.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 2.1 | 50 |
| Lesson 2.2 | 45 |
| Break | 10 |
| Lesson 2.3 | 45 |
| Lesson 2.4 | 50 |
| Break | 10 |
| Lesson 2.5 | 40 |
| Lesson 2.6 | 50 |
| **Total** | **300** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
