---
title: The robust principle
audience: government, developer, designer
category: public-sector
order: 330
summary: WCAG principle 4, robust, for a public body running Exponential - name, role and value for every control, status messages, valid structured markup, and how admin4's ARIA patterns and the rich text pipeline help your templates stay compatible with assistive technologies.
keywords: robust, WCAG principle 4, name role value, status messages, ARIA, WAI-ARIA 1.2, APG, assistive technology, live regions, parsing, 4.1.1, 4.1.2, 4.1.3, templates, rich text XML
type: help
---
# The robust principle

This page is for developers and designers. The fourth WCAG principle says that content must be robust enough to be
interpreted by a wide variety of user agents, including assistive technologies (<https://www.w3.org/TR/WCAG22/>). In
WCAG 2.2 it has two criteria at level A and AA: 4.1.2 Name, role, value and 4.1.3 Status messages; 4.1.1 Parsing was
removed in 2.2.

## Contents

1. [4.1.2 Name, role, value](#412-name-role-value)
2. [4.1.3 Status messages](#413-status-messages)
3. [Why structured content helps](#why-structured-content-helps)
4. [Related pages](#related-pages)

## 4.1.2 Name, role, value

- **Native first.** The project follows the first rule of ARIA: native elements are preferred; a `title` does not count
  as a name, and `aria-label` must not replace a visible label that has text (`AdminFormLabelsTest.php`;
  [WAI-ARIA 1.2](../../../standards/accessibility.md#wai-aria-12-and-the-aria-authoring-practices)).
- **Patterns.** Where custom widgets are needed, admin4 follows the ARIA Authoring Practices: tabs, tree view,
  disclosure navigation (the opt-in mega menu), menus, toggle buttons with `aria-pressed`. WAI-ARIA 1.2 is at
  <https://www.w3.org/TR/wai-aria-1.2/>.
- **Your templates.** Every custom control in your public design (a menu button, an accordion, a filter) needs a name,
  the right role and its state (`aria-expanded`, `aria-selected`, `aria-current`).

The project's status for WAI-ARIA is **Partly complies** for admin4, **Unverified** elsewhere; its tests check markup
and script hooks, not runtime behavior in a screen reader.

## 4.1.3 Status messages

- Admin4 announces toasts through one pair of live regions and help search results through `aria-live`; module messages
  carry `role="alert"` or a status role (`AdminNoticesTest.php`).
- In your design: "item added", "form sent", "no results" must be announced without moving the focus, with
  `role="status"` or an `aria-live` region.

## Why structured content helps

Exponential stores rich text as XML and renders it through templates, rather than storing free HTML
([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md#exponential-features-that-help)).
That means:

- one template fix changes the markup of every heading, table or link at once;
- what editors write is stored in the tags the rich text format allows, not as arbitrary markup with roles of its own;
- output escaping in templates (`|wash`) keeps stored values from breaking the markup.

## Related pages

- [Accessibility standards](../../../standards/accessibility.md)
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [Mapping WCAG 2.2 AA to Exponential](wcag-2-2-mapping.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
