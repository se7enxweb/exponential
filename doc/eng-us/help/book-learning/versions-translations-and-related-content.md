---
title: "Versions, translations and related content"
audience: editor, admin, builder
category: books
order: 290
summary: Going back to an older version, adding a translation, and the two ways to relate objects (the generic related-objects list and the relation datatypes), with the settings that control where browsing starts.
keywords: version, draft, copy version, translation, translate, related objects, relation, ezobjectrelation, ezobjectrelationlist, browse.ini, AddRelatedObject, Learning book
type: help
---
# Versions, translations and related content

## Versions

The version part of the edit screen lets you do two things: carry on with the draft you made, or pick an older version
and **copy it to a new draft**, then publish that. Nothing is overwritten; the history stays within the limit of
[classes, attributes and objects](classes-attributes-and-objects.md). The administration also has a **Versions** view
with side by side display and a list of your drafts: [Content history](../../guides/content-history.md),
[Finding and finishing drafts](../manual/how-do-i-find-and-finish-my-drafts.md).

## Translations

Languages are added in **Setup > Languages** (if you did not choose them at install; see
[Content languages](../../guides/content-languages.md)). To translate an object, edit it, use **Manage** in the
translations part of the edit screen, choose the languages and add them. The translation screen shows the original
text beside the new text to guide the translator. In Exponential this is also described in
[Translating content](../manual/how-do-i-translate-content.md) and
[Finding what is not translated](../manual/how-do-i-find-what-is-not-translated.md).

## Related content

Objects can be related in two ways.

1. **The generic relation list.** The **Related objects** panel of the edit screen holds any number of relations to any
   objects, added by browsing the tree. It belongs to the object, not to an attribute.
2. **The relation datatypes**, `ezobjectrelation` (one object) and `ezobjectrelationlist` (many). The list type is more
   powerful: you can have several, so related objects are categorized ("authors", "see also"); you can limit the
   classes that may be related; and you can create new objects from the attribute.

Where browsing starts is set in `browse.ini`. For the generic relation list the section is `[AddRelatedObject]`
(`StartNode=content`, `SelectionType=multiple`, `ReturnType=ObjectID`). For the datatypes, `content.ini
[ObjectRelationDataTypeSettings] ClassAttributeStartNode[]` gives a start node per class attribute.

From the other side, the **reverse related** objects (what points to this object) are available to templates with the
fetch function `reverse_related_objects`; see [Fetch functions](../template-operators/fetch-functions-how-they-work.md).

Next: [workflows and triggers](workflows-and-triggers.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
