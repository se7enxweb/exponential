---
title: "Lesson 1.1: Content languages and translations"
audience: trainer, editor
category: training
order: 11
summary: Learners tell content languages from the interface language, read an item's language badges and its Translations tab, and explain which translation a site shows. 30 minutes.
keywords: editors 201, content language, translation, interface language, language badge, translations tab, main language, SiteLanguageList, multilingual
type: help
---
# Lesson 1.1: Content languages and translations

A multilingual site holds one item in several languages. Before learners translate anything, they need to know the
difference between the languages content is written in and the language of the administration's buttons, and how
to read which translations an item has.

> **Note**
> Use a training installation with at least two content languages (for example English and German). Adding a
> content language is an administrator's task: see **Preparing this module** in the module overview.

## At a glance

| | |
|---|---|
| Course | [Editors 201](../README.md) |
| Module | [Module 1: Translations and languages](README.md) |
| Duration | 30 minutes (15 presenting, 15 exercise) |
| Level | Intermediate |
| Audience | Editors who publish every week and now work on a multilingual site |
| Prerequisites | Editors 101, or equivalent experience; a training account with the Editor role |

## Learning objectives

After this lesson, learners can:

- explain the difference between a **content language** and the **interface language**;
- read an item's language badges (filled: exists; dashed: missing) and the **Translations** tab;
- say which translation a siteaccess shows, and when an item is not shown at all;
- name the **main language** of an item.

## Trainer's notes

### How to present it

Sources: [Languages](../../../languages.md) and [Content languages](../../../../guides/content-languages.md#in-short).

1. **Two kinds of language** (3 minutes). Content languages are the languages items are written in: an article can
   exist in English and in German, each a **translation** of the same item. The interface language is the language
   of the menus and buttons, chosen in the right column's **Interface** box, for you only; it changes nothing in the
   content.
2. **Badges** (4 minutes). On an item's page, the line under the buttons shows a badge per language of the site. A
   filled badge (such as **EN**) is a translation that exists; a dashed one (such as **DE**) does not exist yet.
   Pointing at a badge reads it, for example *English (American) – main language* or *Translate into German*.
3. **The Translations tab** (4 minutes). Open **Translations (n)**: the box **Existing translations** lists each
   language with its **Locale** and whether it is the **Main** one, and below it *Missing translations*. Buttons:
   **Remove selected**, **Set main**, and **Update** for the setting *Use the main language if there is no
   prioritized translation*. Lesson 1.3 covers these.
4. **Which translation a site shows** (4 minutes). Each siteaccess (for example an English and a German site) shows
   an item in the first language of its own list of languages that the item has. An item in none of them is hidden
   there, unless it is **always available**. Folders, users and media are usually always available.

### Common questions

**If I switch the interface to German, do I edit the German text?** No. The interface language changes the buttons;
you choose the translation to edit in the language list next to **Edit**.

**Why do I see a language badge for a language nobody uses?** The site has that content language. Adding a language
translates nothing; it only makes translating into it possible.

**Can visitors see a translation as soon as I publish it?** Only on a siteaccess that lists that language. Which
siteaccesses show which languages is an administrator's setting.

### Pitfalls

- **Mixing up the two languages.** Ask learners to switch the interface language once and see that the content does
  not change.
- **Test languages.** Some installations carry a test language, a pseudo-language for checking layouts.
  Tell learners to ignore it.
- **Assuming every item exists in every language.** The badges show the truth; read them before you promise a
  translation is online.

## Hands-on exercise

Use the training installation and your Editor training account.

1. Open the item the trainer named (a published article that exists in two languages).
2. Read the language badges under the buttons. Point at each and write down what the tooltip says.
3. Open the **Translations** tab. Write down the languages listed under **Existing translations** and which one is
   marked **Main**.
4. Read the list *Missing translations*.
5. Open the language list next to **Edit** and read its entries (the existing languages and **New translation**).
6. Switch the **Interface language** in the right column to another language and press **Apply**. Check: the
   article's text has not changed. Switch back.
7. Open your own course folder from Editors 101 (or any folder) and read its badges: which languages does it have?
8. Open the public site in the language the trainer names and find the article there.

## Expected result

- The learner's notes list the article's languages with their tooltips, the main language, and the missing ones.
- Step 6 showed that the interface language does not change content.
- Step 8 found the article on the site of the other language (the trainer prepared both siteaccesses).
- The trainer checks by asking a learner to explain the two kinds of badges.

![The Translations tab of an article: its languages, the main language and Always available](../../../images/trainers/e201-translations-tab.webp)

## Quiz

1. What does a dashed language badge on an item's page mean?
2. True or false: changing the interface language changes the language of the text you edit.
3. Which tab lists an item's translations with its main language?
   A. **Details** B. **Translations** C. **Locations** D. **Relations**
4. A siteaccess lists only German. Your item exists only in English and is not always available. What do visitors of
   that siteaccess see?
5. What does adding a content language to the site translate?

<details>
<summary>Answers</summary>

1. The translation does not exist yet; pointing at it says, for example, *Translate into German*
   ([Languages](../../../languages.md#see-which-translations-an-item-has)).
2. **False.**
3. **B.**
4. Nothing: the item is hidden there ([Content languages](../../../../guides/content-languages.md#in-short)).
5. Nothing. Translating is done item by item in the editor.

</details>

## Further reading

- [Languages](../../../languages.md): content languages and the interface language.
- [Content languages](../../../../guides/content-languages.md): the full guide, for the trainer.

## Related pages

- [Module 1: Translations and languages](README.md)
- [Editors 201: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
