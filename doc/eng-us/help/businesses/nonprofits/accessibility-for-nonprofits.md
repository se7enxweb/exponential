---
title: Accessibility for non-profits
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: Why accessibility matters to a non-profit's mission and funders, what the template author and the editors each control on an Exponential site, the features that help, accessible forms and campaign pages, how to test with little budget, and an accessibility statement.
keywords: accessibility, accessible website, WCAG, screen reader, keyboard, contrast, alternative text, accessible forms, accessibility statement, nonprofit, inclusion
type: help
---
# Accessibility for non-profits

This page is for directors, communication staff and volunteer editors of non-profits. Many non-profits serve people
with disabilities, older people, or people with little time or bandwidth; a site they cannot use works against the
mission. This page explains who is responsible for what on an Exponential site, which features help, and how a small
team can test.

> **Note**
> Accessibility law and funders' conditions differ by country and program. This page is not legal advice; check which
> standard applies to you (often the Web Content Accessibility Guidelines) with your adviser or funder.

## Contents

1. [Two people make a site accessible](#two-people-make-a-site-accessible)
2. [What the template author controls](#what-the-template-author-controls)
3. [What editors control](#what-editors-control)
4. [Features that help](#features-that-help)
5. [Accessible forms](#accessible-forms)
6. [Campaign pages and downloads](#campaign-pages-and-downloads)
7. [Testing on a small budget](#testing-on-a-small-budget)
8. [An accessibility statement](#an-accessibility-statement)
9. [Accessibility checklist](#accessibility-checklist)

## Two people make a site accessible

The website creator builds the templates once; editors add content every week. Both matter, and an accessible design
is undone by inaccessible content. The website creator's guide splits the work the same way
([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).

## What the template author controls

From [Accessibility for site builders, What the template author controls](../../creators/website-creators/accessibility-for-site-builders.md#what-the-template-author-controls):

| Area | Ask your website creator for |
|---|---|
| Language | The page language set, and `dir="rtl"` for right-to-left content |
| Landmarks | Header, navigation, main, footer marked up |
| Skip link | "Skip to content" first in the page |
| Headings | One `h1` per page, headings in order |
| Focus | A visible focus style on every link and control |
| Contrast | Text at least 4.5:1 against its background, large text 3:1 |
| Motion | Sliders and animations respect reduced motion |
| Zoom | Layouts that work at 200% zoom and on narrow screens, no horizontal scrolling |

Put these into your contract or brief with the partner who builds the site
([Requirements checklist](../templates/requirements-checklist.md)).

## What editors control

- **Alternative texts** for images that carry meaning; decorative images get an empty one.
- **Link texts** that say where they lead ("Read the annual report", not "click here").
- **Headings** in rich text used as headings, in order, not to make text bigger.
- **Tables** with header rows, for data only.
- **Plain language**, short paragraphs.

Source: [What editors control](../../creators/website-creators/accessibility-for-site-builders.md#what-editors-control).
Give volunteers this list on one page when they start; it is part of the handover
([Handing the site over to editors](../../creators/website-creators/handing-over-to-editors.md)).

## Features that help

- Image fields can require an alternative text, so an image cannot be published without one.
- The online editor writes structured rich text (headings, lists, tables) rather than free HTML.
- Right-to-left content is marked with `lang` and `dir`.
- The administration itself follows an accessibility design, "so editors with disabilities can work too"
  ([admin4 design](../../../features/6.0/admin4-design.md)).
- Content checks find problems in content ([Content checks](../../../features/6.0/content-checks.md)).

Source: [Exponential features that help](../../creators/website-creators/accessibility-for-site-builders.md#exponential-features-that-help).
Volunteers who are disabled can therefore edit as well as read the site, which widens who can help you.

## Accessible forms

Donation, volunteer and contact forms are where an inaccessible site costs a non-profit most directly:

- [ ] Every input has a visible label.
- [ ] Required fields are marked in text.
- [ ] Errors appear next to the field and are summarized at the top, and the summary receives focus.
- [ ] CAPTCHAs have an accessible alternative; "both shipped CAPTCHA services offer one".

Source: [Accessibility for site builders, Forms](../../creators/website-creators/accessibility-for-site-builders.md#forms).
For the forms themselves see [Volunteers and sign-up forms](volunteers-and-sign-up-forms.md).

## Campaign pages and downloads

- Hero blocks with text over images need enough contrast in every state; check them like any other text.
- One `h1` per campaign page; blocks should not each bring their own top heading.
- Buttons say what they do ("Donate to the winter appeal").
- Downloads show format and size before the click, "an accessibility requirement for many sites"
  ([Files and downloads](../../creators/website-creators/files-and-downloads.md#download-links-in-templates)).
- Annual reports as PDF: also publish the key content as a web page, which is easier to read with assistive technology.

## Testing on a small budget

The routine in [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md) needs no
paid tools to start:

1. Pick the pages that matter: home, donate, volunteer form, a campaign page, a news article.
2. Run an automatic checker on each.
3. Use each page with the keyboard only: every menu, form and dialog, with the focus always visible.
4. Zoom to 200% and narrow the window to phone width.
5. Listen to the donate and volunteer pages with a screen reader.
6. Write down what you found and fix it; retest after each change of templates.

If you know people who use assistive technology, ask them to try the donate and volunteer pages; that finds what tools
miss.

## An accessibility statement

Many organizations publish a short statement: the standard you aim for, what you know does not work yet, and how to
report a problem. Put the contact form or address in it, and answer reports. Whether a statement is required, and in
what form, depends on your law; Exponential does not generate one.

## Accessibility checklist

- [ ] Template requirements in the brief or contract.
- [ ] Image fields require alternative texts.
- [ ] Editors have the one-page list of what they control.
- [ ] Donate, volunteer and contact forms labeled, with announced errors and an accessible CAPTCHA.
- [ ] Campaign pages checked for contrast and heading order.
- [ ] Key pages tested by keyboard, at 200% zoom and with a screen reader.
- [ ] An accessibility statement with a way to report problems.
- [ ] Retest after every design change.

## Related pages

- [Non-profits](README.md)
- [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
