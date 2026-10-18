---
title: "RAD tools: building faster"
audience: app-creator, developer
category: app-creation
order: 480
summary: Use Setup > RAD to build extensions in minutes - the catalogue of extension points, the wizards that write working extensions (modules, datatypes, workflow events, operators, handlers, designs), the extension point survey and the health check.
keywords: RAD, rapid application development, Setup > RAD, wizards, module wizard, datatype wizard, extension points, catalogue, survey, health check, expRADCatalogue, app creator
type: help
---
# RAD tools: building faster

This page is for application creators who want working skeletons instead of typing boilerplate. **Setup > RAD**
(`/setup/rad`) lists every point where Exponential can be extended, how it is registered, and offers wizards that write a
complete extension ([RAD tools](../../../features/6.0/rad-tools.md), [Extension points](../../../bc/6.0/rad-extension-points.md)).

## Contents

1. [Five minutes to a working extension](#five-minutes-to-a-working-extension)
2. [The catalogue](#the-catalogue)
3. [The wizards](#the-wizards)
4. [Survey and health check](#survey-and-health-check)
5. [With or without the tools](#with-or-without-the-tools)

## Five minutes to a working extension

1. Open **Setup > RAD**; you need `setup/setup`.
2. Pick an extension point; `/setup/rad/(show)/tools` lists those with a wizard.
3. The wizard asks for a name and what to generate, and lists every file it would write under `extension/<name>`
   before writing any.
4. Confirm, activate the extension, regenerate autoloads, clear caches.
5. Check it in the survey and the health check.

## The catalogue

`kernel/setup/expradcatalogue.php` lists the extension points in groups (content, templates, modules, workflow, storage,
packaging, access), each with its mechanism (directory, handler, autoload, file, design, ini, override, registry,
filter), the kernel file it lives in, and its tool. A kernel class override is the last resort: two extensions replacing
the same class fight without knowing it.

## The wizards

| Wizard | Writes |
|---|---|
| Design extension (`/setup/designextension`) | a design with templates, stylesheets and an override set |
| Template extension (`/setup/templateoperator`) | template operators, fetch functions, attribute operators, template functions |
| Module extension, module wizard (`/setup/moduleextension`, `/setup/modulewizard`) | a module with views and policies, or a module over existing tables |
| Datatype extension (`/setup/datatype`) | a datatype |
| Workflow event extension (`/setup/workflowevent`) | workflow event types and triggers |
| Content extension (`/setup/contentextension`) | a content class written as a script |
| Handler extension (`/setup/handlerextension/<point>`) | handlers, including VAT, shipping, basket totals, exchange rates, login |
| Settings extension (`/setup/settingsextension`) | an extension that only carries settings |
| Kernel override (`/setup/kerneloverride`) | a kernel class replacement, the last resort |

Generated code never writes outside `extension/<name>`, never overwrites an existing extension, and escapes what its
templates print ([RAD security](../../../bc/6.0/rad-security.md)).

## Survey and health check

**Setup > RAD > Extension point survey** (`/setup/radsurvey`) reads what the installation really exposes: settings that
name a class, registries declared in `rad.ini`, and entries whose class or template is missing. The health check loads
each registration and reports what cannot work ([RAD extension surface](../../../bc/6.0/rad-extension-surface.md)).

## With or without the tools

The wizards write the same code these pages show by hand: start with a wizard to see a working example, then read the
matching page ([Custom modules and views](custom-modules-and-views.md), [Writing a custom datatype](custom-datatypes.md),
[Writing a workflow event type](writing-a-workflow-event-type.md), [Writing template operators](template-operators.md)).

## Related pages

- [Your first extension](your-first-extension.md)
- [Anatomy of an extension](extension-anatomy.md)
- [Extension point pattern: INI plug-in points](../../../standards/patterns/ini-plugin-point.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
