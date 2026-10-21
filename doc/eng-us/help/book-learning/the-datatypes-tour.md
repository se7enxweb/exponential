---
title: "The datatypes tour"
audience: admin, builder, developer
category: books
order: 260
summary: The datatypes of the Learning book, one by one, with what each stores, what to watch for, and what changed: dates, the deprecated enum, images, the keyword and matrix types, relations.
keywords: datatype, ezstring, eztext, ezxmltext, ezauthor, ezbinaryfile, ezboolean, ezdate, ezemail, ezenum, ezfloat, ezinteger, ezidentifier, ezimage, ezinisetting, ezisbn, ezkeyword, ezmatrix, ezmedia, ezobjectrelation, ezobjectrelationlist, ezoption, ezselection, Learning book
type: help
---
# The datatypes tour

The book walks through the datatypes one at a time, saying for each what it stores and whether it can be searched or
used to collect information from visitors ([creating and editing objects](creating-and-editing-objects.md)). The same
datatypes exist, and more. This page keeps the book's descriptions and adds today's notes. Which of them to choose for
which job is the subject of [Choosing datatypes](../creators/website-creators/choosing-datatypes.md); the code-side
view is [Datatypes](../extension-development/part-3-extension-developer/datatypes.md). The list on your own site is
the one the class editor shows, because `content.ini [DataTypeSettings] AvailableDataTypes[]` decides it.

## Text and numbers

| Datatype | What it stores | Notes |
|---|---|---|
| `ezstring` | one line of text, with a maximum length and a default | |
| `eztext` | several lines of plain text | |
| `ezxmltext` | structured text ([rich text tags](rich-text-tags.md)) | The online editor writes it for the editor. |
| `ezinteger`, `ezfloat` | whole and decimal numbers, with minimum, maximum and default checked on input | Integers can be searched; the book said floats cannot. |
| `ezemail` | one e-mail address | Only the format is checked, never that the address or domain exists. |
| `ezisbn` | an ISBN, checked for form | |
| `ezidentifier` | a generated serial string such as `doc-007-en`: prefix, suffix and number of digits | Good for serial numbers of articles, photographs or products. |
| `ezboolean` | a check box | Often used to switch a template part on or off, such as a comment button. |
| `ezauthor` | one or more authors (name and e-mail) | Not related to the users of the site. |

## Dates and times

`ezdate`, `ezdatetime` and `eztime` check their input. The book warned that dates are stored as UNIX timestamps and
therefore limited to the years 1901 to 2038 on Linux and 1970 to 2038 on Windows. That limit came from 32-bit
timestamps; on a 64-bit PHP, which every supported Exponential installation uses, it does not apply. The other warning
stands: validation is not foolproof (for example the day of the month is not checked against the month), so test odd
dates before relying on them in a filter or sort.

## Files and media

| Datatype | What it stores | Notes |
|---|---|---|
| `ezbinaryfile` | any file, with an upload limit in MB (0 for none) | Search indexing of non-text files uses the programs set in `settings/binaryfile.ini`. |
| `ezimage` | a picture; the sizes you define (aliases, like medium, small, large) are made on upload | Aliases are in `settings/image.ini`. Alternative text is required where the class asks for it ([content model](../../guides/content-model-and-editing.md)). |
| `ezmedia` | a media clip, with a size limit | The book's Flash, RealPlayer and Quicktime formats are history; use files and a player in the template. |

## Choices

| Datatype | What it stores | Notes |
|---|---|---|
| `ezselection` | one or more values from a list the class defines | The book's replacement for `ezenum`. |
| `ezenum` | a list of values, single or multiple | Deprecated since 3.3 for its complexity and cost. It is still installed for old classes; do not use it for new ones. |
| `ezoption`, `ezmultioption`, `ezmultioption2`, `ezrangeoption` | options with prices for a shop | See [e-commerce](../e-commerce/e-commerce-reading-path.md). |
| `ezkeyword` | comma-separated keywords | Objects with a common keyword can be listed together ("see also"); see [common template tasks](common-template-tasks.md). |
| `ezcountry` | a country (or several) | |

## Structure

| Datatype | What it stores | Notes |
|---|---|---|
| `ezmatrix` | a table: a set number of columns, rows the author can add | The book calls it among the most versatile types, for a varying number of items in one object. |
| `ezobjectrelation` | one related object | Cannot restrict the class of the object chosen. |
| `ezobjectrelationlist` | many related objects, optionally creating them, with a start node for browsing | `content.ini [ObjectRelationDataTypeSettings] ClassAttributeStartNode[]` sets the start node. Useful to build compound objects and categories. |
| `ezinisetting` | an INI setting, chosen by siteaccess, file, section and parameter, changed from an object | Use with caution: anyone who may edit the object changes the setting. |
| `ezuser` | the account (login, e-mail, password) of a user object | |
| `ezurl` | a link with its text | Stored in a table of links so link checks can find it. |

Extensions add more datatypes; examples are tags (`eztags`), a map location (`ezgmaplocation`) and star ratings
(`ezstarrating`). See [Datatypes](../extension-development/part-3-extension-developer/datatypes.md) to learn how one is built.

Next: [rich text tags](rich-text-tags.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
