---
title: "Lesson 1.1: The developer's map of an installation"
audience: trainer, developer
category: training
order: 11
summary: Learners find their way around the folders of an installation, tell the kernel from extensions, and follow the two rules every later lesson builds on. 45 minutes.
keywords: developers course, kernel, extension, folder map, var, settings, design, bin/php, autoload, read the kernel, extension developer
type: help
---
# Lesson 1.1: The developer's map of an installation

Before learners write a line of code, they need to know where things live and which folders are theirs. This lesson
walks through the top-level folders of an installation, sets the two rules the whole course follows, and shows how
a page address leads to the code that makes it.

> **Note**
> Use a development or training installation for every exercise in this course, never a live site.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 1: The development installation](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Beginner |
| Audience | PHP developers new to Exponential |
| Prerequisites | A shell on a machine with an installation; basic PHP and Git |

## Learning objectives

After this lesson, learners can:

- name the purpose of `kernel/`, `lib/`, `settings/`, `design/`, `extension/`, `var/`, `bin/php/` and `autoload/`;
- state the two rules: read the kernel, do not edit it; your work lives in `extension/`;
- find the module and the view behind an administration address such as `/content/view/full/2`;
- find the file of a kernel class through the autoload map.

## Trainer's notes

### How to present it

Source: [Reading the code you already have](../../../extension-development/part-1-from-user-to-developer/reading-the-code-you-have.md)
and [What an extension is](../../../extension-development/part-1-from-user-to-developer/what-an-extension-is.md).

1. **History in one sentence** (2 minutes). Exponential continues the code base of eZ Publish, which its creator
   discontinued after almost 18 years. Kernel identifiers keep their historical names (`eZINI`,
   `eZContentObject`, `ezinfo.php`); new code of learners uses their own prefix, never `ez`.
2. **The map** (8 minutes). Run `ls` in the installation root on the projector and go through the table below.
   Stress the third column.

   | Folder | What is in it | Do you change it? |
   |---|---|---|
   | `kernel/` | One folder per kernel module and `kernel/classes/`, the content model | Never. Read it. |
   | `lib/` | The libraries: `ezdb/`, `eztemplate/`, `ezutils/`, `ezfile/` ... | Never. Read it. |
   | `settings/` | Default `.ini` files; `settings/override/` and `settings/siteaccess/<name>/` hold this installation's own | Only through overrides, siteaccess settings or extensions |
   | `design/` | The kernel's designs: `standard`, `admin4`, `editor` ... | Never. Override in your own design extension |
   | `extension/` | Every extension, one folder each | Your own extensions, yes |
   | `var/` | Caches, compiled templates, logs, uploaded files | Never by hand, except to read logs |
   | `bin/php/` | Command line scripts, run by name through the console | Never. Yours go in `extension/<yours>/bin/php/` |
   | `autoload/` | Generated class maps | Never; regenerate them |
   | `tests/` | The kernel's test suites | Read them: they are executable examples |
   | `doc/` | The documentation | Improve it with a pull request |

3. **The two rules** (3 minutes). The kernel is replaced at every upgrade, so edits there are lost. An extension
   plugs into places the kernel was built to look: settings, directories, the design chain and events.
4. **From an address to the code** (5 minutes). `/content/view/full/2` is the module `content`, the view `view`,
   with the parameters `full` and `2`. Show `grep -n "ViewList\['view'\]" -A6 kernel/content/module.php`.
5. **From a class name to its file** (2 minutes). `grep -n "'eZContentObjectTreeNode'" autoload/ezp_kernel.php`.

### Common questions

**Why can I not just fix a bug in `kernel/`?** You can, as a pull request to the project (lesson 13.3). On your own
site the change would be lost at the next update; carry it in an extension, or get it into the kernel.

**Where do uploaded images go?** Below `var/` (for example `var/<site>/storage`). Never edit them by hand.

**Is `vendor/` part of the kernel?** No. It holds Composer libraries such as the Zeta Components. Do not change it
either.

### Pitfalls

- **Editing a file in `design/standard/`.** It works until the next update. Point learners at overrides
  (module 3) from the start.
- **Searching `var/` for code.** Compiled templates in `var/` look like PHP and confuse beginners. Exclude `var/`
  from searches: `grep -rn --exclude-dir=var ...`.
- **Old tutorials.** Many tutorials on the web were written for older releases. Check every name they use against
  the code of the installation.

## Hands-on exercise

Use your development installation from the root folder (the one that holds `index.php`).

1. List the top-level folders: `ls`.
2. Count the extensions on disk: `ls extension/ | wc -l`.
3. Show the definition of the content module's `view` view:

   ```bash
   grep -n "ViewList\['view'\]" -A6 kernel/content/module.php
   ```

4. Write down the name of the script the view runs.
5. Find the file of the class `eZContentObjectTreeNode` through the autoload map:

   ```bash
   grep -n "'eZContentObjectTreeNode'" autoload/ezp_kernel.php
   ```

6. Find the line where `fetchByNodeID` is defined in that file:

   ```bash
   grep -n "function fetchByNodeID" kernel/classes/ezcontentobjecttreenode.php
   ```

7. Show the last three commits that changed the file: `git log --oneline -3 -- kernel/classes/ezcontentobjecttreenode.php`.
8. Pick one page of the administration you know. Write down its module, its view and its `module.php` file.

## Expected result

- Step 3 prints the `$ViewList['view']` entry with `'script' => 'view.php'` and its parameters.
- Step 5 prints one line mapping the class to `kernel/classes/ezcontentobjecttreenode.php`.
- Step 8: every learner has three paths written down. The trainer checks two learners' paths on the projector by
  opening the files.

## Quiz

1. Which folder holds the work of a developer of a site?
   A. `kernel/` B. `design/standard/` C. `extension/` D. `var/`
2. What do the parts of `/content/view/full/2` mean?
3. True or false: the files in `autoload/` are written by hand.
4. Why should a search for code exclude `var/`?
5. Which prefix may the class names of a learner's new extension not use?

<details>
<summary>Answers</summary>

1. **C.** Your own extensions live in `extension/`; the kernel is replaced at every upgrade.
2. Module `content`, view `view`, parameters `full` and `2` (a view mode and a node ID).
3. **False.** They are generated with `php bin/php/ezpgenerateautoloads.php`.
4. It holds caches and compiled templates that look like code but are generated.
5. `ez`: it belongs to the kernel's existing classes. Use `exp` or your own prefix.

</details>

## Further reading

- [Reading the code you already have](../../../extension-development/part-1-from-user-to-developer/reading-the-code-you-have.md):
  the four techniques for finding code.
- [What an extension is](../../../extension-development/part-1-from-user-to-developer/what-an-extension-is.md): the
  folders inside an extension and the switch that turns it on.
- [The ladder from user to developer](../../../extension-development/part-1-from-user-to-developer/the-ladder-from-user-to-developer.md):
  where this course fits.

## Related pages

- [Module 1: The development installation](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
