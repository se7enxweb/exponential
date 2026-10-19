---
title: Accessibility upkeep
audience: customer, editor, project-manager
category: customer-success
order: 19
summary: How a site owner keeps an Exponential site usable for people with disabilities after launch - the split between template work and content work, the shipped features that help, a quarterly spot check, handling visitors' reports and what to ask the partner after design changes.
keywords: accessibility, upkeep, WCAG, screen reader, keyboard, alternative text, contrast, spot check, barriers, site owner, inclusive
type: help
---
# Accessibility upkeep

This page is for the owner who is responsible for the site being usable by everyone, including people who use screen
readers, keyboards, magnification or voice control. Accessibility is built into templates and stylesheets by the site
builder and **kept up by editors** ([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).
The reference most legislation points to is the W3C's Web Content Accessibility Guidelines
([WCAG](https://www.w3.org/WAI/standards-guidelines/wcag/)). This page is not legal advice about which rules apply
to you.

## Contents

1. [Two kinds of work](#two-kinds-of-work)
2. [What Exponential helps with](#what-exponential-helps-with)
3. [A quarterly spot check](#a-quarterly-spot-check)
4. [When a visitor reports a barrier](#when-a-visitor-reports-a-barrier)
5. [After design changes](#after-design-changes)

## Two kinds of work

| Template work (once, for every page) | Content work (editors, page by page) |
|---|---|
| page language, landmarks, skip link | alternative texts for images |
| one main heading per page, visible focus | headings used as headings, in order |
| contrast of text and buttons | link texts that say where they lead |
| zoom to 200% and phone widths | tables with header rows, not for layout |
| accessible forms and error messages | plain language, short paragraphs |

Source: [Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md). Problems
in the left column go to your partner; problems in the right column are fixed by your team, using the
[Accessibility checklist for editors](accessibility-checklist-for-editors.md).

## What Exponential helps with

- Image fields can **require** an alternative text; ask your administrator to switch it on for your image classes
  ([The content model and editing](../../../guides/content-model-and-editing.md)).
- The online editor writes structured rich text (headings, lists, tables) rather than free HTML.
- Right-to-left content is marked with its language and direction
  ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)).
- The administration itself follows an accessibility design, so editors with disabilities can work too
  ([admin4 design](../../../features/6.0/admin4-design.md)).
- The content checks find broken relations and missing files
  ([The content checks](../../../features/6.0/content-checks.md)).

## A quarterly spot check

Do not test every page; test one page of every kind
([Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)):

1. Pick the front page, one landing page, one article, one list, the search results and every form.
2. Run an automatic checker (a browser extension) on each. Note errors.
3. Put the mouse away and use Tab, Enter and Escape: can you reach and use everything, and do you always see where
   you are?
4. Zoom the browser to 200%: nothing cut off, no sideways scrolling.
5. Read five recent articles for the content rules: alternative texts, headings, link texts.
6. Sort what you found into template work (to the partner) and content work (to the editors).

Keep the findings in a table such as the one on
[Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md#recording-findings).

## When a visitor reports a barrier

Give visitors a way to report barriers, for example a contact form, and answer them
([Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)).

| Step | Who |
|---|---|
| Thank the visitor and offer the information another way at once (phone, e-mail, a plain text version) | whoever reads the form |
| Find the page and the problem; decide template or content | editor-in-chief |
| Fix content problems the same week | editors |
| Send template problems to the partner with the page address, what happens and the visitor's tool if known | owner |
| Tell the visitor when it is fixed | whoever answered first |

## After design changes

- Ask your partner to run the automatic checks after every design change, as part of their release checks
  ([Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)).
- Check the new pages yourself with the keyboard and at 200% zoom before you accept them
  ([Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)).
- Make sure every new color scheme or dark mode keeps enough contrast.

## Related pages

- [Accessibility checklist for editors](accessibility-checklist-for-editors.md)
- [Accessibility, security and compliance checklists](../../leadership/accessibility-security-and-compliance-checklists.md)
- [Accessibility for non-profits](../../businesses/nonprofits/accessibility-for-nonprofits.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
