---
title: "Bootcamp 3.3: The three-day developer bootcamp"
audience: trainer, developer
category: training
order: 33
summary: Three days for PHP developers, day 1 Git installation, content model and classes, day 2 the template language, overrides and debugging, day 3 extensions, the console, INI settings, Velocity and a pull request.
keywords: three days, bootcamp, developers, git, content classes, templates, overrides, extensions, console, ini, velocity, pull request
type: help
---
# Bootcamp 3.3: The three-day developer bootcamp

Three days are enough to build a small site end to end: content classes, templates, overrides and an extension, on
an installation made of Git clones. The bootcamp ends with each developer able to start a real project.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 3: Three-day bootcamps](README.md) |
| Duration | 3 days, 6 hours of sessions each |
| Level | Intermediate |
| Audience | PHP developers, 4 to 12 learners |
| Prerequisites | PHP, Git, Composer on each laptop; a GitHub account |

## Learning objectives

At the end of the bootcamp, learners can:

- work in an installation of Git clones on a branch of their own and keep it updated;
- design content classes for a site;
- write templates with the template language and override them per class or node;
- build, enable and debug an extension, with settings of its own;
- deploy a PHP change under Velocity and send a change upstream as a pull request.

## The agenda

| Day | Morning (3 h) | Afternoon (3 h) |
|---|---|---|
| 1 | Git installation ([Develop with Git](../../../develop-with-git.md)); core concepts ([Teaching the core concepts](../../teaching-the-core-concepts.md)) | Content classes and content ([The content model and editing content](../../../../guides/content-model-and-editing.md), [Class groups](../../../../guides/class-groups.md)) |
| 2 | The template language ([Templates and design, 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)) | Overrides and debugging ([Templates and design, 4 and 7](../../../../guides/templates-and-design.md#4-make-a-change-with-an-override)) |
| 3 | Extensions and the RAD tools ([Extensions](../../../../guides/extensions.md), [RAD tools](../../../../features/6.0/rad-tools.md)); INI ([exp:ini](../../../../features/6.0/exp-ini-command.md)) | The console; Velocity and deploying ([Serving the site, 8.3.14](../../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)); a pull request |

Breaks: 15 minutes mid-morning and mid-afternoon, 45 minutes lunch, each day.

## Trainer's notes

- **The capstone runs through all three days**: an "Events" site section, built a bit more each afternoon.
- **Template syntax**: check templates with the syntax check of
  [Templates and design, 5](../../../../guides/templates-and-design.md#5-check-the-syntax-before-you-look-at-the-browser)
  before opening the browser.
- **Velocity**: a changed class reaches Velocity's workers only after a restart or deploy; teach it once with a
  visible example.

## Hands-on exercise: the bootcamp capstone

1. Day 1: a content class "Event" (title, date, location, text) and five events in a folder "Events".
2. Day 2: a full view override for events and a line view used in the folder's list.
3. Day 3: an extension `bikeevents` (made with a RAD wizard) that carries the override and its settings; enabled,
   autoloads regenerated, caches cleared; a small documentation fix sent as a pull request to a fork.

## Expected result

- The Events folder lists five events with the custom line view; each event shows the custom full view.
- The override lives in the extension, not in `settings/siteaccess/`.
- A pull request link (to a fork, or the project when the change is real).

## Quiz (end of bootcamp)

1. Where should a project's overrides live: the siteaccess settings or an extension?
2. What do you run after adding a class to an extension?
3. Why does a PHP change not show under Velocity at once?
4. What does `|wash` do in a template?

<details>
<summary>Answers</summary>

1. In an extension, so they travel with the code and are under version control.
2. `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches.
3. Velocity's workers keep the application in memory; restart or deploy
   ([Serving the site, 8.3.14](../../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)).
4. It escapes a value for safe output in HTML.

</details>

## Further reading

- [Extension development](../../../extension-development/README.md)
- [Templates and design](../../../../guides/templates-and-design.md)
- [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)

## Related pages

- [Module 3: Three-day bootcamps](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
