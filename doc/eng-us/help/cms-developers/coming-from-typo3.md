---
title: Coming from TYPO3
audience: cms-developer
category: cms-development
order: 6
summary: For TYPO3 developers, how the page tree, content elements and backend layouts, records and storage folders, TCA, TypoScript, Fluid, extensions, site configuration and the scheduler map to Exponential. It also says honestly what the documentation does not cover for a TYPO3 migration.
keywords: TYPO3, coming from TYPO3, page tree, content elements, tt_content, backend layout, colPos, records, sysfolder, storage folder, TCA, TypoScript, Fluid, Extbase, extension, site configuration, scheduler, workspaces, migration
type: help
---
# Coming from TYPO3

This page is for developers who know TYPO3 and start an Exponential project. Both systems are built around a tree,
so the start is easy; the difference is that in TYPO3 a page is a container of content elements, while in Exponential
every item, page or not, is a typed object in the tree. This page compares concepts only and does not rank the two
systems.

## Contents

1. [The tree, seen from both sides](#the-tree-seen-from-both-sides)
2. [Concept by concept](#concept-by-concept)
3. [Content elements and backend layouts](#content-elements-and-backend-layouts)
4. [TCA, TypoScript and settings](#tca-typoscript-and-settings)
5. [Fluid and the template language](#fluid-and-the-template-language)
6. [Extensions and the scheduler](#extensions-and-the-scheduler)
7. [Moving a TYPO3 site](#moving-a-typo3-site)

## The tree, seen from both sides

In TYPO3 the page tree holds pages, and records such as news or addresses are usually kept in storage folders
(sysfolders) and shown on pages by plugins. In Exponential the content tree holds **every** object: a news article
is a node in a news folder, and its node is also its page, with its own URL. A list of news on another page is a
fetch in a template or a Layouts block, not a plugin that reads records from a folder
([Objects, nodes and locations](objects-nodes-and-locations.md)).

Content that should not have a page of its own (for example shared snippets) can still live in a folder of the tree
and be embedded or related; hide that folder or keep it outside the site's root node.

## Concept by concept

| In TYPO3 | In Exponential | Read |
|---|---|---|
| Page in the page tree | object of a container class (folder, landing page) | [The content model](the-content-model.md) |
| Record type defined in TCA | content class | [Content classes and attributes](content-classes-and-attributes.md) |
| TCA column and its type | class attribute and datatype | [Choosing datatypes](choosing-datatypes.md) |
| Storage folder | a folder in the tree, often in its own section | [Sections, roles and policies](sections-roles-and-policies.md) |
| Content element in a column | rich text in the page object, or blocks in a layout zone | [Layouts for CMS developers](layouts-for-cms-developers.md) |
| File references (FAL) | image and file objects, related or embedded | [Images and image aliases](images-and-image-aliases.md) |
| Categories | `eztags`, or folders | [Relations and embeds](relations-and-embeds.md) |
| Backend users, groups and access lists | users and groups as content, roles with policies | [Users and registration](users-and-registration.md) |
| Languages in site configuration | content languages and siteaccesses | [Translations and languages](translations-and-languages.md) |
| Site configuration (sites, base URLs) | siteaccesses and their matching rules | [Siteaccesses and multisite](siteaccesses-and-multisite.md) |
| Workspaces | versions, drafts, object states and approval workflows | [Versions, drafts and publishing](versions-drafts-and-publishing.md) |
| Speaking URLs (slugs) | URL aliases | [URL aliases and routing](url-aliases-and-routing.md) |

## Content elements and backend layouts

A TYPO3 page arranges content elements in the columns of a backend layout. Exponential offers two ways for the same
need:

- **Structured classes.** An article has a title, an intro, a rich text body and an image as attributes; its layout
  is the template. Most pages of a site are built this way.
- **Exponential Layouts.** A layout type defines zones; editors place blocks (text, images, lists of content,
  teasers) in them, and rules decide which pages use which layout. This is the closest match to backend layouts with
  content elements ([Layouts for CMS developers](layouts-for-cms-developers.md),
  [Two ways to build](../build-two-ways/two-ways-to-build.md)).

## TCA, TypoScript and settings

TCA defines record types and fields in PHP arrays; in Exponential that definition is the class, made in the
administration and carried between installations in packages
([Creating a content class](creating-a-content-class.md)). TypoScript configures rendering and many settings; in
Exponential configuration is INI files, with shipped defaults in `settings/`, global overrides in `settings/override/`,
per-siteaccess settings in `settings/siteaccess/<name>/` and extension settings in `extension/<ext>/settings/`
([Settings and INI for CMS developers](settings-and-ini-for-cms-developers.md)). Rendering decisions that TypoScript
would make live in templates and override rules.

## Fluid and the template language

Fluid templates use XML-like view helpers. Exponential templates use a brace syntax with functions and operators
(`{if}`, `{foreach}`, `{def}`, `|wash()`, `fetch()`), and the template for a piece of content is chosen by override
rules that match its class, node, parent, section or view mode
([Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md)). Partials and layouts
correspond to `{include}` and to the pagelayout, the outer template of every page
([The pagelayout](../designers/theme-design/the-pagelayout.md)).

## Extensions and the scheduler

| TYPO3 | Exponential |
|---|---|
| Extension (`ext_emconf.php`, Composer) | extension folder with `extension.xml`, `ezinfo.php` and `composer.json` ([Extensions you will write](extensions-you-will-write.md)) |
| Extbase plugin with a controller | a module with views ([Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md)) |
| ViewHelper | template operator or function ([Template operators](../extension-development/part-3-extension-developer/template-operators.md)) |
| Signals, PSR-14 events, hooks | event listeners and workflow events ([Workflows and triggers](workflows-and-triggers.md)) |
| Scheduler tasks | cronjob scripts in cronjob parts ([Cronjobs](cronjobs.md)) |
| `typo3` command line | `./console` and `bin/php/*.php` |
| Clear caches in the backend | **Setup** > **Caches**, or `php bin/php/ezcache.php` ([Caching for CMS developers](caching-for-cms-developers.md)) |

## Moving a TYPO3 site

This documentation has no TYPO3-specific migration guide and names no tool that reads TYPO3 data directly. Use the
general method for other systems: export the records to CSV, XML or JSON (for example from the database or with a
TYPO3 extension of your choice), map them with
[Mapping content to Exponential](../../install/migration/content-model-mapping.md), and import them with the tools in
[Other applications and databases](../../install/migration/other-applications.md) and
[Importing content](importing-content.md). Plan the redirects from the old speaking URLs
([checklist](../../install/migration/checklist.md)).

## Related pages

- [Exponential for CMS developers](exponential-for-cms-developers.md)
- [Coming from Drupal](coming-from-drupal.md)
- [Planning a migration (trainers' course)](../trainers/migration/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
