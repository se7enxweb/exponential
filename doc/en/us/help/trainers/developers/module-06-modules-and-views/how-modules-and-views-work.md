---
title: "Lesson 6.1: How modules and views work"
audience: trainer, developer
category: training
order: 61
summary: Learners read a module.php, map an address to its module, view, ordered and unordered parameters, follow a view to its script or class, and see how the result reaches the pagelayout. 45 minutes.
keywords: developers course, module, view, module.php, ViewList, params, unordered_params, script, functions, default_navigation_part, single_post_actions, Result, pagelayout, ModuleView, kernel/private/classes/views
type: help
---
# Lesson 6.1: How modules and views work

Every page that is not a content page is a module view: `/user/login`, `/setup/cache`, `/content/edit/12`. Even
content pages are the view `content/view`. This lesson explains how an address becomes a call to a view, what a
`module.php` declares, and how a view hands its HTML to the pagelayout. It prepares lesson 6.2, where learners write
a view of their own.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 6: Modules and views](README.md) |
| Duration | 45 minutes (25 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 1.1](../module-01-the-development-installation/the-developers-map.md) and [Module 5](../module-05-fetch-functions/README.md) |

## Learning objectives

After this lesson, learners can:

- split an address into siteaccess, module, view, ordered parameters and unordered parameters;
- read `$ViewList` entries in a `module.php`: `script`, `params`, `unordered_params`, `functions`,
  `single_post_actions`;
- find the code of a kernel view in `kernel/private/classes/views/<module>/<view>.php`;
- describe the `$Result` array a view returns and how the pagelayout shows `$module_result.content`.

## Trainer's notes

### How to present it

Sources: [Reading the code you already have, technique 2](../../../extension-development/part-1-from-user-to-developer/reading-the-code-you-have.md#technique-2-from-an-address-to-the-code)
and [Commands, cronjob parts and module views as classes](../../../../specifications/6.0/runnable-commands-cronjobs-views.md).

1. **An address** (4 minutes). `/admin/content/view/full/2/(offset)/20`: siteaccess `admin`, module `content`,
   view `view`, ordered parameters `full` and `2` (named `ViewMode` and `NodeID` in `module.php`), unordered
   parameter `offset` with the value `20`.
2. **`module.php`** (6 minutes). Show `$ViewList['view']` in `kernel/content/module.php`:

   ```php
   $ViewList['view'] = array(
       'functions' => array( 'read' ),
       'default_navigation_part' => 'ezcontentnavigationpart',
       'script' => 'view.php',
       'params' => array( 'ViewMode', 'NodeID' ),
       'unordered_params' => array( 'language' => 'Language',
                                    'offset' => 'Offset', ... ) );
   ```

   `functions` names the policy a user needs (`content/read`); `single_post_actions` maps a pressed button to an
   action name. `$FunctionList` at the end lists the policy functions and their limitations.
3. **Script or class** (4 minutes). `kernel/section/view.php` is one line: `return
   \Exponential\View\Kernel\Section\View::main( __FILE__, get_defined_vars() );`. The code moved to
   `kernel/private/classes/views/section/view.php`. Extension views can be plain scripts or classes; both work.
4. **`$Result`** (4 minutes). A view sets `$Result['content']` (the HTML, usually from a template) and
   `$Result['path']` (the breadcrumb). The pagelayout prints the content with `{$module_result.content}`.
5. **Navigation parts** (2 minutes). `default_navigation_part` decides which tab of the administration is
   highlighted.

### Common questions

**Where do `$Params` come from?** The module runner fills `$Params` with the named ordered parameters and
`$Params['UserParameters']` with the unordered ones.

**Can an extension add a view to a kernel module?** Yes, with care: [Extension module override](../../../../features/6.0/extension-module-override.md)
explains how an extension provides or overrides views and templates of a module.

**Why did kernel views become classes?** So they can be replaced and listened to through settings and events
without editing kernel files (the specification above).

### Pitfalls

- **Looking for logic in the one-line entry file.** Follow the class name to `kernel/private/classes/views/`.
- **Confusing the module name and the folder.** The folder name under `modules/` is the module name in the address.
- **Forgetting the siteaccess part.** On a site matched by path, the first part of the address is the siteaccess,
  not the module.

## Hands-on exercise

On the development installation, in a shell.

1. Show the `view` entry: `grep -n "ViewList\['view'\]" -A10 kernel/content/module.php`.
2. Find the entry for `/user/login`: `grep -n "ViewList\['login'\]" -A10 kernel/user/module.php`. Write down its
   script, parameters and whether it names `functions`.
3. Open the entry file `kernel/user/login.php` and follow the class it calls to its file below
   `kernel/private/classes/views/`.
4. Count the kernel view classes: `find kernel/private/classes/views -name "*.php" | wc -l`.
5. List the policy functions of the section module: `grep -n "FunctionList\[" kernel/section/module.php`.
6. In the browser, open `/content/view/full/2/(offset)/10` on your site and on the administration; note how the
   siteaccess part differs.
7. Find where the standard design prints the result (`pagelayout.tpl` includes `page_mainarea.tpl`):
   `grep -rn "module_result.content" design/standard/templates/page_mainarea.tpl`.
8. Write the five parts of the address `/admin/content/view/full/2/(offset)/20` on paper.

## Expected result

- Step 2: the `login` view with its script and post actions.
- Step 3: the learner names the class file, for example `kernel/private/classes/views/user/login.php`.
- Step 7: the line where the pagelayout prints the view's result.
- Step 8: siteaccess `admin`, module `content`, view `view`, parameters `full` and `2`, unordered `offset=20`.
- The trainer checks step 8 with the whole group.

## Quiz

1. In `/content/view/full/2`, which part is the view?
2. Which `$ViewList` key names the policy a user needs for the view?
   A. `script` B. `params` C. `functions` D. `default_navigation_part`
3. What does `/(offset)/20` become inside a view?
4. Which key of `$Result` holds the HTML of the view?
5. Where is the code of the kernel view `section/view` since Exponential 6?

<details>
<summary>Answers</summary>

1. `view` (the module is `content`).
2. **C.** `functions`, for example `array( 'read' )`.
3. An unordered parameter: `$Params['UserParameters']['offset']`, and `$view_parameters.offset` in the
   templates of content views.
4. `$Result['content']`.
5. In `kernel/private/classes/views/section/view.php`, class `Exponential\View\Kernel\Section\View`.

</details>

## Further reading

- [Reading the code you already have](../../../extension-development/part-1-from-user-to-developer/reading-the-code-you-have.md).
- [Commands, cronjob parts and module views as classes](../../../../specifications/6.0/runnable-commands-cronjobs-views.md).
- [Extension module override](../../../../features/6.0/extension-module-override.md).

## Related pages

- [Module 6: Modules and views](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
