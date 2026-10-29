---
title: CMS developer FAQ
audience: cms-developer
category: cms-development
order: 48
summary: For CMS developers, short answers to the questions developers ask in their first weeks with Exponential. They cover the model, templates, caches, APIs, multisite, the product family and the project, each with a link to the page that explains it.
keywords: FAQ, questions, answers, CMS developer, content model, node, object, template change not showing, cache, REST, headless, GraphQL, multisite, Exponential Platform, Velocity, PHP version, database, license, who leads Exponential
type: help
---
# CMS developer FAQ

This page is for developers new to Exponential who want quick answers. Each answer is short and points to the page
that has the whole story.

## Contents

1. [The model](#the-model)
2. [Templates and caches](#templates-and-caches)
3. [Code and APIs](#code-and-apis)
4. [Sites and servers](#sites-and-servers)
5. [The project](#the-project)

## The model

**What is the difference between a node and an object?**
The object is the content, with its versions and translations; a node is a place of that object in the tree. One
object can have several nodes; one of them is the main node. A page view shows a node.
[Objects, nodes and locations](objects-nodes-and-locations.md).

**Where do I define content types?**
In **Setup** > **Classes** (`/class/grouplist`). Carry them to other installations in packages.
[Creating a content class](creating-a-content-class.md).

**Can I change a field's type later?**
Not directly: add a new attribute, copy the values with a script, remove the old one.
[Content classes and attributes](content-classes-and-attributes.md#changing-a-class-that-has-content).

**Is there a "repeater" field?**
No nested repeater datatype. Use child objects of a small class, `ezmatrix` for small tables, or Layouts blocks.
[Mapping content to Exponential](../../install/migration/content-model-mapping.md#2-content-classes-and-datatypes).

**How do I make an area for members only?**
A section on the subtree and a role with `content/read` on that section, assigned to the members' group.
[Sections, roles and policies](sections-roles-and-policies.md#sections-the-main-lever).

## Templates and caches

**My template change does not appear. Why?**
A cache holds the old output or lookup: the template override cache for new files and rules, the view cache, or
Velocity's response cache. [Caching for CMS developers](caching-for-cms-developers.md#which-cache-after-which-change)
and [Why doesn't my change appear?](../build-two-ways/why-doesnt-my-change-appear.md).

**Which template draws this part of the page?**
Turn on `ShowUsedTemplates` and `ShowTemplatePathComments` on a development machine.
[Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md#finding-the-template-behind-a-page).

**Can I write PHP in templates?**
No. Templates call fetch functions and operators; write an operator or a fetch function in your extension when you
need more. [Extensions you will write](extensions-you-will-write.md).

**Templates or the Layouts editor?**
Templates for every page of a class; Layouts for pages editors compose. They combine through template blocks.
[Layouts for CMS developers](layouts-for-cms-developers.md#layouts-or-templates).

## Code and APIs

**How do I create content from code?**
`eZContentFunctions::createAndPublishObject()` and `updateAndPublishObject()`, with a remote ID per object.
[Content from PHP](content-from-php.md).

**Is there a REST API?**
Yes: the `ezprestapi` extension serves v1 and v2 routes for nodes, children and fields, with creates and removals at
v2; authentication by personal API key or OAuth2. Updates answer 501 for now. [The REST API](the-rest-api.md).

**Is there GraphQL?**
Not in Exponential 6. The Exponential Platform line, on the Symfony stack, has it.
[Headless delivery](headless-delivery.md#what-is-not-there).

**Where do hooks go?**
Workflow events on triggers, event listeners in `site.ini [Event]`, or template operators, depending on the need.
[Workflows and triggers](workflows-and-triggers.md),
[Extension points at a glance](../extension-development/part-8-reference/extension-points-at-a-glance.md).

**Can I import a CSV?**
Yes: xrowextract, with a dry run by default, or the kernel's simple `ezcsvimport`. [Importing content](importing-content.md).

## Sites and servers

**Can one installation run several sites?**
Yes, through siteaccesses sharing one database and tree. [Siteaccesses and multisite](siteaccesses-and-multisite.md).

**Which web server should I use?**
Exponential Velocity is the recommended engine; FrankenPHP is the production-ready alternative; Apache or nginx with
PHP-FPM work too. [Deploying a site](deploying-a-site.md#choose-how-the-site-is-served).

**Which PHP and databases?**
PHP 8.0 to 8.5 (PHP 7.4 usable without support) per the root `composer.json`. Databases in the project's order of preference: SQLite 3, PostgreSQL,
MongoDB, MariaDB, Oracle, MySQL. [Database support](../database-support.md), [Requirements](../../install/02-requirements.md).

**How do I create .htaccess and config.php?**
`./console exp:setup:htaccess --apply` copies `.htaccess_root` (with `--static`: `.htaccess_root_static`), and
`./console exp:setup:config --apply` copies `config.php-RECOMMENDED`. Without `--apply` they only show what they would
do, and an existing file that differs is shown as a diff and kept unless you add `--force`.
[Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md).

**How do I change a config.php setting without an editor?**
`./console exp:config:set EZP_INI_FILEMTIME_CHECK false --apply`: the file is backed up, checked with `php -l` and
written in one step. `exp:config:list` shows every documented setting, `exp:config:get <NAME>` one value, and
`exp:config:unset <NAME> --apply` comments it out again.
[Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md#6-set-a-value).

**How do I start a project fast?**
`exp:install` with the default `sevenx_multisite` package on SQLite, then your own extension.
[Development environment](development-environment.md).

## The project

**Who leads Exponential?**
7x, the company that leads it, and the Exponential Foundation, the nonprofit. Exponential is not associated with eZ
Systems or Ibexa. [Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md).

**What is the history?**
Exponential continues the code of a product that its creator discontinued after almost 18 years; 7x has published the
6.0.x releases since 2024. [Exponential for CMS developers](exponential-for-cms-developers.md).

**What license?**
The kernel is GPL 2.0 or later (root `composer.json`); this documentation is free under the GNU FDL 1.3.
[Free documentation](../free-documentation.md).

**Where do I ask?**
[Asking the community](../extension-development/part-1-from-user-to-developer/asking-the-community.md).

## Related pages

- [CMS developers: start here](cms-developers-start-here.md)
- [Glossary for CMS developers](glossary-for-cms-developers.md)
- [Trainer FAQ](../trainers/trainer-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
