---
title: How do I show an item in languages it is not translated into?
audience: editor, admin
category: manual
order: 475
summary: Tick Use the main language if there is no prioritized translation on the item's Translations tab and click Update; a site in another language then shows the item in its main language instead of leaving it out.
keywords: always available, untranslated, main language, fallback, site languages, SiteLanguageList, ShowUntranslatedObjects, translations tab, missing translation
type: help
---
# How do I show an item in languages it is not translated into?

Each site of an installation has an ordered list of languages. It shows an item in the first language of that list
the item has. If the item has none of them, the item is left out: it is missing from menus and lists, and its address
gives no page.

For items that must appear everywhere, such as images, files, contact pages or a shared footer, switch on the item's
**always available** setting. The site then shows the item in its main language when no listed translation exists.

## Who can do this

Editors who may edit the item.

## Steps

1. Open the item and click the **Translations** tab.
2. Tick **Use the main language if there is no prioritized translation**.
3. Click **Update**.

New items take this setting from their class. Administrators set the default in the class
(**Setup** > **Classes**, the class's **Default object availability**).

## What can go wrong

- **The item shows in the wrong language.** It has a translation in a language higher in the site's list. Check the
  **Translations** tab, or ask an administrator about the order of the site's languages.
- **The checkbox is grayed out.** You may not edit the item.
- **Items show in every language in the administration but not on the site.** The administration is usually set to
  show untranslated items anyway, so editors can find them. Visitors do not get that setting.
- **A whole branch is missing in one language.** A parent folder is not translated and not always available, so
  visitors cannot reach what is below it. Translate the folder or switch on the setting for it.

## Related pages

- [Languages](../languages.md#the-main-language)
- [Fallback rules for untranslated content](../government/languages/fallback-rules-for-untranslated-content.md)
- [How do I find what is not translated yet?](how-do-i-find-what-is-not-translated.md)
- [Content languages](../../guides/content-languages.md)
- [User manual](README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
