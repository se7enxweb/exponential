# The admin4 design

This page is for administrators who choose the look of the admin, and for developers who style it. admin4 is a
complete administration design in the look of the setup wizard, with a light and a dark mode. It holds every file the
admin siteaccess served from `admin3`, `admin2` and `admin` (463 files when it was created, 469 at the time of writing;
for each path, the file that won in the search order), so it works with none of them present. Added 2026-10-02 and
refined during the day.

## Switch to it

1. In the admin siteaccess's `site.ini.append.php`, set:

   ```ini
   [DesignSettings]
   SiteDesign=admin4
   ```

2. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

3. Reload the admin. The header shows a light/dark toggle after the search box.

The siteaccess's design list still names `admin3`, `admin2` and `admin`, for the extensions' folders of those names
(the three templates that `bccie` and `enhancedezbinaryfile` override in their own admin2 folder stay with the
extension). The editor siteaccess falls back to `admin4` first, then `admin3`, `admin2`, `admin`. A test siteaccess
`admintest_admin4` also exists.

## What you get

- **Light and dark mode.** Light (default): pale page, frosted header, cards edged by light and shadow. Dark: slate
  page, white cards. The header toggle switches them; the choice is kept in the browser (`localStorage`) and applied
  before the page is drawn.
- **Top menu.** Every item shows at every width from 576 px, wrapping onto more lines; phones keep the hamburger menu.
  The page always starts below the header, even after a resize adds a line.
- **Sign-in page.** One calm form, fast on phones: labels above 46-48 px fields, 16 px text so mobile Safari does not
  zoom, autofill hints, Enter moves from the username to the password and signs in, a show/hide button, one full-width
  button that cannot be sent twice, and forgot-password and register links when those modules exist. The kernel reads
  the same fields as before.
- **Dashboard.** A welcome with quick actions, key figures, the last 14 days of publishing and the system at a glance,
  around every block of `dashboard.ini`. It points to the two updates that keep an installation secure: the CMS with
  the Git manager, and the Composer libraries on the Updates dashboard, linked only where the user may open them.
- **Node view.** One header row with the class icon, title, badges (class, hidden, secondary location) and the actions
  (language, Edit, Move, Remove, View on site, Preview, Manage versions). One meta line with modified and by whom,
  published, version, section, node, object and remote ids (click to copy) and the translations (click to switch).
  The tabs form a segmented row. Tabs and sub-items start about 300 px below the card's top instead of 500. On phones
  the actions wrap onto two short lines inside the card.
- **Edit form.** The button bars (Send for publishing, Store draft, Store draft and exit, Discard draft) stay in view
  while the form scrolls (CSS `position: sticky`, replacing the YUI script that stopped working). They are slim
  (42 px on a desktop) and fit a phone screen. Edit pages show one left column, not two.
- **Frame.** Side column margins are halved (a 16 px gap). The sidebar buttons sit on the window edges at 48% of the
  window height, with labels and titles. Long names in the side columns wrap instead of being cut off.
- **Tables.** A list table's sorted column is readable (soft orange header).

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/design.ini` | `AdminDesignSettings` | `ShowCommunityLinks` | `disabled` | siteaccess |

`ShowCommunityLinks=enabled` shows the footer line "Evaluate Exponential / Become a member of the Exponential
Community". A template can also set `$show_community_links`.

## For developers

- `design/admin4/stylesheets/admin4.css` is loaded after the base stylesheets and before the extensions' own. It uses
  the base rules' own selectors, so what beat a base rule before still beats it. Shared defaults carry no weight at all
  (`:where`), so extensions' rules win. There are no cascade layers.
- admin4 keeps its own copy of `ezajaxsubitems_expdatatable.js`: it must work with no other admin design present, and
  a single copy in `design/standard` would be shadowed by any design that has its own. Change both copies together.
- The CSS packer used to remove the space before a colon in selectors (`a :hover` became `a:hover`). Fixed.

## Changes to the other designs on the same day

- The classic grey administration design (`classic`) got the Exponential logo, a left menu that collapses and
  resizes, and Layouts pages in its own look.
- The header search scope popup of the old `design/admin` now works in both copies (each has its own element ids) and
  closes on an outside click.

## Related pages

- [The responsive admin design (admin3)](admin3-responsive-admin.md), [left sidebar width and font size](admin3-sidebar-width-and-font-size.md)
- [Setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md), [jQuery 4 and YUI removal](jquery4-and-yui-removal.md)
- [Admin links follow permissions](admin-links-follow-permissions.md), [order list sorting](order-list-sorting.md)
- [Sub-items list: columns, presets and CSV export](subitems-table-options.md), [copy selected sub-items](subitems-copy-selected.md), [hide and unhide selected](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [admin4l: the admin page built from layouts](admin4l.md), [its behaviour change](../../bc/6.0/admin4l.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [June 2026, second half](../../history/2026/2026-06b.md), [June 2025](../../history/2025/2025-06.md), [January 2025](../../history/2025/2025-01.md), [November 2024](../../history/2024/2024-11.md), [October 2024](../../history/2024/2024-10.md), [August 2024](../../history/2024/2024-08.md)
