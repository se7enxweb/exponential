---
title: "Part 3: The extension developer"
audience: developer, app-creator
category: extension-development
order: 30
summary: Part 3 of the book: the anatomy of an extension, one mechanism per chapter, from metadata and settings to modules, operators, datatypes, events, security and tests, followed by step-by-step tutorials.
keywords: book, part 3, extension developer, anatomy, ezinfo, extension.xml, module, view, operator, fetch function, datatype, workflow event, cronjob, console command, ajax, persistent object, security, CSP, tests
type: help
---
# Part 3: The extension developer

This is the third and largest part of [Extension development: the book](../README.md). It explains, one mechanism per
chapter, how an extension plugs PHP code into the kernel: where the code goes, which setting registers it, the contract
(class and methods, copied from the kernel source), a minimal example that was checked with `php -l` and, where it is
safe, run with the kernel loaded; how to see that it works; the common mistakes; and the RAD wizard that writes it for
you. The [tutorials](tutorials/README.md) then build real small extensions step by step.

## The anatomy

### The shell of an extension

| Chapter | |
|---|---|
| [The extension directory layout](extension-directory-layout.md) | every folder name and what the kernel does with it |
| [ezinfo.php](ezinfo-php.md), [extension.xml](extension-xml.md) | who made it, which version, which license, dependencies |
| [Activating an extension](activating-an-extension.md), [Extension load order](extension-load-order.md) | switching it on, and who wins |
| [composer.json for an extension](composer-json-for-extensions.md) | installing it with Composer |
| [Settings in an extension](settings-in-an-extension.md) | your own INI file and reading it |
| [Autoloads and class naming](autoloads-and-class-naming.md) | how your classes are found |

### Pages and permissions

| Chapter | |
|---|---|
| [Modules and views](modules-and-views.md), [View parameters](view-parameters.md) | pages with their own address |
| [Policy functions and access checks](policy-functions-and-access.md) | who may open them |
| [View results and the pagelayout](view-results-and-the-pagelayout.md) | what a view returns |

### Templates

| Chapter | |
|---|---|
| [Template operators](template-operators.md), [Fetch functions](fetch-functions.md), [Template functions](template-functions.md) | giving templates new abilities |

### Content and processes

| Chapter | |
|---|---|
| [Datatypes](datatypes.md), [Datatype storage, validation and templates](datatype-storage-validation-and-templates.md) | new kinds of fields |
| [Workflow event types](workflow-event-types.md), [Triggers and workflows for developers](triggers-and-workflows-for-developers.md) | steps around publishing |
| [Cronjob parts](cronjob-parts.md), [CLI scripts and console commands](cli-scripts-and-console-commands.md) | work outside a request |
| [Facts, themes and logos for the system card](system-card-providers.md) | add your extension to `exp:expfetch`, `exp:info` and `exp:about` |

### Front end

| Chapter | |
|---|---|
| [A design inside an extension](design-in-an-extension.md), [CSS and JavaScript in an extension](css-and-javascript-in-an-extension.md) | templates and assets |
| [Ajax with ezjscore server functions](ajax-with-ezjscore-server-functions.md) | dynamic parts |
| [Translations in an extension](translations-in-an-extension.md) | every text in every language |

### Data and the system

| Chapter | |
|---|---|
| [Persistent objects and your own tables](persistent-objects-and-tables.md), [Database schema, .dba files and upgrades](database-schema-and-dba-files.md) | storage |
| [Events, listeners and filters](events-and-filters.md), [Handlers named by settings](handlers-named-by-settings.md), [Kernel class overrides: the last resort](kernel-overrides-the-last-resort.md) | changing system behavior |

### Quality

| Chapter | |
|---|---|
| [Security in extensions](security-in-extensions.md), [Content Security Policy for extension authors](content-security-policy-for-extensions.md) | safe by default |
| [Admin pages in the admin4 style](admin-pages-in-admin4-style.md) | consistent administration pages |
| [Tests for extensions](tests-for-extensions.md), [Debugging extensions](debugging-extensions.md), [Performance in extensions](performance-in-extensions.md) | working, and fast |
| [Documenting your extension](documenting-your-extension.md) | including help pages in this help center |

## Tutorials

[Tutorials: build real extensions step by step](tutorials/README.md): the RAD tools tour, then each kind of extension
built with the RAD wizard and again by hand, ending with a capstone extension from idea to release.

## Before you start

You should have read [Part 1](../part-1-from-user-to-developer/README.md) and know the builder's tools of
[Part 2](../part-2-builder/README.md). You need a development installation with debug output on and basic PHP.

## Related pages

- Previous: [When settings are not enough](../part-2-builder/when-settings-are-not-enough.md)
- Next: [The extension directory layout](extension-directory-layout.md)
- [Extensions: find, install, configure, build and release](../../../guides/extensions.md)
- [Extension points](../../../bc/6.0/rad-extension-points.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
