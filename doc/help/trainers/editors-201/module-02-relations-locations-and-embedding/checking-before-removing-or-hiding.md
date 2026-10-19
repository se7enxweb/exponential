---
title: "Lesson 2.3: Checking before removing or hiding"
audience: trainer, editor
category: training
order: 23
summary: Learners read the list of objects that point at an item on the remove and hide confirmations, decide what to do about them, and know the site settings warn and block. 30 minutes.
keywords: editors 201, reverse relation check, remove, hide, confirmation, references, warn, block, broken links, nothing was removed
type: help
---
# Lesson 2.3: Checking before removing or hiding

Removing an image that three articles embed leaves three holes on the site. Exponential can show, on the
confirmation page, which items point at what is about to go, and can even refuse the removal. This lesson teaches
editors to read that list and act on it.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 2: Relations, locations and embedding](README.md) |
| Duration | 30 minutes (10 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Editors who remove or hide content |
| Prerequisites | [Lesson 2.1](relations-and-embedding.md), [Lesson 2.2](locations.md) |

## Learning objectives

After this lesson, learners can:

- read the list of referring items on the remove confirmation and on hiding;
- decide whether to fix the referring items first, cancel, or remove anyway;
- explain the site settings `warn` (list, then allow) and `block` (refuse while references exist);
- recover when a removal broke a link (restore from the trash, re-link).

## Trainer's notes

### How to present it

Source: [Removing and hiding: the check of reverse relations](../../../../features/6.0/reverse-relation-check.md).

1. **Why** (2 minutes). Removing content leaves references pointing at nothing; hiding it leaves them pointing at
   content the site no longer shows.
2. **The list** (4 minutes). With the shipped setting `warn`, the remove confirmation lists, per selected item, the
   objects that point at what goes. **OK** removes as before. Hiding (also **Hide selected**) shows the same kind of
   warning; a hide is never refused.
3. **Block** (2 minutes). A site can set `block` for removal: the list is shown and the removal is refused while
   references exist; the OK button is disabled and says why. If a form is sent anyway, the page says *Nothing was
   removed*. It is a decision of each site; ask your administrator which yours uses.
4. **After a mistake** (2 minutes). Restore the item from the trash (Editors 101, lesson 6.4). The next time a
   referring text is stored, the online editor reports links to items that are gone (Editors 101, lesson 4.2).

### Common questions

**Who decides between warn and block?** The site's administrator, in the content settings. Editors see the effect
on the confirmation page.

**The list is long. Must I fix every item?** Decide per case: replace the image in the referring articles, or keep
the item and hide it instead of removing it, or accept the broken references if the referring items go too.

**Does the check also look at Layouts blocks?** The layouts extension adds its own provider, so blocks that show the
item can be listed too.

### Pitfalls

- **Unticking Move to trash.** The confirmation page has a **Move to trash** choice. Moved to the trash, items can be
  restored later; without it they are deleted for good. Keep it ticked in class.
- **Confirming out of habit.** The list is only useful if read. Ask learners to read it aloud in the exercise.
- **Testing on shared content.** Remove only learners' own items in the exercise.
- **Hidden confirmation.** A site that switched off the remove confirmation shows no list; the check cannot help
  there unless it blocks.

## Hands-on exercise

Use the training installation and your items from lessons 2.1 and 2.2.

1. Open the image you embedded in *Our team, two places* (lesson 2.1). Check its **Reverse related objects**.
2. Press **Remove** on the image. Read the confirmation page: find *Our team, two places* in the list of items that
   point at it.
3. Do not confirm. Press **Cancel**.
4. Open *Our team, two places* with **Edit** and remove the embedded image from **Body**. Publish.
5. Open the image again: **Reverse related objects** no longer lists the article (it may still list it if the
   article's image field uses it; then remove it there too or choose another image).
6. Press **Remove** on the image again and read the confirmation. Confirm only if no item points at it any more.
7. Open **Trash**, find the image and **Restore** it.
8. In your course folder, tick *Our team, two places* and choose **More actions** > **Hide selected**. Read any
   warning, confirm, then unhide it again.

## Expected result

- Step 2 listed *Our team, two places* among the items pointing at the image (on a site with `warn` or `block`).
- After step 4 the image had fewer references; step 6 removed it without breaking the article; step 7 brought it
  back.
- The learner read the hide warning in step 8 (if any item points at the article) and unhid the article.
- The trainer checks by asking a learner what they would do if the list in step 2 had contained ten articles.

## Quiz

1. What does the remove confirmation list with the shipped setting?
2. What is the difference between `warn` and `block`?
3. True or false: a hide can be refused because of references.
4. The removal page says *Nothing was removed*. Why?
5. A removal broke a link in an article. Name two ways to repair it.

<details>
<summary>Answers</summary>

1. Per selected item, the objects that point at what goes
   ([Removing and hiding: the check of reverse relations](../../../../features/6.0/reverse-relation-check.md)).
2. `warn` lists the references and allows the removal; `block` lists them and refuses the removal while they exist.
3. **False.** A hide is never refused; `block` under hiding is taken as `warn`.
4. The site blocks removals while references exist, and a reference was found when the form was sent.
5. Restore the item from the trash, or edit the article and link to another item.

</details>

## Further reading

- [Removing and hiding: the check of reverse relations](../../../../features/6.0/reverse-relation-check.md): every
  case and setting.
- [Publishing, versions and the trash](../../../publishing-and-versions.md#remove-and-restore): removing and
  restoring.

## Related pages

- [Module 2: Relations, locations and embedding](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
