---
title: "Project 5.3: A multilingual event site"
audience: trainer, developer
category: training
order: 53
summary: A capstone project brief for developers, a two-language event site with an Event class, custom full and line views in an extension, a listing page and translated content, built on a training installation in six to eight hours, with a rubric.
keywords: capstone, project brief, developers, multilingual, event site, content class, templates, overrides, extension, translations, rubric
type: help
---
# Project 5.3: A multilingual event site

Developers prove their skills by delivering a small feature end to end. This brief fits the end of the
[developer bootcamp](../module-03-three-day-bootcamps/developer-bootcamp.md) or the
[full-stack developer week](../module-04-five-day-bootcamps/full-stack-developer-week.md).

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 5: Capstone projects](README.md) |
| Duration | 6 to 8 hours |
| Level | Intermediate to advanced |
| Audience | Developers, alone or in pairs |
| Prerequisites | Content classes, templates and overrides, extensions ([Templates and design](../../../../guides/templates-and-design.md), [Extensions](../../../../guides/extensions.md)) |

## Learning objectives

Learners show that they can:

- model and create a content class;
- write full and line view templates and override them per class;
- package the work as an extension and enable it;
- support two content languages.

## The brief

> The invented "Lakeside Music Week" needs an event site in English and one more language. Each concert has a
> title, a date and time, a venue, a description and an image. The Concerts page lists all concerts in date order.
> Each concert has its own page. All templates and settings live in an extension `lakesidemusic`.

| Part | Requirement |
|---|---|
| Class | "Concert": title, date and time, venue, description, image |
| Views | A full view for concerts; a line view used in the Concerts list |
| Listing | The Concerts folder lists its concerts sorted by date |
| Extension | `lakesidemusic` holds the overrides, templates and settings; enabled, autoloads regenerated |
| Languages | A second content language; at least two concerts translated |

## Trainer's notes

- **Review the code** against the [coding standards](../../../../standards/coding-standards.md) and the template
  rules: output escaped with `|wash`, no logic copied between templates.
- **Check the extension boundary**: nothing outside `extension/lakesidemusic` changed except the activation.
- **Language**: a second language needs to be added to the site before translating
  ([Content languages](../../../../guides/content-languages.md)).

## Hands-on exercise

1. Create the extension skeleton (by hand or with a RAD wizard) and enable it.
2. Create the class "Concert" and four concerts.
3. Write the line and full view templates and the overrides in the extension.
4. Make the Concerts folder list concerts sorted by date.
5. Add the second content language and translate two concerts.
6. Regenerate autoloads if the extension has classes, clear caches, and test every page in both languages.
7. Present the result and the extension's file list.

## Expected result

Rubric (each criterion 0, 1 or 2 points):

| Criterion | 0 | 1 | 2 |
|---|---|---|---|
| Class | Missing fields | Complete | Complete, sensible identifiers and required flags |
| Views | Default views only | One custom view | Full and line views, escaped output |
| Listing | Unsorted or missing | Sorted, default view | Sorted by date with the line view |
| Extension boundary | Changes outside the extension | Mostly inside | Everything inside; enabled cleanly |
| Languages | One language | Second language added | Two concerts translated and shown correctly |
| Presentation | Missing | Works | Works, explained, file list shown |

A pass is 8 of 12 points; the rubric is the trainer's own and claims no official status.

## Quiz

1. Why must template output be escaped with `|wash`?
2. Where do the overrides of this project live?
3. What must exist before content can be translated into a new language?

<details>
<summary>Answers</summary>

1. To output values safely in HTML, so content cannot inject markup or scripts.
2. In the extension `lakesidemusic` (its settings and design), not in `settings/siteaccess/`.
3. The language must be added to the site ([Content languages](../../../../guides/content-languages.md)).

</details>

## Further reading

- [Extension development](../../../extension-development/README.md)
- [Templates and design](../../../../guides/templates-and-design.md)
- [Translations and languages](../../../../features/6.0/translations-and-languages.md)

## Related pages

- [Module 5: Capstone projects](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
