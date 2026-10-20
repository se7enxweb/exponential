---
title: "Lesson 5.3: Languages and right to left in class"
audience: trainer, editor, admin
category: training
order: 53
summary: Teach in the learners' language, switch the administration's Interface language, explain translation completeness honestly, and show a right-to-left interface, with a test account. 35 minutes.
keywords: interface language, translation, right to left, rtl, Hebrew, Arabic, locale, multilingual class, ui switch, languages
type: help
---
# Lesson 5.3: Languages and right to left in class

Not every class learns in English. The administration's **Interface language** list offers every language the
kernel has a locale for, and says honestly how complete each translation is. Right-to-left languages such as Hebrew
and Arabic turn the whole page frame around. This lesson prepares trainers to teach in other languages.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 5: Accessibility and inclusion](README.md) |
| Duration | 35 minutes (10 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Trainers who teach in more than one language |
| Prerequisites | A training installation; a test account (never a shared administrator account) |

## Learning objectives

After this lesson, learners can:

- change the **Interface language** of the administration and back;
- read the completeness figure of a language and set expectations for the class;
- describe what changes in a right-to-left interface;
- tell the interface language from the content languages of a site.

## Trainer's notes

### How to present it

1. Show the list (4 minutes): in the right column, **Interface**, the **Interface language** list box with the groups
   English, Major world languages, Regional variants and All languages (A–Z). Each entry names the language in itself
   and shows how complete its translation is, for example "(99%)" or "(no translation yet)". Nothing changes until
   **Apply**.
2. Explain completeness (3 minutes): a language without a translation keeps English texts, but dates, times and
   numbers follow its locale.
3. Show right to left (3 minutes): with Hebrew (`heb-IL`) the content tree moves to the right, the toolbar to the
   left, and tables and forms read from the right.

### Common questions

**Does this translate our content?** No. The interface language is the language of the administration's labels. The
languages of the content are a separate topic (Editors 201 and [Languages](../../../languages.md)).

**The button names in the lessons are English.** Prepare a small glossary of the main labels in the class language,
taken from the translated interface.

### Pitfalls

- **Changing the shared administrator's language.** Preferences are per account; use a test account.
- **Promising a full translation.** Read the percentage first.

## Hands-on exercise

Use a test account on your training installation.

1. In the right column, find **Interface** and the **Interface language** list box.
2. Choose a language with a high completeness figure (for example German) and press **Apply**. Note three labels in
   that language (for example the names of the top menu tabs).
3. Choose Hebrew (`עברית`) and press **Apply**. Note where the content tree and the toolbar are now.
4. Choose the language you started with and press **Apply**.
5. Make a five-line glossary: the English label and the label in your class language for **Content structure**,
   **Users**, **Setup**, **Edit** and **Send for publishing**.

## Expected result

- The interface changed language twice and came back.
- In Hebrew, the content tree appeared on the right.
- A five-line glossary for the class.

## Quiz

1. Where is the **Interface language** choice?
2. What does a language "(no translation yet)" still change?
3. Does the interface language change the language of the content?
4. Why use a test account for this exercise?

<details>
<summary>Answers</summary>

1. In the right column, under **Interface** ([The interface language list](../../../../features/6.0/ui-switch-languages.md)).
2. Dates, times and numbers follow its locale; the texts stay English.
3. No. Content languages are separate ([Languages](../../../languages.md)).
4. Preferences are stored per account; never change a shared account's preferences.

</details>

## Further reading

- [The interface language list](../../../../features/6.0/ui-switch-languages.md)
- [Right-to-left languages in the administration](../../../../features/6.0/right-to-left.md)
- [Languages](../../../languages.md)

## Related pages

- [Module 5: Accessibility and inclusion](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
