---
title: Reading paths through the book
audience: developer, designer, app-creator, user
category: extension-development
order: 5
summary: Reading orders through the book for each role - curious user, builder, PHP developer, designer, application creator, upgrader, maintainer - with what you can do after each stage.
keywords: reading path, learning path, where to start, roles, builder, developer, designer, application creator, maintainer, upgrade, order
type: help
---
# Reading paths through the book

This page is part of [Extension development: the book](README.md). The book is long, and nobody needs all of it at
once. Pick the path that matches what you want to do. Each path lists the chapters in order and says what you can do
when you reach the end of each stage.

## The curious user: "What can be changed, and by whom?"

1. [The ladder from user to developer](part-1-from-user-to-developer/the-ladder-from-user-to-developer.md)
2. [What an extension is](part-1-from-user-to-developer/what-an-extension-is.md)
3. [Building a site without code](part-2-builder/building-a-site-without-code.md)
4. [Finding ideas on GitHub](part-1-from-user-to-developer/finding-ideas-on-github.md) and
   [projects.exponential.earth](part-1-from-user-to-developer/projects-exponential-earth.md)

After this you can tell a setting from a template from an extension, and find extensions others wrote.

## The builder: settings, templates and overrides, no PHP

1. [Tools you need](part-1-from-user-to-developer/tools-you-need.md) and
   [setting up a development installation](part-1-from-user-to-developer/setting-up-a-development-installation.md)
2. [How INI settings work](part-2-builder/how-ini-settings-work.md), then
   [Settings scopes and their order](part-2-builder/settings-scopes-and-their-order.md) and
   [Siteaccess settings](part-2-builder/siteaccess-settings.md)
3. [Templates for builders](part-2-builder/templates-for-builders.md), [The design chain](part-2-builder/the-design-chain.md),
   [Template overrides](part-2-builder/template-overrides-basics.md), [View modes](part-2-builder/view-modes-for-builders.md)
4. [Fetch for builders](part-2-builder/fetch-for-builders.md),
   [Template operators builders use most](part-2-builder/template-operators-builders-use-most.md)
5. [Content classes](part-2-builder/content-classes-for-builders.md),
   [Sections, roles and access](part-2-builder/sections-roles-and-access-for-builders.md),
   [Workflows](part-2-builder/workflows-for-builders.md), [Cronjobs](part-2-builder/cronjobs-for-builders.md)
6. [When settings are not enough](part-2-builder/when-settings-are-not-enough.md)

After this you can shape a site's behavior and look without writing PHP, and keep your work in an extension.

## The PHP developer new to Exponential

1. [What an extension is](part-1-from-user-to-developer/what-an-extension-is.md),
   [Reading the code you have](part-1-from-user-to-developer/reading-the-code-you-have.md),
   [Debug output and logs](part-1-from-user-to-developer/debug-output-and-logs-for-developers.md)
2. Part 2, chapters 1 to 6 of the builder path (settings, templates, the design chain)
3. [Part 3](part-3-extension-developer/README.md), the anatomy, in order
4. [The RAD tools tour](part-3-extension-developer/tutorials/the-rad-tools-tour.md), then the tutorials pairwise: each
   "With RAD" tutorial and its "By hand" twin
5. [Capstone: a complete extension](part-3-extension-developer/tutorials/capstone-a-complete-extension.md)
6. [Security in extensions](part-3-extension-developer/security-in-extensions.md) and
   [Content Security Policy for extensions](part-3-extension-developer/content-security-policy-for-extensions.md)
7. Keep [Part 8](part-8-reference/README.md) open while you work

After this you can write and test an extension of any kind the kernel supports.

## The designer

1. The builder path, chapters 1 to 3
2. [Part 6](part-6-theme-developers/README.md), in order
3. [admin4 styling for extension pages](part-4-upgrading/admin4-styling-for-extension-pages.md) if you style admin pages
4. [Where the designers' help continues](part-6-theme-developers/where-the-designers-help-continues.md)

After this you can build, ship and maintain a theme.

## The application creator

1. The builder path
2. [Modules and views](part-3-extension-developer/modules-and-views.md),
   [Persistent objects and your own tables](part-3-extension-developer/persistent-objects-and-tables.md),
   [Policy functions and access](part-3-extension-developer/policy-functions-and-access.md)
3. [Ajax with ezjscore server functions](part-3-extension-developer/ajax-with-ezjscore-server-functions.md) and
   [expservices](part-8-reference/expservices.md) for apps that talk to the site
4. [Content functions from PHP](part-8-reference/content-functions-from-php.md) for imports
5. The capstone tutorial

## The upgrader: "I have an old extension"

1. [Evaluating an old extension](part-1-from-user-to-developer/evaluating-an-old-extension.md) and
   [Reading licenses before you reuse anything](part-1-from-user-to-developer/reading-licenses-before-reusing.md)
2. [Part 4](part-4-upgrading/README.md), from [the checklist](part-4-upgrading/the-upgrade-checklist.md) on, in order
3. The four case studies at the end of Part 4
4. [When to fork, and when not to](part-5-extend-fork-contribute/when-to-fork.md) if the extension is someone else's

## The maintainer

1. [Part 5](part-5-extend-fork-contribute/README.md): forks, credit, licenses, pull requests, reviews
2. [Part 7](part-7-releasing/README.md): versions, tags, Packagist, changelogs, the release checklist
3. [The maintainer path](part-5-extend-fork-contribute/the-maintainer-path.md) and
   [The maintenance life cycle](part-7-releasing/the-maintenance-life-cycle.md)

## Related pages

- Previous: [Extension development: the book](README.md)
- Next: [Part 1: From user to developer](part-1-from-user-to-developer/README.md)
- [Book index: every topic from A to Z](index-of-topics.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
