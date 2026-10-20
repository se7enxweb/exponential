---
title: "Lesson 6.3: Versions and comparing them"
audience: trainer, editor
category: training
order: 63
summary: Learners open the versions page of an item, filter and order its versions, view one, and compare two versions field by field with Show differences. 45 minutes.
keywords: editors 101, versions, manage versions, version history, compare, show differences, filter, archived, published, current version, content history
type: help
---
# Lesson 6.3: Versions and comparing them

"Who changed this, and what was it before?" is one of the most frequent questions on a busy site. The versions page
answers it. In this lesson learners open the versions of their own article, read the overview, and compare two
versions field by field.

## At a glance

| | |
|---|---|
| Course | [Editors 101](../README.md) |
| Module | [Module 6: Publishing, versions and drafts](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Beginner |
| Audience | New editors and authors |
| Prerequisites | [Lesson 6.2](previewing-a-draft.md); an article with at least three versions (*My first article*) |

## Learning objectives

After this lesson, learners can:

- open the versions page with **Manage versions** from an item's page or from the edit form;
- read the overview (versions, published, archived, translations, your drafts) and each version's creator and dates;
- filter the list by status and change its order;
- compare two versions with **Compare two versions** and **Show differences**.

## Trainer's notes

### How to present it

Source: [Go back to an earlier version](../../../publishing-and-versions.md#go-back-to-an-earlier-version) and
[Versions of an object](../../../../guides/content-history.md).

1. **Opening it** (2 minutes). The round button with a clock on an item's page, **Manage versions**, or the same
   entry in the edit form's left column. The page is titled *Versions of "<name>" (n)*.
2. **The page's own explanation** (2 minutes). Read the paragraph under the title aloud: each save is a version; a
   draft becomes the published version when it is published, and the version before it is archived; to change an
   older version or someone else's draft, make a new draft from it. When the item reaches the site's limit of
   versions, the oldest archived version is removed to make room.
3. **Overview, filter and order** (4 minutes). **Overview**: the number of versions, published, archived,
   translations and your drafts. **Filter and order**: the status (**All**, **Published**, **Archived** ...) and the
   order (**Newest first**, **Oldest first**, **Last modified**).
4. **A version's card** (3 minutes). Its number and status (*Published*, *Current version*, *Archived*), **View**,
   **Compare**, its translations, **Creator**, **Created** and **Modified**, and **New draft from this version, in**
   with **Make a new draft** (lesson 6.4).
5. **Comparing** (4 minutes). Under **Compare two versions**, choose the **Older version**, the **Newer version** and
   the **Translation**, then press **Show differences**. Changes are marked field by field. **Compare** on a card
   starts the same comparison from that version.

### Common questions

**How many versions are kept?** A limit set by the site (the version history limit of `content.ini`). The page says
so in its explanation.

**Can I remove versions?** Versions can be selected and removed with **Remove selected versions**; the published
version can never be removed this way (the page says *The published version is not removed.*). Leave this to
experienced editors.

**Why are some numbers missing (version 20, then 22)?** Drafts that were discarded or removed leave gaps. That is
normal.

### Pitfalls

- **Comparing in the wrong translation.** On a multilingual item, check **Translation** before **Show
  differences**.
- **Too few versions to compare.** Make sure every learner has an article with three or more versions; lesson 3.3
  gave *My first article* its third version.
- **Removing versions in class.** Do not let beginners use **Remove selected versions**; the history is the safety
  net of lesson 6.4.

## Hands-on exercise

Use the training installation and your article *My first article*.

1. Open *My first article*. Press the round **Manage versions** button.
2. Read the title of the page and write down how many versions the article has.
3. Read the **Overview** and write down the numbers of published and archived versions.
4. Under **Filter and order**, choose **Archived**. Then choose **All** again and set the order to **Oldest first**.
5. On the card of the oldest version, press **View**. Read the title it had. Go back with **Back** (or the browser's
   back button).
6. Under **Compare two versions**, choose the oldest version as **Older version** and the published version as
   **Newer version**. Leave **Translation** as it is.
7. Press **Show differences** and read the marked changes in **Title** and **Body**.
8. Now press **Compare** on the card of the version just before the published one, and read the differences.
9. Write down, for the published version, its **Creator** and **Modified** date.

## Expected result

- The learner's notes have the number of versions (at least three), the published and archived counts, the oldest
  title (*My first article*) and the published version's creator (the learner) and date.
- **Show differences** marked the title change and the body changes of lessons 4.1 to 4.3.
- The trainer checks by comparing two versions of one learner's article on the projector.

![The versions page of an item with several versions, the filters and Compare two versions](../../../images/trainers/e101-versions.webp)

## Quiz

1. Name two ways to open the versions page.
2. What happens to the oldest archived version when an item reaches the site's version limit?
3. Which three choices does **Compare two versions** ask for?
4. True or false: **Remove selected versions** can remove the published version.
5. Version numbers of an item jump from 20 to 22. Is something wrong?

<details>
<summary>Answers</summary>

1. The round **Manage versions** button on the item's page, or **Manage versions** in the edit form's left column.
2. It is removed to make room ([Publishing, versions and the trash](../../../publishing-and-versions.md#drafts-and-versions)).
3. The **Older version**, the **Newer version** and the **Translation**.
4. **False.** The published version can never be removed this way.
5. No. Discarded or removed drafts leave gaps in the numbers.

</details>

## Further reading

- [Versions of an object](../../../../guides/content-history.md): the versions page in full.
- [Publishing, versions and the trash](../../../publishing-and-versions.md#go-back-to-an-earlier-version): going
  back.
- [A tour of the administration](../../../admin-tour.md#7-versions-and-going-back): versions in the folder walk-through.

## Related pages

- [Module 6: Publishing, versions and drafts](README.md)
- [Editors 101: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
