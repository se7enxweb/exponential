---
title: Right-to-left in the administration
audience: government, admin, editor
category: public-sector
order: 160
summary: What staff who work in Hebrew, Arabic or another right-to-left interface language get in the Exponential administration since 6.0.15: a mirrored layout, isolated names, right-to-left editing, and the known follow-ups.
keywords: right to left, RTL, administration, admin interface, Hebrew, Arabic, mirrored layout, bidi, isolated names, editor direction, interface language
type: help
---
# Right-to-left in the administration

This page is for administrators and editors whose staff use the administration in a right-to-left language. Since
6.0.15 the administration turns around when the interface language is written right to left: the page says
`dir="rtl"`, the side columns change places, text starts at the right and icons that point along the line point the
other way ([Right-to-left languages in the administration](../../../features/6.0/right-to-left.md)).

## Contents

1. [Switching to a right-to-left interface](#switching-to-a-right-to-left-interface)
2. [What changes](#what-changes)
3. [Editing right-to-left content](#editing-right-to-left-content)
4. [Known follow-ups](#known-follow-ups)

## Switching to a right-to-left interface

In the right toolbar, the box **Interface** has the list **Interface language**; choose for example
*עברית (ישראל)* (Hebrew, `heb-IL`) and press **Apply** ([Languages (for editors)](../../languages.md)). The choice lasts
until sign-out. The Hebrew administration translation is complete and machine-assisted; Arabic (`ara-SA`) is new and
machine-assisted too; both welcome review by native speakers ([Languages in Exponential, section 4](../../../languages.md#4-every-administration-translation)).

## What changes

| Part | Behavior |
|---|---|
| Page frame | `<html dir="rtl" lang="he-IL">`; the content tree at the right, the toolbar at the left |
| Layout | admin4, admin4l, the classic design and AdminUI mirror through logical CSS properties |
| Icons | chevrons, arrows and carets that point along the line are mirrored |
| Names | a name typed by an editor (a page, a user) is isolated, so an English name in a Hebrew page keeps its punctuation |
| Content alignment chosen by editors | not mirrored: "align right" means right in every language |

## Editing right-to-left content

Whatever the interface language, text in a right-to-left content language carries `lang` and `dir="rtl"` in the edit
forms, the full view and the preview; the online editors (TinyMCE and the tiptap editor) switch their direction.
So a French-speaking editor can edit an Arabic translation and see it in the right direction.

## Known follow-ups

From [Right-to-left languages in the administration](../../../features/6.0/right-to-left.md#follow-ups):

- Some extension stylesheets still use physical left and right in places.
- The login page follows only the siteaccess's own locale; a right-to-left login page needs a siteaccess whose locale
  is right to left.
- Some module pages and untranslated English text inside a right-to-left page follow the plain Unicode bidirectional
  algorithm.

## Related pages

- [Right-to-left languages on public sites](right-to-left-languages-on-public-sites.md)
- [Administration interface languages](administration-interface-languages.md)
- [Interface language list](../../../features/6.0/ui-switch-languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
