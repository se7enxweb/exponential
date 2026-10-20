---
title: Removing a translation safely
audience: government, admin, editor
category: public-sector
order: 280
summary: The difference between removing a language and removing an item's translation in Exponential, why removing a translation deletes content in every version, the safe order of steps to move content into the right language, and who should hold the right.
keywords: remove translation, remove language, delete translation, wrong language, move translation, Set main, Remove selected, content/translations, record keeping
type: help
---
# Removing a translation safely

This page is for administrators and lead editors. Two removals look alike and are very different
([Content languages, section 9](../../../guides/content-languages.md#9-removing-translations-and-languages-safely)).

## Contents

1. [Language versus translation](#language-versus-translation)
2. [Content in the wrong language](#content-in-the-wrong-language)
3. [Removing a language nobody uses](#removing-a-language-nobody-uses)
4. [Who should be allowed](#who-should-be-allowed)

## Language versus translation

| Action | Where | Deletes | Undo |
|---|---|---|---|
| Remove a **language** | Setup > Languages | only the language row; refused while any object or class has a translation in it | add it again |
| Remove an item's **translation** | the item's Translations tab | all fields and names of the item in that language, **in every version** | only from a backup |
| Remove a class's translation | the class's Translations window | the class name and description in that language | translate the class again |

For a public body, removing a translation also removes the record of what was published in that language. Before
removing, consider whether hiding or a notice serves better (see "Withdrawing and correcting published information"
in the transparency section).

## Content in the wrong language

When content was created in the wrong language (for example British instead of American English):

1. **Translate it into the right language**: Edit, **New translation** (right language), **Translate based on**
   (wrong language), **Send for publishing**. Both translations exist now.
2. **Make the right language the main one**: Translations tab, **Main** column, **Set main**.
3. **Remove the wrong translation**: select it, **Remove selected**, confirm.

No shipped console command moves translations in bulk; for many items, a script against the kernel API tried on a
copy of the database is the way.

## Removing a language nobody uses

1. On **Setup > Languages**, the card must say *Unused* and that it can be removed.
2. Tick **Select for removal**, **Remove selected**, confirm.
3. Take the locale out of every siteaccess's `SiteLanguageList[]`; clear the INI and `content_language` caches
   (`php bin/php/ezcache.php --clear-tag=ini`, `--clear-id=content_language`), and Velocity's response cache.

## Who should be allowed

Give the right to remove translations only to administrators or lead editors. Removals are recorded in the audit
trail ([Audit trail](../../../features/6.0/audit-trail.md)); the removed text itself is gone from the database.

## Related pages

- [Content languages](../../../guides/content-languages.md)
- [Main language of an item](main-language-of-an-item.md)
- [Translator roles and permissions](translator-roles-and-permissions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
