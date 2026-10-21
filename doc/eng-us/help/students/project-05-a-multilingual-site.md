---
title: "Project 5: a multilingual site"
audience: student
category: learning
order: 29
summary: Study project 5 - add a second content language, translate part of your club site, decide the main language and always-available items, make a siteaccess show the language with SiteLanguageList, check what visitors see, and hand in a language plan with evidence, graded with a rubric.
keywords: study project, multilingual, content language, translation, Setup Languages, SiteLanguageList, siteaccess, main language, always available, ger-DE, right to left, learner
type: help
---
# Project 5: a multilingual site

Exponential stores content in many languages, including right-to-left scripts. In this project you add a second
language to your study site, translate part of your club site from [project 2](project-02-a-small-site-structure.md)
and make a siteaccess show it. The steps follow [Content languages](../../guides/content-languages.md).

## Contents

1. [At a glance](#at-a-glance)
2. [Goal](#goal)
3. [Before you start](#before-you-start)
4. [Tasks](#tasks)
5. [Hand in](#hand-in)
6. [Rubric](#rubric)
7. [Stretch goals](#stretch-goals)

## At a glance

| | |
|---|---|
| Level | Intermediate |
| Time | About 3 hours |
| Paths | Editor, administrator |
| Prerequisites | [project 2](project-02-a-small-site-structure.md); [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md); a terminal for task 5 |

## Goal

A second content language with at least five translated items, a siteaccess that shows that language first with
English as the fallback, and a language plan that explains what a visitor sees for an item that is not translated.

## Before you start

Read [Content languages](../../guides/content-languages.md), sections 1 to 7, and [Languages](../languages.md).
Choose a language you can write, or use German (`ger-DE`) with short invented sentences.

Know these facts from the guide:

- Adding a language **translates nothing and changes no siteaccess**: no visitor sees anything new yet
  ([section 4](../../guides/content-languages.md#4-adding-a-language)).
- What a visitor sees is decided per siteaccess by `SiteLanguageList[]`, most preferred first
  ([section 5](../../guides/content-languages.md#5-showing-a-language-sitelanguagelist-and-siteaccesses)).
- An item that is **always available** is shown in its main language where no listed language exists
  ([section 6](../../guides/content-languages.md#6-main-language-and-always-available)).

## Tasks

1. **Add the language.** **Setup > Languages**, **Add language**, find it (typing `german` or `ger` works), press
   **Add language**. Expected: a message such as *German (ger-DE) was added*. If your site already has it, note that
   instead.
2. **Translate five items.** On each item, click the dashed badge of the language (or choose it under
   **New translation**), translate based on the English version, and **Send for publishing**
   ([Languages](../languages.md#translate-an-item)).
3. **Look at the badges.** Filled badges are translations that exist, dashed ones are missing.
4. **Decide the always-available items.** Folders and images are usually always available, so a German site still
   finds them. Check the setting in the **Translations** window of one folder.
5. **Show the language.** Choose the siteaccess that will show it. If your installation already has one for that
   language (the default package creates one per additional language you chose at install time,
   [Site packages for learners](site-packages-for-learners.md)), read its `SiteLanguageList` with
   `php bin/php/console exp:ini where site/RegionalSettings/SiteLanguageList <siteaccess>`. Otherwise set it in
   `settings/siteaccess/<siteaccess>/site.ini.append.php` as in the guide's example: an empty `SiteLanguageList[]`
   line first, then your language, then `eng-US`.
6. **Clear the caches** for settings and content: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` and
   `--clear-tag=content` ([Content languages, section 10](../../guides/content-languages.md#10-commands-for-the-shell)).
7. **Check as a visitor.** On that siteaccess, open a translated item, an untranslated item, and an untranslated but
   always-available folder. Write down what you see for each.
8. **Switch the administration language** for yourself in the **Interface** box, and back
   ([Languages](../languages.md#the-language-of-the-administration)). Note what changes and what does not.

## Hand in

- A language plan (half a page): which languages, which is the main one, which items are always available, and why.
- The `exp:ini where` output for `SiteLanguageList` of your siteaccess.
- Screenshots: the languages page; an item with both badges filled; the three visitor checks of task 7.
- A short report on what surprised you.

## Rubric

Score the four common criteria of [Grading your own work](grading-your-own-work.md), and these checks (7):

1. A second content language exists.
2. At least five items are translated and published.
3. The siteaccess lists the new language first and `eng-US` second, shown with `exp:ini where`.
4. Settings were changed in an `.ini.append.php` file, not in `settings/site.ini`.
5. The three visitor checks are documented, with the reason for each result.
6. The report distinguishes content languages from the interface language.
7. No machine translation was published without the learner reviewing it.

## Stretch goals

- Find what is not translated yet ([How do I find what is not translated?](../manual/how-do-i-find-what-is-not-translated.md)).
- Add a right-to-left language and look at the administration with it
  ([Contributing translations](../supporters/contributing-translations.md#right-to-left-languages)).
- Make a translator role that may edit only your language, using the **Language** limitation
  ([Content languages, section 7](../../guides/content-languages.md#7-translating-content-in-the-editor)), and test it
  in [project 9](project-09-roles-for-a-restricted-editor.md).

## Related pages

- [Study projects: overview](study-projects-overview.md)
- [Content languages](../../guides/content-languages.md)
- [Translating as a student](translating-as-a-student.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
