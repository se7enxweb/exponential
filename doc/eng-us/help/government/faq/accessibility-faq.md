---
title: "FAQ: accessibility"
audience: government, project-manager, admin, sales-marketing
category: public-sector
order: 20
summary: Answers to the accessibility questions public bodies ask about Exponential - WCAG 2.2, EN 301 549, Section 508, VPATs, the administration, public designs, the online editor, content and accessibility statements - backed by the accessibility standards page.
keywords: accessibility FAQ, WCAG 2.2, AA, EN 301 549, Section 508, VPAT, ACR, conformance, admin4, screen reader, keyboard, accessibility statement, public sector
type: help
---
# FAQ: accessibility

This page answers the accessibility questions that procurement panels and accessibility officers in public bodies ask
about Exponential. Every answer is backed by [Accessibility standards](../../../standards/accessibility.md); the status
words used there ("Partly complies", "Unverified") are kept exactly.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Does Exponential conform to WCAG 2.2 AA?](#does-exponential-conform-to-wcag-22-aa)
2. [Is there a VPAT or Accessibility Conformance Report?](#is-there-a-vpat-or-accessibility-conformance-report)
3. [What about EN 301 549 and Section 508?](#what-about-en-301-549-and-section-508)
4. [Can staff with disabilities use the administration?](#can-staff-with-disabilities-use-the-administration)
5. [Will our public website be accessible?](#will-our-public-website-be-accessible)
6. [Is the online editor accessible?](#is-the-online-editor-accessible)
7. [Does Exponential help editors write accessible content?](#does-exponential-help-editors-write-accessible-content)
8. [Is there an accessibility statement template?](#is-there-an-accessibility-statement-template)
9. [How is accessibility tested?](#how-is-accessibility-tested)

## Does Exponential conform to WCAG 2.2 AA?

Not as a whole, and the project does not claim it. The **admin4** and **admin4l** administration designs are built
and regression-tested against WCAG 2.2 AA criteria; their status is **Partly complies**. That is not a conformance
claim: there is no full audit of every page and no assistive technology test log
([Accessibility standards](../../../standards/accessibility.md#status)).

## Is there a VPAT or Accessibility Conformance Report?

No. There is no VPAT and no Accessibility Conformance Report for Exponential, and no WCAG-EM evaluation report
([Accessibility standards](../../../standards/accessibility.md#known-gaps)). If your rules require one, ask the bidder
to commission an evaluation of the site they deliver.

## What about EN 301 549 and Section 508?

Both are **Unverified**. EN 301 549's web clause incorporates WCAG 2.1 AA, which the admin4 work covers in substance,
but no evaluation against the standard has been made and its authoring tool clause has not been reviewed. Section 508
incorporates WCAG 2.0 AA; there is no VPAT ([Accessibility standards](../../../standards/accessibility.md#en-301-549)).

## Can staff with disabilities use the administration?

The admin4 design is the best choice for them: a skip link, landmarks, labeled controls, keyboard support for tabs and
menus, error summaries, color tokens checked for contrast in light and dark mode, targets of at least 24 by 24 pixels,
and support for forced colors and reduced motion ([Accessibility standards](../../../standards/accessibility.md#how-exponential-complies)).
The classic administration and admin3 have had fewer fixes. See [Keyboard shortcuts](../../keyboard-shortcuts.md).

## Will our public website be accessible?

That depends on the design built for you and on your content. The project's public designs are **not audited**; their
accessibility is the site builder's responsibility ([Accessibility standards](../../../standards/accessibility.md#known-gaps)).
Make WCAG 2.2 AA a requirement for the delivered site, with testing ([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md),
[Testing accessibility](../../creators/website-creators/testing-accessibility.md)).

## Is the online editor accessible?

**Unverified.** The TinyMCE integrations were not audited for WCAG; the editor engine's own accessibility is that of
the TinyMCE project ([Accessibility standards](../../../standards/accessibility.md#known-gaps)). Test it with your
staff's assistive technology during evaluation.

## Does Exponential help editors write accessible content?

Partly. Image fields offer an alternative text field, but nothing forces editors to fill it. Training covers accessible
content ([Editors 201, Module 8](../../trainers/editors-201/module-08-quality-and-accessibility/README.md)). If you use
Exponential Intelligence, it can suggest alternative text as a draft for an editor to review
([Exponential Intelligence](../../../features/6.0/extensions/expai.md)).

## Is there an accessibility statement template?

No. The EU Web Accessibility Directive requires public bodies to publish one; Exponential ships no template
([Accessibility standards](../../../standards/accessibility.md#known-gaps-2)). The W3C has guidance and a generator at
[w3.org/WAI/planning/statements](https://www.w3.org/WAI/planning/statements/).

## How is accessibility tested?

Design tests in the `design` suite check template and stylesheet sources: labels, landmarks, keyboard patterns,
contrast, focus and more. Automated axe checks are run by hand during reviews, not in CI, and are recorded in the
changelog ([Accessibility standards](../../../standards/accessibility.md#how-exponential-complies)).

## Related pages

- [Government FAQ](README.md)
- [Accessibility checklist](../checklists/accessibility-checklist.md)
- [Accessibility standards](../../../standards/accessibility.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
