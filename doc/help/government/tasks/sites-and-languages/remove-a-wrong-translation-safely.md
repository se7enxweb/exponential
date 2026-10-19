---
title: Remove a wrong translation safely
audience: government, editor, admin
category: public-sector
order: 100
summary: Task for lead editors: move an item created in the wrong language into the right one (translate, set main, remove), knowing that removing a translation deletes it in every version.
keywords: remove translation, wrong language, move content language, Set main, Remove selected, translation removal
type: help
---
# Remove a wrong translation safely

This task is for lead editors and administrators, for an item created in the wrong language (for example British
instead of American English). Removing a translation deletes all its fields in every version; only a backup brings it
back. Background: [Removing a translation safely](../../languages/removing-a-translation-safely.md).

## Before you start

- Decide whether the record of the wrong-language text must be kept (an official publication). If so, do not remove
  it; talk to your records officer.
- You may edit the item in both languages.

## Steps

1. **Translate into the right language**: **Edit**, **New translation** (right language), **Translate based on** (wrong
   language), **Send for publishing**.
2. **Set the main language**: **Translations** tab, **Main** column, the right language, **Set main**.
3. **Remove the wrong translation**: in the same tab select it, **Remove selected**, confirm.

## Check

The item has one translation in the right language, shown on that language's site. If the wrong language now has no
objects and no classes, its card in **Setup > Languages** says *Unused* and it can be removed there.

## Problems

| Problem | Cause |
|---|---|
| The wrong translation cannot be removed | it is still the main language; do step 2 first |
| Many items to fix | no shipped command moves translations in bulk; a script on a copy of the database is the way |

## Related pages

- [Content languages, section 8](../../../../guides/content-languages.md#8-moving-content-from-one-language-into-another)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
