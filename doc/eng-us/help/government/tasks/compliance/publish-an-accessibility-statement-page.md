---
title: Publish an accessibility statement page
audience: government, editor, project-manager
category: public-sector
order: 440
summary: Step by step - publish your accessibility statement as a page of your Exponential site (no template ships), with a stable address such as /accessibility, a link in every footer, a feedback route, and versions kept for the record.
keywords: accessibility statement, publish, page, URL alias, footer link, feedback form, information collector, W3C WAI, Web Accessibility Directive, review date, versions, public sector
type: help
---
# Publish an accessibility statement page

For editors and project managers, once the statement's text is approved. Exponential ships no statement template
([Accessibility standards: EN 301 549](../../../../standards/accessibility.md#en-301-549)); write the text with the
outline in [Writing an accessibility statement](../../accessibility/writing-an-accessibility-statement.md) or your
country's model.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check that it worked](#check-that-it-worked)
4. [Keeping it current](#keeping-it-current)
5. [Related pages](#related-pages)

## Before you start

- The approved text, with the evaluation date and method, known limitations and a contact.
- Rights to create content in the site's information area and, for the footer link, someone who can change the
  pagelayout or shared footer.

## Steps

1. Create a content object (an article or a page of your site's class) titled "Accessibility statement", in every
   language siteaccess. Use real headings, plain text, no PDF-only content.
2. Give it a stable alias: on the node's **URL aliases** tab add `accessibility` (or your country's usual path); or a
   global alias under **Setup > URL translator**.
3. Add a feedback route: an e-mail address in the text and, if you like, a form built with the information collector
   (the shipped Feedback form class), whose submissions reach the coordinator
   ([Building a contact form](../../../creators/website-creators/building-a-contact-form.md)).
4. Link the page from the footer of every page (pagelayout or shared footer) and from the site map.
5. Publish.

## Check that it worked

- `https://www.example.gov/accessibility` opens the statement in every language.
- The footer link is reachable by keyboard on every page template.
- Run a spot check on the statement page itself ("Run an accessibility spot check of a page").

## Keeping it current

- Review at least yearly and after significant changes; update the date line.
- Earlier versions stay on the object's versions page (`/content/history/<object id>`), within the version history
  limit; raise the limit for this class if you must keep every version.

## Related pages

- [Writing an accessibility statement](../../accessibility/writing-an-accessibility-statement.md)
- [Monitoring and feedback](../../accessibility/monitoring-and-feedback.md)
- [Preserving URLs](../../records-and-archiving/preserving-urls.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
