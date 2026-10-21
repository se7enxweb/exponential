---
title: "Designing newsletter emails"
audience: theme-designer
category: theme-design
order: 29
summary: How theme designers design newsletter mails for Exponential sites that use the CJW Newsletter extension - skins with an HTML and a text part, why mail design follows different rules from the web theme, the four shipped skins as starting points, brand accents, placeholders, and how to review without sending real mail.
keywords: theme designer, newsletter, email design, mail, cjw_newsletter, skin, html part, text part, AvailableSkinArray, email templates, placeholders, preview
type: help
---
# Designing newsletter emails

This page is for theme designers asked to make the newsletter look like the brand, on installations that use the
**CJW Newsletter** extension (`cjw_newsletter`). The newsletter's look is a **skin**: two templates, one for the HTML
part and one for the text part of each mail. The steps to create and register a skin are a lesson of the newsletter
course: [Lesson 6.2: Writing your own skin](../trainers/newsletter/module-06-templates-and-skins/writing-your-own-skin.md).
This page is the design brief for it.

## Contents

1. [Mail is not the web](#mail-is-not-the-web)
2. [The shipped skins](#the-shipped-skins)
3. [What a skin contains](#what-a-skin-contains)
4. [Designing the HTML part](#designing-the-html-part)
5. [Designing the text part](#designing-the-text-part)
6. [Brand without breaking](#brand-without-breaking)
7. [Placeholders and the unsubscribe link](#placeholders-and-the-unsubscribe-link)
8. [Reviewing without sending](#reviewing-without-sending)

## Mail is not the web

Mail programs are not browsers. Many ignore linked stylesheets and `<style>` blocks, block remote images by default,
and do not run scripts. The newsletter course therefore teaches a different set of rules for skins: a table layout
about 600 pixels wide, every style written inline, no web fonts, no background images, and absolute addresses for
every link and image. That is the one place where inline styles are the documented practice, and it applies only to
the skin's mail templates. Your web theme keeps its rules: no inline styles, tokens, packed stylesheets
([No inline CSS](../designers/theme-design/no-inline-css.md)).

## The shipped skins

The extension ships four skins, each with `html.tpl` and `text.tpl` under
`newsletter/skin/<name>/outputformat/` in its standard design:

| Skin | Character |
|---|---|
| `default` | the classic skin; its text part is converted from the HTML |
| `company` | a calm corporate letter |
| `news` | a news digest |
| `shop` | cards with buttons |

The administration's **Preview the skins** shows each with its HTML part and text part. Start from the one closest
to your brief and copy it under a new name into your own design, never edit the extension's files
([Lesson 6.1: Skins and output formats](../trainers/newsletter/module-06-templates-and-skins/skins-and-output-formats.md)).

## What a skin contains

- The **subject**, set from the template.
- The **header**: logo, the newsletter's name, the edition's date.
- The **content** of the edition, laid out by the skin.
- The **footer**: who sends it and why, the postal or contact details the client must give, the unsubscribe and
  settings links.

A skin changes how the same edition content is laid out, not the content.

## Designing the HTML part

- One column for most mails; two columns collapse badly in some programs.
- Body text at a size that reads on a phone; buttons large enough to tap, with real text, not text in an image.
- Images with width, height and alternative text, because many readers see the mail with images blocked first. The
  mail must make sense without them.
- Colors with enough contrast; some mail programs apply their own dark mode and invert colors, so test a dark logo on
  a dark background.
- A logo as an image file with an absolute address, not an inline SVG.

## Designing the text part

Some readers, filters and devices only see the text part. Design it too: a clear title line, sections separated by
blank lines, links written out on their own lines, and the unsubscribe link at the end. In skins that write their own
text part, line breaks next to template tags need care; the lesson explains how.

## Brand without breaking

Each skin has a settings group `[Skin_<name>]` with an accent color, so a brand accent can often be set without
changing templates. For a multisite installation with several brands, one skin per brand (registered for that
brand's lists) keeps each simple ([Designing for several brands in multisite](designing-for-several-brands-in-multisite.md)).
Fonts: use a system font stack in the inline styles; a web font will not load in most programs.

## Placeholders and the unsubscribe link

Editors can write placeholders such as `[[name]]` and `[[unsubscribe_url]]` into an edition, which are replaced per
subscriber. Name placeholders need the list's personalization option and subscriber data, so design greetings that
also read well when the name is missing ("Hello," rather than "Hello ,"). Every edition carries an unsubscribe link;
the skin's footer is the place for it, and it must stay visible and readable.

## Reviewing without sending

Never test a skin by sending to real people. The course's setup writes test sends to files on a development
installation, and test addresses use `example.invalid` ([Newsletters with CJW Newsletter](../creators/website-creators/newsletters-with-cjw-newsletter.md#rehearsing-without-real-mail)).
Review:

1. the skin preview, HTML and text part;
2. a test send written to a file, opened in a mail program;
3. with images blocked, at phone width, and in a mail program's dark mode;
4. a long edition and a short one.

## Related pages

- [Lesson 6.2: Writing your own skin](../trainers/newsletter/module-06-templates-and-skins/writing-your-own-skin.md)
- [Newsletters with CJW Newsletter](../creators/website-creators/newsletters-with-cjw-newsletter.md)
- [The cjw_newsletter feature page](../../features/6.0/extensions/cjw_newsletter.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
