---
title: Coming from Drupal
audience: cms-developer
category: cms-development
order: 5
summary: For Drupal developers, how content types and fields, nodes, taxonomy, Views, blocks, modules and hooks, Twig themes, configuration management and Drush map to Exponential. It also covers the false friends to watch for, the word "node" first of all.
keywords: Drupal, coming from Drupal, content type, field, entity, node, taxonomy, vocabulary, Views, blocks, paragraphs, modules, hooks, Twig, configuration management, Drush, settings.php, migration
type: help
---
# Coming from Drupal

This page is for developers who know Drupal and start an Exponential project. Drupal developers already think in
content types and fields, so the content model will feel familiar; the differences are in the words, in how
templates are chosen and in where configuration lives. This page compares concepts only and does not rank the two
systems.

## Contents

1. [False friends](#false-friends)
2. [Concept by concept](#concept-by-concept)
3. [Views and blocks](#views-and-blocks)
4. [Modules and hooks](#modules-and-hooks)
5. [Themes and Twig](#themes-and-twig)
6. [Configuration and Drush](#configuration-and-drush)
7. [Moving a Drupal site](#moving-a-drupal-site)

## False friends

| Word | In Drupal | In Exponential |
|---|---|---|
| **node** | a content entity: one article, one page | a **location** of a content object in the tree; the content itself is the **object** |
| **module** | an add-on package | a group of pages (views) under one URL prefix, such as `content` or `user`; add-ons are **extensions** |
| **view** | a configured query and display (Views) | a page of a module (`content/view`), and a **view mode** such as `full` or `line` |
| **block** | a placeable piece of output in a region | a block of Exponential Layouts, placed in a zone of a layout |
| **section** | (not a core concept) | a label on content used by permissions and template rules |

Keep the first row in mind: in Exponential, `$node` in a template is the place in the tree, and `$node.object` is
the content ([Objects, nodes and locations](objects-nodes-and-locations.md)).

## Concept by concept

| In Drupal | In Exponential | Read |
|---|---|---|
| Content type (node bundle) | content class | [Content classes and attributes](content-classes-and-attributes.md) |
| Field and field type | class attribute and datatype | [Choosing datatypes](choosing-datatypes.md) |
| Entity reference field | `ezobjectrelationlist` | [Relations and embeds](relations-and-embeds.md) |
| Paragraphs | child objects of small classes, or Layouts blocks | [Designing a content model](designing-a-content-model.md) |
| Taxonomy vocabulary and terms | `eztags`, or folders in the tree | [Choosing datatypes](choosing-datatypes.md) |
| Menu links, book outline | the content tree and its sort order | [Objects, nodes and locations](objects-nodes-and-locations.md) |
| Revisions and content moderation | versions, object states, approval workflows | [Versions, drafts and publishing](versions-drafts-and-publishing.md) |
| Content translation | translations of the same object | [Translations and languages](translations-and-languages.md) |
| Roles and permissions | roles with policies and limitations | [Sections, roles and policies](sections-roles-and-policies.md) |
| Path aliases, redirects | URL aliases, URL wildcards | [URL aliases and routing](url-aliases-and-routing.md) |
| Webform | information collection | [Forms and information collection](forms-and-information-collection.md) |
| Multisite | siteaccesses on one installation and one database | [Siteaccesses and multisite](siteaccesses-and-multisite.md) |
| JSON:API, REST | the REST interface of `ezprestapi`, `expservices` | [The REST API](the-rest-api.md) |

## Views and blocks

A Drupal View is configuration that builds a query and a display. In Exponential the query is a **fetch function**
called in a template (`fetch( 'content', 'list', hash( ... ) )`), and the display is that template
([Fetching content in templates](fetching-content-in-templates.md)). Editors who need to configure lists themselves
use **Exponential Layouts**: a block with a collection that is filled by hand or by a query, placed in a zone of a
layout, without code ([Layouts for CMS developers](layouts-for-cms-developers.md)). The two ways meet in template
blocks ([Two ways to build](../build-two-ways/two-ways-to-build.md)).

## Modules and hooks

A Drupal module implements hooks or subscribes to events. An Exponential extension uses precise extension points:

| Drupal habit | Exponential |
|---|---|
| `hook_entity_presave`, `hook_node_insert` | a workflow event on the publish trigger, or an event listener ([Workflows and triggers](workflows-and-triggers.md)) |
| A route and a controller | a module with a view (`module.php`, a view script) ([Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md)) |
| A permission in `*.permissions.yml` | a policy function of your module ([Policy functions and access](../extension-development/part-3-extension-developer/policy-functions-and-access.md)) |
| A Twig filter or function | a template operator or function ([Template operators](../extension-development/part-3-extension-developer/template-operators.md)) |
| `hook_cron` | a cronjob script in a cronjob part ([Cronjobs](cronjobs.md)) |
| A field type plugin | a datatype ([Datatypes](../extension-development/part-3-extension-developer/datatypes.md)) |

## Themes and Twig

Drupal themes use Twig with template suggestions such as `node--article--full.html.twig`. Exponential designs use
its own template language (`.tpl`), and the "suggestions" are explicit **override rules** in `override.ini` that
match class, node, parent, section, view mode and more
([Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md)). View modes exist in
both systems with the same idea: `full`, `line` and your own ([View modes](view-modes.md)). Designs fall back
through a chain of designs, much like a base theme ([The design chain](../extension-development/part-2-builder/the-design-chain.md)).

## Configuration and Drush

| Drupal | Exponential |
|---|---|
| `settings.php` | `settings/override/site.ini.append.php` and the siteaccess folders |
| Configuration export (YAML) | INI files in your extension, under version control; classes and content in packages |
| `drush cr` | `php bin/php/ezcache.php --clear-all` or targeted `--clear-id` / `--clear-tag` |
| `drush` commands | `./console` and `bin/php/*.php` |
| `drush config:get` / `config:set` | `./console exp:ini get` / `exp:ini set` |

Classes live in the database, as Drupal's content types live in active configuration; to version them, export them
in packages ([Content classes and attributes](content-classes-and-attributes.md#classes-in-version-control)).

## Moving a Drupal site

The migration guide covers JSON:API and the Migrate API, mapping, paragraphs, path aliases and redirects:
[Drupal and Exponential](../../install/migration/drupal.md). The trainer's lesson is
[From Drupal and Joomla](../trainers/migration/module-07-from-other-systems/from-drupal-and-joomla.md), and the tools
that import the result are in [Importing content](importing-content.md).

## Related pages

- [Exponential for CMS developers](exponential-for-cms-developers.md)
- [Coming from WordPress](coming-from-wordpress.md), [Coming from TYPO3](coming-from-typo3.md)
- [Mapping content to Exponential](../../install/migration/content-model-mapping.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
