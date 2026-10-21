---
title: Coming from eZ Platform or Ibexa
audience: cms-developer
category: cms-development
order: 7
summary: For developers who know eZ Platform or Ibexa DXP and OSS. The content model you know is the same, and this page shows what differs in templates, configuration, extension points and delivery. It also covers the two Exponential destinations, the Symfony platform line and the Exponential 6 legacy kernel.
keywords: eZ Platform, Ibexa, Ibexa DXP, Ibexa OSS, coming from Ibexa, legacy kernel, Exponential Platform, Nexus, legacy bridge, Twig, TPL, siteaccess YAML, INI, field types, datatypes, RichText, XmlText, bundles, extensions, migration
type: help
---
# Coming from eZ Platform or Ibexa

This page is for developers who have built sites on eZ Platform or Ibexa DXP or OSS and now work with Exponential.
You already know the content model: it is the same one. What changes is the layer above it. This page tells you what
carries over unchanged and what to relearn, and it points to the migration chapter for real projects.

Exponential is led by 7x and the Exponential Foundation; it is not associated with eZ Systems or Ibexa.

## Contents

1. [Two Exponential destinations](#two-exponential-destinations)
2. [What you already know](#what-you-already-know)
3. [What differs on the legacy kernel](#what-differs-on-the-legacy-kernel)
4. [Field types and datatypes](#field-types-and-datatypes)
5. [Configuration: YAML to INI](#configuration-yaml-to-ini)
6. [Templates: Twig to TPL](#templates-twig-to-tpl)
7. [Bundles to extensions](#bundles-to-extensions)
8. [Where to go next](#where-to-go-next)

## Two Exponential destinations

Exponential has two lines for someone coming from the Symfony platforms
([chapter 16.1](../../install/16-migrating-from-ez-platform-and-ibexa.md#161-the-two-destinations)):

| | Exponential Platform (Nexus and the platform distributions) | Exponential 6 (this kernel), alone or as Exponential Platform Legacy |
|---|---|---|
| Stack | the Symfony stack you know, with the packages swapped for the `se7enxweb` forks | the legacy PHP kernel: INI settings, TPL templates, modules and extensions |
| Your Twig and bundles | kept | rewritten as templates and extensions |
| Effort of a move | lowest: same generation, a package swap | highest: presentation and custom code are rewritten, content is kept |

The **legacy bridge** can also run this kernel inside a Symfony platform installation, so both stacks serve one
database during a transition ([Legacy bridge](../../features/6.0/legacy-bridge.md)). The pages of this section
describe Exponential 6, the legacy kernel.

## What you already know

The content model of every generation is the legacy kernel's own. Classes (content types), objects (content items),
versions, attributes (fields), locations, URL aliases, roles and policies, sections, object states and languages are
the same tables with the same meaning
([16.6.1](../../install/16-migrating-from-ez-platform-and-ibexa.md#1661-what-the-two-share)). So these pages will
feel familiar:

| Ibexa word | Exponential word | Read |
|---|---|---|
| Content type | content class | [Content classes and attributes](content-classes-and-attributes.md) |
| Field definition, field type | class attribute, datatype | [Choosing datatypes](choosing-datatypes.md) |
| Content item | content object | [The content model](the-content-model.md) |
| Location | node (location) | [Objects, nodes and locations](objects-nodes-and-locations.md) |
| Siteaccess | siteaccess (the concept started here) | [Siteaccesses and multisite](siteaccesses-and-multisite.md) |
| Policies, limitations, sections, object states | the same | [Sections, roles and policies](sections-roles-and-policies.md) |

## What differs on the legacy kernel

From [16.6.2](../../install/16-migrating-from-ez-platform-and-ibexa.md#1662-what-you-lose-and-what-you-gain), in short:

- **Administration.** The legacy administration and the Online Editor for XmlText, instead of the Symfony admin UI.
- **Rendering.** TPL templates with the override system and template fetch functions, instead of Twig, controllers
  and query types.
- **APIs.** The legacy REST layer (`ezprestapi`, v1 and v2) and `ezjscore` server functions, instead of REST v2 and
  GraphQL of the Symfony stack ([The REST API](the-rest-api.md)).
- **Features you gain.** Workflow events, information collection, the shop, RSS import and export, newsletters,
  Exponential Layouts (the port of Netgen Layouts), Velocity as the web server, and SQLite, MongoDB and Oracle
  drivers.

## Field types and datatypes

Most field types are the legacy datatypes under the same identifier (`ezstring`, `ezimage`, `ezobjectrelationlist`
and so on). The exceptions you will meet:

- `ezrichtext` (DocBook) has no legacy datatype; legacy rich text is `ezxmltext`
  ([Rich text](rich-text.md)). Chapter 16.6.3 has the element mapping and the two ways to handle stored DocBook.
- `ezimageasset` becomes `ezobjectrelation`; `ezcontentquery` becomes a template fetch; landing pages and forms of
  the commercial editions are rebuilt with Exponential Layouts and information collection.

The full table: [16.6.3 Field types](../../install/16-migrating-from-ez-platform-and-ibexa.md#1663-field-types).

## Configuration: YAML to INI

Siteaccess YAML (`ibexa.system.<scope>`) becomes INI files: `settings/siteaccess/<name>/site.ini.append.php` for a
siteaccess, `settings/override/` for global values, and `extension/<ext>/settings/` for what your code ships. The
scopes and their order are explained in
[Settings and INI for CMS developers](settings-and-ini-for-cms-developers.md); the mapping of keys is in
[16.6.5](../../install/16-migrating-from-ez-platform-and-ibexa.md#1665-configuration-siteaccess-yaml-to-ini).

## Templates: Twig to TPL

Content view configuration (`content_view` with `match` rules) becomes `override.ini` rules with `Match[...]` keys;
`ibexa_render_field` becomes `attribute_view_gui`; query types and controllers become `fetch()` calls in the template.
[Templates and overrides for CMS developers](templates-and-overrides-for-cms-developers.md) and
[16.6.6 Templates: Twig to TPL](../../install/16-migrating-from-ez-platform-and-ibexa.md#1666-templates-twig-to-tpl)
take it from there.

## Bundles to extensions

A Symfony bundle becomes an extension in `extension/<name>`: event subscribers become workflow events or event
listeners, controllers become module views, Twig extensions become template operators, commands become console
commands or `bin/php` scripts, and services become PHP classes registered through autoloads
([Extensions you will write](extensions-you-will-write.md)).

## Where to go next

- Moving a real site: [chapter 16](../../install/16-migrating-from-ez-platform-and-ibexa.md), and for the hosting side
  [Migrating customers from eZ Platform and Ibexa](../hosting/migrations/from-ez-platform-and-ibexa.md).
- Training: [From the Symfony platforms](../trainers/migration/module-06-from-the-symfony-platforms/README.md).
- Choosing between the lines: [Choosing a path](../trainers/migration/module-06-from-the-symfony-platforms/two-destinations-and-choosing.md).

## Related pages

- [Exponential for CMS developers](exponential-for-cms-developers.md)
- [Who is 7x?](../who-is-7x.md)
- [Exponential Platform Nexus starter](../../features/6.0/platform-nexus-starter.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
