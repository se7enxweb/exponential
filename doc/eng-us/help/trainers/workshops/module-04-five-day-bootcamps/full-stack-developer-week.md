---
title: "Bootcamp 4.1: The full-stack developer week"
audience: trainer, developer
category: training
order: 41
summary: Five days that prepare developers to lead an Exponential project, installation and Git, content modeling, templates and designs, extensions and modules, operations and Velocity, with a site built across the week and reviewed on day 5.
keywords: five days, bootcamp, developer week, full stack, content modeling, templates, designs, extensions, modules, velocity, deploy, tests, capstone
type: help
---
# Bootcamp 4.1: The full-stack developer week

A week is enough to take developers from their first install to a reviewed, deployable site with an extension of
their own. Each day ends with the capstone a step further; day 5 is review, tests and deployment.

## At a glance

| | |
|---|---|
| Course | [Workshops and bootcamps](../README.md) |
| Module | [Module 4: Five-day bootcamps](README.md) |
| Duration | 5 days, 6 hours of sessions each |
| Level | Intermediate to advanced |
| Audience | PHP developers who will lead or build projects, 4 to 10 learners |
| Prerequisites | PHP, Git, Composer; a GitHub account |

## Learning objectives

At the end of the week, learners can:

- set up, update and deploy an installation built from Git clones;
- model content for a real brief and build it as classes;
- write a design with templates, overrides and stylesheets through the packer;
- write an extension with a module view, settings and a template operator, and test it;
- run the site under Velocity and deploy a change safely.

## The agenda

| Day | Morning (3 h) | Afternoon (3 h) | Capstone step |
|---|---|---|---|
| 1 | Install from Git, update routine ([Develop with Git](../../../develop-with-git.md), [Update with Git](../../../update-with-git.md)) | Core concepts and content modeling ([The content model and editing content](../../../../guides/content-model-and-editing.md)) | The brief turned into classes |
| 2 | Template language, debugging ([Templates and design](../../../../guides/templates-and-design.md)) | Designs, overrides, pagelayout ([Template editor overrides](../../../../features/6.0/template-editor-overrides.md)) | Full and line views of every class |
| 3 | Extensions, RAD tools, autoloads ([Extensions](../../../../guides/extensions.md), [RAD tools](../../../../features/6.0/rad-tools.md)) | Modules and views ([Extension module override](../../../../features/6.0/extension-module-override.md)) | The project's extension with one module view |
| 4 | INI and siteaccesses ([exp:ini](../../../../features/6.0/exp-ini-command.md), [Multi-site INI overrides](../../../../features/6.0/multi-site-ini-overrides.md)), REST ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md)) | Operations: caches, cronjobs, Velocity ([Serving the site](../../../../install/08-serving-the-site.md)) | Settings in the extension; served by Velocity |
| 5 | Tests and quality ([Quality checks](../../../../features/6.0/quality-checks.md)); deploying ([Deploying](../../../../guides/deploying.md)) | Code review of the capstones; presentations; next steps | Reviewed and deployed |

Breaks: 15 minutes mid-morning and mid-afternoon, 45 minutes lunch, each day.

## Trainer's notes

- **One brief for the whole week**, for example a club, a small conference or a library.
- **Code review on day 5**: pairs review each other's extension against the
  [coding standards](../../../../standards/coding-standards.md).
- **Keep the kernel untouched**: all project code in the extension and the design.

## Hands-on exercise: the week's capstone

Brief: a small conference site with talks, speakers and a schedule.

1. Classes: Talk (title, abstract, time, room, speaker relation), Speaker (name, bio, photo).
2. Views: a schedule page listing talks by time; a speaker page listing their talks.
3. Extension `conference` with the overrides, the design, a module view `conference/schedule`, and its settings.
4. Served under Velocity; a change deployed with `php bin/php/console exp:velocity deploy`.

## Expected result

- The schedule and speaker pages work from the extension, with no file changed outside it except the activation.
- The deploy runs without errors and the change is visible.
- Each team presents and gets a review.

## Quiz (end of week)

1. Where does a project's code belong, and why?
2. Which command deploys a PHP change under Velocity in one step?
3. Name two places a setting's value can come from.
4. Why prefer a relation attribute for the speaker of a talk?

<details>
<summary>Answers</summary>

1. In the project's extensions and design, so updates of the kernel never overwrite it.
2. `php bin/php/console exp:velocity deploy` ([Serving the site, 8.3.14](../../../../install/08-serving-the-site.md#8314-deploying-a-change-expvelocity-deploy)).
3. For example the defaults, an extension, the siteaccess, the override directory ([glossary: INI file](../../../../glossary.md)).
4. The speaker is content of its own; the relation keeps one speaker object for many talks.

</details>

## Further reading

- [Extension development](../../../extension-development/README.md)
- [Coding standards](../../../../standards/coding-standards.md)

## Related pages

- [Module 4: Five-day bootcamps](README.md)
- [Workshops and bootcamps: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
