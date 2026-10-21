---
title: Glossary for CMS developers
audience: cms-developer
category: cms-development
order: 49
summary: For CMS developers, the words of Exponential in one place, with the term other systems use where it differs and the page that explains each. It covers content, presentation, configuration, delivery and the project.
keywords: glossary, terms, definitions, content class, attribute, datatype, object, node, location, main node, section, object state, siteaccess, design, override, view mode, pagelayout, fetch, operator, extension, cronjob, workflow, trigger, layout, zone, block, package, Velocity
type: help
---
# Glossary for CMS developers

This page is for developers who meet a word in Exponential and want its meaning and where to read more. Where other
systems use a different word for the same idea, it is given in brackets.

## Contents

1. [Content](#content)
2. [Presentation](#presentation)
3. [Configuration and code](#configuration-and-code)
4. [Delivery and operation](#delivery-and-operation)
5. [The project](#the-project)

## Content

| Term | Meaning | Read |
|---|---|---|
| Content class | a type of content with attributes (content type, post type, bundle) | [Content classes and attributes](content-classes-and-attributes.md) |
| Class attribute | one field of a class, of a datatype (field definition) | [Content classes and attributes](content-classes-and-attributes.md#attribute-properties) |
| Datatype | the kind of a field: storage, validation, editing, output (field type) | [Choosing datatypes](choosing-datatypes.md) |
| Content object | one item of a class, with versions and translations (entry, post, content item) | [The content model](the-content-model.md) |
| Version | a stored state of an object: draft, published, archived and others (revision) | [Versions, drafts and publishing](versions-drafts-and-publishing.md) |
| Node, location | a place of an object in the content tree | [Objects, nodes and locations](objects-nodes-and-locations.md) |
| Main node | the location that gives an object its canonical address | [Objects, nodes and locations](objects-nodes-and-locations.md#the-main-node-and-extra-locations) |
| Content tree | the one tree of all nodes; root content at node 2 | [Objects, nodes and locations](objects-nodes-and-locations.md#the-tree-paths-depth-and-the-fixed-tops) |
| Remote ID | a stable identifier of your own for an object or class | [The content model](the-content-model.md#identifiers-you-will-use-every-day) |
| Relation | a recorded reference between objects: attribute, embed, link or common | [Relations and embeds](relations-and-embeds.md) |
| XML block (`ezxmltext`) | rich text stored as XML and drawn by tag templates | [Rich text](rich-text.md) |
| Custom tag | a rich text tag of your own | [Rich text](rich-text.md#custom-tags-your-own-structure) |
| Image alias | a named size and treatment of images, in `image.ini` (image style, variation) | [Images and image aliases](images-and-image-aliases.md) |
| Section | a label per object used by roles and templates | [Sections, roles and policies](sections-roles-and-policies.md) |
| Object state | labels of your own in groups, usable in policies | [Object states](../../guides/object-states.md) |
| Role, policy, limitation | a set of permissions, one permission, a condition on it | [Sections, roles and policies](sections-roles-and-policies.md) |
| Information collector | a field visitors fill in a form on the site | [Forms and information collection](forms-and-information-collection.md) |
| Always available | an object shown in its main language where no listed language exists | [Translations and languages](translations-and-languages.md#always-available-objects) |

## Presentation

| Term | Meaning | Read |
|---|---|---|
| Design | a set of templates, styles and images; designs fall back in a chain (theme) | [Templates and overrides](templates-and-overrides-for-cms-developers.md#the-design-chain) |
| Pagelayout | the outer template of every page (layout, base template) | [The pagelayout](../designers/theme-design/the-pagelayout.md) |
| Override rule | a rule in `override.ini` that picks a template by class, node, section and more (template suggestion) | [Templates and overrides](templates-and-overrides-for-cms-developers.md#override-rules) |
| View mode | how content is shown in a context: full, line, embed, your own (view mode, display mode) | [View modes](view-modes.md) |
| Fetch function | a template function that returns content (query) | [Fetching content in templates](fetching-content-in-templates.md) |
| Template operator | a function piped onto a value in a template (filter) | [Template operators](../template-operators/README.md) |
| Cache block | a cached part of a template | [Caching for CMS developers](caching-for-cms-developers.md#cache-blocks) |
| Layout, zone, block, rule | Exponential Layouts: a page plan, its areas, its pieces, where it applies (page builder) | [Layouts for CMS developers](layouts-for-cms-developers.md) |

## Configuration and code

| Term | Meaning | Read |
|---|---|---|
| Siteaccess | one way into an installation: a name, settings and a matching rule | [Siteaccesses and multisite](siteaccesses-and-multisite.md) |
| INI file, append file | the settings format; `*.ini.append.php` overrides defaults | [Settings and INI](settings-and-ini-for-cms-developers.md) |
| Extension | a folder under `extension/` that adds designs, settings and code (plugin, module, bundle) | [Extensions you will write](extensions-you-will-write.md) |
| Module, view | a group of pages under one address prefix, and one of its pages (route, controller) | [URL aliases and routing](url-aliases-and-routing.md#pages-of-your-own) |
| URL alias | the readable address of a node (slug, path alias, permalink) | [URL aliases and routing](url-aliases-and-routing.md) |
| Workflow, event, trigger | a list of steps run before or after an operation | [Workflows and triggers](workflows-and-triggers.md) |
| Event listener | a callback for an event the kernel announces (hook, subscriber) | [Workflows and triggers](workflows-and-triggers.md#event-listeners-the-lighter-way) |
| Cronjob, part | a scheduled script, and a group of them | [Cronjobs](cronjobs.md) |

## Delivery and operation

| Term | Meaning | Read |
|---|---|---|
| Velocity | Exponential's own PHP application server, the recommended engine | [Deploying a site](deploying-a-site.md) |
| FrankenPHP | the production-ready alternative server | [Deploying a site](deploying-a-site.md#choose-how-the-site-is-served) |
| View cache | stored rendered views of nodes | [Caching for CMS developers](caching-for-cms-developers.md#the-view-cache) |
| Package (`.ezpkg`) | an archive of classes, content, extensions or a whole site | [Site packages and installers](site-packages-and-installers.md) |
| Site package | a package of type `site` that installs a whole site | [Site packages and installers](site-packages-and-installers.md#the-default-site-package) |
| `exp:install`, kickstarter, setup wizard | the three installers | [Site packages and installers](site-packages-and-installers.md#the-three-installers) |
| `ezprestapi` | the extension that serves the REST content routes | [The REST API](the-rest-api.md) |
| `expservices` | the administration's functions as HTTP services | [Headless delivery](headless-delivery.md#expservices) |

## The project

| Term | Meaning | Read |
|---|---|---|
| Exponential | the product; Exponential 6 is this PHP kernel | [Exponential for CMS developers](exponential-for-cms-developers.md) |
| 7x | the company that leads Exponential | [Who is 7x?](../who-is-7x.md) |
| Exponential Foundation | the nonprofit | [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md) |
| Exponential Platform | the Symfony-based line (Nexus and the platform distributions) | [Coming from eZ Platform or Ibexa](coming-from-ez-platform-or-ibexa.md) |

The extension developers' glossary has more of the code-side words:
[Glossary for extension developers](../extension-development/part-8-reference/glossary-for-extension-developers.md);
the designers' glossary the design words: [Design glossary](../designers/theme-design/design-glossary.md).

## Related pages

- [CMS developer FAQ](cms-developer-faq.md)
- [Two ways to build: glossary](../build-two-ways/glossary.md)
- [Site packages glossary](../site-packages/glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
