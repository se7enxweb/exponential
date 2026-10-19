---
title: "Lesson 5.1: Accessibility: what Exponential does and what your theme must do"
audience: trainer, designer, developer
category: training
order: 51
summary: Learners read Exponential's accessibility status honestly (WCAG 2.2 AA partly complies for admin4, public designs not audited), separate the product's part from the theme's and the editors', and answer a customer's question without overclaiming. 45 minutes.
keywords: theme design, accessibility, WCAG 2.2, AA, WAI-ARIA, EN 301 549, Section 508, VPAT, partly complies, unverified, public designs not audited, site builder responsibility, admin4, accessibility statement
type: help
---
# Lesson 5.1: Accessibility: what Exponential does and what your theme must do

Sooner or later a customer asks: "Is Exponential accessible?" The honest answer has three parts: what the product
ships, what the theme does, and what editors publish. This lesson gives learners the facts from the project's own
standards page, so they can answer precisely and know which part is their job.

## At a glance

| | |
|---|---|
| Course | [Theme design](../README.md) |
| Module | [Module 5: Accessibility and right to left](README.md) |
| Duration | 45 minutes (25 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Designers, developers, and anyone who answers customers' accessibility questions |
| Prerequisites | [Module 4](../module-04-styling-the-administration/README.md) is helpful but not required |

## Learning objectives

After this lesson, learners can:

- state Exponential's status for WCAG 2.2 AA, WAI-ARIA 1.2, EN 301 549 and Section 508 as the standards page does;
- name the parts that are tested (admin4, admin4l) and the parts that are not audited (public designs);
- explain which accessibility work belongs to the theme and which to the editors;
- write an answer to a customer that neither overclaims nor undersells.

## Trainer's notes

### How to present it

Source: [Accessibility standards](../../../../standards/accessibility.md) and
[Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md).

1. **The summary table** (8 minutes). Project it:

   | Standard | Status |
   |---|---|
   | WCAG 2.2, level AA | Partly complies |
   | WAI-ARIA 1.2 and the APG | Partly complies |
   | EN 301 549 v3.2.1 | Unverified |
   | Section 508 (Revised 508 Standards) | Unverified |

   "Partly complies" means: the admin4 and admin4l administration is built and regression-tested against WCAG 2.2 AA
   criteria; that is not a conformance claim. There is no full audit of every page, no assistive technology test log,
   and no published conformance report (no VPAT or Accessibility Conformance Report).
2. **What is tested** (5 minutes). Examples from the page: accessible names on every admin4 form control
   (`AdminFormLabelsTest`), a skip link and landmarks (`AdminLandmarksTest`), contrast of the tokens in both modes
   (`AdminTokenContrastTest`), keyboard patterns (`AdminKeyboardTest`), 24 x 24 px targets. The tests read template
   and stylesheet sources; they do not render pages in a browser, and axe runs are manual.
3. **Known gaps** (5 minutes). Public site designs (`design/standard`, `design/base`, `design/plain`, the themes that
   come as packages) are not audited: their accessibility is the site builder's responsibility. The online editor was
   not audited. Content depends on editors.
4. **Three owners** (5 minutes). Draw three columns: the product (the administration, the edit forms), the theme
   (language, landmarks, skip link, headings, focus, contrast, zoom, motion), the editors (alternative texts, link
   texts, headings in rich text, tables). This course is about the middle column.

### Common questions

**Can we say "Exponential is WCAG compliant"?** No. Say what the standards page says: the administration (admin4) is
designed and tested against WCAG 2.2 AA criteria and partly complies; the public site's accessibility depends on its
theme and content.

**Does the project have a VPAT?** No; the standards page says so.

**Does the admin help editors with disabilities?** admin4 is built for keyboard use, screen readers, dark mode, forced
colors and reduced motion; the classic designs received fewer fixes.

### Pitfalls

- **Overclaiming in a sales meeting.** Keep the wording of the standards page; it is the project's own statement.
- **Underselling.** The administration work is real and tested; say what is tested.
- **Forgetting content.** A perfect theme with images without alternative text is not accessible.

## Hands-on exercise

Individually, then in pairs. Use [Accessibility standards](../../../../standards/accessibility.md).

1. Read the "Summary" table and write the four statuses on a card.
2. In the WCAG section, pick three rows of the table "How Exponential complies" and note for each the test that holds it.
3. Read "Known gaps" and list the gaps that concern a public theme.
4. Open [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md) and copy
   the table "What the template author controls" into your three-column sheet (product, theme, editors).
5. Add the items of "What editors control" to the editors' column.
6. A customer writes: "Our public body must meet the accessibility rules. Is your CMS compliant?" Write a reply of five
   to eight sentences, based only on the two pages.
7. Swap replies with your partner and mark any sentence that claims more than the pages say.

## Expected result

- The card shows: WCAG 2.2 AA partly complies, WAI-ARIA 1.2 partly complies, EN 301 549 unverified, Section 508
  unverified.
- The three-column sheet places the template author's items in the theme column.
- The replies say what the administration is tested against, that the public site depends on the theme and content,
  that no conformance report exists, and what the theme will do. No reply claims full compliance.
- The trainer reads two replies aloud and discusses the wording.

## Quiz

1. What is Exponential's status for WCAG 2.2 AA, according to its standards page?
   A. Fully complies B. Partly complies C. Does not comply D. Certified
2. Which part of the product is built and regression-tested against WCAG 2.2 AA criteria?
3. Who is responsible for the accessibility of a public site design?
4. Name two things in a page that editors control.
5. Is there a VPAT or Accessibility Conformance Report for Exponential?

<details>
<summary>Answers</summary>

1. **B.** ([Accessibility standards](../../../../standards/accessibility.md#summary))
2. The admin4 and admin4l administration designs.
3. The site builder: public designs are not audited, and their accessibility depends on the theme and the content.
4. Any two: alternative texts, link texts, headings in rich text, data tables with header rows, plain language.
5. No.

</details>

## Further reading

- [Accessibility standards](../../../../standards/accessibility.md): the project's statement, with evidence and gaps.
- [Accessibility for site builders](../../../creators/website-creators/accessibility-for-site-builders.md): the theme's
  and the editors' part.
- [Accessibility audit per release](../../../designers/lifecycle/accessibility-audit-per-release.md): keeping a
  theme accessible over its life.

## Related pages

- [Module 5: Accessibility and right to left](README.md)
- [Theme design: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
