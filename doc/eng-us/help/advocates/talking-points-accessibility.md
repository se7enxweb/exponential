---
title: "Talking points: accessibility"
audience: advocate
category: advocacy
order: 17
summary: Sourced talking points about the accessibility of Exponential's administration (keyboard, landmarks, contrast and target-size tests, dark mode, forced colors, reduced motion, right to left, alternative text), with what the sources do and do not claim about public sites.
keywords: talking points, accessibility, a11y, WCAG, keyboard, screen reader, contrast, target size, dark mode, high contrast, forced colors, reduced motion, alternative text, right to left
type: help
---
# Talking points: accessibility

This page is for advocates who talk to people who care about accessibility: people with disabilities, designers,
public bodies, educators. Accessibility claims are easy to overstate. Say exactly what the sources say, and be clear
that they describe the **administration**.

## Contents

1. [What the sources cover](#what-the-sources-cover)
2. [The points](#the-points)
3. [Show it in one minute](#show-it-in-one-minute)
4. [Do not say](#do-not-say)
5. [Questions you will hear](#questions-you-will-hear)

## What the sources cover

The documented accessibility work is in the **administration** (the admin4 design) and the help. The sources do not
state that any public site design meets a particular standard. A public site's accessibility depends on its own
templates and content ([Accessibility checklists](../leadership/accessibility-security-and-compliance-checklists.md#accessibility)).

## The points

| Say | Source |
|---|---|
| "The admin4 design follows WCAG rules that tests check: a contrast test of the color tokens in light and dark mode." | [Styling the administration](../../bc/6.0/admin4-styling.md), [Languages and accessibility](../who-is-the-exponential-foundation.md#languages-and-accessibility) |
| "Targets are at least 24 by 24 pixels (WCAG 2.5.8), and every form field has a name." | [Accessibility checklists](../leadership/accessibility-security-and-compliance-checklists.md#accessibility) |
| "A skip link is the first Tab stop, with landmarks for header, menus, breadcrumb and main content." | [The admin4 design, Landmarks and names](../../features/6.0/admin4-design.md#landmarks-and-names) |
| "The administration and the help can be used with the keyboard alone." | [Keyboard shortcuts](../keyboard-shortcuts.md), [The admin4 design, Keyboard](../../features/6.0/admin4-design.md#keyboard) |
| "Light and dark themes, Windows high contrast (forced colors) and reduced motion are supported." | [Accessibility checklists](../leadership/accessibility-security-and-compliance-checklists.md#accessibility) |
| "Right-to-left languages turn the administration around, and content written in them gets the right direction." | [Right to left](../../features/6.0/right-to-left.md) |
| "Image fields can require an alternative text, per class; it is off until switched on." | [Accessibility checklists](../leadership/accessibility-security-and-compliance-checklists.md#accessibility) |
| "Exponential Intelligence has a 'Missing alt text' report that queues suggestions for a subtree." | [expai, For editors](../../features/6.0/extensions/expai.md#for-editors) |
| "Instant search results in the help are announced to screen readers." | [The help system, Languages and accessibility](../../features/6.0/help-system.md#languages-and-accessibility) |
| "Contributors must follow the same rules: no inline styles, shared components, tokens with dark values; a test enforces it." | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |

## Show it in one minute

1. Open any administration page and press `Tab` once: the skip link appears.
2. Press the sun button in the header: dark mode.
3. In the content tree, use the arrow keys.
4. Open the help with the "?" and press `/` to search.

## Do not say

- "Exponential is WCAG compliant" or "fully accessible". The sources describe tests and features of the
  administration, not a conformance claim for every page or site.
- "Your site will be accessible." The site's own design and content decide that.
- "Screen readers work perfectly." Say what is built: landmarks, names, announcements; invite testing.

## Questions you will hear

| Question | Answer |
|---|---|
| "Is there an accessibility statement?" | The sources do not contain one for the product. Each site publishes its own; the checklists help prepare it ([Accessibility checklists](../leadership/accessibility-security-and-compliance-checklists.md)). |
| "Can editors make accessible content?" | They can add alternative text; a class can require it; headings and lists in the rich text editor help structure. Training editors matters most. |
| "How do I report an accessibility problem?" | As a bug, in the right tracker, with the page, the assistive technology and what happened ([Reporting bugs well](reporting-bugs-well.md)). |
| "Can I help?" | Yes: test with your assistive technology and report; review a theme ([Advocating for accessibility](advocating-for-accessibility.md)). |

## Related pages

- [Advocating for accessibility](advocating-for-accessibility.md)
- [Accessible slides and visuals](accessible-slides-and-visuals.md)
- [Feature highlights, Accessibility of the administration](../business/feature-highlights-for-customers.md#accessibility-of-the-administration)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
