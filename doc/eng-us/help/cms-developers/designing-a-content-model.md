---
title: Designing a content model
audience: cms-developer
category: cms-development
order: 12
summary: For CMS developers starting a project, a step-by-step method from requirements to a set of classes, attributes, a content tree, sections and relations. It ends with a worked example and the review that comes before anything is built.
keywords: content model design, content modelling, requirements, content inventory, classes, attributes, content tree, sections, relations, reuse, worked example, event site, task
type: help
---
# Designing a content model

This page is for developers who start a site and have to decide its classes before anyone builds a template. It is
a task: eight steps, from the content that exists to a model you can review. The building blocks are explained in
[The content model](the-content-model.md); this page is about using them well.

## Contents

1. [Before you start](#before-you-start)
2. [Step 1: list the content, not the pages](#step-1-list-the-content-not-the-pages)
3. [Step 2: group items into classes](#step-2-group-items-into-classes)
4. [Step 3: list the fields and choose datatypes](#step-3-list-the-fields-and-choose-datatypes)
5. [Step 4: decide what is a relation](#step-4-decide-what-is-a-relation)
6. [Step 5: draw the tree](#step-5-draw-the-tree)
7. [Step 6: decide access with sections](#step-6-decide-access-with-sections)
8. [Step 7: languages and versions](#step-7-languages-and-versions)
9. [Step 8: write it down and review it](#step-8-write-it-down-and-review-it)
10. [A worked example: an events site](#a-worked-example-an-events-site)

## Before you start

You need the site's requirements, a sample of real content (not lorem ipsum), and an installation to try classes on
([Development environment](development-environment.md)). Look at what the site package you start from already
ships: the `sevenx_multisite` package brings classes of its own, and reusing them saves templates
([The sevenx classes](../site-packages/sevenx-multisite/sevenx-classes.md)).

## Step 1: list the content, not the pages

Write down every kind of item the site holds or shows: news, events, people, locations, products, downloads,
images, FAQ entries. For each, note how many there will be, who edits them and how often. A "page" in a wireframe is
usually a *view* of one item, or a list of items; design the items, and the pages follow from templates and layouts.

## Step 2: group items into classes

One class per kind of item that has its own fields. Two kinds that share every field and differ only in where they
appear are one class in two places of the tree, not two classes. Two kinds that share a few fields but behave
differently are two classes with the same attribute identifiers for the shared fields.

| Signal | Decision |
|---|---|
| Same fields, different place | one class, separate folders |
| Same fields, different rules for who edits | one class, separate sections |
| A "type" field would change most other fields | separate classes |
| An item has a list of sub-items with fields | a container class with a child class |

## Step 3: list the fields and choose datatypes

For each class write the fields with an identifier, a datatype, required or not, translatable or not, searchable or
not. Use [Choosing datatypes](choosing-datatypes.md). Reuse identifiers across classes (`title`, `intro`, `image`).
Mark which field gives the object name and, if it should differ, which one gives the URL.

## Step 4: decide what is a relation

Anything that exists on its own and is used in more than one place becomes its own object, referenced through an
`ezobjectrelationlist` attribute: a speaker used by several events, a venue, an author, a shared image. Anything that
belongs to one item only stays a field. Relations keep one source of truth; copies drift apart
([Relations and embeds](relations-and-embeds.md)).

## Step 5: draw the tree

Sketch the content tree below the root node (node 2): the folders, which classes go where, and the sort order of
each container. The tree is navigation, URL structure and permission scope at once:

- one top-level folder per main menu item makes URLs and menus follow the editors' tree;
- items that appear in two places get a second location, or appear in a list fetched from elsewhere;
- large collections (thousands of news items) are better sorted by date and paged than split by hand into folders.

## Step 6: decide access with sections

List who may read what and who may edit what. Every rule of the form "only members see X" or "only the events team
edits Y" becomes a section on the subtree plus a role with policies limited to that section
([Sections, roles and policies](sections-roles-and-policies.md)). Stages such as *in review* become object states.

## Step 7: languages and versions

Decide the languages, which classes are translated, which fields are not, and whether untranslated items are shown
in the main language (*always available*). Decide whether publishing needs an approval workflow
([Workflows and triggers](workflows-and-triggers.md)).

## Step 8: write it down and review it

Write the model as a table per class and a sketch of the tree, in your project repository. Review it with an editor
and a designer before building: [Content model review checklist](content-model-review-checklist.md). Then create the
classes ([Creating a content class](creating-a-content-class.md)) and keep them in files
([Content classes and attributes](content-classes-and-attributes.md#classes-in-version-control)).

## A worked example: an events site

Requirements: events with a date, a venue and speakers; speaker profiles; venue pages with a map; a news section;
members-only recordings.

| Class (identifier) | Fields (identifier: datatype) | Notes |
|---|---|---|
| Event (`exp_event`) | `title`: ezstring, `intro`: eztext, `body`: ezxmltext, `start_date`: ezdatetime, `end_date`: ezdatetime, `venue`: ezobjectrelationlist, `speakers`: ezobjectrelationlist, `image`: ezimage, `tags`: eztags | container: no; name `<title>` |
| Speaker (`exp_speaker`) | `first_name`, `last_name`: ezstring, `bio`: ezxmltext, `portrait`: ezimage, `website`: ezurl | name `<first_name> <last_name>` |
| Venue (`exp_venue`) | `title`: ezstring, `address`: eztext, `location`: ezgmaplocation, `body`: ezxmltext | one venue, many events |
| Recording (`exp_recording`) | `title`: ezstring, `event`: ezobjectrelationlist, `file`: ezmedia | in section *Members* |
| News (existing `article`) | the site package's class | reused, not redefined |

The tree:

```text
Home (node 2)
  Events (folder; event lists sort by start_date in the fetch)
  Speakers (folder, sorted by name)
  Venues (folder, sorted by name)
  News (folder, sorted by published, newest first)
  Recordings (folder, section Members)
```

Sorting a list by an attribute such as `start_date` is done in the fetch (`sort_by`, `array( 'attribute', true(),
'exp_event/start_date' )`), not in the node's own sort order, which only knows the fields listed in
[Objects, nodes and locations](objects-nodes-and-locations.md#sort-order-of-children).

Access: the Anonymous role reads section Standard; a Members role reads section Members; an Events editor role may
create `exp_event`, `exp_speaker` and `exp_venue` below the three folders.

## Related pages

- [Mapping content to Exponential](../../install/migration/content-model-mapping.md): the same decisions for a migration
- [Content classes for builders: good models are boring](../extension-development/part-2-builder/content-classes-for-builders.md#good-models-are-boring)
- [Fetching content in templates](fetching-content-in-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
