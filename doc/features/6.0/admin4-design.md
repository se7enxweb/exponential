# The admin4 design

admin4 is a complete administration design in the look of the setup wizard, with a light and a dark mode. It
holds every file the admin siteaccess served from `admin3`, `admin2` and `admin` (463 files when it was created, 469 at
the time of writing; the winner of each path in the search order), so it works with none of them present. Added 2026-10-02 and refined during the day.

## Switch to it

In the admin siteaccess's `site.ini.append.php`:

```ini
[DesignSettings]
SiteDesign=admin4
```

then `php bin/php/ezcache.php --clear-all --allow-root-user`. The siteaccess's design list still names `admin3`,
`admin2` and `admin`, for the extensions' folders of those names (the three templates `bccie` and
`enhancedezbinaryfile` override in their own admin2 folder stay with the extension). The editor siteaccess falls
back to `admin4` first, then `admin3`, `admin2`, `admin`; there is also a test siteaccess `admintest_admin4`.

## What it looks like

- **Light mode** (default): pale page, frosted header, cards edged by light and shadow. **Dark mode**: slate page,
  white cards. The header toggle after the search box switches them; the choice is kept in the browser
  (`localStorage`) and applied before the page is drawn.
- **Top menu**: every item at every width from 576 px, wrapping onto more lines; phones keep the hamburger menu.
  The page always starts below the header, even after a resize adds a line.
- **Sign-in page**: one calm form for fast use on phones (labels above 46-48 px fields, 16 px text so mobile
  Safari does not zoom, autofill hints, Enter moves from the username to the password and signs in, a show/hide
  button, one full-width button that cannot be sent twice, forgot-password and register links when the modules
  exist). The kernel reads the same fields as before.
- **Dashboard**: welcome with quick actions, key figures, the last 14 days of publishing and the system at a glance
  around every block of `dashboard.ini`. It teaches the two updates that keep an installation secure: the CMS with
  the Git manager and the Composer libraries on the Updates dashboard, linked only where the user may open them.
- **Node view**: compact and richer: one header row with the class icon, title, badges (class, hidden, secondary
  location) and the actions (language, Edit, Move, Remove, View on site, Preview, Manage versions); one meta line
  with modified and by whom, published, version, section, node, object and remote ids (click to copy) and the
  translations (click to switch); the tabs as a segmented row. The tabs and sub-items start about 300 px below the
  card's top instead of 500. On phones the actions wrap onto two short lines inside the card.
- **Edit form**: the button bars (Send for publishing, Store draft, Store draft and exit, Discard draft) stay in
  view while the form scrolls (CSS `position: sticky`, replacing the YUI script that stopped working), are slim
  (42 px on a desktop) and fit a phone screen; edit pages show one left column, not two.
- **Frame**: side column margins halved (a 16 px gap), the sidebar buttons sit on the window edges at 48% of the
  window height, with labels and titles; long names in the side columns wrap instead of being cut off.
- A list table's sorted column is readable (soft orange header).

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/design.ini` | `AdminDesignSettings` | `ShowCommunityLinks` | `disabled` | siteaccess |

`ShowCommunityLinks=enabled` shows the footer's "Evaluate Exponential / Become a member of the Exponential
Community" line; a template can also set `$show_community_links`.

## Files and notes for developers

- `design/admin4/stylesheets/admin4.css` is loaded after the base stylesheets and before the extensions' own.
  It uses the base rules' own selectors, so what beat a base rule before still beats it; shared defaults carry no
  weight at all (`:where`), so extensions' rules win; there are no cascade layers.
- admin4 keeps its own copy of `ezajaxsubitems_expdatatable.js`: it must work with no other admin design
  present, and a single copy in `design/standard` would be shadowed by any design that has its own. Change both
  copies together.
- The CSS packer previously removed the space before a colon in selectors (`a :hover` became `a:hover`); fixed.
- The classic grey administration design (`classic`) got the Exponential logo, a left menu that collapses and
  resizes, and Layouts pages in its own look.
- The old `design/admin` header search scope popup now works in both copies (each has its own element ids) and
  closes on an outside click.

Related: [setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md),
[jQuery 4 and YUI removal](jquery4-and-yui-removal.md), [October 2026 chronicle](../../history/2026/2026-10.md).

See also (October 2026): [6.0.15 changelog](../../changelogs/6.0/6.0.15.md), [upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md), [October 2026 chronicle](../../history/2026/2026-10.md), [admin links follow permissions](admin-links-follow-permissions.md), [order list sorting](order-list-sorting.md).
