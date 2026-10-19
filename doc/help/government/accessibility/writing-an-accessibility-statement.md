---
title: Writing an accessibility statement
audience: government, project-manager, editor
category: public-sector
order: 130
summary: For public bodies that must publish an accessibility statement - Exponential ships no template, so this page gives an outline based on the W3C WAI guidance, what to say about the CMS honestly, and how to publish and maintain the statement as a page of your site.
keywords: accessibility statement, W3C WAI, Web Accessibility Directive, conformance status, known limitations, feedback, contact, enforcement, statement template, outline, publish, footer link
type: help
---
# Writing an accessibility statement

This page is for project managers, accessibility coordinators and the editors who publish the statement. Exponential
ships **no accessibility statement template**; the project names that as a gap against the Web Accessibility
Directive ([Accessibility standards: EN 301 549](../../../standards/accessibility.md#en-301-549)). The outline below
follows the W3C WAI guidance, Developing an Accessibility Statement (<https://www.w3.org/WAI/planning/statements/>).
If your country publishes a model statement, use that model; it takes precedence.

![An item's page at 375 pixels wide, one of the checks before writing an accessibility statement](../../images/government/gov-a11y-statement.webp)

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you write](#before-you-write)
2. [An outline](#an-outline)
3. [What to say about the CMS](#what-to-say-about-the-cms)
4. [Publishing it in Exponential](#publishing-it-in-exponential)
5. [Keeping it current](#keeping-it-current)
6. [Related pages](#related-pages)

## Before you write

You need the result of an evaluation of your site (see "Auditing your public design with WCAG-EM"), a list of known
problems with a plan, and a contact that someone actually answers.

## An outline

| Section | Content |
|---|---|
| Commitment | the body's commitment and the site the statement covers (its address) |
| Standard and status | the standard (for example WCAG 2.2 AA) and the status: fully, partially or not conformant, as your law words it |
| Known limitations | each problem in plain words, where it occurs, and the alternative offered meanwhile |
| Plan | what will be fixed and by when |
| Feedback and contact | how to report a barrier or ask for content in another format: e-mail, phone, form |
| Enforcement | the body people can turn to if the answer is unsatisfactory, where your law names one |
| Technical information | the technologies the site relies on (HTML, CSS, JavaScript) |
| Assessment | how the site was evaluated (self-evaluation or external), when, by whom |
| Date | date of the statement and of its last review |

## What to say about the CMS

Most statements do not need to mention the CMS at all: they are about the public site. If you mention it, keep to
the project's words:

- the administration design admin4 is designed and tested against WCAG 2.2 AA and rated "Partly complies" by the
  project;
- EN 301 549 and Section 508 conformance is "Unverified";
- there is no VPAT or Accessibility Conformance Report.

Never write that the site is accessible "because it uses an accessible CMS".

## Publishing it in Exponential

1. Create the statement as a normal content object (an article or a folder with text, in your content tree), in every
   language siteaccess of the site.
2. Give it a stable, readable URL alias, for example `/accessibility`, so the address in your footer and in reports
   never changes ([URL aliases and nice URLs](../../creators/website-creators/url-aliases-and-nice-urls.md)).
   A global alias can also be set under Setup > URL translator
   ([Global URL aliases](../../../guides/urls-and-aliases.md#3-global-url-aliases)).
3. Link it from the footer of every page: in your pagelayout or the shared footer
   ([Shared header and footer](../../creators/website-creators/shared-header-and-footer.md)).
4. If you offer a feedback form, build it with the information collector
   ([Building a contact form](../../creators/website-creators/building-a-contact-form.md)) and state in the privacy
   notice what happens with the messages.
5. Write the statement itself accessibly: real headings, plain language, no information only in a PDF.

## Keeping it current

- Review it at least once a year and after every significant change (redesign, new extension, new service).
- Keep earlier versions: the object's versions page keeps them (`/content/history/<object id>`), subject to the
  version history limit ([Versions of an object](../../../guides/content-history.md)).
- Update the date line with each review.

## Related pages

- W3C WAI, [Developing an accessibility statement](https://www.w3.org/WAI/planning/statements/)
- [Accessibility standards](../../../standards/accessibility.md)
- [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
