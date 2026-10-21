---
title: Accessibility basics for non-profits
audience: non-profit, editor, website-creator, government
category: non-profits
order: 20
summary: The accessibility essentials for a small non-profit team running an Exponential site, in priority order - templates with landmarks, skip link, focus and contrast; alternative text, headings and link texts in content; accessible forms and CAPTCHAs; plain language; testing with the keyboard and an automated checker; and a small routine that keeps the site accessible.
keywords: accessibility basics, priorities, small team, templates, landmarks, skip link, focus, contrast, alternative text, headings, link text, forms, CAPTCHA, plain language, keyboard testing, axe, routine
type: help
---
# Accessibility basics for non-profits

This page is for a small team that cannot do everything at once. It puts the work in an order that removes the biggest
barriers first. It is not a conformance checklist; for that, see [WCAG 2.2 mapping](../../government/accessibility/wcag-2-2-mapping.md).

## Contents

1. [Priority 1: the templates](#priority-1-the-templates)
2. [Priority 2: content](#priority-2-content)
3. [Priority 3: forms](#priority-3-forms)
4. [Priority 4: language](#priority-4-language)
5. [Testing without a budget](#testing-without-a-budget)
6. [A small routine](#a-small-routine)
7. [Basics checklist](#basics-checklist)

## Priority 1: the templates

Fixed once, they help on every page. The website creator checks these
([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls)):

| Area | What good looks like |
|---|---|
| Language | `<html lang="...">` set; other languages marked |
| Landmarks | header, navigation, main, footer |
| Skip link | "Skip to content" first in the page |
| Headings | One `h1` per page, headings in order |
| Focus | A visible focus style on every link and control |
| Contrast | Text at least 4.5:1, large text 3:1 |
| Zoom | Works at 200% and on narrow screens without sideways scrolling |
| Motion | Sliders and animations respect `prefers-reduced-motion` |

If your site uses a shipped design, test it before you assume it passes: public designs are "not audited"
([What Exponential provides](../../government/accessibility/what-exponential-provides.md#software-versus-site)).

## Priority 2: content

Editors control these every day ([Accessibility checklist for editors](../../customers/day-to-day/accessibility-checklist-for-editors.md)):

- **Alternative text** on every meaningful image; decorative images left empty
  ([Alternative text for images](../../government/accessibility/alternative-text-for-images.md)).
  Image fields can be set to require it.
- **Headings** used as headings, in order, not for size ([Headings and structure](../../government/accessibility/headings-and-structure.md)).
- **Link texts** that say where they lead ([Link text](../../government/accessibility/link-text.md)).
- **Tables** only for data, with header rows ([Tables in content](../../government/accessibility/tables-in-content.md)).
- **Documents** accessible, or with an HTML version ([Accessible campaigns and documents](accessible-campaigns-and-documents.md)).

## Priority 3: forms

Your volunteer form, contact form and newsletter sign-up are where people act. From
[Accessibility for site builders, Forms](../../creators/website-creators/accessibility-for-site-builders.md#forms):

- every input has a visible label;
- required fields are marked in text;
- errors are shown next to the field and summarized at the top;
- CAPTCHAs have an accessible alternative (both shipped services offer one).

Also offer another way to reach you (mail, phone). See [Accessible forms](../../government/accessibility/accessible-forms.md).

## Priority 4: language

Plain language helps people with cognitive disabilities, people reading in a second language, and everyone in a hurry
([Plain language](../../government/languages/plain-language.md),
[Writing for readability](../../government/languages/writing-for-readability.md)). Mark passages in another language
([Language of page and parts](../../government/accessibility/language-of-page-and-parts.md)).

## Testing without a budget

| Test | Time | How |
|---|---|---|
| Keyboard only | 15 minutes per key page | Tab through; can you reach and use everything? Is focus visible? |
| Automated checker | 5 minutes per page | An axe-based browser extension ([Testing with axe and the keyboard](../../government/accessibility/testing-with-axe-and-the-keyboard.md)) |
| Zoom to 200% | 5 minutes | Nothing cut off, no sideways scrolling |
| Ask users | Ongoing | A feedback link on every page; people you serve as testers |

Automated tools find only part of the problems; the keyboard test and real users find more
([Testing accessibility](../../creators/website-creators/testing-accessibility.md)).

Key pages to test first: home, Donate, volunteer form, newsletter sign-up, contact, one news article.

## A small routine

| When | What |
|---|---|
| Every publication | Editor's checklist (alt text, headings, links) |
| Every month | Keyboard test of one key page |
| Every campaign | Test the campaign page before launch |
| Every year | Test all key pages; update the statement ([Accessibility statement](accessibility-statement-for-non-profits.md)) |

See also [Accessibility upkeep](../../customers/day-to-day/accessibility-upkeep.md).

## Basics checklist

- [ ] Templates checked against the table in priority 1.
- [ ] Editors trained on alt text, headings and link texts.
- [ ] Forms labeled, errors clear, CAPTCHA alternative tested.
- [ ] Key pages tested with the keyboard and an automated checker.
- [ ] A feedback route on every page.
- [ ] A yearly test in the calendar.

## Related pages

- [Accessibility for non-profits](README.md)
- [Accessible content for editors](../../government/accessibility/accessible-content-for-editors.md)
- [Color and contrast](../../government/accessibility/color-and-contrast.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
