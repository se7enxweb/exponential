---
title: Architecture for application creators
audience: app-creator, developer
category: app-creation
order: 40
summary: How a request flows through Exponential - front controller, siteaccess, module and view, templates and pagelayout - and where kernel, extensions, settings, designs and var/ fit, so you know where your code goes.
keywords: architecture, request flow, index.php, ezpKernelWeb, siteaccess, module, view, eZModule, template, pagelayout, extension, settings, INI, var, autoload, app creator
type: help
---
# Architecture for application creators

This page is for application creators who want the map before they write code: what happens on a request, which part
of the installation does what, and where an application's code belongs. The standards section explains the kernel's
architecture in depth ([Kernel architecture](../../../standards/architecture/kernel-architecture.md)).

## Contents

1. [The parts of an installation](#the-parts-of-an-installation)
2. [A request, step by step](#a-request-step-by-step)
3. [Where your code goes](#where-your-code-goes)
4. [Settings and their order](#settings-and-their-order)
5. [Command line and background work](#command-line-and-background-work)

## The parts of an installation

From [1.2 The parts of an installation](../../../install/01-introduction.md#12-the-parts-of-an-installation):

```text
kernel/, lib/      the kernel: modules, classes, the template engine, the database layer
autoload/          the kernel's class map (autoload/ezp_kernel.php)
extension/         extensions: your application lives here
design/            designs: templates, stylesheets, images
settings/          shipped settings; settings/siteaccess/ and settings/override/ hold yours
var/               runtime: caches, logs, storage, the SQLite database
bin/php/console    one entry point for every command
cronjobs/          the scripts runcronjobs.php runs
index.php          the front controller
```

## A request, step by step

1. **`index.php`** starts the web kernel (`ezpKernelWeb`).
2. **Siteaccess**: the address is matched against `site.ini [SiteAccessSettings] MatchOrder`; the siteaccess's
   settings load ([Siteaccesses](../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
3. **URL translation**: a URL alias is turned into a module address (`/About-us` to `content/view/full/58`).
4. **Module and view**: `eZModule` finds the module in `ModuleRepositories[]` and extension module directories, checks
   the view's policy functions and runs the view script, which returns a `$Result`
   ([Custom modules and views](custom-modules-and-views.md)).
5. **Templates**: the view renders a template found through the design chain; the result goes into `pagelayout.tpl`
   ([View results, templates and the pagelayout](view-results-and-the-pagelayout.md)).
6. **Caches** short-cut most of this for repeat requests: the content view cache, cache blocks, the Velocity response
   cache.

## Where your code goes

| Code | Place |
|---|---|
| Addresses and screens | `extension/<ext>/modules/<module>/` |
| Business logic | PHP classes in `extension/<ext>/classes/`, named `exp<Ext><Thing>` |
| Templates, stylesheets, scripts | `extension/<ext>/design/<design>/` |
| Settings | `extension/<ext>/settings/*.ini.append.php` |
| New field types | `extension/<ext>/datatypes/` |
| Process steps | `extension/<ext>/eventtypes/event/` |
| Scheduled work | `extension/<ext>/cronjobs/` plus a `cronjob.ini` part |
| Commands | `extension/<ext>/bin/php/` (console name `ext:<ext>:<name>`) |
| Tables | `extension/<ext>/share/` or `sql/` schema files |

Never edit `kernel/`, `lib/` or another extension in place: an update replaces them. Use the registered extension
points first; a kernel class override is the last resort ([Extension points](../../../bc/6.0/rad-extension-points.md)).

## Settings and their order

An INI value is read from `settings/<file>.ini`, then the active extensions' `settings/`, then
`settings/siteaccess/<siteaccess>/` (and extensions' siteaccess directories), then `settings/override/`; the last one
wins. `./console exp:ini where <file>/<block>/<key> <siteaccess>` shows every file that sets a value
([Settings and INI files](settings-and-ini-files.md)).

## Command line and background work

The same classes run outside a web request: console commands (`bin/php/console`), cronjob parts
(`runcronjobs.php`), and queue workers (`expqueue`). Kernel scripts, parts and views are classes now
(`Exponential\Runnable\Command`, `CronjobPart`, `ModuleView`), and your own can be too
([Commands, cronjob parts and module views as classes](../../../bc/6.0/cli_cronjob_view_abstractions.md)).

## Related pages

- [Exponential as an application platform](exponential-as-an-application-platform.md)
- [Anatomy of an extension](extension-anatomy.md)
- [Layering and naming](../../../standards/architecture/layering-and-naming.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
