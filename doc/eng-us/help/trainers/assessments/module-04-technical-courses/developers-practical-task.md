---
title: "Lesson 4.2: Developers, practical task"
audience: trainer, developer
category: training
order: 42
summary: A 75-minute practical task for the end of the Developers course, build a small extension with a protected module view and a template, activate it, prove the access control with a test user, add a template override, check syntax and commit the work to a local Git repository with matching version fields, scored with a six-criterion rubric. 105 minutes with scoring.
keywords: assessments and practical exercises, developers, practical task, rubric, extension, module.php, view, FunctionList, policy, template, wash, override, eztemplatecheck, php -l, ezinfo.php, extension.xml, git commit
type: help
---
# Lesson 4.2: Developers, practical task

The task is a small, complete extension: a page that greets a visitor, protected by a policy, with its own template,
and kept in Git with correct metadata. It covers the developer's daily loop: write, check, activate, clear, test.

> **Note**
> This is the trainer's own assessment, not an official certification. Each learner works on their own training
> installation (a Git clone, as in the Developers course). Nothing is pushed anywhere and no tag is made: a pushed tag
> is permanent.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 4: Assessing the technical courses](README.md) |
| Duration | 105 minutes (5 briefing, 75 task, 25 scoring) |
| Level | Intermediate to advanced |
| Audience | Learners at the end of the Developers course |
| Prerequisites | [Lesson 4.1](developers-written-test.md); a shell, an editor and Git on the learner's own training installation |

## Learning objectives

Learners who pass this task have shown that they can:

- write an extension with metadata, settings, a module, a view and a template;
- protect a view with a function and grant it with a role;
- activate the extension and make the system see it (autoloads, caches);
- write a template override for one object and check template syntax;
- keep the work in Git with consistent version fields.

## Trainer's notes

### How to run it

1. **Before**: each learner has a training installation from a Git clone, served on a local port, signed in as
   administrator, and a terminal. The guide [Extensions, section 6.1](../../../../guides/extensions.md#61-the-smallest-working-extension-by-hand)
   may be open: it shows a similar but simpler extension.
2. **Briefing**: hand out the steps and the rubric; 75 minutes. Learners name the extension `<account>greet` (lower
   case, letters, digits and underscores).
3. **Scoring**: read the files, run `php -l` on them, open the page in a private window and as the test user, and
   read `git log` in the extension.

### Common questions

**May learners copy the guide's code?** Yes, and adapt it: the task differs (a policy, an override, Git). Copying
without adapting fails steps 4 and 6.

**The page is empty after activation.** A new template needs the caches cleared; see
[Extensions, section 8](../../../../guides/extensions.md#8-when-it-does-not-work). That is part of the task.

### Pitfalls

- **Velocity workers.** If the site runs on Velocity, new classes and settings need a restart or deploy; tell learners
  which server their lab uses.
- **A shared installation.** Two learners activating extensions on one installation get in each other's way. One
  installation per learner.

## Hands-on exercise

The task, as handed to learners. Work in the installation root of your own training installation.

1. Create `extension/<account>greet/` with `ezinfo.php` and `extension.xml`, both with version `0.1.0`, your name in
   the copyright line, and the license "GNU General Public License v2.0 (or any later version)".
2. Add `settings/module.ini.append.php` and `settings/design.ini.append.php` so the kernel finds your module and your
   design.
3. Write a module `greet` with a view `hello` that takes a parameter `Name` and shows it through the template
   `design/standard/templates/greet/hello.tpl`. Print the name with `|wash`.
4. Protect the view: declare a function `read` in `module.php` and require it on the view.
5. Check every PHP file with `php -l`, and the template with
   `php bin/php/eztemplatecheck.php -s<public siteaccess> <path to hello.tpl> --allow-root-user`.
6. Activate the extension (browser or `exp:ini add`), regenerate the autoloads and clear the caches. Reload the server
   process if your lab needs it.
7. Open `/greet/hello/Ada` in a private window as an anonymous visitor. Note what you see.
8. Create a role `Greet readers` with the policy `greet` `read`, assign it to the Anonymous Users group, and open the
   page again. Note what you see. Then try `/greet/hello/<script>alert(1)</script>` and note what the page prints.
9. Write an override for one article of the site (by object id) of `node/view/full.tpl` in your extension's design that
   adds a line `Override from <account>greet` above the content. Add the rule in your extension's
   `settings/override.ini.append.php`, clear the override and template caches, and check the article.
10. In `extension/<account>greet/`, run `git init`, add your files and commit with the message
    `Added: Greeting page with access control`.
11. Raise the version to `0.1.1` in both `ezinfo.php` and `extension.xml`, check both files, and commit with
    `Updated: Version 0.1.1`. Do not tag and do not push.

## Expected result

- Step 7: access is refused for the anonymous visitor. Step 8: the page shows **Hello, Ada**; the script text is shown
  as text, not run.
- The article shows the override line; the other articles do not.
- `php -l` reports no syntax errors; `eztemplatecheck.php` reports the template valid.
- **Setup > Extensions** lists the extension with version 0.1.1 after a cache clear.
- `git log` shows two commits with the two messages; both version fields say `0.1.1`; no tag exists.

## Scoring rubric

| Criterion | 0: Not shown | 1: Partly | 2: Mostly | 3: Fully | Evidence |
|---|---|---|---|---|---|
| Extension structure and metadata | Missing files | Files present, metadata inconsistent | Consistent, license or copyright missing | Both files consistent and complete | The files |
| Module, view and template | Page does not work | Page works without the parameter | Works, name not washed | Works, name washed | `/greet/hello/Ada` and the script test |
| Access control (must) | View unprotected | Function declared, not required | Required, role not created or misassigned | Refused before, allowed after the role | Steps 7 and 8, the role page |
| Activation and checks | Not activated | Activated, caches or autoloads skipped | Working, checks not run | Activated, `php -l` and template check clean | Terminal history, **Setup > Extensions** |
| Override | None | File without rule, or rule without file | Works, but on every article | Works for the one object only | The article and another article |
| Git and versions | No repository | One commit, message without prefix | Two commits, versions disagree | Two commits with prefixes, both fields 0.1.1, no tag | `git log`, the files |

**Pass**: 12 of 18 points or more and **Access control** at 2 or 3.

## Quiz

Debrief questions to ask after scoring.

1. Why did step 7 refuse access, and what would have happened without step 4?
2. What did `|wash` do in the script test?
3. Why are you told not to tag in this task?
4. Your override shows on every article. What is wrong in the rule?

<details>
<summary>Answers</summary>

1. The view requires the function `read` and no role granted it; without it anyone could open the view
   ([Extensions](../../../../guides/extensions.md#61-the-smallest-working-extension-by-hand)).
2. It escaped the HTML, so the script was printed as text
   ([Templates and design](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes)).
3. A pushed tag is permanent; releases are chosen from what is published and tagged after the version commit
   ([Extensions](../../../../guides/extensions.md#7-release-it)).
4. The `Match[object]` condition is missing or wrong
   ([Templates and design](../../../../guides/templates-and-design.md#4-make-a-change-with-an-override)).

</details>

## Further reading

- [Extensions](../../../../guides/extensions.md): the smallest extension, activation, release.
- [Templates and design](../../../../guides/templates-and-design.md): overrides and template checks.
- [Develop with Git](../../../develop-with-git.md): everyday work with clones.

## Related pages

- [Lesson 4.1: Developers, written test](developers-written-test.md)
- [Module 4: Assessing the technical courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
