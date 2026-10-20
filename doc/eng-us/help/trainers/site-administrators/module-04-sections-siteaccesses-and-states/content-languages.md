---
title: "Lesson 4.4: Content languages"
audience: trainer, admin
category: training
order: 44
summary: Learners tell content languages from interface languages, read Setup > Languages, add a language, make a siteaccess show it with SiteLanguageList, and know what removing a translation or a language does. 40 minutes.
keywords: site administration, content languages, languages, add language, SiteLanguageList, ShowUntranslatedObjects, always available, main language, interface language, locale, remove translation
type: help
---
# Lesson 4.4: Content languages

Exponential keeps one translation per language for every item. Which languages exist, which siteaccess shows which,
and what visitors see when a translation is missing are administrator decisions. This lesson reads and adds content
languages safely and shows the one action in this area that deletes content for good.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 4: Sections, siteaccesses and object states](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Site administrators |
| Prerequisites | [Lesson 4.2](siteaccesses-for-administrators.md) |

## Learning objectives

After this lesson, learners can:

- explain the difference between a content language and the interface language (`Locale`);
- read **Setup > Languages**: the figures and the card of each language;
- add a content language and say what adding does not do;
- read and change `SiteLanguageList` for a siteaccess, and explain `ShowUntranslatedObjects` and always available;
- say why removing a language never deletes content, but removing a translation does.

## Trainer's notes

### How to present it

Source: [Content languages](../../../../guides/content-languages.md), sections 1, 3, 4, 5, 6 and 9.

1. **Two kinds of language** (3 minutes). Content languages hold articles; the interface language (`Locale` and the
   translation files) decides the words of buttons and menus. They are set in different places.
2. **The page** (3 minutes). **Setup > Languages**: the figures (content languages, how many more can be added, need a
   look, unused), the default for new content (`ContentObjectLocale`), and a card per language with its objects,
   classes and the siteaccesses that show it.
3. **Adding** (3 minutes). **Add language**, find it in **Find a language**, **Add language**. Adding translates
   nothing and changes no siteaccess.
4. **Showing** (4 minutes). `site.ini [RegionalSettings] SiteLanguageList[]` per siteaccess, most preferred first. An
   object is shown in the first listed language it has; objects in none are hidden unless *always available* or
   `ShowUntranslatedObjects=enabled`.
5. **Removing** (2 minutes). A language can only be removed when no object and no class has a translation in it, so
   removing a language never deletes content. Removing an object's **translation** deletes that translation in every
   version, for good.

### Common questions

**How many content languages can an installation have?** At most 62 on 64-bit PHP; each is a bit in a language mask.

**I added German, but the site shows nothing new.** Correct: translate content, and add `ger-DE` to the
`SiteLanguageList` of the siteaccess that should show it, then clear the INI and content caches.

**What is the main language?** The language an object was created in, or the one it was moved to. Fields that are not
translatable come from it.

### Pitfalls

- **Changing `ContentObjectLocale` on a site with content.** Read the warning in `settings/site.ini` first; on the lab
  leave it.
- **Removing a translation to "clean up".** It is final. Translate first, make the other language the main one, then
  remove the old translation.
- **Forgetting the empty `SiteLanguageList[]` line.** Without it the list adds to what other files gave.

## Hands-on exercise

On your training installation, signed in as an administrator, with a terminal in the root:

1. Open **Setup > Languages**. Note the figures and the default language for new content.
2. Read the card of the main language: objects, classes, and the siteaccesses that show it.
3. Press **Add language**, type `german` in **Find a language**, choose German (Germany, `ger-DE`) and press
   **Add language**.
4. Read the new card: is it used, and does any siteaccess show it?
5. Translate one article of your `News` folder into German (the language choice next to **Edit**, or the
   **Translations** tab), changing only the title, and publish.
6. Read the current `SiteLanguageList` of the public siteaccess:

   ```bash
   php bin/php/console exp:ini where site.ini/RegionalSettings/SiteLanguageList site
   ```

7. Open the article on the public site. Which language is shown?
8. Open **Setup > Ini settings**, `site.ini` for `site`, and read `SiteLanguageList` and `ShowUntranslatedObjects`.
   Do not change them in class; discuss what adding `ger-DE` first would change.
9. Back on **Setup > Languages**, open the German card: can it be removed now? Why not?

## Expected result

- A card *German, ger-DE* exists; before step 5 it is not used, and no siteaccess shows it.
- Step 7: the public site still shows the article in its original language, because `ger-DE` is not in its
  `SiteLanguageList`.
- Step 9: German cannot be removed while the article has a German translation.
- The trainer checks the German card on one learner's site.

![The languages content can be written in, with the number of items in each](../../../images/trainers/sa-languages.webp)

## Quiz

1. Which setting decides the words of the administration's buttons?
   A. `SiteLanguageList` B. `Locale` C. `ContentObjectLocale` D. `ShowUntranslatedObjects`
2. You added a content language. What else is needed before visitors see it?
3. A siteaccess lists `ger-DE` then `eng-US`. An article exists only in `eng-US`. What does a visitor see?
4. Can removing a content language delete content? Can removing a translation?
5. What does *always available* do for an object?

<details>
<summary>Answers</summary>

1. **B.** `Locale` and the translation files ([Content languages](../../../../guides/content-languages.md#1-content-languages-and-interface-languages)).
2. Translated content, the language in the siteaccess's `SiteLanguageList`, and cleared INI and content caches.
3. The `eng-US` translation, the first listed language the article has.
4. Removing a language: no, it is refused while anything uses it. Removing a translation: yes, for good.
5. It is shown even on siteaccesses whose `SiteLanguageList` holds none of its languages.

</details>

## Further reading

- [Content languages](../../../../guides/content-languages.md): every step, with worked workflows.
- [Translations and languages](../../../../features/6.0/translations-and-languages.md): the feature reference.
- [Languages](../../../languages.md): every language and its support level.

## Related pages

- [Module 4: Sections, siteaccesses and object states](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
