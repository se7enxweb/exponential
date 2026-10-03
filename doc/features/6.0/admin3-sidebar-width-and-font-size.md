# Left sidebar width and font size

Make the left menu of the admin as wide, and its text as large, as you need. The
choice is remembered for your account across sessions. Introduced in June 2026
for the [responsive admin design](admin3-responsive-admin.md).

## Who benefits

- People who need larger text: the font grows with the width.
- People on a very wide monitor who want a roomy content tree.
- People on a small laptop who want to give the width back to the content.

## Use it

1. Open the admin and open the user menu (`design/admin3/templates/parts/user/menu.tpl`).
   It holds three links **Small**, **Medium** and **Large** in a block with the id
   `widthcontrol-links`. Click one: the page reloads at that size.
2. For an exact width, drag the thin handle on the right edge of the left menu.
   The width follows the pointer (touch and pen work too) and is saved when you
   let go.

| Preset | Width | Font size |
|---|---|---|
| Small (default) | 16rem | 0.8225rem (about 13px) |
| Medium | 22rem | 1rem (16px) |
| Large | 30rem | 1.305rem (about 21px) |
| Dragged | 224px up to 72% of the window width | the default font size |

## How it is stored

The width is a user preference named `admin_left_menu_size`. Its value is
`small`, `medium`, `large`, or a pixel width such as `380px`. The template
`design/admin3/templates/pagelayout.tpl` reads the preference and writes two CSS
variables on `:root`:

```css
:root { --left-sidebar-width: 22rem; --left-sidebar-font-size: 1rem; }
```

`pagelayout.css` uses the variables for the menu and everything inside it, so
the menu items scale together. The drag handler saves with the normal preference
URL, protected by the form token:

```
user/preferences/set_and_exit/admin_left_menu_size/<value>
```

If you want a different default for everybody, override `pagelayout.tpl` in your
own design extension and change the two default values (`16rem` and
`0.8225rem`) in the `{def $left_sidebar_width ...}` lines.

## Limits

- The preference belongs to a user, not to a browser: a second computer shows
  the same width.
- A dragged width is clamped to 224px at the lower end and 72% of the window at
  the upper end, so the content area never disappears.
- It needs the form token script of `ezformtoken` (active by default); without
  it the width is applied but not saved.

## See also

[Chronicle: June 2026, second half](../../history/2026/2026-06b.md); [Paging and page sizes](admin-list-paging.md); [Hide and unhide selected](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md). The 6.0.15 line has no release tag yet, see [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md) for what it covers.

## Related pages

- [Copy selected subitems](subitems-copy-selected.md)
- [The sub-items list: 129 columns, presets and CSV export](subitems-table-options.md)
- [The admin4 design](admin4-design.md)
- [August 2024](../../history/2024/2024-08.md)
- [October 2024](../../history/2024/2024-10.md)
- [November 2024](../../history/2024/2024-11.md)
- [June 2025](../../history/2025/2025-06.md)
- [January 2025](../../history/2025/2025-01.md)
