---
title: Keyboard and focus
audience: government, developer, designer, editor
category: public-sector
order: 230
summary: For developers, designers and staff who rely on the keyboard - what keyboard support the admin4 administration provides (skip link, tabs, tree, menus, drag alternative), the keys users can learn, and the rules for keeping a public design fully usable without a mouse.
keywords: keyboard, focus, focus visible, focus order, skip link, keyboard trap, tab, arrow keys, content tree, context menu, shortcuts, WCAG 2.1.1, 2.1.2, 2.4.3, 2.4.7, 2.4.11, admin4
type: help
---
# Keyboard and focus

This page is for developers and designers who build templates and scripts, and for administrators and trainers who
support staff working with the keyboard. Everything a mouse can do must be possible with the keyboard, and people
must always see where the focus is. WCAG 2.2 covers this under 2.1.1, 2.1.2, 2.4.3, 2.4.7 and 2.4.11
(<https://www.w3.org/TR/WCAG22/>).

## Contents

1. [In the administration](#in-the-administration)
2. [Keys to teach your staff](#keys-to-teach-your-staff)
3. [Rules for your public design](#rules-for-your-public-design)
4. [Testing](#testing)
5. [Related pages](#related-pages)

## In the administration

admin4 is built and tested for keyboard use ([The admin4 design: Keyboard](../../../features/6.0/admin4-design.md#keyboard),
`tests/tests/design/AdminKeyboardTest.php`, `AdminTreeViewTest.php`, `AdminContextMenuTest.php`):

- "Skip to main content" is the first Tab stop.
- Node tabs follow the WAI-ARIA tabs pattern (arrows, Home, End).
- The content tree follows the tree view pattern (arrows, letters, Shift+F10 for the item's menu).
- Context menus and header menus open and move with the keyboard; Escape closes and returns the focus.
- Reordering sub items by drag and drop has a keyboard mode on the grip.
- Calendar pickers open from a named button and return the focus.
- One shared focus ring, 3 px, checked for 3:1 contrast.

## Keys to teach your staff

The user page [Keyboard shortcuts](../../keyboard-shortcuts.md) lists them; the most useful:

| Key | Does |
|---|---|
| Tab (first press) | shows Skip to main content |
| `/` | jumps to the search field |
| Esc | closes the open menu or pop-up |
| arrows in the tree | move, open and close items |
| Shift+F10 | opens the menu of a tree item or a sub items row |

## Rules for your public design

- **Native elements first**: links for navigation, buttons for actions; no clickable `div`.
- **Order**: the Tab order follows the visual order; do not use positive `tabindex`.
- **Visible focus** on every link and control; never `outline: none` without a replacement.
- **Not obscured**: sticky headers, cookie notices and chat widgets must not cover the focused element (2.4.11).
- **No traps**: embedded maps, video players and CAPTCHAs must let the focus leave with Tab.
- **Menus**: drop-down menus open with Enter or Space, close with Escape, and return the focus.
- **Dialogs**: move focus into the dialog, keep it there while open, return it on close.

## Testing

Put the mouse away and use the page with Tab, Shift+Tab, Enter, Space, Escape and the arrows, following the keyboard
pass in [Testing your site's accessibility](../../creators/website-creators/testing-accessibility.md#keyboard-pass).
Do it for every page template, every form and every interactive block.

## Related pages

- [Keyboard shortcuts](../../keyboard-shortcuts.md)
- [The admin4 design](../../../features/6.0/admin4-design.md)
- [Menus and navigation](../../creators/website-creators/menus-and-navigation.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
