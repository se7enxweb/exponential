---
title: How do I translate an object into another language?
audience: editor, admin
category: manual
order: 300
summary: Click Edit on the object, choose the language under New translation and the language to copy from under Translate based on, translate the fields and click Send for publishing.
keywords: translate, translation, new translation, translate based on, language, content/translate, multilingual
type: help
---
# How do I translate an object into another language?

A translation is another language version of the same object. Its fields can be copied from an existing language and
then translated.

## Who can do this

Users whose role has `content/edit` or `content/translate`, and `content/create`, for the language. A role can limit
these by **Language**, so a translator may be allowed one language only. The language must exist on the site; see
[How do I add a content language?](how-do-i-add-a-content-language.md)

## Steps

1. Open the object in **Content structure** and click **Edit**.
   When the object can be edited in more than one language, the page **Edit** *name* opens with the languages.
2. Under **New translation**, choose the language to add.
3. Under **Translate based on**, choose the language to copy the fields from, or **None** to start empty.
4. Click **Edit**.
   The edit form opens in the new language. The fields of the base language are shown beside the fields where the
   design has a side column.
5. Translate every field, including the URL-relevant name or title.
6. Click **Send for publishing**.
7. Check the object's **Translations** window: the new language is listed.

## What can go wrong

- **"You do not have permission to create a translation in another language."** Your role is limited to other
  languages. Ask an administrator.
- **The new language is not offered.** It is not a content language of the site yet, or the object already has it
  (then choose it under **Existing translations**).
- **Visitors do not see the translation.** No siteaccess shows that language. An administrator adds it to the
  siteaccess's language list.

## Related pages

- [How do I find what is not translated yet?](how-do-i-find-what-is-not-translated.md)
- [Content languages](../../guides/content-languages.md)
- [Languages](../languages.md)
- [Translation routine](../customers/day-to-day/translation-routine.md)
- [User manual](README.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
