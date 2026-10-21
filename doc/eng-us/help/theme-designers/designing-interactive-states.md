---
title: "Designing interactive states"
audience: theme-designer
category: theme-design
order: 17
summary: The states every interactive element of an Exponential theme needs - default, hover, focus, active, visited, current, selected, disabled, error and loading - with what each must show, how they map to CSS selectors and tokens, and a state matrix to hand over.
keywords: theme designer, states, hover, focus, focus-visible, active, visited, current page, aria-current, disabled, error state, loading, state matrix
type: help
---
# Designing interactive states

This page is for theme designers who draw buttons, links, form fields, menus and cards. A mockup usually shows each
element in one state; a browser shows it in many. Every state you do not design is designed by accident, usually
badly, and usually in dark mode first. This page lists the states, what each must show, and how they reach CSS.

## Contents

1. [The states](#the-states)
2. [Focus comes first](#focus-comes-first)
3. [Current and selected](#current-and-selected)
4. [Disabled, error and loading](#disabled-error-and-loading)
5. [From state to selector](#from-state-to-selector)
6. [The state matrix](#the-state-matrix)

## The states

| State | When | Must show |
|---|---|---|
| Default | at rest | that it is interactive (links distinguishable from text, not by color alone) |
| Hover | pointer over it | a change, but nothing needed to understand the element |
| Focus | reached with the keyboard | a clearly visible ring, at least 3:1 against its surroundings |
| Active | being pressed | brief feedback |
| Visited | a link already followed | optional; useful in long lists and search results |
| Current | the link to the page you are on | marked in menus and breadcrumbs, also for screen readers |
| Selected / checked | a chosen option or tab | different from hover, not by color alone |
| Disabled | not available now | recognizable, but not the main thing on screen |
| Error | invalid input | message text and an icon, not only a red border |
| Loading | waiting for a result | an indication that something happens, without layout shift |

## Focus comes first

Design the focus state before hover. Keyboard users, switch users and many screen reader users depend on it, and the
project's checklists require it ([Keyboard and focus](../designers/theme-design/keyboard-and-focus.md)):

- one focus style for the whole theme, from a `--site-focus` token, with a dark value;
- an outline with an offset, so it does not merge with the element's own border;
- visible on every surface the element can sit on, including the accent color and images;
- never removed without a replacement.

Hover and focus may look alike, but focus must never be weaker than hover.

## Current and selected

The current page in the main menu and the last item of the breadcrumbs need a visible mark and
`aria-current="page"` in the markup. Ask for both in the handoff; the template decides which item is current
([Menus and navigation](../designers/templates/menus-and-navigation.md#marking-the-current-page)).

Selected tabs, checked filters and the active language in a language switcher follow the same rule: a mark that does
not rely on color, such as an underline, a check mark or a bolder weight.

## Disabled, error and loading

- **Disabled**: avoid it where you can. A button that is disabled until a form is valid leaves people guessing what
  is missing; an enabled button that shows the errors is clearer ([Designing forms](designing-forms.md)).
- **Error**: draw the message next to the field and in a summary at the top of the form, with text, icon and color
  together. The error colors come from status tokens ([Color palettes and ramps](color-palettes-and-ramps.md#status-colors)).
- **Loading**: reserve the space for the result, so nothing jumps when it arrives
  ([Measuring CLS and LCP](../designers/theme-design/measuring-cls-and-lcp.md)). Respect reduced motion for spinners
  ([Motion and interaction](motion-and-interaction.md)).

## From state to selector

| State | CSS |
|---|---|
| Hover | `:hover` (only where a pointer can hover: `@media (hover: hover)`) |
| Focus | `:focus-visible` for the ring; `:focus-within` on a container such as a card |
| Active | `:active` |
| Visited | `:visited` (only colors can change, by browser rules) |
| Current | `[aria-current="page"]` |
| Selected | `[aria-selected="true"]`, `:checked`, `[aria-pressed="true"]` |
| Disabled | `:disabled`, `[aria-disabled="true"]` |
| Error | `[aria-invalid="true"]` on the field |

Styling states through ARIA attributes keeps the visual state and the state announced to assistive technology in step:
the template sets the attribute, and the stylesheet follows it. All values come from tokens; no state is drawn with an
inline style or a script that sets colors.

## The state matrix

Hand over one matrix per component, light and dark:

| Component | Default | Hover | Focus | Active | Current/selected | Disabled | Error |
|---|---|---|---|---|---|---|---|
| Primary button | drawn | drawn | drawn | drawn | n/a | drawn | n/a |
| Text link | drawn | drawn | drawn | drawn | drawn (menu) | n/a | n/a |
| Text field | drawn | n/a | drawn | n/a | n/a | drawn | drawn |
| Card | drawn | drawn | drawn (focus-within) | n/a | n/a | n/a | n/a |

Add the matrix to the [component inventory](component-inventory.md), and show the states on the
[style guide page](building-a-style-guide-page.md) where they can be shown statically.

## Related pages

- [Keyboard and focus](../designers/theme-design/keyboard-and-focus.md)
- [Color contrast](../designers/theme-design/color-contrast.md#check-every-state-and-both-themes)
- [Accessible forms](../designers/theme-design/accessible-forms.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
