---
title: "Lesson 13.1: The RAD tools"
audience: trainer, developer
category: training
order: 131
summary: Learners use Setup > RAD to find the right extension point, generate a small extension with a wizard and read every file it writes, read the extension point survey, and check classes with the health check and checkclasses.php. 45 minutes.
keywords: developers course, RAD, Rapid Application Development Tools, setup/rad, extension points, catalogue, wizard, settings extension, template operator wizard, extension point survey, radsurvey, health check, checkclasses.php
type: help
---
# Lesson 13.1: The RAD tools

After twelve modules learners have written most kinds of extension code by hand. The RAD tools write the same code
in minutes, and they are also the map of every place the kernel can be extended. In this lesson learners use the
catalogue to choose an extension point, let a wizard write an extension, read what it wrote, and use the survey and
health check to see what an installation really exposes.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 13: RAD, Velocity and contributing](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Module 7](../module-07-extensions/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- open **Setup** > **RAD** and find an extension point in its seven groups;
- generate an extension with a wizard and read the list of files before anything is written;
- switch the generated extension on and check it;
- read the **Extension point survey** and the health check findings;
- run `php bin/php/checkclasses.php` to find classes PHP refuses to load.

## Trainer's notes

### How to present it

Source: [RAD tools: extension wizards, catalogue, survey and health check](../../../../features/6.0/rad-tools.md).

1. **The catalogue** (4 minutes). **Setup** > **RAD** (`/setup/rad`, heading "Rapid Application Development
   Tools") lists the extension points in groups: Content, Templates and design, Modules and views, Workflow, events
   and jobs, Storage and infrastructure, Packaging and shop, Users, access and language. Each entry says how it is
   registered and links its wizard. Count the entries on your installation; the number grows with the kernel.
2. **A wizard** (4 minutes). Each wizard asks for a name and what to generate, then lists every file it would write
   under `extension/<name>`, before writing any. It never writes outside the extension folder and never overwrites
   an existing extension.
3. **After writing** (2 minutes). Activate the extension, run `php bin/php/ezpgenerateautoloads.php -e`, clear the
   caches.
4. **The survey** (2 minutes). **Extension point survey** (`/setup/radsurvey`) reads off disk what this installation
   exposes: settings that name a class, template operators, module views, events, overrides.
5. **Health** (3 minutes). The health check reports settings that name a class that cannot be loaded, directories
   searched and missing, views without a script, datatypes offered and not found. `checkclasses.php` loads every
   declared class and lists the ones PHP refuses.

### Common questions

**Should I always use a wizard?** Use it to start fast and to see the conventions; understand every file it wrote,
as in the earlier modules, before you change it.

**Who may use the RAD tools?** Users with the `setup/setup` policy.

**Is generated code safe?** Generated templates escape what they print; the wizards follow
[RAD tools security](../../../../bc/6.0/rad-security.md).

### Pitfalls

- **Generating on a live site.** Wizards write files; use them on development installations only.
- **A name that exists.** The wizard refuses; choose another name rather than deleting the old extension.
- **Forgetting the autoloads.** A generated class is not found until `-e` has run.

## Hands-on exercise

On the training installation, signed in to the administration as an administrator.

1. Open **Setup** > **RAD** and read the group headings. Count the entries of **Templates and design**.
2. Find the entries you built by hand in this course: **Template operator**, **Template fetch function**,
   **Module**, **Datatype**, **Workflow event type**, **REST provider**.
3. Choose **Template operator**. Enter the extension name `mysite_wizard` and an operator name of your choice.
4. Read the list of files the wizard would write; compare it with your `mysite_hello/autoloads/`.
5. Write the extension, then in a shell: `find extension/mysite_wizard -type f | sort`.
6. Activate it, regenerate the autoloads and clear the caches:

   ```bash
   php bin/php/console exp:ini add site.ini/ExtensionSettings/ActiveExtensions mysite_wizard global
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   ```

7. Use the generated operator in your playground template.
8. Open **Extension point survey** from the RAD page and find your operator in the list of template operators.
9. Run `php bin/php/checkclasses.php --quiet-ok`.

## Expected result

- Step 4: the file list with the operator class, `autoloads/eztemplateautoload.php`, the settings, `ezinfo.php`,
  `extension.xml` and the license, before anything is written.
- Step 7: the operator works in the playground.
- Step 8: the survey lists the new operator.
- Step 9: no output when every class loads.
- The trainer asks each learner to name one file the wizard wrote that their hand-written version lacks.

## Quiz

1. Where are the RAD tools?
2. What does a wizard show before it writes?
   A. Nothing B. The list of every file it would write C. Only the class name D. A diff of the kernel
3. What must happen after a generated extension is written?
4. What does the extension point survey read?
5. Which command lists classes PHP refuses to load?

<details>
<summary>Answers</summary>

1. **Setup** > **RAD** (`/setup/rad`).
2. **B.** Every file it would write, before writing any.
3. Activate it, run `php bin/php/ezpgenerateautoloads.php -e` and clear the caches.
4. What the installation exposes, read off disk on every request: settings that name a class, operators, views,
   events, overrides.
5. `php bin/php/checkclasses.php`.

</details>

## Further reading

- [RAD tools](../../../../features/6.0/rad-tools.md).
- [Extension points](../../../../bc/6.0/rad-extension-points.md) and [The extension surface](../../../../bc/6.0/rad-extension-surface.md).
- [RAD tools security](../../../../bc/6.0/rad-security.md).

## Related pages

- [Module 13: RAD, Velocity and contributing](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
