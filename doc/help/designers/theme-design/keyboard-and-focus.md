---
title: Keyboard and focus
audience: designer
category: theme-design
order: 450
summary: Making every control of a design usable with a keyboard: a visible focus ring with :focus-visible, a logical tab order, real buttons and links, menus and dialogs that manage focus, and admin4's focus rules as an example.
keywords: keyboard, focus, focus ring, focus-visible, tab order, tabindex, keyboard navigation, buttons, links, dropdown menu, dialog, focus trap, escape key
type: help
---
# Keyboard and focus

This page is for designers and front-end developers of a design. Many people use a keyboard instead of a mouse. The page
answers: how focus must look, how it must move, and which elements to use.

## Contents

1. [A visible focus ring](#a-visible-focus-ring)
2. [Real elements](#real-elements)
3. [Tab order](#tab-order)
4. [Menus, disclosures and dialogs](#menus-disclosures-and-dialogs)
5. [Testing with the keyboard](#testing-with-the-keyboard)

## A visible focus ring

Every element that can receive focus must show it. admin4's rule (`components.css`):

![The theme switch of the header with the keyboard focus ring around it](../../images/designers/focus-ring.webp)

```css
.a4-page :focus-visible,
.a4-focus:focus-visible,
.a4-btn:focus-visible {
  outline: 3px solid var(--a4-ring);
  outline-offset: 2px;
}
```

- `:focus-visible` shows the ring for keyboard focus and not after a mouse click.
- The ring color is a token with 3:1 contrast against the page in both themes.
- Never write `outline: none` unless the same rule draws another clear indicator.

For a public design:

```css
:where(a, button, input, select, textarea, summary, [tabindex]):focus-visible {
  outline: 3px solid var(--site-focus);
  outline-offset: 2px;
}
```

## Real elements

- A thing that goes somewhere is an `<a href>`; a thing that does something is a `<button type="button">`.
- Do not make a `<div>` clickable: it is not focusable and does not react to Enter and Space.
- `<details>` and `<summary>` give a working disclosure without script.

## Tab order

- Follow the visual order in the source order; do not fix it with positive `tabindex` values.
- `tabindex="-1"` only for elements you focus by script (a dialog, the target of a skip link).
- Hidden content (closed menus, off-canvas panels) must be out of the tab order: `hidden`, `display: none` or `inert`.

## Menus, disclosures and dialogs

| Widget | Keyboard behavior |
|---|---|
| Menu button | Enter/Space toggles; `aria-expanded` says the state; Escape closes and returns focus to the button |
| Off-canvas navigation | focus moves into it when opened, Tab stays inside while open, Escape closes |
| Dialog | prefer `<dialog>` with `showModal()`; focus goes in, Escape closes, focus returns to the opener |

admin4's side drawers follow this model (focus to the first control, focus trap, Escape returns focus to the tab,
`aria-expanded` on the tab), as described in the admin styling guide.

## Testing with the keyboard

1. Click into the address bar, then press Tab repeatedly through the whole page.
2. Is the first stop the skip link? Is focus always visible? Does the order make sense?
3. Open every menu and dialog with the keyboard, and close it with Escape.
4. Repeat at 200% zoom and in dark mode.

## Related pages

- [Skip links](skip-links.md)
- [Accessibility basics for themes](accessibility-basics-for-themes.md)
- [Reduced motion](reduced-motion.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
