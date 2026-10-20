---
title: Translating an item step by step
audience: government, editor
category: public-sector
order: 210
summary: For translators and editors: how to add a translation to a page in Exponential, starting from an existing language, how to update a translation later, how to check it on the public site, and what to watch in fields that are not translatable.
keywords: translate, translation, new translation, Translate based on, language badge, edit language, Send for publishing, translator, editor
type: help
---
# Translating an item step by step

This page is for translators and editors. Each translation of an item is published on its own; publishing the French
text does not change the English one ([Languages (for editors)](../../languages.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Add a translation](#add-a-translation)
3. [Update a translation](#update-a-translation)
4. [Fields that are not translated](#fields-that-are-not-translated)
5. [Check it on the site](#check-it-on-the-site)
6. [Problems](#problems)

## Before you start

- Your role allows the language (see "Translator roles and permissions").
- You know the glossary for the subject (see "Glossaries of terms").

## Add a translation

1. Open the item. The line under the buttons shows a badge per language; a dashed badge is a missing translation.
2. Click the dashed badge of your language, or choose **New translation** in the language list next to **Edit** and
   press **Edit**.
3. If the page asks: choose the language under **New translation** and, under **Translate based on**, the existing
   translation to start from (its text is copied into the form), or **None** for empty fields.
4. Translate every field. The edit form names the language you are editing next to the title.
5. Press **Send for publishing** (or **Store draft and exit** to continue later).

## Update a translation

Choose the language in the list next to **Edit**, press **Edit**, change the text, **Send for publishing**. If the
source changed, compare the source versions first (**Manage versions**, **Compare two versions**) to see what to
change.

## Fields that are not translated

A class can mark fields as not translatable (dates, numbers, codes, sometimes images). They show the value of the main
language and cannot be changed in a translation. If a field should differ per language but cannot, ask the
administrator to change the class.

Images: the alternative text belongs to the image object, often in one language. If the image is used on pages in
several languages, its alternative text needs a translation too: translate the image object itself.

## Check it on the site

Open the page on the language's address (for example `/fr/...`), signed out, and check the title, the menu entry, the
address (URL aliases are kept per language) and the language switcher.

## Problems

| Problem | Cause |
|---|---|
| My language is not offered | your role has no **Language** limitation for it, or the language was not added |
| The translation does not show on the site | the siteaccess does not list the language in `SiteLanguageList[]`, or a cache; ask the administrator |
| The page shows in another language | that is the fallback: your translation is not published yet |

## Related pages

- [Languages (for editors)](../../languages.md)
- [Editing content](../../editing-content.md)
- [Main language of an item](main-language-of-an-item.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
