---
title: "Theme design review checklist"
audience: theme-designer
category: theme-design
order: 39
summary: A checklist for reviewing a theme design before it is built or before a release - concept, content fit, system consistency, accessibility, multilingual and multi-brand readiness, editor experience, and the project's design rules - for a designer reviewing their own or a colleague's work.
keywords: theme designer, design review, checklist, critique, peer review, design system review, consistency, accessibility, editor experience, design rules
type: help
---
# Theme design review checklist

This page is for theme designers who review a design: their own before handoff, or a colleague's. It checks the
**design** (mockups, tokens, components and the decisions behind them), before or alongside the build. To check the
built pages, use the [Design QA checklist](design-qa-checklist.md); to review template code, use
[Checklist: code review for templates](../designers/theme-design/checklist-code-review-for-templates.md).

## Contents

1. [How to run the review](#how-to-run-the-review)
2. [Concept and scope](#concept-and-scope)
3. [Content fit](#content-fit)
4. [System consistency](#system-consistency)
5. [Accessibility in the design](#accessibility-in-the-design)
6. [Languages and brands](#languages-and-brands)
7. [Editor experience](#editor-experience)
8. [Project rules](#project-rules)
9. [Outcome](#outcome)

## How to run the review

Review in one sitting of one to two hours, with the mockups, the token table, the component inventory and the mapping
sheet open. Write findings as you go, each with the frame and the item below it fails. A second reviewer who did not
make the design finds more.

## Concept and scope

- [ ] The design answers the brief's goals, in a sentence you can say.
- [ ] Every item of the inventory is designed: frame, class views, special pages, blocks
      ([From mockup to theme](from-mockup-to-theme.md#stage-1-inventory)).
- [ ] Nothing is designed that has no content behind it.
- [ ] The number of layout types, view modes and block view types is as small as the site allows
      ([Designing view modes](designing-view-modes.md#how-many-modes-a-design-needs)).

## Content fit

- [ ] Every visible element maps to a field ([Mapping a design to content classes](mapping-a-design-to-content-classes.md)).
- [ ] Shortest and longest content shown for headlines, intros, names and labels.
- [ ] Every optional field has an empty design ([Designing for editors](designing-for-editors.md#rule-2-every-optional-field-has-an-empty-design)).
- [ ] Rich text elements all styled.
- [ ] Lists for zero, one, few and many items.
- [ ] Images in any shape handled ([Designing image crops and art direction](designing-image-crops-and-art-direction.md)).

## System consistency

- [ ] All values come from the token table; no one-off colors, sizes or spaces.
- [ ] One accent and one component radius.
- [ ] Every color token has a dark value, and every key frame is drawn in dark.
- [ ] Each component appears the same everywhere it is used; near-duplicates merged
      ([Component inventory](component-inventory.md)).
- [ ] Names match across design tool, inventory and planned class names.

## Accessibility in the design

- [ ] Contrast ratios measured and written down for both themes ([Color contrast](../designers/theme-design/color-contrast.md)).
- [ ] Focus state designed for every interactive element ([Designing interactive states](designing-interactive-states.md)).
- [ ] No meaning carried by color alone (links, errors, current page, required fields).
- [ ] Heading structure makes sense when read in order ([Landmarks and headings](../designers/theme-design/landmarks-and-headings.md)).
- [ ] Targets large enough to tap; layouts work at 200% zoom.
- [ ] Motion has a reduced version ([Motion and interaction](motion-and-interaction.md#reduced-motion)).

## Languages and brands

- [ ] Longest language tried in navigation, buttons and headings ([Designing for many languages](designing-for-many-languages.md)).
- [ ] Mirrored layout considered for right-to-left languages.
- [ ] No text inside images.
- [ ] For several brands: only matrix items vary ([Designing for several brands in multisite](designing-for-several-brands-in-multisite.md)).

## Editor experience

- [ ] Editors can achieve every designed look without remembering special rules
      ([Designing for editors](designing-for-editors.md#rule-6-no-design-depends-on-editors-remembering-it)).
- [ ] Block view types have clear names ([Designing layout blocks](designing-layout-blocks.md#naming-view-types-for-editors)).
- [ ] Field guidance written where lengths or image shapes matter.

## Project rules

- [ ] Nothing in the design needs inline styles or inline scripts to build ([CSP-safe design](../designers/theme-design/csp-safe-design.md)).
- [ ] Fonts self-hosted, licenses checked; third-party embeds listed with placeholders
      ([Designing for performance budgets](designing-for-performance-budgets.md#third-parties)).
- [ ] The design overrides shipped designs in its own extension, never by editing them.
- [ ] Administration styling, if touched, uses the shared admin components and tokens
      ([admin4 components](../designers/theme-design/admin4-components.md)).

## Outcome

End the review with one of: ready for handoff, ready after listed fixes, or needs another round. Record the result
and the findings next to the design in the theme's repository.

## Related pages

- [Handoff checklist](handoff-checklist.md)
- [Design QA checklist](design-qa-checklist.md)
- [An accessibility audit for every release](../designers/lifecycle/accessibility-audit-per-release.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
