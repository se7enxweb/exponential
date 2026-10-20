---
title: "Lesson 1.4: A siteaccess per language"
audience: trainer, admin, developer
category: training
order: 14
summary: Learners give the second site a German siteaccess, set its languages and fallback order, and check the result on the Content languages page. 50 minutes.
keywords: multisite and site packages, language, translation siteaccess, SiteLanguageList, Locale, ContentObjectLocale, ShowUntranslatedObjects, ExtensionSettingsSiteAccess, Content languages, ger-DE
type: help
---
# Lesson 1.4: A siteaccess per language

Most multisites are also multilingual: each language of a site is a siteaccess of its own, with its own address, its
own locale and its own order of content languages. This lesson adds a German siteaccess to the site from lesson 1.3
and shows how Exponential decides which translation a visitor sees.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 1: Siteaccesses for several sites](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 1.3](adding-a-second-site.md): the siteaccess `site_b` works |

## Learning objectives

After this lesson, learners can:

- explain that adding a content language translates nothing and changes no siteaccess;
- create a language siteaccess with `Locale`, `ContentObjectLocale` and `SiteLanguageList[]`;
- choose a fallback order and `ShowUntranslatedObjects` for a language siteaccess;
- read the table **Siteaccesses and their languages** on the **Content languages** page.

## Trainer's notes

### How to present it

Sources: [Building a multilingual site](../../../creators/website-creators/multilingual-sites.md) and
[Content languages](../../../../guides/content-languages.md).

1. **Three rules** (5 minutes), from the website creators' page:
   - adding a content language translates nothing and changes no siteaccess; it only makes the language available
     for translations;
   - a siteaccess shows each object in the first language of its `SiteLanguageList[]` that the object has;
   - objects in none of those languages are hidden, unless they are marked always available or the siteaccess has
     `ShowUntranslatedObjects=enabled`.
2. **The settings of a language siteaccess** (5 minutes). Show the block on the projector:

   ```ini
   # settings/siteaccess/site_b_ger/site.ini.append.php
   [RegionalSettings]
   Locale=ger-DE
   ContentObjectLocale=ger-DE
   SiteLanguageList[]
   SiteLanguageList[]=ger-DE
   SiteLanguageList[]=eng-US
   ShowUntranslatedObjects=disabled
   TextTranslation=enabled
   ```

   The empty `SiteLanguageList[]` line resets the list the copy brought along.
3. **Extensions and design for a translation** (3 minutes). A translation siteaccess often needs the extension
   settings of the siteaccess it was made from. `site.ini [SiteAccessSettings] ExtensionSettingsSiteAccess=site_b`,
   set in the translation siteaccess's own `site.ini.append.php`, makes it use them as well, so it renders with the
   same design in its own language.
4. **Where to look** (2 minutes). **Setup** > **Languages** opens **Content languages**, whose table **Siteaccesses
   and their languages** lists each siteaccess with its languages in order.

### Common questions

**What is the difference between `Locale` and `ContentObjectLocale`?** `Locale` decides dates, numbers and the
interface texts of the site; `ContentObjectLocale` is the language new content is created in on that siteaccess.

**Why does the German site show English articles?** Because `eng-US` is the second entry of its
`SiteLanguageList[]`: it is the fallback. Remove it to hide untranslated content.

**How many content languages can an installation have?** More than 30 need a 64-bit PHP, because the languages of an
object are stored as bits of one integer.

**Is there a redirect from `/` to the visitor's language?** Yes, with `DefaultHostUriMatchMapItems[]`: lesson 2.4.

### Pitfalls

- **Forgetting the language itself.** Without the content language `ger-DE` (step 1), editors cannot translate into
  it, and the siteaccess shows only the fallback.
- **Changing `ContentObjectLocale` later.** On a site with content it needs care; read the warning in
  `settings/site.ini` first. On the lab it does not matter.
- **Removing a translation.** Removing a translation of an object deletes it for good. Do not let learners
  experiment with that on shared content.

## Hands-on exercise

Use your training installation with the siteaccess `site_b` from lesson 1.3.

1. In the administration, open **Setup** > **Languages**. If `ger-DE` is not listed, press **Add language**, choose
   German (Germany) and confirm.
2. Copy the siteaccess of the second site:

   ```bash
   cp -r settings/siteaccess/site_b settings/siteaccess/site_b_ger
   ```

3. Set the regional settings, starting with an empty list:

   ```bash
   php bin/php/console exp:ini set site.ini/RegionalSettings/Locale ger-DE siteaccess:site_b_ger
   php bin/php/console exp:ini set site.ini/RegionalSettings/ContentObjectLocale ger-DE siteaccess:site_b_ger
   php bin/php/console exp:ini clear 'site.ini/RegionalSettings/SiteLanguageList[]' siteaccess:site_b_ger
   php bin/php/console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' ger-DE siteaccess:site_b_ger
   php bin/php/console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' eng-US siteaccess:site_b_ger
   ```

4. Give it a German name: `exp:ini set site.ini/SiteSettings/SiteName "Seite B" siteaccess:site_b_ger`.
5. Register it in `AvailableSiteAccessList[]` and `RelatedSiteAccessList[]` with `exp:ini add ... global`, as in
   lesson 1.3, step 7.
6. Clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini` (and restart Velocity if it serves the lab).
7. Open `/site_b_ger` in the browser. The folder *Site B* is shown, in English, because it has no German
   translation yet and `eng-US` is the fallback.
8. In the administration, open the folder *Site B*, choose German in the language list next to **Edit**, translate
   the name to `Seite B` and publish.
9. Reload `/site_b_ger`: the German name appears. `/site_b` still shows the English one.
10. Open **Setup** > **Languages** and read the table **Siteaccesses and their languages**.

## Expected result

- `/site_b_ger` answers and shows *Seite B* after step 9, while `/site_b` shows *Site B*.
- The table on **Content languages** lists `site_b_ger` with `ger-DE` before `eng-US`.
- `exp:ini where 'site.ini/RegionalSettings/SiteLanguageList[]' site_b_ger` shows the reset line and the two values
  in the siteaccess file.
- The trainer checks by opening both addresses of one learner side by side.

## Quiz

1. You add the content language French. Which siteaccesses show French content now?
2. A German siteaccess has `SiteLanguageList[]=ger-DE` and `eng-US`. An article exists only in English. What does a
   visitor of the German siteaccess see?
   A. Nothing B. The English article C. An error D. A machine translation
3. What does the empty `SiteLanguageList[]` line do?
4. Which setting decides the language new content is created in on a siteaccess?
5. Which setting lets a translation siteaccess use the extension settings of the siteaccess it was made from?

<details>
<summary>Answers</summary>

1. None: adding a content language changes no siteaccess
   ([Building a multilingual site](../../../creators/website-creators/multilingual-sites.md#how-languages-work)).
2. **B.** `eng-US` is the fallback; the article is shown in English.
3. It resets the array, so the values the file inherited from earlier files are dropped and only the following lines
   count.
4. `ContentObjectLocale` in `site.ini [RegionalSettings]`.
5. `ExtensionSettingsSiteAccess` in `site.ini [SiteAccessSettings]`, set in the translation siteaccess's own
   `site.ini.append.php` (comment in `settings/site.ini`).

</details>

## Further reading

- [Building a multilingual site](../../../creators/website-creators/multilingual-sites.md): fallbacks, translators,
  interface texts.
- [Content languages](../../../../guides/content-languages.md): the full reference.
- [Language switchers and addresses per language](../../../creators/website-creators/language-switcher-and-language-urls.md).
- [Translations and languages](../../../../features/6.0/translations-and-languages.md).

## Related pages

- [Module 1: Siteaccesses for several sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
