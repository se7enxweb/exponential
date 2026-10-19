---
title: "Tutorials: build real extensions step by step"
audience: developer, app-creator, designer
category: extension-development
order: 900
summary: Step-by-step tutorials that build real small extensions, first with the RAD wizards of Setup > RAD, then by hand, ending with a complete extension from idea to release. Every file shown was checked with php -l and run.
keywords: tutorial, step by step, RAD, wizard, by hand, template operator, fetch function, module, policy, datatype, workflow event, cronjob, persistent object, ajax, custom tag, translation, help pages, phpunit, capstone
type: help
---
# Tutorials: build real extensions step by step

These tutorials belong to [Part 3](../README.md) of the book. Each one builds a small, working extension and shows every
file in full, the commands to switch it on, and what you should see. The PHP files were checked with `php -l` and, where
it is safe, run with the kernel loaded; the template samples were rendered by the template engine.

## Two ways to the same result

| | With the RAD tools | By hand |
|---|---|---|
| Where | **Setup > RAD** (`/setup/rad`) in the administration | your editor and a terminal |
| Speed | a working skeleton in minutes | minutes for small things, longer for big ones |
| What you learn | which files and settings a mechanism needs | why each line is there |
| Best for | starting something new; checking you did not forget a setting | small changes; understanding; working without the admin |

Do one of each for the same mechanism and compare the files: that is the fastest way to learn the anatomy.

## With RAD

| Tutorial | Builds |
|---|---|
| [The RAD tools tour](the-rad-tools-tour.md) | the catalogue, the survey, the health check, and how every wizard works |
| [A template operator in five minutes](rad-template-operator.md) | operators, functions, fetch functions and fetch aliases |
| [A datatype](rad-datatype.md) | a new kind of field with its templates |
| [A module with views](rad-module.md) | a module with views and policies, or a module over existing tables |
| [A workflow event](rad-workflow-event.md) | a workflow event type with its templates |
| [A design extension](rad-design-extension.md) | a theme skeleton with pagelayout, override and stylesheets |
| [A settings-only extension](rad-settings-extension.md) | settings, listeners and siteaccess customizations |
| [Handlers and kernel overrides](rad-handlers-and-kernel-overrides.md) | a handler named by a setting, and the last resort |

## By hand

| Tutorial | Builds |
|---|---|
| [The smallest extension in five minutes](by-hand-minimal-extension.md) | metadata and settings, nothing else |
| [A template operator](by-hand-template-operator.md) | `mysite_greet` and `mysite_initials` |
| [A fetch function](by-hand-fetch-function.md) | `fetch( 'hello', 'newest', ... )` |
| [A module with a view](by-hand-module-with-a-view.md) | `/hello/world/<name>` |
| [A module protected by a policy](by-hand-module-with-a-policy.md) | an admin page only some roles may open |
| [A datatype](by-hand-datatype.md) | a rating from 1 to 5 |
| [A workflow event](by-hand-workflow-event.md) | a log line for every publication |
| [A cronjob part](by-hand-cronjob-part.md) | a nightly digest |
| [Your own table with eZPersistentObject](by-hand-own-table-with-persistent-object.md) | a greetings table with its class |
| [An ezjscore server function](by-hand-ajax-server-function.md) | an ajax call answered in JSON |
| [A rich text custom tag](by-hand-custom-tag.md) | a tag editors can use in rich text |
| [Translating an extension](by-hand-translation.md) | German texts for everything above |
| [Help pages for your extension](by-hand-help-pages-for-your-extension.md) | pages and context help in the help center |
| [A PHPUnit test](by-hand-phpunit-test.md) | tests that run without a database |

## Capstone

[A complete extension from idea to release](capstone-a-complete-extension.md) puts the pieces together and publishes
the result, following Part 7.

## What you need

A development installation with debug output on ([Setting up a development installation](../../part-1-from-user-to-developer/setting-up-a-development-installation.md)),
and for the RAD tutorials an administrator with the `setup/setup` policy. All tutorials use the extension name
`mysite_hello`; pick your own prefix for real work.

## Related pages

- Previous: [Documenting your extension, including help pages](../documenting-your-extension.md)
- Next: [The RAD tools tour: Setup > RAD](the-rad-tools-tour.md)
- [RAD tools](../../../../features/6.0/rad-tools.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
