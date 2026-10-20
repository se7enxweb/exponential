---
title: Always available content
audience: government, editor, admin
category: public-sector
order: 100
summary: What the "always available" flag of an object does in a multilingual Exponential site, which items should carry it (logos, media folders, users, documents in one language), how to set it per object and per class, and its pitfalls.
keywords: always available, Use the main language if there is no prioritized translation, translations tab, language mask, shared items, media, users, class default
type: help
---
# Always available content

This page is for editors and administrators. **Always available** is a flag per object: such an object is shown in its
main language on a siteaccess that lists none of its languages, where it would otherwise be hidden
([Content languages, section 6](../../../guides/content-languages.md#6-main-language-and-always-available)).

## Contents

1. [Which items should be always available](#which-items-should-be-always-available)
2. [Setting it on one item](#setting-it-on-one-item)
3. [Setting the default for a class](#setting-the-default-for-a-class)
4. [Pitfalls](#pitfalls)

## Which items should be always available

| Item | Why |
|---|---|
| Media folders and images | an image is the same in every language |
| User accounts and groups | so an author's name shows on every language site |
| Logos, icons | language-neutral |
| A document that exists in one language only (a legal text) | so other language sites can link to it |
| Folders that only hold structure | so the tree stays navigable |

Pages with text meant for readers should normally not be always available: they should be translated, or fall back
by the siteaccess's `SiteLanguageList[]` (see "Fallback rules for untranslated content").

## Setting it on one item

1. Open the item; open the **Translations** tab.
2. Tick **Use the main language if there is no prioritized translation.**
3. Press **Update**.

On a language's page in **Setup > Languages**, always available objects are marked in the object list.

## Setting the default for a class

In **Setup > Classes**, edit the class and set **Default object availability** ("always available"); new objects of
the class get it. Existing objects keep their setting.

## Pitfalls

- An always available page in English appears on the French site in English, without warning, if the French
  siteaccess lists only French. Use it on purpose, not as a shortcut for missing translations.
- Removing the main translation is impossible while it is the main one; change the main language first (see
  "Main language of an item").
- The flag is bit 0 of the object's language mask; it does not change anything else about the object.

## Related pages

- [Main language of an item](main-language-of-an-item.md)
- [Content languages](../../../guides/content-languages.md)
- [Languages (for editors)](../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
