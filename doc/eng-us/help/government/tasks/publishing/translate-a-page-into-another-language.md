---
title: Translate a page into another language
audience: government, editor
category: public-sector
order: 260
summary: For editors and translators - add a translation of a page in Exponential with New translation and Translate based on, translate with the original as reference, publish, and keep translations in step after changes.
keywords: translate, translation, New translation, Translate based on, language, multilingual, Translations tab, always available, main language, translator role
type: help
---
# Translate a page into another language

This page is for editors and translators on multilingual public sites. In Exponential one object holds all its
translations; each language siteaccess shows the visitor's language
([Content languages, 7](../../../../guides/content-languages.md#7-translating-content-in-the-editor)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Keeping translations in step](#keeping-translations-in-step)
4. [Check the result](#check-the-result)
5. [Pitfalls](#pitfalls)

## Before you start

- The language is added to the site (the content languages page, `/content/translations`).
- Your role has `content/edit` (or `content/translate`) and `content/create` for that language; translator roles are
  often limited by **Language**.
- The source text is final and published.

## Steps

1. Open the page and press **Edit**. When the object can be edited in more than one language, the page *Edit <name>*
   opens.
2. Under **New translation**, choose the language. Under **Translate based on**, choose the language to start from
   (its fields are copied) or **None**.
3. Press **OK**. The form shows the new language, with the original available as a reference.
4. Translate every text field; fields that are not translatable (dates, relations, tags) are shared and not shown for
   editing in every language.
5. Translate the alternative texts of images in their own Image objects, if those are translated.
6. **Send for publishing**.

## Keeping translations in step

| When the original changes | Do |
|---|---|
| A fact changes (date, address, amount) | update every translation the same day |
| The text is rewritten | update translations, or mark them as older in your process |
| A translation is no longer maintained | on the **Translations** tab, tick it and **Remove selected** |

The **Translations** tab lists the languages of the object with the main one marked; **Set main** changes the main
language; **Update** saves the always-available setting ("Use the main language if there is no prioritized
translation.").

## Check the result

- [ ] Open the page in the language siteaccess, signed out: the translation shows.
- [ ] The language switcher, if your site has one, leads between the versions.
- [ ] The page title and description (metadata) are translated.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Machine translation published without review | review by someone who knows the language and the subject |
| Editing the wrong language | check the language shown in the edit form's title line |
| Pages missing in a language but linked from translated menus | decide whether always available shows the main language ([Content languages, 6](../../../../guides/content-languages.md#6-main-language-and-always-available)) |
| Right-to-left text in a left-to-right page | the siteaccess sets the direction; mark mixed text in the editor ([Right-to-left languages](../../../../features/6.0/right-to-left.md)) |

## Related pages

- [Publishing tasks](README.md)
- [Languages](../../../languages.md)
- [Multilingual sites](../../../creators/website-creators/multilingual-sites.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
