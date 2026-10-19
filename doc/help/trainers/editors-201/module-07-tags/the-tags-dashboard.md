---
title: "Lesson 7.2: The tags dashboard and the tag tree"
audience: trainer, editor
category: training
order: 72
summary: Learners find their way around the Tags dashboard, open a tag and its content, add a child tag, and know what synonyms, merging and moving tags are for. 45 minutes.
keywords: editors 201, tags, tags dashboard, tag tree, add tag, add child tag, new tag, synonym, merge, move tags, translation, main tag
type: help
---
# Lesson 7.2: The tags dashboard and the tag tree

Editors who are trusted with the taxonomy look after the tag tree: they add tags, fix duplicates and translate tags.
This lesson shows the **Tags** dashboard and the most common upkeep, for learners whose role allows it.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 7: Tags](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Editors who maintain tags |
| Prerequisites | [Lesson 7.1](tagging-content.md); a role with the tags policies (read, add, edit) |

## Learning objectives

After this lesson, learners can:

- open the **Tags dashboard** and read its list (**ID**, **Tag name**, **Parent tag name**, **Modified**);
- open a tag and see its details, its children and the content tagged with it;
- add a tag with **Add tag** and a child tag with **Add child tag**;
- explain synonyms, merging and moving tags, and when to ask an administrator.

## Trainer's notes

### How to present it

Source: [eztags](../../../../features/6.0/extensions/eztags.md).

1. **The dashboard** (4 minutes). The **Tags** tab (`/tags/dashboard`) opens the **Tags dashboard**: the tag tree in
   the left column, a list with **ID**, **Tag name**, **Parent tag name** and **Modified**, **Children tags (n)**, and
   **Add tag**, **More actions** and **Table options**.
2. **A tag** (4 minutes). Open a tag: **Tag ID**, **Last modified**, whether it is a **Main tag** or a **Synonym**,
   its translations, its children and the content tagged with it. **Add child tag** adds a tag below it.
3. **Upkeep** (5 minutes). Name the actions without doing all of them: edit and translate a tag (**Edit in
   <language>**), add a synonym, make a tag a synonym of another, **merge** two tags (their content gets one tag),
   **move** tags to another parent, delete. Merging and deleting change many items at once; agree in the team who
   does it.
4. **Search index** (2 minutes). The tag page may say that a manual search index regeneration is needed for changes
   to be seen in search; that is an administrator's task unless the site reindexes automatically.

### Common questions

**Who may change tags?** Roles with the policies of the `tags` module (read, dashboard, add, edit, delete and more),
which can be limited to parts of the tag tree.

**Merge or synonym?** Merge when the two tags mean the same and one should disappear; a synonym when both words should
keep working.

**Can editors in the Editor siteaccess use the dashboard?** Where their role allows it; some sites keep the tag tree
for administrators.

### Pitfalls

- **Deleting a used tag.** The content loses the tag. Look at the content tagged with it first.
- **Translations missing.** A new tag created in one language may not show on another language's site.
- **No permission.** Without the tags policies the dashboard is not shown (an access message instead). Demonstrate.

## Hands-on exercise

Use the training installation with a role that may read and add tags.

1. Open the **Tags** tab. Read the **Tags dashboard** list and write down three tags with their parents.
2. In the tag tree on the left, open the tag `Training` (the trainer created it).
3. Read the tag's details: **Tag ID**, **Main tag** or **Synonym**, and its translations.
4. Find the content tagged with `Training`: your article from lesson 7.1 is there.
5. Press **Add child tag**. Name it `<your first name> workshop` and save.
6. Edit your article and add the new child tag to it; publish.
7. Open the new tag and check that your article is listed.
8. The trainer demonstrates on the projector: merging two duplicate tags (prepared before the class) and adding a
   synonym.

## Expected result

- Each learner created a child tag below `Training` and tagged an article with it.
- The new tag's page lists the article.
- The learners saw a merge and a synonym being made.
- The trainer checks by opening `Training` in the tag tree: one child per learner.

## Quiz

1. Where is the **Tags dashboard**?
2. Which button adds a tag below the tag you are looking at?
   A. **Add tag** B. **Add child tag** C. **New translation** D. **Set main**
3. What is the difference between merging two tags and making one a synonym?
4. Why look at a tag's content before deleting it?
5. Which policies does an editor need to add tags?

<details>
<summary>Answers</summary>

1. Under the **Tags** tab (`/tags/dashboard`) ([eztags](../../../../features/6.0/extensions/eztags.md#use-it)).
2. **B.**
3. Merging makes one tag out of two (the content gets the remaining tag); a synonym keeps both words leading to one
   main tag.
4. The content loses the tag when it is deleted.
5. Policies of the `tags` module, at least read and add (and edit to change tags).

</details>

## Further reading

- [eztags](../../../../features/6.0/extensions/eztags.md): the extension, its policies and settings.
- [Roles and policies](../../../../guides/roles-and-policies.md): giving editors the tags policies.

## Related pages

- [Module 7: Tags](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
