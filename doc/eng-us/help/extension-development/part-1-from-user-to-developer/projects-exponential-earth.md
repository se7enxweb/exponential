---
title: Exponential Projects (projects.exponential.earth)
audience: user, developer, designer, app-creator
category: extension-development
order: 120
summary: Exponential Projects, the forge that replaced projects.ez.no: thousands of projects by type, how to read an entry, and how to reuse what you find without breaking copyright.
keywords: projects.exponential.earth, project forge, projects.ez.no, extensions directory, template operator, datatype, module, workflow event, login handler, downloads, history
type: help
---
# Exponential Projects (projects.exponential.earth)

Part 1, chapter 12. [Exponential Projects](https://projects.exponential.earth/) is a project forge: a directory of
extensions and other projects with their news, downloads, forums and source links. It calls itself "a responsive
projects.ez.no replacement portal", and much of its directory comes from that older forge, which makes it the largest
single index of historical extensions for this code base. Facts on this page are as of October 2026.

## What is there

When this page was written the portal counted **3,934 registered projects**. They can be browsed in several ways:

| Browse | Address | Shows |
|---|---|---|
| By type | [/types](https://projects.exponential.earth/types) | Projects grouped by what they are |
| By organization | [/org](https://projects.exponential.earth/org) | Projects grouped by who publishes them |
| As a tree | [/tree](https://projects.exponential.earth/tree) | The whole directory |
| Recent source changes | [/changes](https://projects.exponential.earth/changes) | The newest Git commits of projects with a connected repository |
| News | [/news](https://projects.exponential.earth/news) | Announcements |

A project's address has the form `https://projects.exponential.earth/org/<organization>/<project>`.

## The types a developer looks at

The type "eZ publish (Via Projects.ez.no)" ([/types/ez_publish_via_projects_ez_no](https://projects.exponential.earth/types/ez_publish_via_projects_ez_no))
held 338 projects, in subtypes that map one to one to the chapters of this book:

| Subtype | Projects | Book chapter |
|---|---|---|
| Module | 121 | [Modules and views](../part-3-extension-developer/modules-and-views.md) |
| Datatype | 73 | [Datatypes](../part-3-extension-developer/datatypes.md) |
| Template operator | 71 | [Template operators](../part-3-extension-developer/template-operators.md) |
| Command line script | 61 | [CLI scripts and console commands](../part-3-extension-developer/cli-scripts-and-console-commands.md) |
| Workflow event type | 38 | [Workflow event types](../part-3-extension-developer/workflow-event-types.md) |
| Template function | 27 | [Template functions](../part-3-extension-developer/template-functions.md) |
| Admin interface integration | 26 | [Admin pages in the admin4 style](../part-3-extension-developer/admin-pages-in-admin4-style.md) |
| Content classes | 20 | [Content classes for builders](../part-2-builder/content-classes-for-builders.md) |
| Translation | 14 | [Translations in an extension](../part-3-extension-developer/translations-in-an-extension.md) |
| Extended attribute filter | 11 | [Fetch functions](../part-3-extension-developer/fetch-functions.md) |
| Content action handler | 10 | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| Payment gateway | 9 | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| Search plugin | 9 | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| Login handler | 8 | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |
| Binary file handler | 7 | [Handlers named by settings](../part-3-extension-developer/handlers-named-by-settings.md) |

Smaller subtypes include single sign-on handlers, collaboration handlers, notification events and handlers, XML
custom tags, database plugins, SOAP functions, shop account, VAT and shipping handlers. A second type,
"3rd party (via Projects.ez.no)", held 110 more projects; the "Exponential", "Exponential Platform" and "Exponential
Platform Legacy" types hold the current distributions and a design extension.

## Reading a project entry

Each entry in a listing shows the project's name, its status (for example Alpha or Stable), its "unix name"
(usually the extension folder name), the last update, the version and the versions it was written for, and a short
description. A project page adds news, downloads, a forum, images and, where connected, the Git or GitHub source.

The "compatible versions" field is the most important one for you: most projects in the historical types were written
for PHP 5 and older releases of the kernel. Treat them as **ideas and starting points**, then follow
[Part 4](../part-4-upgrading/README.md) to bring one up to date, or search GitHub for a maintained fork first
([Finding ideas on GitHub](finding-ideas-on-github.md)).

## Copyright: link, do not copy

The portal's footer reads "Copyright © 1998 - 2026 7x (except where otherwise noted). All rights reserved." The
portal's own text is therefore not free to copy: **link to project pages** instead of reproducing their descriptions.

The **projects themselves** carry their own licenses, almost always the GNU GPL, stated in their downloads (a
`LICENSE` file, the header of the PHP files, `ezinfo.php`). The project's license is what governs the code, not the
portal's footer. Read it before you reuse anything:
[Reading licenses before you reuse anything](reading-licenses-before-reusing.md).

## A workflow for finding a building block

1. Decide the kind of thing you need (a datatype, an operator, a module).
2. Open its subtype on [/types](https://projects.exponential.earth/types) and skim the descriptions.
3. For a promising project, open its page: status, compatible versions, last update, download.
4. Search GitHub for the same unix name; a maintained fork may exist under se7enxweb or elsewhere.
5. Read the license, then the code. Decide: use as is, bring up to date, or only take the idea.

## Related pages

- Previous: [Share Exponential (share.exponential.earth)](share-exponential-earth.md)
- Next: [eZpedia (ezpedia.exponential.earth)](ezpedia-exponential-earth.md)
- [Evaluating an old extension before you use it](evaluating-an-old-extension.md)
- [Adopting an abandoned extension](../part-5-extend-fork-contribute/adopting-an-abandoned-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
