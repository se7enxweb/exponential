---
title: Exponential for CMS developers
audience: cms-developer
category: cms-development
order: 2
summary: For CMS developers meeting Exponential for the first time, what the system is, where it comes from, how an installation is laid out, what happens during a request, what ships in the box, and how site projects are usually built on it.
keywords: Exponential, Exponential 6, overview, architecture, kernel, extensions, designs, settings, var, request lifecycle, siteaccess, module, view, template, PHP, databases, Velocity, history, 7x, Exponential Foundation, site project
type: help
---
# Exponential for CMS developers

This page is for developers who build sites and are new to Exponential. It gives the system in overview: what it is,
how an installation is organized, what a request goes through, and how a site project is usually structured. The
following pages go deep on each part.

## Contents

1. [What Exponential is](#what-exponential-is)
2. [Where it comes from](#where-it-comes-from)
3. [An installation on disk](#an-installation-on-disk)
4. [A request, step by step](#a-request-step-by-step)
5. [What ships in the box](#what-ships-in-the-box)
6. [How a site project is built](#how-a-site-project-is-built)
7. [What you will learn first](#what-you-will-learn-first)

## What Exponential is

Exponential 6 is a content management system written in PHP, for PHP 8.0 to 8.5 (PHP 7.4 usable without support), licensed GPL 2.0 or later (root
`composer.json`). Its core ideas:

- a **content model you define**: classes with typed attributes; every item is an object with versions and
  translations, placed in one content tree ([The content model](the-content-model.md));
- **templates chosen by rules**, in its own template language, inside designs that fall back in a chain
  ([Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md));
- **siteaccesses**: many sites, languages and interfaces from one installation and database
  ([Siteaccesses and multisite](siteaccesses-and-multisite.md));
- **extensions** for everything a project adds, so the kernel stays untouched
  ([Extensions you will write](extensions-you-will-write.md));
- **Exponential Layouts** for pages editors compose from blocks ([Layouts for CMS developers](layouts-for-cms-developers.md)).

## Where it comes from

Exponential continues the code base of a CMS that its creator discontinued after almost 18 years. 7x, the company
that leads Exponential, has published the 6.0.x releases since the start of 2024, and the Exponential Foundation is
the nonprofit behind the project; neither is associated with eZ Systems or Ibexa
([Who is 7x?](../who-is-7x.md), [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)).
Developers who know eZ Platform or Ibexa will recognize the content model:
[Coming from eZ Platform or Ibexa](coming-from-ez-platform-or-ibexa.md).

## An installation on disk

```text
index.php                 the front controller
kernel/                   the kernel: content, user, shop, workflow, setup ... modules and classes
lib/                      libraries: template engine, database, INI, utilities
design/                   shipped designs (standard, admin designs)
settings/                 shipped default settings; never edited
  override/               this machine's settings (database, mail, hosts); never committed
  siteaccess/<name>/      settings of one siteaccess
extension/                every extension: shipped ones from Composer, and yours
var/                      storage (uploaded files), caches, logs
bin/php/                  command line scripts; bin/php/console is the console
```

The map with every directory and its purpose: [The developer's map](../trainers/developers/module-01-the-development-installation/the-developers-map.md)
and [Directories under var](../extension-development/part-8-reference/directories-under-var.md).

## A request, step by step

1. The web server (Velocity, FrankenPHP, or Apache or nginx with PHP-FPM) hands the request to Exponential.
2. The **siteaccess** is chosen from the host, port or first path element; its settings apply from now on.
3. The **URL alias** is translated to a module and view, for content `content/view/full/<node id>`.
4. The view checks **permissions**, then renders: for content, the node's view template chosen by **override rules**,
   or the stored copy from the **view cache**.
5. The result is placed in the **pagelayout**, which may draw a **layout** of Exponential Layouts around it.
6. The answer goes out, possibly kept by the HTTP cache or Velocity's response cache for the next visitor.

Each step has its page: [URL aliases and routing](url-aliases-and-routing.md),
[Sections, roles and policies](sections-roles-and-policies.md), [Caching for CMS developers](caching-for-cms-developers.md).

## What ships in the box

| Area | Built in or in the default distribution |
|---|---|
| Content | 35 kernel datatypes, versions, translations, relations, object states, trash, rich text with the Online Editor |
| Site building | designs and override rules, view modes, Exponential Layouts, site packages with demo sites |
| Users | user accounts as content, groups, roles and policies, registration, two-factor sign-in |
| Processes | workflows (approval, dated publishing), cronjobs, notifications, information collection, a shop |
| Delivery | the REST interface (`ezprestapi`), `expservices`, RSS, sitemaps, search (built in, or Solr through eZ Find) |
| Operation | the console (`./console`), Velocity, caches at several layers, audit trail, maintenance mode |
| Databases | SQLite 3, PostgreSQL, MongoDB, MariaDB, Oracle and MySQL ([Database support](../database-support.md)) |

The list of every feature of the 6.0 line: [Features of 6.0](../../features/6.0/README.md).

## How a site project is built

| Step | Page |
|---|---|
| Set up a development installation | [Development environment](development-environment.md) |
| Design the content model, then create the classes | [Designing a content model](designing-a-content-model.md), [Creating a content class](creating-a-content-class.md) |
| Build the design and templates in your extension | [Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md) |
| Decide which pages editors compose with Layouts | [Layouts for CMS developers](layouts-for-cms-developers.md) |
| Configure roles, languages and siteaccesses | [Sections, roles and policies](sections-roles-and-policies.md), [Translations and languages](translations-and-languages.md) |
| Import content | [Importing content](importing-content.md) |
| Deploy and launch | [Deploying a site](deploying-a-site.md), [Project launch checklist](project-launch-checklist.md) |

Two ways of building meet in every project: templates in a design extension, and the Layouts editor without code
([Two ways to build](../build-two-ways/two-ways-to-build.md)).

## What you will learn first

Three habits make everything else easier: think in classes and nodes, never edit shipped files, and know which cache
follows which change. The [Reading paths](reading-paths.md) page orders the rest by the time you have.

## Related pages

- [CMS developers: start here](cms-developers-start-here.md)
- [The template language: what it is](../template-language/what-the-template-language-is.md)
- [Getting started](../getting-started.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
