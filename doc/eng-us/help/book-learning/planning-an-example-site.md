---
title: "Planning an example site: the club portal"
audience: builder, creators, developer
category: books
order: 320
summary: The book's worked planning example, a portal for a nature photography and astronomy club, turned into a recipe for planning any site: requirements, classes, the tree, with the classes written out as attribute lists.
keywords: example site, planning, requirements, classes, taxonomy, structure, documents, images, forum, calendar, club portal, Learning book
type: help
---
# Planning an example site: the club portal

The book ends its content chapter with a planning exercise, and it is a good one. The project: a portal for a nature
photography and astronomy club. Use it as a template for your own planning, with the order the book follows:
**requirements, classes, structure, sections, groups, roles.**

## 1. Requirements

A **public part**: documents, members' pictures in galleries, discussion forums, a calendar of activities, logbooks,
news with a subscription service, and a few personal features for registered users. A **private part** for the club's
management: statutory documents, meeting reports, actions and private discussions.

The book's point: with a flexible model you often have more than one way to do it. Reduce the number of classes. Ask for
each: is it the same thing with a flag, or a different thing?

## 2. The classes

| Class | Attributes (datatype) |
|---|---|
| Document | Title (text line); Intro (rich text); Body (rich text); Attachment (file); Keywords (keywords, an automatic "related documents"); Related items (object relation list); Allow comments (check box); Show on the home page as news (check box) |
| Logbook entry | Subject (text line); Body (rich text); Related items published elsewhere (object relation list). The logbook itself is a folder. |
| Image | Title; Description (rich text); Image; Caption (text); Equipment used (rich text); Date and time; Location (rich text); Keywords |
| Forum message | Subject; Message (plain text); Type (selection: question, remark, important news ...). The forum is a folder. |
| Calendar item | Subject; Description; Location; Start and end date and time; Keywords; Category (selection); Related items |
| Dummy object | Name (text line). Used as a hook for special pages such as the home page, with a template of its own. |
| Link | The standard link class: title, description, link. |

The datatypes are in [the datatypes tour](the-datatypes-tour.md); to create a class see
[creating a content class](creating-a-content-class.md). Comments are added with templates
([common template tasks](common-template-tasks.md)). Today you could also use the shipped classes of the site package you
installed (article, folder, image, file, link, comment) and add only what is missing.

## 3. The tree

```
Home (dummy object: aggregates news and other items)
  News (with an archive navigator)
  Documents: Technical documents, Meeting reports, Miscellaneous
  Calendar of events
  Links: Astronomy related, General photography
  Galleries: Astronomy, Nature
  Forums: Announcements, Astronomy related, Nature related
  My site (personal)
  Restricted part: Forum, Statutory documents
```

The structure is plain on purpose. The visual experience comes from the templates
([content and presentation](content-and-presentation.md)). Check your plan by drawing the tree and walking one visitor
through it.

## 4. Sections, groups, roles

Next: [users, groups and roles in the example](users-groups-and-roles-example.md).

## Source and license

Adapted from chapter 2, "Content Management with eZ publish", of *Learning eZ publish: Building Content Management Solutions* by Paul Borgermans, Tony Wood,
Paul Forsyth, Martin Bauer, Björn Dieding and Ben Pirt (additional material by Bruce Morrison), second edition, 2024,
[Book-Learning-eZPublish](https://github.com/se7enxweb/Book-Learning-eZPublish). Copyright (c) 2004-2024 7x and Graham
Brookins. Licensed under the GNU Free Documentation License, version 1.3 or any later version, with no Invariant
Sections, no Front-Cover Texts and no Back-Cover Texts ([the license](../../LICENSE-GFDL-1.3.txt)).
Changes: rearranged and shortened; settings, labels, datatypes, tags and URLs checked against Exponential 6 and corrected; links to the current guides added.
