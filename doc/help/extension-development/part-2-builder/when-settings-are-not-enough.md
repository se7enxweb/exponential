---
title: "When settings are not enough: from builder to extension developer"
audience: developer, designer, app-creator
category: extension-development
order: 220
summary: How to tell that a requirement needs code, and which extension point fits: a table from need to mechanism (operator, fetch function, module, datatype, event, cronjob, listener, handler) and its RAD wizard.
keywords: decision, when to code, extension point, template operator, fetch function, module, datatype, workflow event, cronjob, event listener, handler, kernel override, RAD
type: help
---
# When settings are not enough: from builder to extension developer

Part 2, chapter 22, the last of this part. Builders can do a great deal without PHP. This chapter helps you see the
point where code is the better tool, and which of the kernel's extension points fits the requirement, so your first
PHP is small and in the right place.

## Signs that a template is doing a program's job

- A template has more `{if}`, `{set}` and arithmetic than HTML.
- The same twenty lines of template logic appear in several templates.
- You fetch hundreds of nodes to compute one number.
- You need data that is not content: an external service, a file, a calculation, a database table of your own.
- Something must happen without a page view: at publish time, at night, on a schedule.

## From symptom to mechanism

| You need | Mechanism | Part 3 chapter | RAD wizard |
|---|---|---|---|
| A value or transformation in templates (`{$price\|mysite_tax}`) | template operator | Template operators | `/setup/templateoperator` |
| A list or object that `fetch()` cannot express | fetch function | Fetch functions | `/setup/templateoperator` |
| A page with its own address, a form, a report | module and view | Modules and views | `/setup/moduleextension`, `/setup/modulewizard` |
| A new kind of field for editors | datatype | Datatypes | `/setup/datatype` |
| A step around publishing that can wait or ask | workflow event | Workflow event types | `/setup/workflowevent` |
| Something on a schedule | cronjob part | Cronjob parts | `/setup/cronjobs` |
| A command for administrators | console command | CLI scripts and console commands | |
| A reaction to a kernel event, or a filter of a value | event listener | Events, listeners and filters | `/setup/settingsextension` |
| A different mail transport, login, session, search, VAT | handler named by a setting | Handlers named by settings | `/setup/handlerextension/<kind>` |
| A dynamic part of a page from JavaScript | ezjscore server function | Ajax with ezjscore server functions | `/setup/handlerextension/ajaxfunction` |
| Your own table | persistent object | Persistent objects and your own tables | `/setup/modulewizard` |
| To change a kernel class's behavior and nothing else works | kernel override | Kernel class overrides: the last resort | `/setup/kerneloverride` |

The RAD catalogue at `/setup/rad` lists every extension point with the same information; this table is the short
version.

## Prefer the lightest mechanism

1. A setting.
2. A template or an override.
3. An operator or a fetch function (pure functions, easy to test).
4. A listener or a handler (registered by a setting, replaceable).
5. A module, a datatype, a workflow event (more code, more surface).
6. A kernel override (last).

Each step up adds code you maintain at every upgrade. Choose the lowest step that meets the requirement.

## Before you write code: look for it

The extension you need may exist: check the extensions on your disk, the
[se7enxweb catalogue](../part-1-from-user-to-developer/the-se7enxweb-extension-catalogue.md),
[Exponential Projects](../part-1-from-user-to-developer/projects-exponential-earth.md) and GitHub. An operator for
string replacement, a tags field, a sitemap: all exist already.

## Your first PHP

Start with Part 3: The extension developer and the
tutorials: the smallest extension takes five minutes, and a
template operator is a good first piece of code because it is small, pure and visible at once.

## Related pages

- Previous: [Building a site without writing code](building-a-site-without-code.md)
- Next: Part 3: The extension developer
- [Extension points](../../../bc/6.0/rad-extension-points.md)
- [RAD tools](../../../features/6.0/rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
