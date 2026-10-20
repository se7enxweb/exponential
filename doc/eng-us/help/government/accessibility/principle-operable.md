---
title: The operable principle
audience: government, editor, designer, developer
category: public-sector
order: 310
summary: WCAG principle 2, operable, for a public body running Exponential - keyboard access, enough time, seizures, navigation and input modalities, with each role's tasks and what the admin4 administration provides.
keywords: operable, WCAG principle 2, keyboard, enough time, timeouts, seizures, flashing, navigable, skip link, page titled, focus order, link purpose, focus visible, target size, dragging, 2.1, 2.2, 2.3, 2.4, 2.5
type: help
---
# The operable principle

This page is for everyone on a public website team. The second WCAG principle says that interface components and
navigation must be operable (<https://www.w3.org/TR/WCAG22/>). Below, its five guidelines, with each role's tasks in
an Exponential project.

## Contents

1. [2.1 Keyboard accessible](#21-keyboard-accessible)
2. [2.2 Enough time](#22-enough-time)
3. [2.3 Seizures and physical reactions](#23-seizures-and-physical-reactions)
4. [2.4 Navigable](#24-navigable)
5. [2.5 Input modalities](#25-input-modalities)
6. [Related pages](#related-pages)

## 2.1 Keyboard accessible

| Role | Does |
|---|---|
| Developers | native elements; menus, dialogs, sliders and embedded widgets work with the keyboard; no traps |
| Editors | avoid embedding third-party widgets that trap the keyboard |

Admin4: tabs, tree, context menus, drag-and-drop alternative, date pickers (`AdminKeyboardTest.php`). See "Keyboard and
focus".

## 2.2 Enough time

| Role | Does |
|---|---|
| Administrators | weigh session length for security against people who work slowly: `site.ini [Session] SessionTimeout` (259200 s shipped) ([13.7](../../../install/13-security-hardening.md#137-sessions-and-cookies)) |
| Designers | sliders and carousels can be paused; no automatic page refresh |
| Editors | no content that moves by itself without a pause control |

Expired forms show the "form expired" page ([Form expired page](../../../features/6.0/form-expired-page.md)).

## 2.3 Seizures and physical reactions

| Role | Does |
|---|---|
| Editors | no videos or animations that flash more than three times per second |
| Designers | respect `prefers-reduced-motion` (admin4 does in `admin4.css`) |

## 2.4 Navigable

| Role | Does |
|---|---|
| Developers | skip link, landmarks, descriptive `<title>`, logical focus order, current page marked with `aria-current` |
| Editors | clear object names (they become page titles), descriptive headings, meaningful link texts |
| Designers | visible focus; sticky elements that do not cover the focus (2.4.11) |

Admin4: skip link, `aria-current="page"` in breadcrumb and top menu, one focus ring (`AdminLandmarksTest.php`,
`AdminHeaderTabOrderTest.php`).

## 2.5 Input modalities

| Role | Does |
|---|---|
| Designers | targets at least 24 x 24 px (2.5.8); visible labels match accessible names |
| Developers | single-pointer alternatives to dragging (2.5.7); no actions on pointer down only |

Admin4: targets at least 24 x 24 px ([admin4 styling](../../../bc/6.0/admin4-styling.md)); reordering sub items by drag
also works with the keyboard and by editing the priority cells.

## Related pages

- [Keyboard and focus](keyboard-and-focus.md)
- [Mapping WCAG 2.2 AA to Exponential](wcag-2-2-mapping.md)
- [Keyboard shortcuts](../../keyboard-shortcuts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
