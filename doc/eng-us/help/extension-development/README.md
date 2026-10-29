---
title: "Extension development: the book (start here)"
audience: developer, designer, app-creator, user
category: extension-development
order: 0
summary: A complete book on growing from user to builder, extension developer and theme developer with Exponential: finding ideas, building extensions with and without RAD tools, upgrading, forking ethically, releasing.
keywords: book, extension development, start here, developer, builder, theme developer, designer, application creator, tutorial, RAD, upgrade, release, contribute, reference
featured: true
type: help
---
# Extension development: the book (start here)

This book teaches you, step by step, to change what Exponential can do. It starts where you are, as a user of the
administration, and takes you through four steps: **builder** (settings, templates, overrides), **extension
developer** (modules, operators, datatypes, workflow events, cronjobs and more), **theme developer** (design
extensions), and **maintainer** (releasing your work and contributing to other people's). Along the way it shows you
where the community's existing extensions are, so that you start from what others built rather than from nothing,
and how to reuse their work fairly.

Every command and code sample in the book was checked against the kernel it describes: the classes, methods and
settings it names exist, the PHP samples pass `php -l`, and the template samples were rendered by the template engine.
When a page states a fact, it names the file or the page where the fact comes from.

Official Exponential certifications are planned, part of the project's future plans for the community, partners,
Exponential developers and students; they are not available yet
([Certifications and certificates](../students/certifications-and-certificates.md)).

## Developing on the whole stack: Velocity is part of Exponential 6.0.15

As of 6.0.15 the Velocity Internet / Web Application Server is part of Exponential: the root `composer.json` requires
`se7enxweb/exponential-velocity ~0.0.4.55` (through `se7enxweb/exponential-php-floor`, on PHP 8.0 and later), so your development installation has the same server production runs, on
PHP 8.0 and later. For an extension developer that changes three things:

- **You develop against the engine that serves the site.** Velocity's own server is the recommended engine for every
  stage, from your laptop to production (FrankenPHP is the production-ready alternative). Start it with
  `./console exp:velocity start` after `[ServerSettings] Engine=qbix`.
- **Workers keep your code loaded.** A changed class or setting reaches the site after
  `./console exp:velocity deploy` (or `restart`), which regenerates the autoloads, clears the caches and restarts the
  workers in the right order; `./console exp:velocity cache clear --apply` empties the response cache. Write code that keeps
  no per-request state in statics or globals, so it behaves the same under persistent workers and under PHP-FPM.
- **More is in the box.** The RAD tools write a working extension in minutes, and Velocity adds WebSockets and
  Server-Sent Events, a scheduler and a response cache without another package. Exponential is one working stack
  (server, RAD platform, CMS, digital experience, e-commerce), not just a framework you complete yourself.

The story: [Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md). The engines:
[Velocity engines](../../bc/6.0/velocity-engines.md). The RAD tools: [RAD tools](../../features/6.0/rad-tools.md).

## The parts

| Part | For | What you learn |
|---|---|---|
| [Part 1: From user to developer](part-1-from-user-to-developer/README.md) | everyone | What an extension is, the tools, a development installation, reading the code and the logs, finding existing extensions on GitHub, Packagist, Share Exponential, Exponential Projects and eZpedia, and reusing them within their licenses |
| [Part 2: The builder](part-2-builder/README.md) | builders, designers, application creators | Settings and their scopes, templates, the design chain, overrides, view modes, fetch, content classes, roles, workflows, cronjobs, all without PHP |
| [Part 3: The extension developer](part-3-extension-developer/README.md) | developers | The anatomy of an extension, chapter by chapter: metadata, settings, autoloads, modules and views, policies, operators, fetch functions, datatypes, workflow events, cronjobs, commands, designs, ajax, translations, tables, events, handlers, security, CSP, admin pages, tests, documentation |
| [Tutorials](part-3-extension-developer/tutorials/README.md) | developers | Real small extensions built step by step, with the RAD tools and by hand |
| [Part 4: Upgrading an old extension](part-4-upgrading/README.md) | developers | Bringing an extension written for older PHP and older releases to Exponential 6, with before and after examples from extensions this project modernized |
| [Part 5: Extending, forking and contributing](part-5-extend-fork-contribute/README.md) | developers, designers | Extending without forking, forking and rebranding ethically, licenses, attribution, pull requests, adopting abandoned extensions, the maintainer path |
| [Part 6: Theme developers](part-6-theme-developers/README.md) | designers | Themes as design extensions, with the RAD wizard and by hand; assets, the packer, CSP, dark mode, distribution |
| [Part 7: Releasing your extension](part-7-releasing/README.md) | maintainers | Version numbers, metadata, Composer and Packagist, changelogs, GitHub releases, packages, the release checklist |
| [Part 8: Reference](part-8-reference/README.md) | developers | The kernel classes, APIs, operators, functions, settings and commands an extension author uses most |

## Where to start

| You are | Start with |
|---|---|
| An editor or administrator who wants to build | [Part 1](part-1-from-user-to-developer/README.md), then [Part 2](part-2-builder/README.md) |
| A PHP developer new to Exponential | [What an extension is](part-1-from-user-to-developer/what-an-extension-is.md), then [Part 3](part-3-extension-developer/README.md) and the [tutorials](part-3-extension-developer/tutorials/README.md) |
| A designer | [Part 2](part-2-builder/README.md), then [Part 6](part-6-theme-developers/README.md) |
| An application creator | [Part 2](part-2-builder/README.md), then the [capstone tutorial](part-3-extension-developer/tutorials/capstone-a-complete-extension.md) |
| Someone with an old extension to bring up to date | [Part 4](part-4-upgrading/README.md) |
| About to publish an extension | [Part 7](part-7-releasing/README.md) and [Part 5](part-5-extend-fork-contribute/README.md) |

[Reading paths through the book](reading-paths.md) gives complete reading orders for each role, and the
[book index](index-of-topics.md) lists every topic from A to Z.

## Fast: what can I build, and how quickly?

| You want | Fastest way | Chapter |
|---|---|---|
| A new value or function for templates | a template operator, with the RAD wizard in minutes | [With RAD: a template operator in five minutes](part-3-extension-developer/tutorials/rad-template-operator.md) |
| A page with its own address | a module with a view | [By hand: a module with a view](part-3-extension-developer/tutorials/by-hand-module-with-a-view.md) |
| A new kind of field for content | a datatype | [With RAD: a datatype](part-3-extension-developer/tutorials/rad-datatype.md) |
| Something to happen when content is published | a workflow event | [With RAD: a workflow event](part-3-extension-developer/tutorials/rad-workflow-event.md) |
| A job that runs every night | a cronjob part | [By hand: a cronjob part](part-3-extension-developer/tutorials/by-hand-cronjob-part.md) |
| A new look | a design extension | [Part 6](part-6-theme-developers/README.md) |
| The site's settings under version control | a settings-only extension | [With RAD: a settings-only extension](part-3-extension-developer/tutorials/rad-settings-extension.md) |

The RAD tools live at **Setup > RAD** (`/setup/rad`); they list every place the kernel can be extended and write a
working extension for most of them. Every tutorial also shows the same extension written by hand, so you understand
what the wizard wrote and can work without it.

## Conventions in this book

- Commands run from the installation root. `./console` is a shortcut to `bin/php/console`
  ([how to create it](part-1-from-user-to-developer/setting-up-a-development-installation.md)). Add
  `--allow-root-user` when you run a command as root.
- Example extensions are called `mysite_hello` and similar, and new classes start with `exp` or your own prefix,
  never with `ez`: that prefix belongs to the kernel's existing classes.
- Administration paths are written as `Setup > RAD` and addresses as `/setup/rad`.
- The product is **Exponential**. It continues the code base of eZ Publish, which its creator discontinued after
  almost 18 years; kernel identifiers such as `eZINI` and file names such as `ezinfo.php` keep their historical names.

**Demo mode.** Code that sets or changes user passwords should respect demo mode (`EXP_DEMO_MODE=1` on public demos): [Demo mode for developers](part-8-reference/demo-mode-for-developers.md).

## Related pages

- [Develop with Git: grow from user to developer](../develop-with-git.md)
- [Extensions: find, install, configure, build and release](../../guides/extensions.md)
- [RAD tools](../../features/6.0/rad-tools.md)
- [Contribute your extensions back](../contribute-extensions-back.md)
- [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this book is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
