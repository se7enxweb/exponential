---
title: "Motion and interaction"
audience: theme-designer
category: theme-design
order: 30
summary: How theme designers specify motion and interaction for an Exponential theme - what motion is for, durations and easing as tokens, reduced motion, carousels and sliders editors fill, disclosures and dialogs, and how interaction reaches the theme as packed scripts with no inline code.
keywords: theme designer, motion, animation, transitions, easing, duration, reduced motion, carousel, slider, accordion, dialog, interaction design, micro-interactions
type: help
---
# Motion and interaction

This page is for theme designers who add movement and behavior to a theme: transitions, menus that open, accordions,
sliders. Motion helps when it explains a change, and harms when it decorates: it slows people down, distracts, and can
make some visitors ill. The accessibility rules are in [Reduced motion](../designers/theme-design/reduced-motion.md)
and [Keyboard and focus](../designers/theme-design/keyboard-and-focus.md); how scripts reach a theme in
[Loading JavaScript](../designers/theme-design/loading-javascript.md). This page is about specifying motion and
interaction well.

## Contents

1. [What motion is for](#what-motion-is-for)
2. [Durations and easing as tokens](#durations-and-easing-as-tokens)
3. [Reduced motion](#reduced-motion)
4. [Carousels and sliders](#carousels-and-sliders)
5. [Disclosures, menus and dialogs](#disclosures-menus-and-dialogs)
6. [How interaction reaches the theme](#how-interaction-reaches-the-theme)
7. [Specifying motion in a handoff](#specifying-motion-in-a-handoff)

## What motion is for

Use motion to:

- show where something came from or went (a menu sliding from its button);
- confirm an action (a short highlight after saving);
- connect two states (an accordion opening).

Do not use it to decorate scrolling, to animate content into view as the page is read, or to make a page "feel alive".
Content sites are read; motion that competes with reading costs more than it gives.

## Durations and easing as tokens

Keep two or three durations and one or two easing curves, and make them tokens like the other values
([Design tokens](../designers/theme-design/design-tokens.md)):

```css
:root {
  --site-duration-fast: 120ms;   /* hover, focus, small fades */
  --site-duration-base: 200ms;   /* menus, accordions */
  --site-ease: cubic-bezier(0.2, 0, 0, 1);
}
```

Short is better: interface transitions above about 300 ms feel slow when repeated. Motion tokens are the same in light
and dark.

## Reduced motion

Visitors can ask their system for less motion. The designer help's pattern writes motion only inside
`@media (prefers-reduced-motion: no-preference)`, so it is opt-in ([Reduced motion](../designers/theme-design/reduced-motion.md#the-pattern)).
For every animation you design, also design the reduced version: usually an instant change, or a fade instead of a
movement. Opening a menu still works; it just does not slide.

## Carousels and sliders

Block types such as slider and carousel exist in the layouts editor, so editors will use them
([Block definitions](../designers/theme-design/block-definitions.md#the-shipped-blocks)). If your theme styles them:

- no automatic rotation, or a visible pause control and a stop on hover and focus;
- previous and next controls that are real buttons, reachable with the keyboard;
- an indication of position ("2 of 5");
- every slide readable without the motion; the first slide carries the most important content, because many visitors
  never see the others;
- a design for one slide only, where the controls disappear.

Often a grid view type of the same block serves visitors better; offer it next to the slider
([Designing layout blocks](designing-layout-blocks.md)).

## Disclosures, menus and dialogs

- **Disclosures** (accordions, "show more"): the trigger is a button that says whether it is open; content opens
  below it. The native `details` and `summary` elements give this with no script.
- **Menus**: open on click or Enter, close with Escape and on focus leaving; hover-only menus fail on touch screens and
  for keyboards ([Designing navigation and menus](designing-navigation-and-menus.md#small-screens-and-keyboards)).
- **Dialogs**: use sparingly; focus moves into the dialog and returns to the trigger afterward. The native `dialog`
  element handles much of this.

Design the open and closed state of each, and the focus position after opening and closing.

## How interaction reaches the theme

Behavior is written in JavaScript files of the design, listed in the design's script settings or required from a
template, and packed by ezjscore. Templates carry no inline scripts and no `on*=` attributes, so a strict Content
Security Policy can be used ([No inline JavaScript](../designers/theme-design/no-inline-javascript.md),
[CSP-safe design](../designers/theme-design/csp-safe-design.md)). Where jQuery is used, it is the version ezjscore
ships ([jQuery in designs](../designers/theme-design/jquery-in-designs.md)). Scripts toggle classes and ARIA
attributes; the stylesheet draws the states from them ([Designing interactive states](designing-interactive-states.md#from-state-to-selector)).

## Specifying motion in a handoff

For each animated element, write one line:

| Element | Trigger | Property | Duration | Easing | Reduced motion |
|---|---|---|---|---|---|
| Main menu panel | menu button | slide in from the start edge | `--site-duration-base` | `--site-ease` | appears without sliding |
| Card | hover, focus-within | shadow and 2 px lift | `--site-duration-fast` | `--site-ease` | shadow only |
| Accordion | button | height and fade | `--site-duration-base` | `--site-ease` | instant |

A short screen recording of a prototype helps, but the table is what the developer builds from.

## Related pages

- [Reduced motion](../designers/theme-design/reduced-motion.md)
- [Loading JavaScript](../designers/theme-design/loading-javascript.md)
- [Designing for performance budgets](designing-for-performance-budgets.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
