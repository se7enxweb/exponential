---
title: Coming from WordPress
audience: cms-developer
category: cms-development
order: 4
summary: For WordPress developers, how what you know maps to Exponential. It covers post types and custom fields, themes and the template hierarchy, plugins and hooks, WP_Query, shortcodes and blocks, wp-config.php, WP-CLI and wp-cron, and what to unlearn first.
keywords: WordPress, coming from WordPress, post type, custom fields, ACF, theme, template hierarchy, plugin, hooks, actions, filters, WP_Query, shortcode, Gutenberg, block editor, wp-config, WP-CLI, wp-cron, migration
type: help
---
# Coming from WordPress

This page is for developers who know WordPress and start an Exponential project. It translates the WordPress
concepts you use every day into their Exponential counterparts and points at the page that explains each. It
compares concepts only; it does not rank the two systems, which are built for different starting points.

## Contents

1. [The biggest difference](#the-biggest-difference)
2. [Concept by concept](#concept-by-concept)
3. [Templates: from the template hierarchy to overrides](#templates-from-the-template-hierarchy-to-overrides)
4. [Queries: from WP_Query to fetch](#queries-from-wp_query-to-fetch)
5. [Plugins and hooks: extensions, events and operators](#plugins-and-hooks-extensions-events-and-operators)
6. [Configuration and the command line](#configuration-and-the-command-line)
7. [What to unlearn](#what-to-unlearn)
8. [Moving a WordPress site](#moving-a-wordpress-site)

## The biggest difference

WordPress starts from posts and pages and lets you add custom post types and custom fields on top, often through
plugins. Exponential starts from a **content model you define**: every kind of content is a **content class** with
typed **attributes**, and everything, from a page to an image or a user, is an object of a class placed in one
**content tree** ([The content model](the-content-model.md)). Expect to spend the first day of a project on classes
rather than on a theme.

## Concept by concept

| In WordPress | In Exponential | Read |
|---|---|---|
| Post type, custom post type | content class | [Content classes and attributes](content-classes-and-attributes.md) |
| Custom field, meta box, ACF field | class attribute of a datatype | [Choosing datatypes](choosing-datatypes.md) |
| Post, page | content object with versions | [Versions, drafts and publishing](versions-drafts-and-publishing.md) |
| Page parent, menu order | node in the content tree, priority | [Objects, nodes and locations](objects-nodes-and-locations.md) |
| Category, tag | locations, `ezkeyword` or `eztags` | [Relations and embeds](relations-and-embeds.md) |
| Media library attachment | image or file object in the media library | [Images and image aliases](images-and-image-aliases.md) |
| Revisions | versions, with compare and restore | [Versions, drafts and publishing](versions-drafts-and-publishing.md) |
| Roles and capabilities | roles with policies, limited by section, subtree, class | [Sections, roles and policies](sections-roles-and-policies.md) |
| Permalinks | URL aliases made from the tree | [URL aliases and routing](url-aliases-and-routing.md) |
| Theme | design in a design extension | [Templates and overrides](templates-and-overrides-for-cms-developers.md) |
| Plugin | extension | [Extensions you will write](extensions-you-will-write.md) |
| Shortcode | custom tag in rich text | [Rich text](rich-text.md) |
| Block editor layouts | Exponential Layouts (zones and blocks) | [Layouts for CMS developers](layouts-for-cms-developers.md) |
| Multisite network | siteaccesses on one installation | [Siteaccesses and multisite](siteaccesses-and-multisite.md) |
| REST API | the REST interface of `ezprestapi`, and `expservices` | [The REST API](the-rest-api.md) |

## Templates: from the template hierarchy to overrides

WordPress picks a PHP template file by a naming hierarchy (`single-{post_type}.php`, `page-{slug}.php` and so on).
Exponential's templates are written in its own template language (`.tpl`), not PHP, and a template is chosen by
**override rules** in `override.ini`: "for `node/view/full.tpl`, when the class is `exp_event`, use
`full/exp_event.tpl`". Rules can match the class, the node, the parent, the section, the depth, the view mode and more.
The template language escapes output with `|wash()` and has no direct access to PHP, which keeps templates safe to
hand to designers. Start with
[Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md); the language itself is in
[The template language](../template-language/README.md).

## Queries: from WP_Query to fetch

Where a WordPress template calls `WP_Query` or `get_posts()`, an Exponential template calls a **fetch function**:

```smarty
{def $events = fetch( 'content', 'list', hash(
        'parent_node_id', $node.node_id,
        'class_filter_type', 'include',
        'class_filter_array', array( 'exp_event' ),
        'sort_by', array( 'attribute', true(), 'exp_event/start_date' ),
        'limit', 10 ) )}
```

Fetches check permissions and hidden nodes for you. Details:
[Fetching content in templates](fetching-content-in-templates.md); in PHP: [Content from PHP](content-from-php.md).

## Plugins and hooks: extensions, events and operators

A WordPress plugin hooks into actions and filters. An Exponential **extension** is a folder under `extension/` that
can add settings, templates, modules (pages with their own URL), template operators, datatypes, workflow events,
cronjobs and console commands. Instead of one hook system there are several precise extension points:

| WordPress habit | Exponential |
|---|---|
| `add_action` on save or publish | a workflow event on the publish trigger, or an event listener ([Workflows and triggers](workflows-and-triggers.md), [Events, listeners and filters](../extension-development/part-3-extension-developer/events-and-filters.md)) |
| `add_filter` on output | a template operator, or an override of the template ([Template operators](../extension-development/part-3-extension-developer/template-operators.md)) |
| A custom admin page | a module with a view and a policy ([Modules and views](../extension-development/part-3-extension-developer/modules-and-views.md)) |
| `functions.php` helpers | template operators and PHP classes in your extension |

The whole list: [Extension points at a glance](../extension-development/part-8-reference/extension-points-at-a-glance.md).

## Configuration and the command line

| WordPress | Exponential |
|---|---|
| `wp-config.php` | INI files: shipped defaults in `settings/`, your values in `settings/override/` and `settings/siteaccess/<name>/` ([Settings and INI](settings-and-ini-for-cms-developers.md)) |
| Options table | INI settings, read and changed with `./console exp:ini` |
| WP-CLI | `./console` (`bin/php/console`) and the scripts in `bin/php/` |
| wp-cron | cronjobs run by the system crontab ([Cronjobs](cronjobs.md)) |
| Page caching plugins | view cache, HTTP cache and the Velocity response cache, built in ([Caching for CMS developers](caching-for-cms-developers.md)) |

## What to unlearn

- **Do not query the database from templates or plugins.** Use fetch functions and the kernel API; they keep
  permissions, versions, caches and search right.
- **Do not put markup in content.** Rich text is stored as XML and drawn by templates; editors choose structure, not
  HTML ([Rich text](rich-text.md)).
- **Do not edit shipped files.** Kernel files and `settings/*.ini` are replaced on update; your work lives in your
  extension and in override settings.
- **Clear the right cache.** Templates, settings and content views are cached; [Caching for CMS
  developers](caching-for-cms-developers.md) says which cache follows which change.

## Moving a WordPress site

The migration guide covers the WXR export, mapping, HTML to rich text, permalinks and redirects step by step:
[WordPress and Exponential](../../install/migration/wordpress.md). For the tools that import the result, see
[Importing content](importing-content.md); the trainer's lesson is
[From WordPress](../trainers/migration/module-07-from-other-systems/from-wordpress.md).

## Related pages

- [Exponential for CMS developers](exponential-for-cms-developers.md)
- [Mapping content to Exponential](../../install/migration/content-model-mapping.md)
- [Moving from Smarty](../template-language/moving-from-smarty.md): if you know Smarty, the template language will look familiar
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
