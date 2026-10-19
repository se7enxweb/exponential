---
title: "Lesson 3.1: The admin search and the advanced search"
audience: trainer, admin, editor
category: training
order: 31
summary: Learners use the administration's quick search with its scope, read the results page, and narrow a search with the advanced search by phrase, class, class attribute, section and publication date. 30 minutes.
keywords: search course, quick search, Search scope, advanced search, Class attribute, Update attributes, section, Published, Edit selected, admin4
type: help
---
# Lesson 3.1: The admin search and the advanced search

Administrators and support staff search the administration many times a day, often for editors who "cannot find"
something. This lesson makes them fast with the quick search, its scope and the advanced search, so they can teach
the same to editors and answer support questions precisely.

## At a glance

| | |
|---|---|
| Course | [Search](../README.md) |
| Module | [Module 3: Searching in practice](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators, support staff, lead editors |
| Prerequisites | [Module 2](../module-02-indexing/README.md); an administrator account on the training installation |

## Learning objectives

After this lesson, learners can:

- start a search with the **Search...** field or the `/` key and narrow it with **Search scope** (section, current
  location);
- read the results page (heading with the number of matches, **Name**, **Type**, the **In:** list) and use **Edit
  selected**;
- build an advanced search with **Search for an exact phrase:**, **Class:** with **Class attribute:** (after **Update
  attributes**), **In:** and **Published:**;
- explain to an editor why a known item is not found.

## Trainer's notes

### How to present it

Source: [Searching](../../../searching.md) and [Keyboard shortcuts](../../../keyboard-shortcuts.md). The names below
are those of the admin4 design.

1. **Quick search** (3 minutes). Click **Search...** in the header or press `/` (not while typing in another field).
   The funnel button **Search scope** offers **Section**, **Where** (everywhere or **Current location**) and
   **Advanced search**. The results page heading reads, for example, `Search for <word> returned 6 matches`; the
   **In:** list limits to a section.
2. **Edit selected** (2 minutes). Tick several results and press **Edit selected** to edit them in one form. Useful
   for support: fix the same mistake in several items.
3. **Advanced search** (5 minutes). Show every field: **Search for all of the following words:**, **Search for an
   exact phrase:**, **Class:** (*Any class* or one class), **Class attribute:** (only after **Update attributes**),
   **In:** (*Any section* or one), **Published:** (*Any time*, *Last day*, *Last week*, *Last month*, *Last three
   months*, *Last year*). Press **Find**.

### Common questions

**Why does the class attribute list stay empty?** Choose the class first and press **Update attributes**; the list is
filled for that class.

**How many results do I get per page?** The search caps the number per request (`MaximumSearchLimit`, 30 as shipped);
use the paging links for more.

**Can I search a part of a word?** Only when wildcards are switched on (Module 5); on a standard installation search
finds whole words.

**An editor cannot find an item I can see. Why?** Their role may not let them read it; it may be a draft, in the trash
or hidden. The help for editors lists the same reasons.

### Pitfalls

- **The scope stays set.** A learner who chose **Current location** keeps getting few results in other places. Show
  how to set **Where** back to everywhere in **Search scope**.
- **Pressing Enter in the advanced form after choosing a class.** The class attribute list is not updated until
  **Update attributes** is pressed.

## Hands-on exercise

Use your own training installation, signed in as an administrator. Choose words that occur in your sample content.

1. Press `/` on the dashboard, type a common word from the sample content and press Enter. Note the number of matches
   in the heading.
2. Use the **In:** list on the results page to limit the search to the section *Media*, then to *Standard* (or the
   sections your installation has).
3. Open a folder in **Content structure**, then use **Search scope**, **Where**: **Current location**, and search for
   the same word.
4. Tick two results and press **Edit selected**. Look at the form, then leave it without saving (use the form's cancel
   or discard button).
5. Open **Advanced search**. In **Search for an exact phrase:** type two words that stand next to each other in an
   article title. Press **Find**.
6. Clear the phrase. Choose **Class:** *Article*, press **Update attributes**, choose **Class attribute:** *Title* and
   search for one word from a title.
7. Add **Published:** *Last week* and search again.
8. Write down, for one search, how the result count changed from step 1 to step 7.

## Expected result

- Step 1 shows the heading with the number of matches and the columns **Name** and **Type**.
- Steps 2 and 3 show fewer results than step 1.
- Step 4 opens one edit form for both items; nothing is changed when it is left.
- Step 5 finds only items where the words stand together in that order.
- Step 6 finds only articles with the word in the title; step 7 only those published in the last week.
- The trainer asks two learners to explain their result counts from step 8.

## Quiz

1. Which key starts the quick search from anywhere in the administration?
2. In the advanced search, what must you press before **Class attribute:** lists the fields of a class?
   A. **Find** B. **Update attributes** C. **Edit selected** D. **Search scope**
3. Name the six choices of **Published:**.
4. What does **Edit selected** do on the results page?
5. Name three reasons an editor may not find an item that exists.

<details>
<summary>Answers</summary>

1. `/` (not while typing in another field) ([Searching](../../../searching.md)).
2. **B.**
3. *Any time*, *Last day*, *Last week*, *Last month*, *Last three months*, *Last year*.
4. It opens one form to edit all ticked results.
5. Any three of: their role does not let them read it; it is a draft; it is in the trash; it is hidden; the word is in
   a field that is not searchable; the scope limits the search.

</details>

## Further reading

- [Searching](../../../searching.md): the editors' page to hand out after this lesson.
- [Keyboard shortcuts](../../../keyboard-shortcuts.md): shortcuts in the administration and the help.
- [Admin tour](../../../admin-tour.md): where the header and its search sit in the administration.

## Related pages

- [Module 3: Searching in practice](README.md)
- [Search: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
