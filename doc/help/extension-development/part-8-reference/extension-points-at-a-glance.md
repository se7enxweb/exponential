---
title: Extension points at a glance
audience: developer
category: extension-development
order: 10
summary: Every way an extension plugs into Exponential on one page, grouped as the kernel's RAD catalogue groups them, with the registration mechanism of each and the chapter that explains it.
keywords: extension points, RAD catalogue, registration, directory, handler, autoload, file, design, ini, override, registry, filter, Setup RAD
type: help
---
# Extension points at a glance

Part 8, reference 1. The kernel keeps a catalogue of its extension points (`kernel/setup/expradcatalogue.php`,
`expRADCatalogue::points()`), and **Setup > RAD** (`/setup/rad`) is drawn from it. The full catalogue, with the code
location, the registering setting, the contract and the kernel file of every point, is
[Extension points](../../../bc/6.0/rad-extension-points.md). This page is the short map.

## How things are registered

| Mechanism | Meaning |
|---|---|
| directory | a class in a directory the kernel scans, named by a `RepositoryDirectories` or `ExtensionDirectories` setting |
| handler | a class named by an INI setting and loaded through `eZExtension::getHandlerClass()` |
| autoload | a class reached through the autoload array, registered once in an INI file |
| file | a file in a place the kernel looks by name |
| design | a template found through the design chain |
| ini | settings only |
| registry | an entry of a registry that `rad.ini` declares |
| filter | a listener of a filter event in `site.ini [Event] Listeners[]` |
| override | a kernel class replaced by one of your own: the last resort |

## The points, by group

| Group | Extension points | Book chapters |
|---|---|---|
| Content | datatype, content class, XML custom tag, XML text input and output handlers, information collection behavior, view cache clearing rules, asynchronous publishing filter | [Datatypes](../part-3-extension-developer/datatypes.md), [custom tag tutorial](../part-3-extension-developer/tutorials/by-hand-custom-tag.md) |
| Templates and design | template operator, template fetch function, attribute operator, template function, fetch alias, design extension, template override set, icon theme | [Template operators](../part-3-extension-developer/template-operators.md), [Fetch functions](../part-3-extension-developer/fetch-functions.md), [Template functions](../part-3-extension-developer/template-functions.md), [Part 6](../part-6-theme-developers/README.md) |
| Modules and views | module, module over existing tables, view for an existing module, policy function and limitation, content policy limitation, REST provider, REST route filter, server-side Ajax function, registry declared in `rad.ini` | [Modules and views](../part-3-extension-developer/modules-and-views.md), [Policy functions](../part-3-extension-developer/policy-functions-and-access.md), [Ajax](../part-3-extension-developer/ajax-with-ezjscore-server-functions.md) |
| Workflow, events and jobs | workflow event type, trigger, notification event type and handler, cronjob script and part, cronjob part left out, kernel event listener, filter event listener | [Workflow event types](../part-3-extension-developer/workflow-event-types.md), [Cronjob parts](../part-3-extension-developer/cronjob-parts.md), [Events and filters](../part-3-extension-developer/events-and-filters.md) |
| Storage and infrastructure | database handler, cluster file handler, DFS backends, binary file handler, search engine, session handler, mail transport, static cache handler, image handler and aliases, Redis caches, debug bar panel | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| Packaging and shop | package handlers, payment gateways, VAT, shipping, basket info and exchange rate handlers | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| Users, access and language | URL alias filter, mobile device filter, REST filters, user login handler, additional extension roots, extension root filter, site extension, writing settings from code, siteaccess settings extension, translation, RSS import handler | [Translations](../part-3-extension-developer/translations-in-an-extension.md), [Settings](../part-3-extension-developer/settings-in-an-extension.md) |

The catalogue counts 70 extension points, 65 of them with a tool in **Setup > RAD**.

## What is actually there

The catalogue is written by hand. What is really registered on an installation (every active extension's
settings, classes and files) is listed by the **Extension point survey** (`/setup/radsurvey`), read off disk on
every visit; its generated reference is [The extension surface](../../../bc/6.0/rad-extension-surface.md). The RAD
page's health check reports settings that name classes that cannot be loaded, missing directories and similar
mistakes ([The RAD tools tour](../part-3-extension-developer/tutorials/the-rad-tools-tour.md)).

## Choosing

Prefer the lightest mechanism that does the job, in roughly this order: ini, design, file, directory, handler,
filter, registry, autoload, override. [Extending without forking](../part-5-extend-fork-contribute/extending-without-forking.md)
explains why.

## Related pages

- Previous: [Part 8: Reference of built-in features for extension authors](README.md)
- Next: [eZINI: reading settings](ezini.md)
- [Extension points](../../../bc/6.0/rad-extension-points.md)
- [RAD tools](../../../features/6.0/rad-tools.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
