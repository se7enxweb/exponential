---
title: "Classes, attributes and objects"
audience: user, editor, developer, builder
category: books
order: 220
summary: How a content class defines a kind of content through its attributes, how objects are created from it, and how versions, translations and the version limit work.
keywords: content class, class attribute, datatype, content object, version, version history, translation, DefaultVersionHistoryLimit, content.ini, Learning book
type: help
---
# Classes, attributes and objects

## Class, attribute, object

- A **content class** says how one kind of content is structured: an article, a folder, a person.
- A **class attribute** is one field of the class, of a given **datatype** (a short text, rich text, a date, an image).
  A datatype is the lowest level of typing, though several are powerful compound types
  ([the datatypes tour](the-datatypes-tour.md)).
- A **content object** is an instance of a class. Its content elements are the attributes the class names. Besides those
  it has generic properties: object ID, name, creation date, owner and more. The name is built from one or more
  attributes by the class's **object name pattern**.

Exponential ships a large set of datatypes, and extensions add more (tags, a map location, star ratings).

## Versions

An object is always under **version control**. Editing makes a draft; publishing makes it the current version and the
previous one an archived version. The history is kept within a limit, and can be changed in `content.ini`:

```ini
[VersionManagement]
DefaultVersionHistoryLimit=45
VersionHistoryClass[1]=75
```

`DefaultVersionHistoryLimit` is the number of versions to keep for every class; `VersionHistoryClass[<class id>]` sets it
for one class (here, class 1 keeps 75). Two things from the book still hold:

- A limit below **2** is not honored: editing needs a draft beside the published version, so the kernel keeps at least 2.
- Older versions are not edited; to go back, copy the older version to a new draft and publish it. For a site with
  quality rules for documents, this is the document history.

Leftover drafts (an editor who left a window) are cleaned up by settings in the same section (`DraftsDuration` and
related) and by a cronjob; see [Drafts and pending items](../../guides/drafts-and-pending.md).
The book noted that comparing versions needed a diff tool. Today the version view shows the versions side by side
([Content history](../../guides/content-history.md)).

Version management applies to **every** attribute, binary files included.

## Translations

An object can be translated into other languages. An attribute that should not be translated (a photograph, say) can be
marked so at the class level. See [Content languages](../../guides/content-languages.md) and
[Translating content](../manual/how-do-i-translate-content.md).

## Where to go on

[Content model and editing](../../guides/content-model-and-editing.md) is the reference for this in the administration.
Next: [content and presentation](content-and-presentation.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
