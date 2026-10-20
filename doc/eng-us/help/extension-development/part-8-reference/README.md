---
title: "Part 8: Reference of built-in features for extension authors"
audience: developer
category: extension-development
order: 80
summary: Part 8 of the book: a reference of the kernel classes, template features, settings, commands and directories extension authors use most, with signatures taken from the source and links to the full documentation.
keywords: book, part 8, reference, eZINI, eZDB, eZPersistentObject, eZContentObject, eZTemplate, eZModule, eZScript, eZDataType, operators, fetch functions, ini files, console, var directories
type: help
---
# Part 8: Reference of built-in features for extension authors

This is the eighth and last part of [Extension development: the book](../README.md). The earlier parts teach; this
one is for looking things up. Each page covers one kernel class or one area, lists what an extension author uses most
(with method signatures copied from the source as of October 2026), shows a short example and links to the full
documentation. It is not a complete API reference: the kernel's source and the pages linked from each chapter are.

## The pages

### Classes of the kernel

| Page | For |
|---|---|
| [Extension points at a glance](extension-points-at-a-glance.md) | every way to plug in, on one page |
| [eZINI](ezini.md) | reading settings |
| [eZDebug and logging](ezdebug-and-logging.md) | debug output and log files |
| [eZDB](ezdb.md) | the database |
| [eZPersistentObject](ezpersistentobject.md) | your own tables |
| [eZContentObject](ezcontentobject.md) | content objects |
| [eZContentObjectTreeNode](ezcontentobjecttreenode.md) | nodes, the content tree |
| [eZContentClass](ezcontentclass.md) | content classes and their attributes |
| [eZUser and access checks](ezuser-and-access.md) | the current user and policies |
| [Demo mode for developers](demo-mode-for-developers.md) | `EXP_DEMO_MODE`, password changes on a public demo |
| [eZHTTPTool](ezhttptool.md) | request variables, sessions, redirects |
| [eZSys and eZExtension](ezsys-and-ezextension.md) | paths, the environment, extensions |
| [eZTemplate](eztemplate.md) | templates from PHP |
| [eZModule](ezmodule.md) | modules and view results |
| [eZScript and eZCLI](ezscript-and-ezcli.md) | command line scripts |
| [eZFile, eZDir and cluster files](ezfile-ezdir-and-cluster-files.md) | files |

### APIs for extension types

| Page | For |
|---|---|
| [The eZDataType API](ezdatatype-api.md) | datatypes |
| [The eZWorkflowType API](ezworkflowtype-api.md) | workflow event types |
| [Content functions from PHP](content-functions-from-php.md) | creating and publishing content |
| [ezjscore server functions](ezjscore-server-functions-reference.md) | Ajax calls |
| [ezformtoken and CSRF](ezformtoken-and-csrf.md) | form protection |
| [eZMail](ezmail.md) | sending mail |
| [Cache tags and clearing](cache-tags-and-clearing.md) | caches |

### Templates, settings, commands

| Page | For |
|---|---|
| [Built-in template operators](template-operators-reference.md) | operators by group |
| [Built-in template functions](template-functions-reference.md) | `{def}`, `{foreach}`, `{include}` and the rest |
| [Built-in fetch functions](fetch-functions-reference.md) | `fetch( module, function, ... )` |
| [INI files an extension author meets](ini-files-for-extension-authors.md) | which file holds what |
| [Console commands for developers](console-commands-for-developers.md) | `./console` and `bin/php` |
| [Directories under var/](directories-under-var.md) | caches, logs, storage |
| [Error codes and module results](error-codes-and-module-results.md) | errors from views |

### Exponential 6 additions

| Page | For |
|---|---|
| [The expui JavaScript API](expui-javascript-api.md) | `Exp.*` in the browser |
| [admin4 components and tokens](admin4-components-and-tokens.md) | admin pages |
| [The help system for extensions](help-system-for-extensions.md) | help pages in your extension |
| [expservices](expservices.md) | remote services |
| [Glossary](glossary-for-extension-developers.md) | the words of this book |

## Related pages

- Previous: [The release checklist](../part-7-releasing/the-release-checklist.md)
- Next: [Extension points at a glance](extension-points-at-a-glance.md)
- [Extension points](../../../bc/6.0/rad-extension-points.md)
- [The extension surface](../../../bc/6.0/rad-extension-surface.md)
- [Extension development: the book](../README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
