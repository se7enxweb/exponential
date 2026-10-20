---
title: How do I find what is not translated yet?
audience: editor, admin
category: manual
order: 305
summary: Each object's Translations window lists the languages it has; Setup > Languages shows per language how many objects have it and which ones; inside an object, an empty field of a translation is the untranslated one.
keywords: untranslated, missing translation, translations window, content languages, translation status, fields, multilingual
type: help
---
# How do I find what is not translated yet?

There are three places to look: the object, the language, and the fields of a translation.

## Who can do this

Editors with `content/read` see the **Translations** window. **Setup** > **Languages** needs `content/translations`
(administrators).

## Steps

1. **One object:** open it in **Content structure** and look at its **Translations** window. A language the site uses
   but the object does not list is missing for this object.
2. **One language:** open **Setup** > **Languages** and click the language's name. **Objects with a translation in
   it** lists the objects that have it, the last changed first. Compare the count on the card with the count of your
   main language.
3. **One translation:** edit the object in the language. A field left empty, or still in the base language, is not
   translated. Fields that are the same in every language (images, numbers) are often marked as not translatable and
   cannot differ.
4. Translate what is missing: see [How do I translate an object into another language?](how-do-i-translate-content.md)

## What can go wrong

- **A visitor sees English text on the German site.** The object has no German translation and is "always available",
  so the main language is shown. Translate it, or ask whether it should be always available.
- **The counts never match.** Some content is not meant to be translated (users, internal folders). Compare only the
  parts of the tree visitors see.

## Related pages

- [Content languages](../../guides/content-languages.md)
- [Translations and languages](../../features/6.0/translations-and-languages.md)
- [Translation routine](../customers/day-to-day/translation-routine.md)
- [User manual](README.md)
