---
title: "Lesson 6.2: A view of your own"
audience: trainer, developer
category: training
order: 62
summary: Learners add a view greet to their hello module, with an ordered parameter, a template in the extension's design and a breadcrumb, and open it at its own address. 55 minutes.
keywords: developers course, custom view, module.php, ViewList, script, params, Params, Result, content, path, eZTemplate, design:, extension design standard, hello/greet
type: help
---
# Lesson 6.2: A view of your own

A view gives an extension a page with its own address: a report, a form, a small tool. In this lesson learners add
the view `greet` to the `hello` module from lesson 5.4. It takes a name from the address, renders a template from
the extension's design and shows it inside the site's pagelayout with a breadcrumb.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 6: Modules and views](README.md) |
| Duration | 55 minutes (15 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 6.1](how-modules-and-views-work.md); the `hello` module of [Lesson 5.4](../module-05-fetch-functions/a-fetch-function-of-your-own.md) |

## Learning objectives

After this lesson, learners can:

- declare a view in `$ViewList` with `script` and `params`;
- read ordered and unordered parameters from `$Params`;
- render a template with `eZTemplate::factory()` and `design:` and return it in `$Result['content']`;
- set the breadcrumb with `$Result['path']`;
- place the view's template in the extension's `design/standard/templates/` so every siteaccess finds it.

## Trainer's notes

### How to present it

Sources: a real extension view such as `extension/nxc_powercontent/modules/content/newcontent.php` (if present),
and [Commands, cronjob parts and module views as classes](../../../../specifications/6.0/runnable-commands-cronjobs-views.md).

1. **The declaration** (3 minutes). Add `$ViewList['greet']` to `modules/hello/module.php`: `script` names the
   file next to `module.php`, `params` names the ordered parameters.
2. **The script** (5 minutes). Walk through `greet.php` of the exercise: read `$Params['Name']`, wash nothing in
   PHP (the template washes), set template variables, fetch the template, fill `$Result`.
3. **Where the template lives** (3 minutes). `extension/mysite_hello/design/standard/templates/hello/greet.tpl`.
   `standard` is in every siteaccess's chain, so the view works in the site and the administration. The site
   design can still override it.
4. **Class or script** (2 minutes). The kernel's own views are classes extending `Exponential\Runnable\ModuleView`;
   a plain script is fine for an extension and easier to start with.
5. **Access** (2 minutes). Without `functions`, a user needs a policy for the whole module (`hello`, every
   function). Administrators have every policy, visitors none: sign in to the site as an administrator for this
   lesson. Lesson 6.3 adds a policy function so a role can grant just this view.

### Common questions

**Why does the page show inside the site's design?** The pagelayout prints `$module_result.content`; your view only
returns its part of the page.

**How do I return JSON instead?** Print the JSON and end with `eZExecution::cleanExit()`, or use the REST or ajax
mechanisms of module 11. Do not mix HTML pagelayout and JSON.

**Can the view take optional parameters?** Ordered parameters may be missing; check with `isset()`. Unordered ones
come as `/(name)/value`.

### Pitfalls

- **Printing a parameter without washing.** `$Params['Name']` comes from the address; the template must print it
  with `|wash`.
- **Template not found.** The design folder must be `design/standard/templates/` inside an extension whose
  `design.ini.append.php` has `DesignExtensions[]` (lesson 3.1); clear the `template-override` cache after adding it.
- **Module cache.** A new view in an existing module may need the `ini` and `global_ini` caches cleared.

## Hands-on exercise

On the development installation, in `extension/mysite_hello`.

1. Add the view to `modules/hello/module.php`:

   ```php
   <?php
   $Module = array( 'name' => 'hello' );
   $ViewList = array();
   $ViewList['greet'] = array(
       'script' => 'greet.php',
       'params' => array( 'Name' ),
       'unordered_params' => array( 'times' => 'Times' ) );
   $FunctionList = array();
   ```

2. Create `modules/hello/greet.php`:

   ```php
   <?php
   /**
    * hello/greet: /hello/greet/<Name>/(times)/<n>
    */
   $name = isset( $Params['Name'] ) && $Params['Name'] !== '' ? $Params['Name'] : 'world';
   $times = isset( $Params['UserParameters']['times'] ) ? max( 1, min( 5, (int)$Params['UserParameters']['times'] ) ) : 1;

   $tpl = eZTemplate::factory();
   $tpl->setVariable( 'name', $name );
   $tpl->setVariable( 'times', $times );

   $Result = array();
   $Result['content'] = $tpl->fetch( 'design:hello/greet.tpl' );
   $Result['path'] = array( array( 'url' => false, 'text' => 'Hello' ) );
   ```

3. Create `design/standard/templates/hello/greet.tpl`:

   ```
   <h1>Hello</h1>
   {for 1 to $times as $i}
       <p>Hello, {$name|wash}!</p>
   {/for}
   ```

4. Check the PHP files with `php -l` and the template with `php bin/php/eztemplatecheck.php`.
5. Make sure the extension's design is registered (lesson 3.1 registered `DesignExtensions[]=mysite_hello`), then
   clear caches: `php bin/php/ezcache.php --clear-tag=ini`, `--clear-id=global_ini` and `--clear-id=template-override`.
6. Sign in to the site as an administrator and open `/hello/greet` (with the siteaccess part if your site uses
   one, for example `/site/hello/greet`). Then open it in a private browser window, signed out.
7. Open `/hello/greet/Ada` and `/hello/greet/Ada/(times)/3`.
8. Remove `|wash` from `greet.tpl` for a moment and discuss with your neighbor what a visitor could put into the
   address; then put `|wash` back. Every value that comes from the address is user input.
9. Commit the three files.

## Expected result

- Step 6 shows "Hello" and "Hello, world!" inside the site's pagelayout, with "Hello" in the breadcrumb; signed
  out, the site refuses access, because the anonymous role has no policy for the `hello` module.
- Step 7 greets Ada once, then three times.
- Step 8: the learner can explain why the name must be washed in the template.
- The trainer opens one learner's view on the projector and reads its `greet.php` with the group.

## Quiz

1. Which key of a `$ViewList` entry names the file of the view?
2. How does a view read the unordered parameter `/(times)/3`?
   A. `$Params['times']` B. `$Params['UserParameters']['times']` C. `$_GET['times']` D. `$Module->times`
3. Why is the template in `design/standard/templates/` of the extension?
4. Where must the name from the address be escaped?
5. What does `$Result['path']` control?

<details>
<summary>Answers</summary>

1. `script`.
2. **B.** `$Params['UserParameters']['times']`.
3. `standard` is in every siteaccess's design chain, so the view finds its template everywhere; a site design can
   still override it.
4. In the template, with `|wash`, where it is printed.
5. The breadcrumb (path) shown by the pagelayout.

</details>

## Further reading

- [Extension module override](../../../../features/6.0/extension-module-override.md): views and templates of modules
  in extensions.
- [Commands, cronjob parts and module views as classes](../../../../specifications/6.0/runnable-commands-cronjobs-views.md):
  the class form of a view.
- [RAD tools](../../../../features/6.0/rad-tools.md): the module wizard writes modules with views.

## Related pages

- [Module 6: Modules and views](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
