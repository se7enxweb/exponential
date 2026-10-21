---
title: Extensions you will write
audience: cms-developer
category: cms-development
order: 38
summary: For CMS developers, the extensions a typical site project needs, seen from the project. These are a site extension with design and settings, and optionally a code extension with operators, fetch functions, modules, workflow events, cronjobs or datatypes. It covers how they are laid out, activated and kept under version control, and where the extension development book goes deeper.
keywords: extension, site extension, design extension, settings extension, project structure, extension.xml, ezinfo.php, composer.json, ActiveExtensions, autoloads, ezpgenerateautoloads, template operator, fetch function, module, RAD tools, version control
type: help
---
# Extensions you will write

This page is for developers planning the code of a site project. In Exponential, everything a project adds lives in
**extensions** under `extension/`: designs, settings, PHP code, templates, translations. The kernel and the shipped
extensions stay untouched, which is what makes updates safe. A typical project needs one or two extensions of its own.

## Contents

1. [The usual set](#the-usual-set)
2. [The site extension](#the-site-extension)
3. [A code extension](#a-code-extension)
4. [What a code extension can add](#what-a-code-extension-can-add)
5. [Activating and loading](#activating-and-loading)
6. [Version control](#version-control)
7. [Fast starts: the RAD tools](#fast-starts-the-rad-tools)
8. [Where the book goes deeper](#where-the-book-goes-deeper)

## The usual set

| Extension | Holds | Example name |
|---|---|---|
| Site extension | the design, override rules, siteaccess settings, image aliases, custom tags, classes as packages | `exp_mysite` |
| Code extension (when needed) | PHP: operators, fetch functions, modules, workflow events, cronjobs, console commands | `exp_mysite_tools` |

Small projects keep both in one extension. Split them when the code is reused by other sites, or when designers and
PHP developers work on different release cycles. Name them with your project's prefix and `exp` naming in new code
([Naming prefixes](../../standards/naming-prefixes-sevenx-and-exp.md)).

## The site extension

```text
extension/exp_mysite/
  extension.xml, ezinfo.php, composer.json, README.md
  settings/
    design.ini.append.php            DesignExtensions[]=exp_mysite
    site.ini.append.php              extension-wide settings
    image.ini.append.php, content.ini.append.php
    siteaccess/<public sa>/          site.ini, override.ini ... of the public siteaccess
  design/exp_mysite/
    templates/  override/templates/  stylesheets/  javascript/  images/
  translations/<locale>/translation.ts
  packages/                          classes exported as packages
```

Design work: [Your own design extension](../build-two-ways/your-own-design-extension.md),
[Design extension anatomy](../extension-development/part-6-theme-developers/design-extension-anatomy.md) and the
designers' [Theme extensions: start here](../designers/extensions/theme-extensions-start-here.md).

## A code extension

```text
extension/exp_mysite_tools/
  extension.xml, ezinfo.php, composer.json
  classes/                    PHP classes, autoloaded
  autoloads/                  eztemplateautoload.php for template operators
  modules/exp_mysite_tools/   module.php, views, function_definition.php
  eventtypes/event/<type>/    workflow event types
  cronjobs/                   cronjob scripts
  bin/php/                    command line scripts
  settings/                   the registrations and your own .ini
  tests/
```

Every folder and the setting that makes the kernel find it:
[Extension directory layout](../extension-development/part-3-extension-developer/extension-directory-layout.md).

## What a code extension can add

| You need | Add | Read |
|---|---|---|
| A computed value or a formatter in templates | a template operator | [Template operators](../extension-development/part-3-extension-developer/template-operators.md) |
| Data no fetch returns | a fetch function | [Fetch functions](../extension-development/part-3-extension-developer/fetch-functions.md) |
| A page with its own address, a form handler, a JSON endpoint | a module view with a policy | [Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md) |
| A step in publishing or checkout | a workflow event type | [Workflows and triggers](workflows-and-triggers.md) |
| Scheduled work | a cronjob | [Cronjobs](cronjobs.md) |
| A field with its own storage and editing | a datatype | [Choosing datatypes](choosing-datatypes.md#when-to-write-a-datatype-of-your-own) |
| Data that is not content | a table with `eZPersistentObject` | [Persistent objects and tables](../extension-development/part-3-extension-developer/persistent-objects-and-tables.md) |
| An import or maintenance job | a console command or script | [CLI scripts and console commands](../extension-development/part-3-extension-developer/cli-scripts-and-console-commands.md) |

Try settings, templates and existing extensions before code; the book's chapter
[When settings are not enough](../extension-development/part-2-builder/when-settings-are-not-enough.md) helps decide.

## Activating and loading

1. Add the extension to `site.ini [ExtensionSettings] ActiveExtensions[]` (or `ActiveAccessExtensions[]` in one
   siteaccess):

   ```bash
   ./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' exp_mysite global --allow-root-user
   ```

2. Regenerate the autoloads after adding or renaming classes: `php bin/php/ezpgenerateautoloads.php -e`.
3. Clear the INI cache and restart the PHP workers that serve the site ([Deploying a site](deploying-a-site.md)).

Order matters: an extension earlier in `ActiveExtensions[]` wins in settings and in the design chain
([Activating an extension](../extension-development/part-3-extension-developer/activating-an-extension.md),
[Extension load order](../extension-development/part-3-extension-developer/extension-load-order.md)).

## Version control

Each extension of yours is its own Git repository, or part of your project repository; the kernel and the shipped
extensions come from Composer. Commit templates, settings, packages and code; never commit `settings/override/`,
`var/` or credentials ([Develop with Git](../develop-with-git.md), [Development environment](development-environment.md)).
Give the extension an `extension.xml` and `ezinfo.php` with a version, so the administration's about page and the
upgrade checks can tell which release is installed ([extension.xml](../extension-development/part-3-extension-developer/extension-xml.md),
[ezinfo.php](../extension-development/part-3-extension-developer/ezinfo-php.md)).

## Fast starts: the RAD tools

The administration has wizards that write a working skeleton: a design extension, a module, a template operator, a
datatype, a workflow event, a settings extension, a content extension with classes
([RAD tools](../../features/6.0/rad-tools.md), [The RAD tools tour](../extension-development/part-3-extension-developer/tutorials/the-rad-tools-tour.md)).
Read what they generate; it is your code from then on.

## Where the book goes deeper

The book [Extension development](../extension-development/README.md) covers all of this in depth, from the first
extension to releasing it. Use its [Reading paths](../extension-development/reading-paths.md): the builder path
(settings, templates, overrides) and the PHP developer path fit a CMS developer best. Releasing an extension to others
follows strict rules on versions and tags ([Releasing](../extension-development/part-7-releasing/README.md)).

## Related pages

- [Extension points at a glance](../extension-development/part-8-reference/extension-points-at-a-glance.md)
- [What an extension is](../extension-development/part-1-from-user-to-developer/what-an-extension-is.md)
- [Module 7: Extensions](../trainers/developers/module-07-extensions/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
