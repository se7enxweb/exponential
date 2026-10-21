---
title: The content model for engineers
audience: software-engineer, developer
category: software-engineering
order: 150
summary: The Exponential content model as a data model - classes and attributes defined at run time, objects with versions and translations, nodes in a tree, datatypes that own storage and validation, sections, object states and relations - with the kernel classes behind each, and what this means for queries, migrations and APIs.
keywords: content model, content class, attribute, datatype, content object, version, translation, node, location, tree, section, object state, relation, eZContentObject, eZContentObjectTreeNode, eZContentClass, eZDataType, software engineer
type: help
---
# The content model for engineers

This page is for engineers who need to understand the content model as a data model before they design on top of it:
what the entities are, which kernel class represents each, and what follows for queries, migrations and APIs. The
editor's view of the same model is in [The content model and editing content](../../guides/content-model-and-editing.md).

## Contents

1. [The entities](#the-entities)
2. [Runtime-defined schema](#runtime-defined-schema)
3. [Versions and translations](#versions-and-translations)
4. [The tree](#the-tree)
5. [Datatypes](#datatypes)
6. [Consequences for engineers](#consequences-for-engineers)

## The entities

| Entity | Meaning | Kernel class |
|---|---|---|
| Content class | a type of content: name, identifier, attributes | `eZContentClass` |
| Class attribute | one field of a class, with a datatype and its settings | `eZContentClassAttribute` |
| Content object | one piece of content, an instance of a class | `eZContentObject` |
| Version | a state of an object: draft, published, archived | `eZContentObjectVersion` |
| Object attribute | the value of one class attribute in one version and one language | `eZContentObjectAttribute` |
| Node (location) | a place of an object in the content tree; one object can have several | `eZContentObjectTreeNode` |
| Datatype | storage, validation, editing and indexing of one kind of attribute | subclasses of `eZDataType` |
| Section | a partition of content used by permissions and designs | `eZSection` |
| Object state | a value from a state group, usable in permissions | `eZContentObjectState` |

## Runtime-defined schema

Content classes are data, not code. Administrators add classes and attributes in the administration without a
deployment, and the tables do not change: attribute values live in generic columns of `ezcontentobject_attribute`,
interpreted by the datatype. Classes can also be shipped in packages ([Packages](../../guides/packages.md)).

That flexibility has a price: a query on a field value is a join through the attribute table, filtered by class
attribute; use the fetch functions and their attribute filters rather than hand-written SQL.

## Versions and translations

- Every edit creates a new version; publishing makes it the current version. Unpublished edits stay as drafts.
- The number of archived versions kept is limited per class by `content.ini [VersionManagement]`
  (`DefaultVersionHistoryLimit`, `VersionHistoryClass[]`); `exp:cleanupversions` removes old versions (read its
  `--help` first: without it, it deletes).
- Each object attribute exists per language. Which languages a siteaccess shows, and the fallback, are settings
  ([Content languages](../../guides/content-languages.md), [Content object fallback locale](../../bc/6.0/content-object-fallback-locale.md)).

## The tree

Nodes form one tree; every node has a parent, a path string and a depth. Node 2 is the root of the content tree in a
default installation. Permissions with subtree limitations, URL aliases and most lists follow the tree, so its shape is
part of your design. Moving or hiding a node changes the URLs and the visibility of everything below it.

## Datatypes

A datatype is the extension point of the content model: one PHP class that stores, validates, edits, renders and
indexes one kind of attribute. Datatypes register in `settings/content.ini [DataTypeSettings] AvailableDataTypes[]`;
an extension adds its own through `ExtensionDirectories[]`. A datatype must validate input in
`validateObjectAttributeHTTPInput()` and must not change the stored format of existing attributes without an update
script ([Custom datatypes](../creators/app-creators/custom-datatypes.md), [Datatype lifecycle](../creators/app-creators/datatype-lifecycle.md)).

## Consequences for engineers

| Concern | Consequence |
|---|---|
| Reads | use fetch functions (templates) or the content API (PHP); they apply permissions ([The content API](../creators/app-creators/the-content-api.md)) |
| Writes | create and publish through the kernel so versions, caches, search and the audit trail stay consistent ([Creating content in code](../creators/app-creators/creating-content-in-code.md)) |
| Migrations of content structure | class changes are data changes; plan them with packages or scripts, not SQL |
| APIs | the REST API exposes objects, nodes and attributes with the same permission checks ([API design](api-design-rest-ezjscore-headless.md)) |
| Large operations | removes, copies and moves of big subtrees run as content jobs in the background ([Content jobs](../../bc/6.0/content-jobs.md)) |
| Caches | a publish clears the view caches of the affected nodes through `eZContentCacheManager` ([Cache invalidation and clearing](cache-invalidation-and-clearing.md)) |

## Related pages

- [Persistence and the database layer](persistence-and-the-database-layer.md)
- [Content functions from PHP](../extension-development/part-8-reference/content-functions-from-php.md)
- [eZContentObject reference](../extension-development/part-8-reference/ezcontentobject.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
