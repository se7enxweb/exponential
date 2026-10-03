# Left sidebar width and font size

This page is for anyone who works in the admin every day. You can make the left menu as wide, and its text as large,
as you need. The choice is saved with your account, so it follows you across sessions and computers. It arrived in
June 2026 with the [responsive admin design](admin3-responsive-admin.md).

It helps:

- people who need larger text: the font grows with the width;
- people on a very wide monitor who want a roomy content tree;
- people on a small laptop who want to give the width back to the content.

## Use it

1. Open the admin and open the user menu. It holds three links: **Small**, **Medium** and **Large**. Click one; the
   page reloads at that size.
2. For an exact width, drag the thin handle on the right edge of the left menu. The width follows the pointer (touch
   and pen work too) and is saved when you let go.

| Preset | Width | Font size |
|---|---|---|
| Small (default) | 16rem | 0.8225rem (about 13px) |
| Medium | 22rem | 1rem (16px) |
| Large | 30rem | 1.305rem (about 21px) |
| Dragged | 224px up to 72% of the window width | the default font size |

The three links live in `design/admin3/templates/parts/user/menu.tpl`, in a block with the id `widthcontrol-links`.

## Change the default for everybody

Override `design/admin3/templates/pagelayout.tpl` in your own design extension and change the two default values
(`16rem` and `0.8225rem`) in the `{def $left_sidebar_width ...}` lines.

## How it is stored

The width is a user preference named `admin_left_menu_size`. Its value is `small`, `medium`, `large`, or a pixel width
such as `380px`. `pagelayout.tpl` reads the preference and writes two CSS variables on `:root`:

```css
:root { --left-sidebar-width: 22rem; --left-sidebar-font-size: 1rem; }
```

`pagelayout.css` uses these variables for the menu and everything inside it, so the menu items scale together. The drag
handler saves through the normal preference URL, protected by the form token:

```
user/preferences/set_and_exit/admin_left_menu_size/<value>
```

## Limits

- The preference belongs to a user, not to a browser: a second computer shows the same width.
- A dragged width is clamped to 224px at the lower end and 72% of the window at the upper end, so the content area
  never disappears.
- Saving needs the form token script of `ezformtoken` (active by default). Without it the width is applied but not
  saved.

## Related pages

- [The responsive admin design (admin3)](admin3-responsive-admin.md), [the admin4 design](admin4-design.md)
- [Paging, sorting and page sizes](admin-list-paging.md)
- [Sub-items list: columns, presets and CSV export](subitems-table-options.md), [copy selected sub-items](subitems-copy-selected.md)
- [Hide and unhide selected](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md) (the 6.0.15 line has no release tag yet)
- History: [August 2024](../../history/2024/2024-08.md), [October 2024](../../history/2024/2024-10.md), [November 2024](../../history/2024/2024-11.md), [January 2025](../../history/2025/2025-01.md), [June 2025](../../history/2025/2025-06.md), [June 2026, second half](../../history/2026/2026-06b.md)
