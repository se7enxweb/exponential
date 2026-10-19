---
title: "Build with Exponential: why, which product, first steps"
audience: user, developer, designer, admin, app-creator, website-creator
category: project
order: 50
summary: The "Build with Exponential" page of exponential.earth, checked against the software - why build on it (simple, stable, upgradable), what you get out of the box, the product family as the repositories describe it (Exponential 6, Basic, Platform, Platform Legacy, Nexus, DXP), the five first steps from download to the first published page, and the console commands, with the corrections where the website and the code disagree.
keywords: build, why Exponential, simple, stable, upgradable, content model, content class, content tree, siteaccess, roles, search, workflow, REST, extension API, cluster, static cache, product family, Exponential 6, Exponential Basic, Exponential Platform, Platform Legacy, Platform Nexus, Platform DXP, download, setup wizard, design, override, console, exp:ezcache, exp:preload, exp:updatesearchindex
featured: true
type: help
---
# Build with Exponential: why, which product, first steps

The website's [Build with Exponential](https://exponential.earth/build) page explains why to build websites and
applications on Exponential, which member of the product family to choose, and how to get from a download to a first
published page. This page follows it, in this help's words, and checks every claim against the software in this
repository. Where the two disagree, this page follows the repository and says so in a note.

## Why build on it

The website gives three reasons. Each is visible in the software:

### Simple by design

Everything rests on one content model that you can learn in an afternoon:

- **content classes** define the shape of your content (an article has a title, an intro, a body, an image);
- **content objects** hold the content itself, with versions and translations;
- **the content tree** arranges it, and the address of a page follows the tree, readable and predictable.

There is no build step between a change to a template and the page that shows it.

### Stable by default

Caches are built into the kernel and managed for you; you clear them selectively or all at once, from **Setup >
Cache management** in the administration or from the command line. Within the 6.0 line an upgrade is new code, one
database update file for your database engine, and clearing caches
([Upgrading, 11.6](../../install/11-upgrading.md#116-from-any-60x-to-today)).

### Upgradable for the long term

Every release is tagged, and every change is recorded in the [6.0.15 changelog](../../changelogs/6.0/6.0.15.md) and
in backwards-compatibility notes under `doc/bc/`. Because the license is the GNU GPL version 2 or later, you can
fork, self-host and upgrade on your own schedule; no vendor can lock you in.

## What you get out of the box

The website's list, each with where to read more in this help:

| Feature | Read |
|---|---|
| Content modeling with content classes and datatypes | [Editing content](../editing-content.md) |
| Several sites, languages or audiences from one installation (siteaccesses) | [Languages](../languages.md) |
| Role-based permissions down to single objects | [Accounts and sign-in](../accounts-and-sign-in.md) |
| Built-in full-text search, with other search engines as plug-ins | [Searching](../searching.md) |
| Workflows for approval and scheduled publishing | [Publishing and versions](../publishing-and-versions.md) |
| A REST API and JSON views for headless use | the [README](https://github.com/se7enxweb/exponential/blob/main/README.md) |
| An extension API: modules, datatypes, template operators, workflow events and more | [Extensions](../../guides/extensions.md) |
| Cluster-ready file storage | the [installation book](../../install/README.md) |
| Static cache for the fastest pages | the [installation book](../../install/README.md) |
| Translation into any number of languages | [Languages](../languages.md) |

## The product family

The website presents Exponential as a family of products that share a lineage. The repositories of the
[se7enxweb organization](https://github.com/se7enxweb) describe each one as follows; the full account, with sources,
is on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance).

| Product | What it is, according to its repository | Choose it when |
|---|---|---|
| **Exponential 6** (this software) | The CMS, framework and shop kernel, with its own template language and administration | You want the full CMS on PHP alone, with or without a database server |
| **Exponential Basic** | A small, simple CMS from an older, separate line of the code base, for learning, personal use and small sites | You want something small to learn with |
| **Exponential Platform** | A CMS on the Symfony framework from the last free version of that platform, without Exponential 6 | You run that Symfony platform and want to stay on it |
| **Exponential Platform Legacy** | The Symfony platform with a full copy of Exponential 6 next to it (two kernels) | You keep an Exponential 6 site and want Symfony beside it |
| **Exponential Platform Nexus** | The Symfony platform with Exponential 6, a layouts editor and a complete demo media site | You want a complete site with layouts and demo content fast |
| **Exponential Platform DXP** | A Symfony-only digital experience platform with REST and GraphQL, without Exponential 6 | You start a new Symfony project with no legacy kernel |

> **Note: where the website and the repositories differ**
> The website's build page describes some products differently from their repositories. It calls Exponential Basic a
> pre-configured distribution of Exponential CMS, while its repository describes a separate, older line of the code
> base. It describes Platform Legacy as a migration package of compatibility shims and migration tools, while its
> repository describes a dual-kernel distribution. It describes Nexus as a headless-first edition reached by an
> extension install, with a JavaScript frontend bridge and event and AI pipelines, and the DXP as a set of Exponential
> extensions, while their repositories describe Symfony distributions (Nexus with Exponential 6 and the layouts
> editor; the DXP without the legacy kernel). This help follows the repositories. Read each product's own README
> before you choose.

For moving an existing site onto any of them, see the [migration hub](../../install/migration/README.md).

## From download to first page

The website's five steps, with the details from this help:

1. **Download.** Get the latest stable release from
   [GitHub releases](https://github.com/se7enxweb/exponential/releases), or clone the repository so that later
   updates are a `git pull` away. The project recommends Git clones for building websites
   ([Develop with Git](../develop-with-git.md); [Getting the code](../../install/03-getting-the-code.md)).
2. **Install.** Put the files in your web server's document root, or serve them with Velocity, open the site in a
   browser and follow the setup wizard: database, administrator account, site and languages
   ([The setup wizard](../../install/05-setup-wizard.md); other ways in
   [Choosing an install method](../../install/04-choosing-an-install-method.md)).
3. **Model your content.** In the administration, open the content classes and define the kinds of content you need.
   The website's advice holds: start with two or three classes and add more as you learn what the project needs;
   attributes and classes can be added later without migrating content.
4. **Build your design.** Create a design folder (best inside your own extension), add templates and stylesheets,
   and point your siteaccess at it. Override only the templates you need; never edit the core's own, so your work
   survives upgrades ([Exponential: frequently asked questions](faq.md#may-i-change-exponentials-core-files-for-my-site)).
5. **Publish.** Create content in the tree, publish it now or later, add an approval workflow if your team needs one
   ([Publishing and versions](../publishing-and-versions.md)), and warm the caches.

## The console

Exponential 6.0.15 adds `bin/php/console` (with the shortcut `./console` in the root of the installation), which
runs every command-line script under one name. The commands the website shows, checked against `console list`:

```bash
php bin/php/console list                        # every command
php bin/php/console exp:ezcache --clear-all     # clear all caches
php bin/php/console exp:preload                 # warm the page caches and report broken links
php bin/php/console exp:updatesearchindex       # rebuild the search index
php bin/php/console exp:ezpgenerateautoloads -e # regenerate the class autoload arrays of the extensions
```

Run them as the user that owns the files, in the root of the installation. `list` prints every command with a
one-line description and the script it runs (`exp:preload` runs `bin/php/preload.php`, for example); the options are
those of that script.

## Get help, give back

Stuck? [Getting support](getting-support.md). If Exponential saves you time or earns you money, the website asks you
to give something back: sponsor the work, contribute code or documentation, or tell others about it
([Give and donate](give-and-donate.md), [Contribute to the project](contribute-to-the-project.md)).

## Related pages

- [Getting started](../getting-started.md)
- [The installation book](../../install/README.md)
- [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
