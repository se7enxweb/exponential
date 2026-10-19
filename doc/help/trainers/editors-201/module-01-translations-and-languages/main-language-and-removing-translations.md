---
title: "Lesson 1.3: Main language, always available and removing a translation"
audience: trainer, editor
category: training
order: 13
summary: Learners change an item's main language with Set main, explain the always-available setting, and remove a translation safely, knowing that it deletes that language's text in every version. 45 minutes.
keywords: editors 201, main language, set main, always available, use the main language if there is no prioritized translation, remove translation, translations tab, wrong language
type: help
---
# Lesson 1.3: Main language, always available and removing a translation

Every item has one **main language**, and some items are shown even where their language is missing. Editors meet
both when content was created in the wrong language or a translation must go. This lesson covers the
**Translations** tab's three actions, with one warning: removing a translation deletes content for good.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 1: Translations and languages](README.md) |
| Duration | 45 minutes (20 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Experienced editors and translators |
| Prerequisites | [Lesson 1.2](translating-an-item.md): each learner has *Opening hours* in two languages |

## Learning objectives

After this lesson, learners can:

- explain what the main language is and change it with **Set main**;
- explain what *Use the main language if there is no prioritized translation* (always available) does, and when it
  is useful;
- remove a translation with **Remove selected**, and say why the main language cannot be removed;
- follow the safe order for content created in the wrong language: translate, set main, remove.

## Trainer's notes

### How to present it

Sources: [The main language](../../../languages.md#the-main-language) and
[Main language and always available](../../../../guides/content-languages.md#6-main-language-and-always-available).

1. **The main language** (4 minutes). It is the language the item was created in, or the one chosen later in the
   **Translations** tab: select it in the **Main** column and press **Set main**. The main translation cannot be
   removed; another one has to become the main one first.
2. **Always available** (4 minutes). The tick *Use the main language if there is no prioritized translation* with
   **Update**. An always-available item is shown in its main language on a siteaccess that lists none of its
   languages, where it would otherwise be hidden. Folders, users, user groups and media are usually always
   available, so that a German site still finds the image folder or the author.
3. **Removing a translation** (5 minutes). Tick the translation in **Existing translations** and press **Remove
   selected**. Warn clearly: removing a translation deletes all its fields in that language, in every version, for
   good. It needs the permission; ask an administrator when unsure.
4. **The safe order** (5 minutes). Content created in the wrong language (for example British instead of American
   English): first translate it into the right language based on the wrong one, then make the right language the
   main one with **Set main**, then remove the wrong translation. Nothing is lost.

### Common questions

**Why can I not remove the main language?** Every item needs a main language. Make another translation the main one
first.

**Is removing a translation the same as removing the item?** No. The item and its other languages stay; only that
language's text goes, in every version, and cannot be restored from the trash.

**Should articles be always available?** Usually not: a German visitor would then see English articles. Folders,
images and users usually are.

### Pitfalls

- **Removing before translating.** The text is gone and the trash cannot bring it back. Always translate first.
- **Missing permission.** Many sites let only administrators remove translations; the button may be grayed out.
- **Always available by accident.** Ticking it on an article makes it appear in the main language on every language
  site. Check with the trainer before changing it on real content.

## Hands-on exercise

Use the training installation and your article *Opening hours* from lesson 1.2.

1. Open *Opening hours* and the **Translations** tab. Write down the main language.
2. In the **Main** column, select the second language and press **Set main**.
3. Point at the badges: the second language is now marked as the main language.
4. Set the original language back as the main language the same way.
5. Read the tick *Use the main language if there is no prioritized translation* and write down whether it is set for
   this article. Do not change it.
6. Open your course folder's **Translations** tab and compare: is the folder always available?
7. Create a short article `Wrong language test` in the second language only (choose it in the language list when
   creating, or ask the trainer how your site offers it), and publish it.
8. Translate it into the main language, based on the second language, and publish.
9. In its **Translations** tab, make the main language the main one with **Set main**.
10. Tick the second language and press **Remove selected**. Read the confirmation carefully and confirm (if your role
    may).
11. Check the badges: only the main language is filled.

## Expected result

- *Opening hours* has its original main language again.
- The learner knows whether their article and folder are always available.
- *Wrong language test* ended with only the main language, without losing its text (it was translated first).
- The trainer checks by opening *Wrong language test* and its **Translations** tab on the projector. If the role may
  not remove translations, the trainer demonstrates step 10.

## Quiz

1. How do you change an item's main language?
2. Why can the main translation not be removed?
3. What does always available do on a siteaccess that lists none of the item's languages?
   A. Hides the item B. Shows it in its main language C. Translates it D. Shows an error
4. Put in order: remove the wrong translation, translate into the right language, set the right language as main.
5. True or false: a removed translation can be restored from the trash.

<details>
<summary>Answers</summary>

1. In the **Translations** tab, select the language in the **Main** column and press **Set main**.
2. Every item needs a main language; another must become the main one first
   ([Content languages](../../../../guides/content-languages.md#6-main-language-and-always-available)).
3. **B.**
4. Translate into the right language, set it as main, then remove the wrong translation.
5. **False.** It deletes that language's fields in every version, for good.

</details>

## Further reading

- [Content languages](../../../../guides/content-languages.md#8-moving-content-from-one-language-into-another):
  moving content from one language into another.
- [Content languages](../../../../guides/content-languages.md#9-removing-translations-and-languages-safely): what
  removing deletes.
- [Languages](../../../languages.md#remove-a-translation): the short version.

## Related pages

- [Module 1: Translations and languages](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
