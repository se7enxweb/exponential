---
title: "Project 8: your first extension"
audience: student
category: learning
order: 32
summary: Study project 8 - build a small extension by hand following the extensions guide (ezinfo.php, extension.xml, module and design settings, a module with one view and a template), switch it on, protect the view with a policy, compare it with what the RAD wizard generates, keep it in its own Git repository, and hand it in, graded with a rubric.
keywords: study project, extension, module, view, template, ezinfo.php, extension.xml, module.ini, design.ini, ActiveExtensions, policy, RAD, autoloads, php -l, learner
type: help
---
# Project 8: your first extension

Extensions are how Exponential is extended without changing the kernel. In this project you build the smallest
working extension, a page that greets a visitor by name, then make it safe and keep it in Git. The steps follow
[Extensions, section 6](../../guides/extensions.md#6-build-your-own-extension).

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Intermediate |
| Time | About 4 hours |
| Paths | Developer |
| Prerequisites | PHP basics; a terminal; [project 3](project-03-a-template-override.md); [Using Git as a student](using-git-as-a-student.md) |

## Goal

An extension with your own name (for example `studyhello`) that serves `/hello/world/<name>`, shows the name escaped
inside your site's layout, is listed under **Setup > Extensions** with its version, is protected by a policy, and
lives in its own Git repository.

## Before you start

- Read [Extensions](../../guides/extensions.md), sections 1, 4 and 6.1, and
  [What an extension is](../extension-development/part-1-from-user-to-developer/what-an-extension-is.md).
- Pick a name that is yours: lower case, letters, digits and underscores. The guide uses `myhello`.

## Tasks

1. **Create the folders** `extension/<name>/settings`, `modules/hello` and `design/standard/templates/hello`.
2. **Write `ezinfo.php` and `extension.xml`** with the same version (`0.1.0`), your name in the copyright line, and
   "GNU General Public License v2.0 (or any later version)" as the license.
3. **Write the settings**: `settings/module.ini.append.php` with `ExtensionRepositories[]=<name>` under
   `[ModuleSettings]`, and `settings/design.ini.append.php` with `DesignExtensions[]=<name>` under
   `[ExtensionSettings]`.
4. **Write the module and the view**: `modules/hello/module.php` with one view `world` that takes the parameter
   `Name`, and `modules/hello/world.php` that passes the name to the template and returns `$Result`.
5. **Write the template** `design/standard/templates/hello/world.tpl`, printing the name with `{$name|wash}`.
6. **Check the PHP**: `php -l` on each PHP file. Expected: `No syntax errors detected in <file>`.
7. **Switch it on** and refresh:

   ```bash
   php bin/php/console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' <name> global --allow-root-user
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

8. **Open** `/hello/world/Ada` on your site. Expected: **Hello, Ada** inside your site's layout, and
   **Setup > Extensions** lists your extension with version 0.1.0.
9. **Try to break it.** Open `/hello/world/<b>Ada</b>` (URL encoded if needed). The name must be shown as text, not
   as bold markup. Explain why.
10. **Protect the view.** A view without a `'functions'` entry has no access control: anyone who can reach the site
    can open it. Declare a function (`$FunctionList['read'] = array();` in `module.php`, `'functions' => array( 'read' )`
    on the view) and grant it in **Users > Roles and policies** to a role. Check as anonymous visitor and as a user
    with the role ([Extensions](../../guides/extensions.md#61-the-smallest-working-extension-by-hand)).
11. **Commit** the extension to its own Git repository, with a commit subject such as `Added: The hello world view`.

## Hand in

- The repository (or an archive of it) with every file of the extension.
- Screenshots: the greeting page; **Setup > Extensions** with your extension; the denied page for a visitor without
  the policy.
- The output of `php -l` for each PHP file.
- A short report: what each file does, and why the policy matters.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (8):

1. The extension name is the learner's own, lower case.
2. `ezinfo.php` and `extension.xml` carry the same version and the license.
3. Every PHP file passes `php -l`.
4. The extension was switched on with a settings override, not by editing `settings/site.ini`.
5. The greeting works and the name is escaped.
6. The view is protected by a function and a policy, shown for both cases.
7. The extension is in its own Git repository with readable commit subjects.
8. No kernel file was changed.

## Stretch goals

- Generate a module with the RAD wizard (`/setup/moduleextension`) and compare its files with yours
  ([Extensions](../../guides/extensions.md#62-let-the-rad-tools-write-it)). Note: the wizards list every file before
  writing and never overwrite an existing extension.
- Add a setting of your own in `settings/<name>.ini` and read it with `eZINI::instance( '<name>.ini' )`
  ([Extensions](../../guides/extensions.md#64-settings-content-and-cronjobs-of-your-own)).
- Add a translation of the greeting ([Extensions, section 6.4](../../guides/extensions.md#64-settings-content-and-cronjobs-of-your-own)).
- Publish it on GitHub as your own project ([Contribute extensions back](../contribute-extensions-back.md)). Your own
  extension is not covered by the CLA ([The CLA for students](the-cla-for-students.md)).

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Extensions](../../guides/extensions.md)
- [Learning path: developer](learning-path-developer.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
