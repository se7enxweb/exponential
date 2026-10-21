---
title: "Inside the core: content classes, workflows, notifications, information collection, search"
audience: developer
category: books
order: 510
summary: A map of the kernel classes behind the concepts of the earlier chapters: content classes and objects, workflows and triggers, notifications, information collection and search, with where each lives now.
keywords: eZContentClass, eZContentObject, eZContentObjectAttribute, eZContentObjectVersion, eZContentObjectTreeNode, eZWorkflow, eZWorkflowEvent, notification, eZInformationCollection, eZSearch, kernel/classes, Learning book
type: help
---
# Inside the core: content classes, workflows, notifications, information collection, search

The second half of the book's chapter 4 shows how the concepts map to kernel classes. The names are still the names.

| Concept | Kernel classes (in `kernel/classes`) | Notes |
|---|---|---|
| [Content class](creating-a-content-class.md) | `eZContentClass`, `eZContentClassAttribute` | A class has attributes; each attribute has a datatype string and the data fields (`data_int1`..`data_text5`) the datatype uses for its settings. |
| [Content object](classes-attributes-and-objects.md) | `eZContentObject`, `eZContentObjectVersion`, `eZContentObjectAttribute` | An object has versions; each version has attributes; the attribute's value is stored by its datatype. |
| [Node tree](the-node-tree-and-sections.md) | `eZContentObjectTreeNode` | A node; also the place all the tree fetches are written. |
| [Workflows and triggers](workflows-and-triggers.md) | `eZWorkflow`, `eZWorkflowEvent`, `eZWorkflowProcess`, workflow event types in `kernel/classes/workflowtypes` | An operation (such as publish) asks the workflow system whether a trigger has a workflow; the process keeps its state until done. |
| Notifications | `kernel/classes/notification` | Events are collected, handlers turn them into mails, a cronjob sends them ([Notifications for developers](../../guides/notifications-developer.md)). |
| Information collection | `eZInformationCollection` and its attributes | The data a visitor sends to an [information collector](creating-and-editing-objects.md) object. |
| Search | `eZSearch` and the engine in `kernel/search/plugins` (the default `ezsearchengine`) | Words are indexed when an object is published; other engines (Solr through `ezfind`) plug in the same way. |

## Creating an object in code

The book builds an object by hand from a class, a version and attributes. That is how the model works inside, and the
classes above are the parts: `eZContentClass::fetchByIdentifier()` gives the class, `instantiate()` makes the object with
its first draft, the attributes hold the values, and publishing goes through the content operation so that workflows run.
You rarely write those steps yourself any more. `eZContentFunctions::createAndPublishObject()` does all of them in
one call, and `updateAndPublishObject()` changes an object the same way. The page
[Creating and updating content in code](../creators/app-creators/creating-content-in-code.md) has the tested examples,
the value format for each datatype, and idempotent imports. To run a script, see
[CLI scripts and console commands](../extension-development/part-3-extension-developer/cli-scripts-and-console-commands.md).

## Information collection: the settings

The collector is set up in `collect.ini`. Three types ship (`poll`, `form`, `feedback`), chosen by default with
`[InfoSettings] Type=form`, or per object by an attribute (`TypeAttribute=collection_type`). The main settings, each with a
`...List[<type>]` form for one type and an `...Attribute` form that lets an attribute of the object decide:

| Setting | Meaning |
|---|---|
| `[CollectionSettings] CollectAnonymousData` | whether visitors who are not logged in may submit |
| `[CollectionSettings] CollectionUserData` | `multiple` (many submissions per user), `unique` (one; a second gets a warning), `overwrite` (one; a new one replaces the old) |
| `[EmailSettings] SendEmail` | whether a collected item is also mailed |
| `[DisplaySettings] Display`, `RedirectURL` | what the visitor sees after sending, and where to go |

A logged-in user is tracked by user ID, an anonymous one by the remote IP address. The shipped values make `poll` allow
anonymous data and one submission per user. Collected data is read in the administration
([Collected information](../../guides/collected-information.md)).

Next: [Extending: why, where and how](extending-why-where-how.md).

## Source and license

Adapted from chapter 4, "A Glimpse Inside the Core", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: shortened; PHP 4 code replaced by current calls; class names, settings and constants checked against Exponential 6.
