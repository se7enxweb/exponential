---
title: "Lesson 7.1: Tagging content"
audience: trainer, editor
category: training
order: 71
summary: Learners tag articles in the edit form with the Tags field, find tags with autocomplete, use suggestions, add a new tag where allowed, and see everything tagged with a tag on its tag page. 45 minutes.
keywords: editors 201, tags, eztags, tagging, taxonomy, find a tag to add, selected tags, suggested tags, add new, remove tag, tag page, autocomplete
type: help
---
# Lesson 7.1: Tagging content

Tags classify content across the tree: an article in *News* and a page in *Projects* can both be tagged *Climate*,
and a tag page lists everything with that tag. Exponential's **Tags** extension (eztags) keeps the tags in a tree of
their own. This lesson teaches editors to tag well.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 7: Tags](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors on sites that use tags |
| Prerequisites | Editors 101, module 3; a content type with a tags field on the training installation |

## Learning objectives

After this lesson, learners can:

- explain what a tag is and how it differs from a folder and from a section;
- add tags in the edit form with **Find a tag to add** and autocomplete, and remove one with **Remove tag**;
- use **Suggested tags** and, where the field allows it, **Add new**;
- open a tag's page and see everything tagged with it.

## Trainer's notes

### How to present it

Source: [eztags](../../../../features/6.0/extensions/eztags.md).

1. **What tags are** (4 minutes). eztags adds a tag field to content types and a tree of tags managed under **Tags**.
   Tags can be predefined (a closed list) or open (editors add new ones), or a mix. A tag can have synonyms and
   translations. Unlike a folder, a tag does not place the item; unlike a section, it does not change permissions.
2. **The field** (5 minutes). In the edit form, a tags field shows **Selected tags** (or *There are no selected
   tags*), a search field **Find a tag to add** that suggests existing tags as you type, **Suggested tags** based on
   the content (or *There are no tags to suggest*), and, where open tags are allowed, **Add new**. **Remove tag**
   takes a tag off. A field can also be shown as a select list or a tree (**Choose a tag**), depending on the content
   type's settings.
3. **Tag pages** (3 minutes). Each tag has a page (by default `/tags/view/<tag>`) listing the content tagged with it.
   The **Tags** tab of an item's page shows its tags in the administration.
4. **Good tagging** (3 minutes). Prefer existing tags to new ones; three or four precise tags beat ten vague ones; agree
   on singular or plural.

### Common questions

**Why can I not add a new tag?** The field allows only predefined tags, or your role may not add tags.

**What is a synonym?** Another word for the same tag (for example *Car* for *Automobile*). Content tagged with a
synonym is found under the main tag.

**Do tags work in every language?** Tags can be translated; a tag without a translation in a language may not be shown
there, depending on the site's settings.

### Pitfalls

- **No tags field on the training site.** The standard article has no eztags field; the trainer adds one before the
  class (see the module overview). The keywords field some types have (*Tags* of the kernel keyword type) is a
  different, simpler field.
- **Duplicate tags.** *climate*, *Climate* and *Climate change* in one class. Show the tag tree first.
- **Search delay.** Tag changes may need the search index to catch up before search finds them.

## Hands-on exercise

Use the training installation; the trainer added a tags field to the *Article* type and a few tags (for example
*Training*, *Events*, *Climate*).

1. Open your article *Our team, two places* and press **Edit**. Go to the tags field with the field bar.
2. In **Find a tag to add**, type the first letters of `Training`. Choose it from the suggestions.
3. Add a second existing tag the same way.
4. Read **Suggested tags** and add one suggestion if it fits.
5. If the field offers **Add new**, add a new tag `<your first name> test`.
6. Remove one tag with **Remove tag**, then add it again.
7. Press **Send for publishing**.
8. Open the article's **Tags** tab and read its tags.
9. Open the tag page of `Training` on the site (the trainer gives the address, by default `/tags/view/Training`) and
   find your article and the other learners' articles.

## Expected result

- The article carries two or three tags, visible on its **Tags** tab.
- The tag page of `Training` lists every learner's tagged article.
- If **Add new** was offered, a new tag exists below the place the field puts new tags.
- The trainer checks by opening the tag page on the projector.

## Quiz

1. How is a tag different from a folder?
2. Which part of the tags field suggests existing tags as you type?
   A. **Selected tags** B. **Find a tag to add** C. **Remove tag** D. **Suggested tags**
3. Why might **Add new** be missing?
4. Where do you see all content tagged with a tag?
5. Give one rule for good tagging.

<details>
<summary>Answers</summary>

1. A tag classifies content without placing it; an item can have many tags but a folder is a place in the tree.
2. **B.** ([eztags](../../../../features/6.0/extensions/eztags.md#use-it): autocomplete suggests existing tags).
3. The field allows only predefined tags, or the role may not add tags.
4. On the tag's page (by default `/tags/view/<tag>`).
5. For example: prefer existing tags; use few precise tags; agree on singular or plural.

</details>

## Further reading

- [eztags](../../../../features/6.0/extensions/eztags.md): what the extension does and its settings.

## Related pages

- [Module 7: Tags](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
