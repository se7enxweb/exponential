---
title: How do I add a content language?
audience: admin
category: manual
order: 530
summary: Open Setup > Languages, click Add language, find the language under Language and country and click Add language; then add it to the siteaccesses that should show it and let editors translate.
keywords: content language, add language, languages, translations, siteaccess, SiteLanguageList, content/translations, multilingual
context: content/translations
type: help
---
# How do I add a content language?

A content language is a language content can be written in. Adding one lets editors translate into it; a siteaccess
then decides which visitors see it.

## Who can do this

Users whose role has `content/translations` (administrators). Making visitors see the language needs a settings change
by whoever manages the siteaccesses.

## Steps

1. Open **Setup** > **Languages**.
   The page shows each content language as a card.
2. Click **Add language**.
3. Under **Language and country**, type part of the name, code or country into **Find a language** (for example
   `german` or `ger`) and choose it.
4. Click **Add language**.
   The page says the language was added.
5. Ask whoever manages the siteaccesses to add the language to the siteaccess that should show it.
6. Editors can now translate: see [How do I translate an object into another language?](how-do-i-translate-content.md)

## What can go wrong

- **The card says "In no SiteLanguageList".** No siteaccess shows the language yet, so visitors see nothing in it.
- **Interface language and content language are different things.** The language of the administration's buttons is
  chosen in the right toolbar under **Interface language**, not here.
- **A language was added by mistake.** It can be removed while nothing uses it; the card says whether it can.

## Related pages

- [Content languages](../../guides/content-languages.md)
- [Adding a language](../customers/growth/adding-a-language.md)
- [Multilingual sites](../creators/website-creators/multilingual-sites.md)
- [User manual](README.md)
