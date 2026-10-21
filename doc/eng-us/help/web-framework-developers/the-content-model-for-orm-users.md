---
title: The content model for ORM users
audience: web-framework-developer, developer
category: web-frameworks
order: 140
summary: Content classes, objects, versions, translations, nodes, datatypes, sections and object states explained in the terms of an ORM - what each is in the database and in PHP, how a typical blog or catalog maps onto it, and the rules for reading and writing content from code.
keywords: content model, content class, content object, version, translation, node, location, datatype, attribute, data map, section, object state, remote id, eZContentObject, eZContentObjectTreeNode, eZContentClass, eZContentFunctions, ORM, web framework developer
type: help
---
# The content model for ORM users

This page is for developers who would start a project by writing entity classes and migrations. In Exponential, most
data that editors manage is **content**: you define its types in the administration (or ship them as packages), and the
kernel stores, versions, translates, permits, caches and indexes it. Your code reads and writes content through the
content API instead of a model class of its own.

## Contents

1. [The pieces in ORM terms](#the-pieces-in-orm-terms)
2. [Datatypes](#datatypes)
3. [Versions and translations](#versions-and-translations)
4. [Nodes: one object, several places](#nodes-one-object-several-places)
5. [Sections, states and relations](#sections-states-and-relations)
6. [A blog, mapped](#a-blog-mapped)
7. [Reading and writing from code](#reading-and-writing-from-code)

## The pieces in ORM terms

| Exponential | Closest ORM idea | Class |
|---|---|---|
| Content class (Article: Title, Intro, Body, Image) | an entity or model definition | `eZContentClass` |
| Class attribute | a field definition with its type and settings | `eZContentClassAttribute` |
| Content object | one row of the entity, with its history | `eZContentObject` |
| Version | a revision of the row: draft, published, archived and more | `eZContentObjectVersion` |
| Object attribute | one field's value, per version and language | `eZContentObjectAttribute` |
| Node (location) | where the row appears in the site tree; parent and children | `eZContentObjectTreeNode` |
| Remote ID | a stable external key you choose, unlike the numeric id | `remote_id` of objects and nodes |

The administrator's view of the same terms: [The content model and editing content](../../guides/content-model-and-editing.md#1-how-the-content-model-fits-together).

## Datatypes

Each class attribute has a **datatype**, which is a column type, a form widget, a validator and a renderer in one:
`ezstring` (one line), `eztext`, `ezxmltext` (rich text), `ezinteger`, `ezfloat`, `ezboolean`, `ezdate`, `ezdatetime`,
`ezemail`, `ezurl`, `ezimage`, `ezbinaryfile`, `ezobjectrelationlist`, `ezselection`, `ezmatrix`, `ezuser` and more
(the shipped ones are in `kernel/classes/datatypes/`). The ones offered for new attributes are
`content.ini [DataTypeSettings] AvailableDataTypes[]`. A new kind of field is a datatype class extending
`eZDataType` in your extension ([Custom datatypes](../creators/app-creators/custom-datatypes.md)).

## Versions and translations

- Every edit creates a **version**; publishing makes it current and archives the previous one. Drafts are versions
  with their own status (`eZContentObjectVersion::STATUS_DRAFT`, `STATUS_PUBLISHED`, `STATUS_ARCHIVED` and others).
- Each version holds the attribute values for each **language** it is translated into. Which language a siteaccess
  shows follows its `SiteLanguageList` ([Content languages](../../guides/content-languages.md)).
- History, compare and restore come with it; you write none of it.

## Nodes: one object, several places

An object is placed in the tree by **nodes**. One object can have several nodes (the same article in "News" and in
"Sport"); one is its main node. The tree gives you navigation, URL aliases, subtree permissions and subtree queries
(`path_string`, `depth`). There is no separate "category" table to design unless you want one.

## Sections, states and relations

| Feature | Use | Read |
|---|---|---|
| Section | a group of content for permissions and design (Standard, Users, Media...) | [Sections](../../guides/sections.md) |
| Object state | a workflow-like status per object (for example "locked", "in review") usable in policies and request rules | [Object states](../../guides/object-states.md) |
| Object relation | a link from one object to others, kept with reverse lookups | [The content API](../creators/app-creators/the-content-api.md) |

## A blog, mapped

| Framework approach | Exponential approach |
|---|---|
| `posts` table, `Post` model, migration | class `blog_post` (title, intro, body, image, tags) created in **Setup > Classes** or installed from a package |
| `categories` table, foreign key | folders in the tree, or a relation attribute, or tags from `eztags` |
| `post_revisions` table | versions, built in |
| `post_translations` table | translations, built in |
| `slug` column, unique index | URL alias, built in ([Pretty URLs](url-aliases-and-wildcards.md)) |
| Admin CRUD screens | the administration, built in |
| Policy class per model | roles with `content/read`, `content/edit` limited by class, section, subtree ([Authorization](authorization-roles-and-policies.md)) |
| Controller and templates for the front end | override templates for the class's full and line views ([Template resolution](template-resolution-designs-and-overrides.md)) |

## Reading and writing from code

```php
$node   = eZContentObjectTreeNode::fetchByURLPath( 'about_us' );
$object = eZContentObject::fetchByRemoteID( 'expdemo-settings-page' );
$title  = $object->dataMap()['title']->content();

$article = eZContentFunctions::createAndPublishObject( array(
    'creator_id'       => $userID,
    'class_identifier' => 'article',
    'parent_node_id'   => $parentNodeID,
    'remote_id'        => 'feed-item-' . $externalID,
    'attributes'       => array( 'title' => 'A new article' ) ) );
```

Rules:

- **Write content only through the content API** (`eZContentFunctions`, operations), never with SQL on the content
  tables: publishing runs workflows, clears caches and updates the search index.
- **Prefer remote IDs** for content your code depends on; numeric IDs differ between installations.
- **Set a `Limit`** on every list fetch.

Full reference: [The content API in PHP](../creators/app-creators/the-content-api.md) and
[Creating and updating content in code](../creators/app-creators/creating-content-in-code.md).

## Related pages

- [ORM and active record: eZPersistentObject](orm-and-ezpersistentobject.md)
- [Designing your content model](../creators/website-creators/designing-your-content-model.md)
- [Datatype life cycle](../creators/app-creators/datatype-lifecycle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
