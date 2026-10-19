---
title: "Agenda 2.3: The developers day"
audience: trainer, developer
category: training
order: 23
summary: A one-day workshop for PHP developers new to Exponential, a Git installation, the content model, a content class, templates and an override, the console, and a first extension with the RAD tools. 6 hours of sessions.
keywords: one day, developers, workshop agenda, git, content class, templates, override, console, RAD tools, extension
type: help
---
# Agenda 2.3: The developers day

Developers learn fastest by building. This day goes from a Git installation to a first extension, touching the parts
of Exponential a developer meets in the first week of a project: the content model, templates, overrides, the console
and extensions.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 2: One-day agendas](README.md) |
| Duration | 6 hours of sessions (9:00 to 16:30 with breaks and lunch) |
| Level | Intermediate |
| Audience | PHP developers, 4 to 12 learners |
| Prerequisites | PHP, Git and Composer on each laptop; a GitHub account for the optional last block |

## Learning objectives

At the end of the day, learners can:

- set up an installation from Git clones on a branch of their own;
- define a content class and create content with it;
- find which template draws a page and change it with an override they can undo;
- list the console commands and create, enable and see a first extension.

## The agenda

| Time | Block | Uses | Checkpoint |
|---|---|---|---|
| 9:00 | Welcome; Git installation (60 min) | [Develop with Git](../../../develop-with-git.md), [Getting the code](../../../../install/03-getting-the-code.md), [One installation per learner](../../train-the-trainer/module-02-building-the-lab/one-install-per-learner.md) | Site served from a clone on branch `training` |
| 10:00 | The content model (30 min) | [The content model and editing content](../../../../guides/content-model-and-editing.md), [Teaching the core concepts](../../teaching-the-core-concepts.md) | Object, node, version explained |
| 10:30 | Break (15 min) | | |
| 10:45 | A content class "Event" (60 min) | [The content model and editing content](../../../../guides/content-model-and-editing.md) | An Event created |
| 11:45 | Lunch (45 min) | | |
| 12:30 | Templates, debug and an override (80 min) | [Templates and design](../../../../guides/templates-and-design.md) | Override visible, then removed |
| 13:50 | Break (10 min) | | |
| 14:00 | The console (30 min) | [The console install, 7.1](../../../../install/07-console-install.md#71-the-console-in-brief) | `php bin/php/console list` read |
| 14:30 | A first extension with the RAD tools (60 min) | [RAD tools](../../../../features/6.0/rad-tools.md), [Extensions](../../../../guides/extensions.md) | Extension counted in **Setup > RAD** |
| 15:30 | Optional: a documentation fix as a pull request; wrap-up (60 min) | [Develop with Git](../../../develop-with-git.md) | A pull request link, or a prepared commit |
| 16:30 | End | | |

Cut items, in order: the pull request block; the console block (fold into the extension block).

## Trainer's notes

- **The clone is big** (the whole history). Prepare clones in advance on slow networks.
- **Overrides**: use the click path of the Template Editor first, then show the file it wrote
  (`settings/siteaccess/<siteaccess>/override.ini.append.php`).
- **Extensions**: after enabling, run `php bin/php/ezpgenerateautoloads.php -e` and clear the caches, as the RAD tools
  page says.
- **After the day**, point to the full Developers course and the [Extension development](../../../extension-development/README.md) book.

## Hands-on exercise: the capstone of the day

1. Create a content class "Event" with a title, a date and a text.
2. Create two events.
3. Make an override so that the full view of one event shows an extra line, check it, then remove the override.
4. Generate an extension with a RAD wizard, enable it, regenerate the autoloads, clear the caches and see it in the
   extension point survey.

## Expected result

- Two events exist; the override showed and was removed in one click.
- The new extension is listed in **Setup > RAD > Extension point survey**.

## Quiz (end of day)

1. Which command lists every console command?
2. Where does the Template Editor write an override rule?
3. What must you run after enabling a new extension with classes?
4. Why work on a branch of your own in the clone?

<details>
<summary>Answers</summary>

1. `php bin/php/console list`.
2. `settings/siteaccess/<siteaccess>/override.ini.append.php` ([Templates and design](../../../../guides/templates-and-design.md)).
3. `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches ([RAD tools](../../../../features/6.0/rad-tools.md)).
4. Updates never overwrite your own commits, and you can send changes back as pull requests.

</details>

## Further reading

- [Extension development](../../../extension-development/README.md)
- [Develop with Git](../../../develop-with-git.md)
- [Templates and design](../../../../guides/templates-and-design.md)

## Related pages

- [Module 2: One-day agendas](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
