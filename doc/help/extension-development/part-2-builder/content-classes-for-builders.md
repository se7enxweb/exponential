---
title: Content classes for builders
audience: developer, designer, app-creator
category: extension-development
order: 170
summary: Designing the content model - classes, attributes, datatypes, identifiers, object name patterns, containers - and how the model connects to templates, overrides and fetches, with rules that keep it maintainable.
keywords: content class, content model, attribute, datatype, identifier, object name pattern, container, class group, data_map, AvailableDataTypes, content modeling
type: help
---
# Content classes for builders

Part 2, chapter 17. A **content class** is a type of content: Article, Event, Product. Its **attributes** are the
fields, each of a **datatype** (one line of text, rich text, image, relation...). Everything a builder does with
templates rests on this model, so a little care here saves a lot of template code later. The step-by-step creation of a
class is in [The content model and editing content](../../../guides/content-model-and-editing.md).

## The terms

| Term | Meaning |
|---|---|
| Content class | a type: name, identifier, attributes |
| Attribute | one field of a class: name, identifier, datatype, required or not, translatable or not |
| Datatype | what kind of value an attribute holds (`ezstring`, `eztext`, `ezxmltext`, `ezimage`, `ezobjectrelationlist` ...) |
| Content object | one piece of content of a class, with versions and translations |
| Node (location) | a place of an object in the content tree; one object can have several |

The datatypes offered when you add an attribute are `content.ini [DataTypeSettings] AvailableDataTypes[]`; extensions
add theirs (eztags adds a tags field, for example).

## Identifiers are the contract

The class identifier (`article`) and the attribute identifiers (`title`, `intro`, `body`, `image`) are what templates,
overrides and fetches use:

```text
{$node.data_map.title.content|wash}
{attribute_view_gui attribute=$node.data_map.body}
```

```ini
Match[class_identifier]=article
```

So choose them once, carefully:

- lower case, letters, digits and underscores; English words that say what the field is,
- never rename one that templates use (rename the human name instead; the identifier can stay),
- the same identifier for the same meaning in every class (`title`, `image`, `intro`), so one template part can draw
  the image of an article, a news item and an event alike.

## The object name pattern

Each class has an **object name pattern** such as `<title>` or `<first_name> <last_name>`. It builds the name shown
in the tree, in lists and in URLs. Use the attribute that people recognize the content by; URL aliases are made from
the name.

## Containers

A class marked **container** can have children. Folders, frontpages and sections of a site are containers; articles
usually are not. A builder's lists (`fetch( 'content', 'list' )`) only make sense below containers.

## Good models are boring

| Prefer | Over |
|---|---|
| one class per real kind of content | one class with a "type" selection that changes everything |
| a relation attribute to shared content (an author, a location) | copying the same data into many objects |
| rich text for long text | many small text fields for paragraphs |
| a datatype that exists | a new datatype, until you are sure (see [Datatypes](../part-3-extension-developer/datatypes.md)) |

Model the content as editors think about it; the templates follow.

## Classes in an extension

A class made in the administration lives in the database. To ship it with an extension (so another installation can
create it), there are two ways:

- export it as a **package** (`Setup > Packages`, class export) and put the package in the extension; see
  [Packages](../../../guides/packages.md),
- write it as a **script** that creates the class: the RAD wizard **Content extension** (`/setup/contentextension`)
  writes one for you ([RAD tools](../../../features/6.0/rad-tools.md)).

## Related pages

- Previous: [Caching and templates](caching-and-templates.md)
- Next: [Sections, roles and access for builders](sections-roles-and-access-for-builders.md)
- [The content model and editing content](../../../guides/content-model-and-editing.md)
- [Class groups](../../../guides/class-groups.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
