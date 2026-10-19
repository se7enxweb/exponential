---
title: Right-to-left accessibility
audience: government, admin, designer, developer
category: public-sector
order: 260
summary: For public bodies that publish in Arabic, Hebrew, Persian, Urdu or other right-to-left languages - how Exponential sets direction and language in the administration and in content, how names are isolated, and what your public design must do.
keywords: right to left, RTL, bidirectional text, bidi, Arabic, Hebrew, Persian, Urdu, dir attribute, lang attribute, bdi, TextDirection, locale, content languages, WCAG 1.3.2, 3.1.1, 3.1.2, multilingual public services
type: help
---
# Right-to-left accessibility

This page is for administrators, designers and developers in public bodies that serve communities in right-to-left
languages. Direction and language are accessibility matters: a screen reader reads Arabic text with the wrong voice
if `lang` is wrong, and punctuation jumps to the wrong end if direction is not isolated. The facts below come from
[Right-to-left languages in the administration](../../../features/6.0/right-to-left.md).

## Contents

1. [Where the direction comes from](#where-the-direction-comes-from)
2. [In the administration](#in-the-administration)
3. [In content](#in-content)
4. [In your public design](#in-your-public-design)
5. [Testing](#testing)
6. [Related pages](#related-pages)

## Where the direction comes from

The only source is the locale file: `share/locale/<locale>.ini`, `[LanguageSettings] TextDirection=rtl`; 28 locale
files carry it. Templates can ask `$site.text_direction`, or a locale's `is_right_to_left`.

## In the administration

- When the **interface language** is right to left, every administration pagelayout writes `dir="rtl"` on `<html>`;
  side columns change places and directional icons point the other way.
- Names typed by editors (node names, user names) are isolated with `dir="auto"` or `<bdi>` where the administration
  inserts them into its text, so mixed-direction text keeps its punctuation in place.
- A left-to-right page is unchanged, byte for byte.
- Held by `tests/tests/design/AdminRightToLeftTest.php`; the project counts this under WCAG 1.3.2 Meaningful sequence
  ([Accessibility standards](../../../standards/accessibility.md#wcag-22)).

## In content

Independent of the interface language, text in a right-to-left **content** language carries `lang` and `dir="rtl"`
where it is shown and edited:

- `design/standard/templates/parts/exp_content_direction.tpl` prints ` lang="he-IL" dir="rtl"` (for example) for a
  right-to-left content locale; your design can include it;
- the edit forms of the standard, admin, admin4 and AdminUI designs;
- the online editor switches its direction (TinyMCE 3 and 8 `directionality`, the tiptap editor's
  `contentDirection`).

Content languages themselves are set up as described in [Content languages](../../../guides/content-languages.md).

## In your public design

- Write `lang` and `dir` on `<html>` from the siteaccess locale, as the administration does.
- Use CSS logical properties (`margin-inline-start` rather than `margin-left`) so one stylesheet serves both
  directions.
- Include `parts/exp_content_direction.tpl` where content of another direction appears inside a page (a quotation, a
  translated summary).
- Mirror directional icons (arrows, chevrons), not logos or media controls.
- See [Right-to-left sites](../../creators/website-creators/right-to-left-sites.md) for the builder's view.

## Testing

- Switch the interface language of a test user to a right-to-left language and use the main editing tasks.
- On the public site, check each right-to-left siteaccess with a screen reader set to that language: the voice must
  change with `lang`.
- Check mixed text: an English name in an Arabic sentence, a phone number, a date.

## Related pages

- [Right-to-left languages in the administration](../../../features/6.0/right-to-left.md)
- [Content languages](../../../guides/content-languages.md)
- [Languages](../../languages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
