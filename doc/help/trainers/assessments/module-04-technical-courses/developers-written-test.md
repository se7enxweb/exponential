---
title: "Lesson 4.1: Developers, written test"
audience: trainer, developer
category: training
order: 41
summary: A fourteen-question written test for the end of the Developers course, on extensions and their loading order, autoloads, modules and views, access control, templates and overrides, Git clones with Composer, the REST API and releasing an extension, with an answer key that cites the guides. 30 minutes for learners.
keywords: assessments and practical exercises, developers, written test, extension, ActiveExtensions, autoload, module.php, view, FunctionList, override.ini, template language, wash, Git, Composer, REST API, release, tag
type: help
---
# Lesson 4.1: Developers, written test

This test closes the Developers course. It checks the knowledge a developer needs before writing code for a site:
how an extension is switched on and found, how a module view is protected, how a template is chosen, how to work with
Git clones, and how a release is made without breaking anyone's install.

> **Note**
> This is the trainer's own assessment, not an official certification. Commands are those of the guides; check them
> on the release you teach.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 4: Assessing the technical courses](README.md) |
| Duration | 45 minutes (5 briefing, 30 test, 10 going through the answers) |
| Level | Intermediate to advanced |
| Audience | Learners at the end of the Developers course |
| Prerequisites | The Developers course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- explain how an extension is activated, ordered and given classes, settings and templates;
- protect a module view with a function and a policy;
- explain the template lookup order and an override rule;
- explain why Git clones help development and how to set Composer to prefer them;
- describe a safe release of an extension.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 30 minutes.
2. Go through the answers with [Extensions](../../../../guides/extensions.md) and
   [Templates and design](../../../../guides/templates-and-design.md) on the projector.

### Common questions

**The REST question is out of scope for us.** Drop question 12 and lower the pass mark by one.

### Pitfalls

- **Learners answering from other frameworks.** "Routes in YAML" is a wrong answer here; this kernel uses
  `module.php`. Accept only what the guides say.

## The test

1. Which setting switches an extension on for the whole installation, and which for one siteaccess?
2. Two active extensions override the same template. Which one wins?
3. You added a class in your extension's `classes/` folder and get "Class not found". Which command fixes it?
4. Which two files describe an extension for **Setup > About** and **Setup > Extensions**, and what must agree between
   them?
5. What tells the kernel to look for modules in your extension?
6. Your new view has no `'functions'` entry. Who can open it?
7. In which order does the template system look for `node/view/full.tpl`?
8. In an override rule, what do `Source`, `MatchFile` and `Match[...]` mean?
9. Which command checks a template's syntax without opening a browser?
10. Why should every value a user typed be printed with `|wash`?
11. Name two things a Git clone of a package lets you do that a release archive does not.
12. Which REST API version answers writes (create, update, delete)?
13. Before tagging a release of your extension, how do you choose the number?
14. A tag you pushed points at the wrong commit. What do you do?

<details>
<summary>Answers</summary>

1. `[ExtensionSettings] ActiveExtensions[]` in `site.ini` for the installation; `ActiveAccessExtensions[]` for one
   siteaccess ([Extensions](../../../../guides/extensions.md#1-how-extensions-work-in-one-minute)).
2. The one loaded earlier; with `ExtensionOrdering=enabled` the dependencies in `extension.xml` decide the order first
   ([Extensions](../../../../guides/extensions.md#1-how-extensions-work-in-one-minute)).
3. `php bin/php/ezpgenerateautoloads.php -e`, then clear the caches and reload PHP-FPM or Velocity
   ([Extensions](../../../../guides/extensions.md#8-when-it-does-not-work)).
4. `ezinfo.php` and `extension.xml`; the version must be the same in both
   ([Extensions](../../../../guides/extensions.md#61-the-smallest-working-extension-by-hand)).
5. `ExtensionRepositories[]=<extension>` in the extension's `settings/module.ini.append.php`
   ([Extensions](../../../../guides/extensions.md#61-the-smallest-working-extension-by-hand)).
6. Anyone who can reach the site: a view without a function has no access control
   ([Extensions](../../../../guides/extensions.md#61-the-smallest-working-extension-by-hand)).
7. Matching override templates, the site design's `templates/`, the additional designs, then `standard`
   ([Templates and design](../../../../guides/templates-and-design.md#1-how-a-template-is-found)).
8. `Source`: the template replaced; `MatchFile`: your file relative to `override/templates/`; `Match[...]`: the
   conditions, all of which must hold
   ([Templates and design](../../../../guides/templates-and-design.md#4-make-a-change-with-an-override)).
9. `php bin/php/eztemplatecheck.php` with the siteaccess and the file or directory
   ([Templates and design](../../../../guides/templates-and-design.md#5-check-the-syntax-before-you-look-at-the-browser)).
10. `wash` escapes HTML, so typed text cannot inject markup or scripts
    ([Templates and design](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)).
11. Two of: see every change with `git diff`, find why a line is there with `git log`/`git blame`, find a breaking
    change with `git bisect`, use a fix before release, keep own changes on a branch, send a pull request
    ([Develop with Git](../../../develop-with-git.md#why-git-clones-and-not-archives)).
12. v2 only; reads answer at v1 and v2
    ([REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md#in-short)).
13. From what is already published: `git fetch --tags`, then list tags sorted by version
    (`--sort=version:refname`), never by name ([Extensions](../../../../guides/extensions.md#7-release-it)).
14. Leave it; publish the next version with the fix and say in its notes what was wrong. Never move or delete a pushed
    tag ([Extensions](../../../../guides/extensions.md#7-release-it)).

</details>

## Scoring

- One point per question; questions 1, 4 and 8 need every part for the point (half a point otherwise).
- Pass: 10 of 14 points or more, with questions 6 and 14 correct: one is security, the other cannot be undone.

## Further reading

- [Extensions](../../../../guides/extensions.md): activation, building, releasing.
- [Templates and design](../../../../guides/templates-and-design.md): lookup, overrides, the language.
- [Develop with Git](../../../develop-with-git.md) and [REST API v1 and v2](../../../../guides/rest-api-v1-and-v2.md).
- [Extension development](../../../extension-development/README.md): the book behind the course.

## Related pages

- [Lesson 4.2: Developers, practical task](developers-practical-task.md)
- [Module 4: Assessing the technical courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
