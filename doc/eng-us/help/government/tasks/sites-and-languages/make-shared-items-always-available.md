---
title: Make shared items always available
audience: government, editor, admin
category: public-sector
order: 70
summary: Task for editors and administrators: mark images, media folders, user accounts and single-language documents as always available so every language site shows them.
keywords: always available, translations tab, media, users, shared items, multilingual, class default
type: help
---
# Make shared items always available

This task is for editors and administrators of a multilingual site. Background:
[Always available content](../../languages/always-available-content.md).

## Before you start

You can edit the items. Decide which items are language-neutral (images, logos, media folders, users) or exist in one
language only on purpose (a legal text).

## Steps

For one item:

1. Open the item; open the **Translations** tab.
2. Tick **Use the main language if there is no prioritized translation.**
3. Press **Update**.

For future items of a class (administrators):

1. **Setup > Classes**, the class (for example Image), **Edit**.
2. Set the default object availability to always available; **OK**. Existing objects keep their setting.

## Check

On a language siteaccess whose `SiteLanguageList[]` does not contain the item's language, the item now shows (in its
main language). On a language's page in **Setup > Languages**, always available objects are marked.

## Problems

| Problem | Cause |
|---|---|
| A whole folder of images still missing | the folder itself, or the images below, are not always available |
| A text page appears untranslated on another language site | it was made always available by mistake; untick it |

## Related pages

- [Content languages, section 6](../../../../guides/content-languages.md#6-main-language-and-always-available)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
