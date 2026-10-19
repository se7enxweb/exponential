---
title: "Lesson 4.2: Dynamic collections and query types"
audience: trainer, editor, website-creator
category: training
order: 42
summary: Learners give a listing block a dynamic collection, choose a query type, use the current location or a fixed parent, filter by content type and sort, and explain what makes a layout reusable. 60 minutes.
keywords: exponential layouts, dynamic collection, query, query type, children, subtree, latest, exponential content search, use current location, parent location, content type filter, sort type, sort direction
type: help
---
# Lesson 4.2: Dynamic collections and query types

A list that keeps itself current needs a **dynamic** collection: a query that runs when the page is shown. This
lesson teaches the query types and the one setting that makes a layout reusable across a whole site.

## At a glance

| | |
|---|---|
| Course | [Exponential Layouts](../README.md) |
| Module | [Module 4: Collections](README.md) |
| Duration | 60 minutes (20 presenting, 40 exercise) |
| Level | Intermediate |
| Audience | Editors and site builders |
| Prerequisites | [Lesson 4.1](listing-blocks-and-manual-collections.md); a folder with several articles on the training site |

## Learning objectives

After this lesson, learners can:

- give a listing block a dynamic collection and choose a query type;
- choose between **use current location** and a fixed parent location, and explain the difference;
- filter by content type and set the sort type and direction;
- pick a simpler query (children, subtree, latest) over the general content search when it fits.

## Trainer's notes

### How to present it

Sources: [Query types and their parameters](../../../../bc/6.0/LAYOUTS.md#query-types-and-their-parameters) and
[The Exponential content search](../../../../bc/6.0/LAYOUTS.md#the-exponential-content-search).

1. **Query types** (5 minutes). Twelve ship: **Children of a node**, **Parent of a node**, **Subtree of a node**,
   **Siblings of a node**, **Latest content**, **Random content**, **Manual collection**, relation list and reverse
   relation list, tags, the general **Exponential** content search, and **Topics**.
2. **The parameter panel** (5 minutes). Four basic parameters come first: use the topic from the current content, use
   the current location, sort type and sort direction. Everything else is advanced and collapsed. Some checkboxes
   reveal fields: *use current location* shows a parent location field when **unchecked**; *filter by content type*,
   *filter by section* and *filter by object state* show their lists when checked.
3. **The reusable layout** (5 minutes). With *use current location* ticked, a **Children of a node** query lists the
   children of whatever page is shown: one layout serves every section. Unticked, it is pinned to one place. That is
   the difference between a layout written once and one copied twenty times.
4. **Simple first** (3 minutes). Use children or subtree when they fit; the general content search when you need its
   filters. Simple queries are easier to read six months later.

### Common questions

**Why is my dynamic list empty in the editor?** With *use current location*, the editor has no current page; the
preview or the site shows the real items. Or the filter excludes everything.

**Can a dynamic list show items from two folders?** Use the content search with a common parent, or two blocks.

**Does the list update immediately when an article is published?** On the next page view, depending on the site's
caches.

### Pitfalls

- **Reversed checkboxes.** *Use current location* hides the location field when ticked; learners look for the field.
- **Too broad queries.** A subtree of the whole site without a content type filter lists folders and images too.
- **Sorting by default.** Set the sort explicitly (newest first for news).

## Hands-on exercise

Use the training installation and your course layout. The trainer named a folder with articles (for example
**Training** with the learners' course folders below it).

1. Open your course layout with **Open** (continue your draft if asked).
2. Add a second **List** block in *MAIN*, below *Hand-picked articles*. Name it `Newest articles`.
3. Give it a dynamic collection and choose the query type **Exponential** (the general content search), or **Subtree
   of a node** if your training installation offers it more simply.
4. Untick *use current location* and pick the trainer's folder as the parent location.
5. Tick *filter by content type* and choose *Article*.
6. Set the sort type to the publication date and the direction to descending.
7. Look at the items the block shows in the editor.
8. Change the query to **Children of a node** with *use current location* ticked, and note what the editor shows. Then
   set it back to the content search of steps 3 to 6.
9. Choose **Save draft and close**.

## Expected result

- The draft has a *Newest articles* block listing the newest articles below the trainer's folder, newest first.
- Step 8 showed that a query bound to the current location shows nothing (or a sample) in the editor.
- The trainer checks by previewing one learner's layout after module 6, or by opening the draft on the projector.

## Quiz

1. Name four query types.
2. What does *use current location* do?
3. Which setting turns a children list into one that serves every section of a site?
   A. Sort direction B. Use current location C. Limit D. Offset
4. Why prefer **Children of a node** over the general content search when it fits?
5. Your dynamic list shows folders and images. What do you add?

<details>
<summary>Answers</summary>

1. Any four of: children, parent, subtree, siblings, latest, random, manual, relation list, reverse relation list,
   tags, the Exponential content search, topics ([Exponential Layouts](../../../../bc/6.0/LAYOUTS.md#query-types-and-their-parameters)).
2. The query uses the page being shown as its location, instead of a fixed one.
3. **B.**
4. It is easier to read and understand later.
5. A content type filter (for example *Article*).

</details>

## Further reading

- [Exponential Layouts: Query types and their parameters](../../../../bc/6.0/LAYOUTS.md#query-types-and-their-parameters)
- [Exponential Layouts: Walkthrough B](../../../../bc/6.0/LAYOUTS.md#walkthrough-b-building-a-new-layout-from-scratch)

## Related pages

- [Module 4: Collections](README.md)
- [Exponential Layouts: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
