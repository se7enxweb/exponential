---
title: Accessibility checklist
audience: government, admin, editor, project-manager
category: public-sector
order: 30
summary: An accessibility checklist for a public body running Exponential - the administration, the public design, editors' content, testing and the accessibility statement - with what Exponential provides and what remains your responsibility.
keywords: accessibility, checklist, WCAG 2.2, AA, EN 301 549, Section 508, accessibility statement, alternative text, keyboard, contrast, admin4, public design, testing, public sector
type: help
---
# Accessibility checklist

This checklist is for the accessibility lead, site administrator and editorial lead of a public body. It separates
what Exponential provides from what your organization must do. The status words come from
[Accessibility standards](../../../standards/accessibility.md): WCAG 2.2 AA **Partly complies** for the admin4 and
admin4l administration; EN 301 549 and Section 508 **Unverified**; public designs not audited; no VPAT.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel. Official texts: [WCAG 2.2](https://www.w3.org/TR/WCAG22/),
> [EN 301 549](https://www.etsi.org/deliver/etsi_en/301500_301599/301549/03.02.01_60/en_301549v030201p.pdf),
> [Directive (EU) 2016/2102](https://eur-lex.europa.eu/eli/dir/2016/2102/oj), [Section 508](https://www.section508.gov/).

## Contents

1. [The administration](#the-administration)
2. [The public design](#the-public-design)
3. [Content](#content)
4. [Testing](#testing)
5. [Statement and feedback](#statement-and-feedback)

## The administration

- [ ] Staff who need it use the **admin4** design, which is built and tested against WCAG 2.2 AA criteria ([The admin4 design](../../../features/6.0/admin4-design.md)).
- [ ] Staff know the [Keyboard shortcuts](../../keyboard-shortcuts.md) and the light, dark and system modes.
- [ ] You know the gaps: the classic administration, the online editor (TinyMCE) and some extensions are not covered by the same tests ([Accessibility standards](../../../standards/accessibility.md#known-gaps)).
- [ ] Interface language set per user where needed ([UI switch settings](../../../guides/ui-switch-settings.md)).

## The public design

- [ ] The design was built or reviewed for WCAG 2.2 AA; the project's public designs are not audited and are the site builder's responsibility.
- [ ] Skip link, landmarks, one `h1` per page, visible focus, `lang` on `<html>` ([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)).
- [ ] Color contrast of text 4.5:1 and of controls 3:1; no information by color alone.
- [ ] Layout reflows at 320 CSS pixels and at 200% zoom.
- [ ] Forms have labels, error messages and instructions ([Building a contact form](../../creators/website-creators/building-a-contact-form.md)).
- [ ] Right-to-left languages set the direction ([Right-to-left sites](../../creators/website-creators/right-to-left-sites.md)).
- [ ] CAPTCHA, if any, has an accessible alternative ([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md)).

## Content

- [ ] Editors are trained in accessible content ([Editors 201, Module 8](../../trainers/editors-201/module-08-quality-and-accessibility/README.md)).
- [ ] Every informative image has alternative text; nothing in the software forces editors to fill it ([Accessibility standards](../../../standards/accessibility.md#known-gaps)).
- [ ] Headings are in order; tables have header rows; link texts describe the target.
- [ ] Documents (PDF, office files) are accessible or have an accessible alternative.
- [ ] Videos have captions; audio has transcripts.
- [ ] Plain language ([Plain language checklist](plain-language-checklist.md)).

## Testing

- [ ] Test with the keyboard alone and with a screen reader on key journeys ([Testing accessibility](../../creators/website-creators/testing-accessibility.md)).
- [ ] Run an automated checker on the main templates; it finds only part of the problems.
- [ ] Follow a method such as [WCAG-EM](https://www.w3.org/TR/WCAG-EM/) for a formal evaluation.
- [ ] Retest after design changes and after upgrades.
- [ ] If an auditor's report is required, commission one for your site; the project publishes none.

## Statement and feedback

- [ ] Accessibility statement published: conformance status, known problems, feedback contact, date ([W3C guidance](https://www.w3.org/WAI/planning/statements/)).
- [ ] A feedback route that works without the inaccessible content (e-mail, phone).
- [ ] Feedback is answered within your own deadline and fixes are tracked.
- [ ] The statement is reviewed at least once a year ([Annual review](annual-review.md)).

## Related pages

- [Government checklists](README.md)
- [Accessibility standards](../../../standards/accessibility.md)
- The accessibility section of this government help (written by another author)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
