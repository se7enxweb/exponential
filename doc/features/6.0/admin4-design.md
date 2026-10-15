# The admin4 design

This page is for administrators who choose the look of the admin, and for developers who style it. admin4 is a
complete administration design in the look of the setup wizard, with a light and a dark mode. It holds every file the
admin siteaccess served from `admin3`, `admin2` (no longer part of the tree) and `admin` (463 files when it was
created, 491 at the time of writing; for each path, the file that won in the search order), so it works with none of them
present: every template a view of the kernel fetches or an admin4 template includes is in `design/admin4` or
`design/standard` (`tests/tests/design/AdminSelfContainedTest.php` fails for one that only `design/admin` or
`design/admin3` has). Added 2026-10-02 and refined during the day.

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

3. Reload the admin. The header shows a mode switch (light, dark, system) after the search box.

4. Optional, for the class, file type and class group icons in Bootstrap Icons as well: in the admin siteaccess's
   `icon.ini.append.php`, set

   ```ini
   [IconSettings]
   Theme=bootstrap-icons
   AdditionalThemeList[]
   AdditionalThemeList[]=crystal-admin
   ```

   and clear the caches again. Without it these icons stay the crystal PNGs; everything else in admin4 draws
   Bootstrap Icons either way (see [Icons](#icons)).

The siteaccess's design list still names `admin3`, `admin2` and `admin`, for the extensions' folders of those names
(the three templates that `bccie` and `enhancedezbinaryfile` override in their own admin2 folder stay with the
extension). The editor siteaccess (`SiteDesign=editor`) falls back to `admin4l` first, then `admin4`, `admin3`,
`admin2`, `admin` (`kernel/setup/steps/ezstep_create_sites.php`).

## What you get

- **Light and dark mode.** Light (default): pale page, frosted header, cards edged by light and shadow. Dark: slate
  page, white cards. The header switch steps light -> dark -> system; "system" follows the operating system's
  setting (`prefers-color-scheme`), also when it changes while the page is open. The switch's icon (sun, moon, half
  circle) and its accessible name say the mode in use ("Color mode: light/dark/system"); its title names the next
  step ("Switch to dark mode"), and a status region announces a press. The choice is the signed-in user's
  eZPreference `admin_color_mode`, so it follows the user from browser to browser (see "Remembered choices" below);
  it is applied before the page is drawn, and without a choice the admin is light, as before.
  `html[data-a4-theme]` stays light or dark for the stylesheets, `html[data-a4-theme-choice]` carries the chosen
  mode. A copy of the switch without `data-label-system` (an extension's own header) keeps stepping between light
  and dark. The mode needs the head script: without JavaScript the admin is light, its documented default (a dark
  fallback for `prefers-color-scheme` would repeat the whole dark token block, and the admin does not work without
  JavaScript anyway).
- **Printing.** A printed page is the content alone on the full width (no header, menus, side columns, control bars,
  toasts or footer, no shadows), and always in the light colours: the head script switches to light before printing
  and back afterwards.
- **One density.** Lists, tables, cards and controls have one spacing, tuned for 1440 to 1920 px: one control
  height (32 px), list cells as `.a4-table` cells (8/12 px), a 24 px gutter between the frame's cards. The compact
  view of October 2026 and its header switch are gone; a stored choice does nothing.
- **Remembered choices.** The color mode, the side columns on a wide window and the closed groups of
  the left menus and the height of the node view's tab box are the signed-in user's eZPreferences
  (`admin_color_mode`, `admin_left_sidebar`, `admin_right_sidebar`, `admin_navgroups`,
  `admin_node_preview_height`; the version preview's device: `admin_preview_device`, `admin_preview_landscape`),
  like the column width. One helper,
  `window.a4Pref` (the first script of `page_head_style.tpl`), reads them from `<html>`
  (`design:parts/a4_preference_attributes.tpl`, `data-a4-pref-*`) and saves them through
  `user/preferences/set_and_exit`. An anonymous page keeps them in the browser (`localStorage`) only; a browser
  choice made before is taken along once. A preference that cannot be saved is kept in the browser instead, and
  `set( name, '' )` forgets a choice (the preference set empty, the browser's copy removed). `tests/tests/design/AdminPreferencesTest.php` checks it.
- **Header and top menu.** One row of 56 px from 1280 px (logo, menu, search, site, light/dark, user menu, logout),
  two rows of 48 + 40 px from 576 px, one row of 56 px on a phone with the hamburger menu (logo, site, light/dark,
  menu button). The menu stays on one line; tabs that do not fit go into a "More" list. On a phone the menu opens
  as a panel under the header that always fits the visible screen (at most `100dvh` less the header, scrolling
  inside): the search first, the tabs as a grid of 44 px tiles (three to a row at 390 px, two at 320 px), the user's
  actions (name, Change information, Change password, My e-mail preferences, Logout) as a row of their own, and a
  Close button at the end; Tab stays in the header while it is open, Escape, Close and a tap outside close it
  (`AdminPhoneMenuTest`). admin4l's tab menu block ends with the same Close button. "Open the
  site" is the first of the tools, a round icon button like the others; logout is an icon button ("Logout
  (admin)"); the user menu holds the user's name and links, and the name leads to the user's own object when the
  user may read it (not on edit and browse pages). The Tab key reaches the tools in the order they are
  drawn. "/" jumps to the search. The page always starts below the header (`AdminHeaderTest`).
- **Logo.** One template draws it, `parts/a4_logo.tpl`, included in the header (`page_topmenu.tpl`, admin4l's
  `admin_logo` block) and on the sign-in page. It is inline SVG: letters and outlines in `currentColor` (the link's
  `--a4-ink`, so dark on light pages and light on dark ones), the orange square in the brand color `#f15a28`, the
  inner square of the back mark in `--a4-surface` (white on light pages as in the artwork; on dark pages the page's
  color instead of a white block). Under forced colors CanvasText and Canvas. 24 px high in the header (158 px wide,
  in the 32 px box of the old image, so the header keeps 56/88 px), 28 px on the sign-in page; below 576 px the
  header shows the mark alone (in a 36 px link, like the tools). The drawings are `aria-hidden`; the links around them carry the name. The same
  drawing as files: `images/admin4/exponential-logo.svg` (word and mark, viewBox cut to the drawing) and
  `images/admin4/exponential-mark.svg` (the mark, square). `AdminLogoTest` keeps the partial and the files equal and
  checks that they hold no script, style or outside reference.
- **Sign-in page.** One calm form, fast on phones: labels above 46-48 px fields, 16 px text so mobile Safari does not
  zoom, autofill hints, Enter moves from the username to the password and signs in, a show/hide button, one full-width
  button that cannot be sent twice, and forgot-password and register links when those modules exist. The kernel reads
  the same fields as before.
- **Dashboard.** A welcome with quick actions, key figures, the last 14 days of publishing and the system at a glance,
  around every block of `dashboard.ini`. It points to the two updates that keep an installation secure: the CMS with
  the Git manager, and the Composer libraries on the Updates dashboard, linked only where the user may open them.
- **Page header and breadcrumb.** Every page title has one size (22px; 16px on windows up to 600px): the kernel's
  title bar, the node view and the dashboard. The breadcrumb above it is a list in a navigation landmark: separators
  drawn by the stylesheet, the current page marked for screen readers, long names ending in an ellipsis with the full
  name as tooltip (no longer cut to 18 characters). On windows up to 600px (and at 400% zoom) the middle steps fold
  into "..." for the eye only: screen readers still read the whole path, and a step that takes the keyboard focus
  shows again.
  Each key figure has an icon tile; a figure that leads somewhere shows an arrow in its corner. The week's figure
  says how its full days compare with the 6 days before them, without today (a number, an arrow and words, never a colour for "good" or "bad"),
  counted from the same 14 days as the chart, so it needs no query of its own. The chart's numbers fold out under
  it as a table ("Show the numbers"). States in tables use the existing `.a4-badge.is-ok|is-warn|is-bad|is-info`
  (the state's tint and text colour); there is no second "pill" component.
- **Node view.** One header row with the class icon, title, badges (class, hidden, secondary location) and the actions
  (language, Edit, Move, Remove, View on site, Preview, Manage versions). One meta line with modified and by whom,
  published, version, section, node, object and remote ids (click to copy) and the translations (click to switch).
  The tabs form a segmented row. Tabs and sub-items start about 300 px below the card's top instead of 500. On phones
  the actions wrap onto two short lines inside the card.
- **Version preview.** The preview of a version (content/versionview: "Preview" from the edit form, the node view and
  the version history) shows the site's own rendering for the chosen siteaccess in the width of a device: a segmented
  pill iPhone (390 × 844 px) | iPad (820 × 1180) | Desktop (1280 × 800) | Large Desktop (1920 × 1080), and a
  landscape switch for phone and tablet. The page is drawn at the device's size and scaled down when the column is
  narrower; the line beside the switches says the size and the scale ("390 × 844 px, shown at 100 %"). The choice
  is the signed-in user's, as the eZPreferences `admin_preview_device` and `admin_preview_landscape` (through
  `window.a4Pref`, so it follows the user to another browser; the browser on an anonymous page); without a
  stored choice it is Desktop. In a narrow column the switches show their icons only. Without JavaScript the
  switches stay hidden and the preview fills the column as before. The arrow keys move between the devices.
- **Height of the node view's tab box.** The box of the tabs (View, Details, ...) keeps the height the user gives
  it, one height for every node: the grip at its bottom right edge is dragged with the mouse, a finger or a pen, or
  moved with the keyboard (it is a focusable separator: Arrow Up and Down 16 px, Shift+Arrow, Page Up and Page Down
  64 px, Home and End the smallest and largest height, the steps of the left column's and the debug panel's grips). The height is held between 10em and 60em (half and three
  times the default 20em) and at most 85% of the window, so a height saved on a large screen fits a small one; a
  phone (below 576 px) always shows the default, and the box with the tabs switched off keeps its own size. A button
  beside the grip, "Default size", shows only while the height is not the default; it, and a double-click on the
  grip as on the other grips, go back to the default and forget the height instead of storing the default, so a later change of the default reaches the user. The height
  is the eZPreference `admin_node_preview_height` (pixels), saved once at the end of a drag or 400 ms after the last
  key and only when it changed; it is written on `<html>` outside every cache-block (the node view itself is view
  cached and carries no value of the user's) and applied before the page is drawn. An anonymous page, and a
  preference that cannot be saved (a network error, an error status, the login page of an expired session), keep it
  in the browser; the preference wins over the browser's copy. Without JavaScript the box keeps the browser's own
  resize grip. The top node's view has the same control. `tests/tests/design/AdminNodeTabsHeightTest.php` checks it.
- **Environment badge.** Off by default. With `site.ini [AdminEnvironment] Level` set, the header shows the
  environment's name beside the logo, in the level's color: development green, test amber, staging violet,
  production red, each with a darker or lighter edge for the dark mode (`--a4-env-<level>`, `-ink`, `-line` in
  `admin4.css`; text 4.5:1 on the fill, the edge 3:1 on the header). The page title starts with the name in capitals,
  "[PRODUCTION] Dashboard - Site", so windows and tabs can be told apart; the sign-in page shows the badge and the
  prefix too. The badge is text, not only color: from 768 px the name (and, for signed-in users, `Release` and with
  `ShowVersion=enabled` the Exponential version), up to 767 px, phones included, the short code (`ShortName`, default DEV,
  TEST, STAGE, PROD; the full logo gives way to the mark alone from 576 to 767 px too), the name always read out by
  screen readers; the title attribute holds the whole text. It is 24 px high in the 36 px row of the header tools,
  so the header keeps 56, 88 and 56 px; up to 1279 px the search field gives it its room,
  from 1280 px the main menu does (its last tabs go into "More"). In forced colors it is drawn in the system colors
  with its text. The same in admin4l (the logo block). See [Settings](#settings).
  `tests/tests/design/AdminEnvironmentBadgeTest.php` checks it.
- **Site menu.** On by default. The globe in the header, which opens the site, is a button for signed-in users:
  its list opens the site first, highlighted, then the installation's other sites the signed-in user may log in to
  (`user/login` with its SiteAccess limitation; the administrations as a group of their own only with
  `ListAdministrations=enabled`), each with its name (`SiteName`, else the identifier), identifier, locale and address; the
  siteaccess in use is marked (`aria-current`). The addresses are worked out as the globe's own
  (`ezpSiteAccessURL::root()`: MatchOrder, URIMatchType, HostMatchMapItems, SiteURL, the scheme, host and port of
  the request) and open in the same window. A disclosure as the user menu: ArrowDown opens it and goes to the first
  link, the arrow keys, Home and End move, Escape closes it and gives the focus back, a click outside closes it. One
  button in the place of the link, so the header takes no more room; on a phone the list opens under the header.
  Without JavaScript, with `Show=disabled`, or with no siteaccess besides the site, the globe stays a plain link.
  The same in admin4l (the tools block). See [Settings](#settings). `tests/tests/design/AdminSiteAccessMenuTest.php`
  checks it.
- **Edit form.** The button bars (Send for publishing, Store draft, Store draft and exit, Discard draft) stay in view
  while the form scrolls (CSS `position: sticky`, replacing the YUI script that stopped working). They are slim
  (42 px on a desktop) and fit a phone screen. Edit pages show one left column, not two. Required fields carry a red
  `*` (a hint above the attributes says what it means; screen readers hear "required", and the controls are
  `aria-required`). After a failed store a summary at the top takes the focus and links to each field; every invalid
  field shows its message under its label, carries a red edge, and its controls are `aria-invalid` with the message as
  description. Each attribute is a named group (`role="group"`, id `ezcca-attr-<attribute id>`). The datatype
  templates are unchanged: the marks are set on their data controls by the page's script (not on buttons, file inputs
  or remove checkboxes); a datatype template opts a control out with the class `a4-no-field-mark`. Multiedit, user
  registration and the template look editor include the same attribute template and behave the same. The fields,
  buttons and checkboxes of the datatype templates look like admin4's components (`.a4-input`, `.a4-btn`,
  `.a4-check`), the attribute names carry no trailing colon, and a grouped input has no grey box of its own
  ([styling rules, "Edit forms"](../../bc/6.0/admin4-styling.md#edit-forms)).
- **Frame.** Side column margins are halved (a 16 px gap). The sidebar buttons sit on the window edges at 48% of the
  window height; they are named after their column ("Left column", "Right column"), say with `aria-expanded` whether
  it shows, and on a narrow window Escape closes a column open over the page and returns the focus to its tab. Long names in the side columns wrap instead of being cut off.
  The User preferences block of the right column (Locations, Re-edit) shows "on" and "off" as tabs, like the node
  view's tabs: the current state in the accent tint with a bar under it (the pressed button of a POST form, `aria-pressed`), the other one quiet
  ([styling rules, "Legacy controls and the side columns"](../../bc/6.0/admin4-styling.md#legacy-controls-and-the-side-columns)).
- **Left menu.** The left column is the "Side menu" navigation. A long menu shows in groups: Setup in Content model,
  Site and URLs, Design (while its tab is left out), Workflows, Users and messages, Audit (likewise), System, AI, Updates, and "More" last for links in no group. Each group has a small
  capital title with a caret and opens and closes (the eZPreference `admin_navgroups` keeps the choice; the group of the current page always
  opens). The current page's link carries `aria-current="page"` and is marked with the accent tint and a bar. A menu
  is grouped through menu.ini (`Groups[]`, `GroupNames[<group>]`, `GroupLinks_<group>[]`); without `Groups[]`, or with
  `Groups[]` reset to no values, it is one list, named by its heading. Access is checked per link as before.
  Under every left menu, the width of the column (narrow, medium, wide) is one pill of three round icon buttons
  (`.a4-tabs.is-segmented.is-icons`); the current width is tinted in the accent and pressed (`aria-pressed="true"`).
- **Setup overview.** The Setup tab opens an overview of every Setup function as a tile, in the groups of the left
  menu (see [Setup overview](#setup-overview)).
- **Tables.** A list table's sorted column is readable (soft orange header).
- **Icons.** Bootstrap Icons in the text colour, so they follow links, buttons, hover and the dark mode (see below).
- **Languages.** A short code instead of a flag (see [Languages](#languages)).

## Icons

admin4 draws its icons with [Bootstrap Icons](https://icons.getbootstrap.com/) 1.13.1 (MIT license). The SVG files
are in `share/icons/bootstrap-icons/svg/`, unchanged, with the license beside them; nothing is loaded from a CDN.

- **In templates:** `<span class="a4-icon a4-bi-pencil" aria-hidden="true"></span>`, the component `.a4-icon`
  of `components.css`. The icon is a mask in `currentColor`, 1em square, or 12, 16 or 22 px with `.is-small`,
  `.is-medium`, `.is-large`. A control that is only an icon is a `<button>` or link with `.a4-icon-btn` and its name
  in `title` and `aria-label`; an icon that stands for a word inside a link carries `role="img"` and `aria-label`.
  The GIF icons of the old templates (`edit.gif`, `toggle-button-16x16.gif`, `up-16x16-grey.png`, `move.gif`,
  `button-move_up.gif` ...) and the inline SVGs are gone from admin4's templates. The image buttons of class editing
  (move an attribute to the top, up, down, bottom) and of the Locations window (move) are `<button>`s that post the
  same variable, `MoveUp` = the attribute id where the image button posted `MoveUp_<id>_x` and `_y` that
  `eZHTTPTool` turned into the same.
- **In admin4's stylesheets:** the messages, the sorted columns, the tabs switch, the sub-items table (its row menu,
  image preview, the buttons above it and their menus), the search box, the context menus and the content tree draw
  Bootstrap icons as masks on a pseudo-element, in place of the GIF and PNG sprites. The tree's open and close are
  a caret right and a caret down on the column's own background, so the connector line does not cross them.
- **Class, file type, class group and other icons** (`class_icon`, `mimetype_icon`, `classgroup_icon`, `icon`,
  `icon_info` of the content tree): the icon theme `bootstrap-icons`, chosen in the admin siteaccess's `icon.ini`
  (step 4 above). Its `icon.ini` maps every class and MIME type of `crystal-admin`, the classes of the website
  interface and demo packages and the OOXML, EPUB, RTF, CSV, XML, JSON and SVG types; one SVG serves every size. For
  a class it does not map, `class_icon` and the other operators take the `crystal-admin` PNG of
  `AdditionalThemeList[]`, but the content tree (`icon_info`) reads only the first theme and shows the theme's
  `Default`: add a class of your own to `ClassMap` in an `icon.ini.append.php`. In dark mode a filter in `admin4.css` turns these
  `<img>` icons light, since an SVG in an `<img>` cannot take the page's colours.
- The classic designs (`admin`, `admin3`) keep their images; `.a4-icon` and `.a4-icon-btn` are in their
  copy of `components.css` as well, for templates shared by every administration design.

To add an icon: copy the SVG from the Bootstrap Icons 1.13.1 package unchanged into
`share/icons/bootstrap-icons/svg/`, add `.a4-bi-<name>` to both copies of `components.css` (or name the file in the
theme's `icon.ini`), and use it. `tests/tests/design/AdminIconsTest.php` fails for a file nothing names, a name with
no file, a changed file, and a GIF icon, inline SVG or image button in an admin4 template.

## Setup overview

The Setup tab opens `setup/menu`: a page header and every function of the Setup menu as a tile, in the groups of the
left menu (Content model, Site and URLs, Design, Workflows, Users and messages, Audit, System, AI, Updates; Design and Audit only while their tabs are left out; and "More" last for links
in no group).

- **One source.** The tiles are the left menu, drawn another way: `setup/setupmenu.tpl` includes
  `parts/setup/menu.tpl`, which hands `[Leftmenu_setup]` of `menu.ini` to `parts/ini_menu.tpl` with
  `menu_layout='tiles'`. The links, their names, their groups and the access checks (`PolicyList_<link>[]`,
  `CheckViewAccess`, `NoAccessLinks`) are the menu's: a function the user may not open has no tile, and an
  extension's link appears by adding it to `[Leftmenu_setup] Links[]`, as for the menu: in the group that names it in
  `GroupLinks_<group>[]`, else under "More". The order inside a group is that of `Links[]`.
- **Groups for extensions.** "AI" and "Updates" hold the Setup links of the se7enx extensions, already named in core
  `menu.ini`, so they do not land under "More" where an unsorted entry looks out of place: `expai` (`expai_summary`,
  `expai_models`, `expai_budgets`) under AI; `ezupdate` (`ezupdate`) and `git_manager` (`git_manager`,
  `git_manager_backup`) under Updates. The job queue (`expqueue`, also shipped with `expai`) and the database editor of
  `sevenx_dse` (`dse`) are System tools and sit under System. A key whose extension is not active has no link and shows
  nothing; a group with no link the user may open is not drawn, so without these extensions the page looks as before.
  "More" stays the last group, for links that name no group yet.
- **A tile** is a card (`.a4-card`) with a Bootstrap icon in an accent square, the
  function's name as its link, stretched over the card with one focus ring around it, and one line on what the
  function does as the link's description. Icon, description and figure come from the menu's own section in
  `menu.ini`, per link key (see "Extending it" below); a link without them takes the plug icon and no description.
  With `NoAccessLinks=disabled` a function the user may not open is a tile without link and without figure, as the
  menu shows its name without link.
- **Figures**, only where one cheap fetch gives them and only on a tile the user may open
  (`setup/setupmenu_figure.tpl`, named by `LinkFigureTemplates[]`): classes, sections, content languages, active
  extensions that exist (`expinfo( 'active' )`), signed-in users, paused cronjob parts (only while one is paused,
  `fetch( 'setup', 'cronjob_pauses' )`); one and many are two strings.
- **Grid.** One column on a phone, then two, three and four as the content column grows (container queries on
  `.a4-setup-overview`, so a wide left column counts). Styles in `stylesheets/modules/setup.css`, tokens only, light and
  dark, forced colors.
- **Left menu.** "Overview" is the first entry of the Setup menu, before the groups: menu.ini `LeadLinks[]` names
  links that stand before the groups and in none of them (`.a4-navlead`). It is marked as the current page on the
  overview, which leaves it out of its tiles. `TrailLinks[]` is its counterpart at the end: the links it names are
  drawn after all other links, in its order, also after the links extensions append; in a grouped menu after the
  groups and "More", in none of them (`.a4-navtrail`), in a plain list as its last items, in the tiles as the last
  tiles. The Dashboard menu ends with Change password and Edit profile this way.
- **Access.** The view `setup/menu` opens with any one of the setup module's functions (`setup`, `administrate`,
  `managecache`, `managecronjobs`, `preload`, `system_info`), so a role that reached the tab's former page
  (`setup/cache`) keeps the tab.
- **Classic designs** show a short page with a pointer to the Setup menu and the `setup` toolbar of toolbar.ini.

| Link key | Icon | Link key | Icon |
|---|---|---|---|
| classes | `diagram-3` | cache | `arrow-clockwise` |
| sections | `collection` | system_information | `info-circle` |
| states | `toggles` | upgrade_check | `arrow-up-circle` |
| languages | `translate` | maintenance | `wrench-adjustable` |
| contentfinder | `search` | cronjobs | `clock` |
| collected | `inbox` | extensions | `puzzle` |
| url_management | `link-45deg` | ini | `sliders` |
| url_translator | `signpost-split` | sitedata | `table` |
| url_wildcards | `asterisk` | sessions | `people` |
| menu_management | `menu-button-wide` | notification | `bell` |
| rss | `rss` | mailpreferences | `envelope-paper` |
| pdf_export | `file-earmark-pdf` | oauth_list | `key` |
| packages | `box-seam` | audit | `journal-check` |
| rad | `magic` | workflows | `diagram-2` |
| preload | `speedometer2` | workflow_processes | `hourglass-split` |
| search_statistics | `bar-chart-line` | triggers | `lightning` |
| roles_and_policies | `person-lock` | templates | `file-earmark-code` |
| template_editor | `code-square` | toolbar_management | `tools` |
| look_and_feel | `palette` | global_setting | `globe` |
| expai_summary | `stars` | ezupdate | `cloud-arrow-down` |
| expai_models | `cpu` | git_manager | `git` |
| expai_budgets | `piggy-bank` | git_manager_backup | `archive` |
| expqueue | `list-task` | dse | `database` |
| unreferenced_objects | `file-earmark-break` | broken_relations | `exclamation-diamond` |
| (an extension's link) | `plug` | | |

### Extending it

Any left menu drawn as tiles (`parts/ini_menu.tpl` with `menu_layout='tiles'`) reads three optional settings of its
section, per link key; the core Setup links carry theirs in `settings/menu.ini [Leftmenu_setup]`:

| Key | Value | Without it |
|---|---|---|
| `LinkIcons[<key>]` | an icon name of `design.ini [AdminDesignSettings] IconList[]`, e.g. `bar-chart` | the plug icon; a name not in the list draws the plug icon too |
| `LinkDescriptions[<key>]` | one line in English, translated in the menu's context (`design/admin/parts/setup/menu` for Setup), like `LinkNames[]` | no description |
| `LinkFigureTemplates[<key>]` | `design:<path>.tpl`, included with `figure_key=<key>` on a tile the user may open | no figure; a value that does not start with `design:` is ignored |

An extension that adds a Setup function gives it a tile in its own `settings/menu.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[Leftmenu_setup]
Links[myreports]=myreports/list
LinkNames[myreports]=Reports
PolicyList_myreports[]=myreports/read
LinkIcons[myreports]=bar-chart
LinkDescriptions[myreports]=Monthly reports of the shop.
LinkFigureTemplates[myreports]=design:myreports/setup_figure.tpl

*/ ?>
```

The link shows under "More" in the menu and on the overview until it names a group. An extension sorts its link into
a group of the menu with one more line in the same section, so that "More" is only the catch-all for links without a
category:

```ini
# one of: ai (assistants, models, their costs), updates (packages, updates, backups) or any other group of Groups[]
GroupLinks_ai[]=myreports
```

A group lists the keys of several extensions; each one adds its own line, and a key whose extension is missing shows
nothing. A link named in two groups shows in the first only. A new group goes into `Groups[]` with a name in
`GroupNames[<group>]` (translated in the menu's context); it is drawn after the groups before it, and only when it
holds a link the user may open.
The figure template draws one short `<span class="a4-setup-tile-figure">`, asks only what its tile's function may
show, and stays cheap: it runs on every view of the overview. The description is translated when the extension's
translation file has the string in the menu's context.

**Icons.** A tile draws only icons listed in `IconList[]`, each a mask rule `.a4-bi-<name>` over a Bootstrap Icons
SVG; no inline style, no image, and a setting cannot put markup or an unknown class on the page. Every Bootstrap icon
admin4 ships is listed (see [Icons](#icons)). An extension that needs another one ships it itself:

1. the SVG, unchanged from Bootstrap Icons 1.13.1 (MIT), in the extension, e.g.
   `extension/myreports/design/admin4/images/icons/bar-chart.svg`;
2. a mask rule in a stylesheet of the extension registered in `design.ini [StylesheetSettings] BackendCSSFileList[]`,
   with the path relative to that stylesheet:
   `.a4-bi-bar-chart { -webkit-mask-image: url(../images/icons/bar-chart.svg); mask-image: url(../images/icons/bar-chart.svg); }`;
3. `IconList[]=bar-chart` under `[AdminDesignSettings]` in the extension's `design.ini.append.php`.

`tests/tests/design/AdminSetupOverviewTest.php` fails for a Setup link without icon or description, an icon that is
not listed, has no rule or no file, an icon used twice, a missing access check, a figure outside the link branch or
from another fetch, an image in the page, or a missing translation; it draws the tiles of a fixture menu with an
extension's link (icon, description, figure) and one with an unknown icon.

### Moving a tab into Setup

A site that wants fewer top tabs leaves Design, Audit or the Dashboard out of the top menu and loses no page:

```ini
<?php /* #?ini charset="utf-8"?

[TopAdminMenu]
HiddenTabs[]
HiddenTabs[]=dashboard
HiddenTabs[]=design
HiddenTabs[]=audit

*/ ?>
```

in `settings/siteaccess/<admin siteaccess>/menu.ini.append.php` or `settings/override/menu.ini.append.php`, then clear
the INI and template caches (`php bin/php/ezcache.php --clear-tag=ini` and `--clear-tag=template`): `ezini()` values
are compiled into the templates. Restating `Tabs[]` (a line `Tabs[]` without value, then the tabs to keep) works as
well for the core tabs; an extension's tab is left out only with `HiddenTabs[]`. Every tab stays by default.

- **Per role:** `[TopAdminMenu] SetupTabs[]` names tabs that leave the top menu only for a user who may open the
  Setup overview (`setup/menu`, checked as the request is). That user finds the tab's group under Setup; a user who
  may not open the overview, such as a role that holds only the tab's own policy, keeps the tab with its own left
  menu, and its pages keep marking it. The top menu operator, the Setup menu, `parts/a4_navigation_part.tpl` and the
  logo ask the same question. The header is cached per set of roles (`$user_hash`), which decides the answer; after
  a change of a role's policies clear the template cache.
- **Design and Audit** have a group of the Setup menu and the overview, named by `[Leftmenu_setup] GroupTabs[]`
  (`GroupTabs[design]=design`, `GroupTabs[audit]=audit`). Such a group is drawn only while its tab is not in the top
  menu (left out of `Tabs[]`, named in `HiddenTabs[]`, or in `SetupTabs[]` for a user who may open `setup/menu`); while the tab is there, the group and the links it names are
  not drawn at all, so nothing shows twice.
- **One source.** `GroupSections[design]=Leftmenu_design` and `GroupSections[audit]=Leftmenu_audit` add the links of
  the tab's own left menu to the group, after the group's own `GroupLinks_<group>[]`: the same links, names
  (`LinkNames[]` in that menu's context, else `parts/setup/menu.tpl` names them `<group>-<key>`), access checks
  (`PolicyList[]`, `PolicyList_<key>[]`, `CheckViewAccess`, the audit channels the user may not read) and tile
  settings (`LinkIcons[]`, `LinkDescriptions[]`, `LinkFigureTemplates[]` of that section, the descriptions translated
  in its context, e.g. `design/admin/parts/audit/menu`). A link an extension adds to `[Leftmenu_audit]` is in the
  group too; give it `LinkNames[<key>]`, or it shows its key as its name there. The Design group starts with the Design tab's own page, `Links[design_node]=content/view/full/58` in
  `[Leftmenu_setup]` with the tab's `PolicyList[]=58`.
- **A link once.** A Setup link to an address the added menu has as well shows in that group only: with the Audit
  group, "Settings" (`audit/settings`) is there and the "Audit" link of "Users and messages" is not. A link the
  added menu leaves out for the user (an audit channel the user may not read) keeps the Setup link to its address
  out too.
- **Navigation.** A page of such a tab (its `NavigationPartIdentifier`, e.g. `audit/console` or `visual/toolbarlist`)
  marks the Setup tab and draws the Setup menu, with its group open at the current link, also when its view names a
  left menu of its own (`$module_result.left_menu`, as the audit views do). No page marks a tab that is
  not in the top menu or draws a menu no tab leads to; the breadcrumb is the page's own. While the tab is in the top
  menu, its pages keep their tab and left menu. `parts/a4_navigation_part.tpl` gives the navigation part both menus
  use, in admin4 and in admin4l's header and left menu blocks.
- **Dashboard.** Without the Dashboard tab the logo leads to the dashboard (`[Topmenu_dashboard] URL[default]`) for a
  user who may open it (`content/dashboard`), instead of the content root, which the Content tab still opens. The
  dashboard is also the admin's start page after login when `site.ini [SiteSettings] DefaultPage=content/dashboard`,
  as the setup wizard writes it; its left menu holds the account's pages (password, drafts, bookmarks, notifications).
  The dashboard has no Setup group: it belongs to every user, Setup to administrators.

An extension's tab moves the same way, in the extension's or the site's `menu.ini.append.php`: a group in
`[Leftmenu_setup] Groups[]` with `GroupNames[<group>]`, `GroupTabs[<group>]=<tab>`, `GroupSections[<group>]=<its
Leftmenu_ section>` (or `GroupLinks_<group>[]` with links of its own), the tile settings in its section, and the tab
in `HiddenTabs[]`. `GroupTabs[]` and `GroupSections[]` are read by admin4's `parts/ini_menu.tpl` only; the classic
designs keep their tabs.

| Link key | Icon | Link key | Icon |
|---|---|---|---|
| design_node | `brush` | audit-dashboard | `speedometer` |
| design-look_and_feel | `palette` | audit-console | `terminal` |
| design-toolbar_management | `tools` | audit-recent | `list-ul` |
| design-template_editor | `code-square` | audit-charts | `graph-up-arrow` |
| design-templates | `file-earmark-code` | audit-alerts | `exclamation-triangle` |
| | | audit-export | `download` |
| | | audit-archives | `archive` |
| | | audit-settings | `journal-check` |

`AdminSetupOverviewTest` fails when a moved tab's group, section, page, icon, description or translation is missing,
when a tab is hidden by default, and draws `parts/a4_navigation_part.tpl` with the tabs shown and hidden.

### Mega menu (prototype, opt-in)

A small button beside a top tab opens a panel under the header with the most used functions of the tab in their
groups: each with its icon, its name and its description under it, up to three groups side by side, and "Go to
<tab>" (the tab's own page, for Setup the overview) at the end for the rest. Setup and Users have one. Off by
default; to switch them on, in a siteaccess or override `menu.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TopAdminMenu]
MegaMenuTabs[]=setup
MegaMenuTabs[]=users
*/ ?>
```

Then clear the caches (the INI cache and the template caches: the header reads the setting when its template is
compiled). With `MegaMenuTabs[]` empty the header draws no button and the page loads no script. One panel is open
at a time: opening one, by a click, a key or the hover preview, closes the other. A panel opened by a click or a key is not
replaced by the hover preview of another tab.

#### Which tabs take part, and how to add one

A tab takes part when it is named in `MegaMenuTabs[]` **and** its `[Topmenu_<tab>]` names a `MegaMenuTemplate`; a
name without one is ignored. The template draws the tab's menu as the panel's groups, always through
`parts/ini_menu.tpl` with `menu_layout='tiles'` and `tiles_layout='mega'`, so that the links keep the names, groups,
icons, descriptions and access checks of a menu.ini section:

| Tab | `MegaMenuTemplate` | Section | Default choice (`MegaMenuLinks[]`) |
|---|---|---|---|
| `setup` | `design:parts/setup/mega_menu.tpl` (the Setup menu's own `parts/setup/menu.tpl`) | `Leftmenu_setup` | twelve, see below |
| `users` | `design:parts/user/mega_menu.tpl` | `Leftmenu_user_mega` | eight, see below |

To give another tab one (an extension's, say): write a template in admin4 that includes `parts/ini_menu.tpl` with
the tab's section, `menu_layout='tiles'`, `tiles_layout='mega'` and the `i18n_hash` of its link names; name it in
`[Topmenu_<tab>] MegaMenuTemplate=design:...` (a `design:` template, nothing else is accepted) and its section in
`MegaMenuSection=<section>` (read only by the check of the settings, below); give the section's
links `LinkIcons[]` (names of `design.ini [AdminDesignSettings] IconList[]`) and `LinkDescriptions[]`, and a
`MegaMenuLinks[]`; then add the tab to `MegaMenuTabs[]`. A tab whose left menu is no menu.ini list (a tree, a
template of its own) gets a section that only gathers its functions, as the Users tab does.

#### The Users tab

The Users tab's left menu is the user tree and `[Leftmenu_user]` (Roles and policies, Unactivated users), drawn by
`parts/user/menu.tpl`; a tree is no list of links, and the left menu stays as it is. Its mega menu therefore has a
section of its own, `[Leftmenu_user_mega]`, which defines no link a menu already defines: `GroupSections[]` adds
`[Leftmenu_user]` to its group "Access control" and `[Leftmenu_my]` (the user's own settings, the Dashboard tab's
menu) to "My account", with their addresses, policies, icons and descriptions (`LinkIcons[]`/`LinkDescriptions[]`
are set in those sections; they are read only where a menu is drawn as tiles or as a mega menu). Its own links are
the two the tab has no menu entry for: the user tree (`content/view/full/5`, the tab's own page and policy, as
`[Topmenu_users]` has it; a site with another `content.ini [NodeSettings] UserRootNode` changes both) and the
sessions (`setup/session`, the Setup menu's policy). It is named `Leftmenu_user_*` so that it shares the translation
context of the tab's menu, `design/admin/parts/user/menu`. The default choice, by group:

| Group | Links (`MegaMenuLinks[]`) |
|---|---|
| Users | `accounts` (Users and user groups), `sessions` (Sessions) |
| Access control | `access-roles_and_policies` (Roles and policies), `access-unactivated` (Unactivated users) |
| My account | `account-edit_profile`, `account-change_password`, `account-my_notifications`, `account-my_mail` |

There is no link to create a user: content is created by a POST from the tree (`content/action`), and no address
opens the user form directly. A permission check for one user has no view in the kernel either.

- **A short choice, not the whole menu.** The section's `MegaMenuLinks[]` (for Setup `[Leftmenu_setup]`) names the links shown, by link key,
  in their order; at most 12 are shown (6 to 12 read best). It is a choice from the menu's links, not a second
  menu: each link keeps its name, icon, description, group (`GroupNames[]`) and access checks. A link the user may
  not open, or whose extension is not active, is left out, and so is a group with no link left; the groups follow
  the order of their first chosen link. The default in `settings/menu.ini` is twelve, here by group: Classes, Sections
  (Content model) | Link management (Site and URLs) | Audit (Users and messages) | Cache management, Cronjobs,
  Extensions, System information, Ini settings (System) | `expai_summary` (AI) | `ezupdate`, `git_manager` (Updates). Without `expai`, `ezupdate` and `git_manager` the AI and Updates
  groups are not there, here as on the overview, and nine links remain. To choose others, restate the list:

  ```ini
  [Leftmenu_setup]
  MegaMenuLinks[]
  MegaMenuLinks[]=classes
  MegaMenuLinks[]=languages
  MegaMenuLinks[]=cache
  ```

  A tab moved into Setup ([Moving a tab into Setup](#moving-a-tab-into-setup)) brings its own menu into its group,
  and its links are chosen as `<group>-<key>`, e.g. `MegaMenuLinks[]=audit-console` (the names the i18n hash of
  `parts/setup/menu.tpl` uses too). A Setup link that is then not drawn because the group has a link to the same
  address takes that link: with the Audit tab moved, `audit` (`audit/settings`) shows as the Audit group's Settings,
  so the default works with and without moved tabs. A key whose link is drawn is always taken by its key first.

  A list reset to no values shows every link of the menu, as the overview does (not recommended: about 35 links).
  The default is in `settings/menu.ini` rather than in the template so that it can be read and changed where the
  links are defined.
- **The tab stays a link.** The button beside it is a disclosure (the WAI-ARIA disclosure navigation pattern, not a
  menu): `aria-expanded`, `aria-controls`, named "<tab> menu" (a screen reader adds "collapsed" or "expanded"
  from `aria-expanded`, so the name does not say "open"). Click, Enter or Space opens and closes the
  panel; ArrowDown opens it and goes to its first link; Tab goes through its links in order and on past the panel,
  which closes it, as does a click outside; Escape closes it and gives the focus back to the button. Each group is a
  section with a heading (`h2`, drawn small); the links are plain links, the description is the link's description
  (`aria-describedby`); the link to the page shown (the tab's link too) is marked `aria-current="page"`.
- **Hover is a preview only.** A mouse resting on the tab or the button for 300 ms opens the panel, leaving the tab,
  the button and the panel for 400 ms closes it (the gap between the header and the panel belongs to the panel, so
  the way across it does not count as leaving). A click on the button, or the focus moving into the panel, keeps
  the preview open. Only a pointer of type
  mouse on a device that reports `(hover: hover) and (pointer: fine)`; never on touch, and not while the header's
  "More" list or user menu is open.
- **From 576px**, wherever the main menu is one line: the one-row header from 1280px and the two-row header from 576
  to 1279px, which is also what a browser at 200% zoom shows on a 1920px screen (960px). The panel is placed under
  the header's measured height (`--header-height`) and its groups wrap to two or one column on a narrower window,
  scrolling inside the panel when they are taller than the window. Below 576px, in the phone menu, the button and the
  panel are not drawn and the tab is a plain link. A tab moved into the "More" list loses its button there.
- **Unknown settings are named in the debug report.** `parts/a4_mega_menu_check.tpl` (included with the script, so
  only while `MegaMenuTabs[]` names a tab) writes a debug message for a `MegaMenuTabs[]` value without
  `MegaMenuTemplate` and `NavigationPartIdentifier`, for a key of the section's `MegaMenuLinks[]`
  (`[Topmenu_<tab>] MegaMenuSection`) that is no link of the section and no `<group>-<key>` of a section its
  `GroupSections[]` adds (an unknown key, or the link of an extension that is not active on this siteaccess), and
  for more than 12 keys. The header and the panel simply leave out what they cannot use. The message is written when
  the page's cache block is built, so clear the template-block cache to see it again.
- **One source, loaded when needed.** The links are those of the tab's menu: `user/megamenu/<tab>`, one view for
  every tab, without page layout (`ui_context` ajax, `Cache-Control: private, no-store`), draws the tab's
  `MegaMenuTemplate` (`user/megamenu.tpl`), which goes through `parts/ini_menu.tpl` with `tiles_layout='mega'`
  (`parts/a4_mega_menu.tpl`): the same names, groups, icons (`LinkIcons[]`), descriptions (`LinkDescriptions[]`)
  and access checks (`PolicyList_<link>[]`, `CheckViewAccess`, `NoAccessLinks`), without figures. `a4_mega_menu.js`
  asks for it once, when the panel is first opened; the page itself carries only the button, a status line and the
  tab's link (about 1 KB), cached with the header per role set (`$user_hash`), as the tabs are. A load that fails
  says so in the panel and is tried again on the next opening; the tab's link stays.
- **The rights.** The view's policy is `user/login` (any user of the siteaccess, as `user/preferences`), since the
  tabs belong to different modules and a policy of one of them would keep the others' users out. The rights are
  the tab's and the links': the view answers only for a tab named in `MegaMenuTabs[]` with a `MegaMenuTemplate`
  (any other: not available), and only while the tab is in the user's top menu, enabled and open to the user
  (denied otherwise), never for the anonymous user. That verdict is the topmenu operator's, the one the header draws
  the tabs with: `Tabs[]`, `HiddenTabs[]`, `SetupTabs[]`, the tab's `PolicyList[]` and, with `CheckViewAccess`, its
  address; `Shown[]` and `Enabled[]` are read for the `navigation` context, the one of the admin's pages (a tab
  that differs there from the page's context gets a button whose panel is refused). Each link is then checked as in
  the left menu. The header draws a button only on a tab it shows enabled and open. A tab name is lower case
  letters, digits and `_` (as `[Topmenu_<tab>]`); give a tab with a mega menu a `PolicyList[]`, as the shipped
  ones have. The request is a
  view of the user module, so the audit trail records it as `access.view.sensitive` once per page on which a panel
  is opened.
- **Look:** an admin4 card (`--a4-card`, `--a4-line`, `--a4-radius-s`, `--a4-shadow-m`), centred under the header
  and as wide as its groups need: up to three groups of 17rem side by side, so a short choice makes a narrow card;
  descriptions wrap instead of being cut. Light and dark, a CanvasText border and Highlight states under forced colors, no transition
  with reduced motion. Styles in `admin4.css`.
- **admin4 and admin4l alike:** the tabs are drawn by one template, `parts/a4_topmenu_items.tpl`, which decides
  which tabs get a mega menu; admin4's `page_topmenu.tpl` and admin4l's tab menu block
  (`design/admin4l/templates/explayouts/block/admin_tab_menu.tpl`) both include it.
- **Layers:** the panel is `z-index: var(--a4-z-menu)` inside the fixed header (`--a4-z-header`): over the side
  columns' edge tabs, under a dialog's mask and the dialog (`--a4-z-overlay`, `--a4-z-modal`) and under toasts
  (`--a4-z-toast`); the debug report's bar stays above everything.

`tests/tests/design/AdminMegaMenuTest.php` holds the defaults of both tabs, the markup, the source and the view
(which tabs it serves, and only while the user has them: hidden, disabled, not shown, not listed), the widths and
the strings, the Users section (its added menus, existing links, icons, names and descriptions in both languages), renders the panel's template with fake groups (the choice, its order, access, groups left out, the
limit of 12, no choice, a key of a moved tab that falls back to the group's link of the same address), and runs the script in node (`tests/tests/design/js/mega_menu.js`: click, ArrowDown, Escape, Tab, a
click outside, the delays, touch, the focus coming into a preview, the width, the "More" list, one load and a
failed one, two tabs with one panel open at a time), checks that admin4 and admin4l draw the tabs with the one
template, and renders the settings check with unknown tabs and keys and reads the debug messages it writes.

## Languages

A flag stands for a country, not for a language: English is spoken under several flags, and one country has several
languages. admin4 therefore shows a content language as a short code badge and its name, the way most back offices
list languages:

- **The code** is the language part of the locale's language tag (`ger-DE` has `de-DE`, so `DE`; `eng-GB` gives
  `EN`), always: when the installation has two content languages of the same language (`eng-GB` and `eng-US`), both
  show `EN`. The badge names the language, not the region; the two differ by their name (English (United Kingdom),
  English (American)), which is the badge's title and accessible name or is written next to it. A locale without a
  language tag shows its locale code. Every locale follows the rule, also one added later (`esl-AR`, `es-AR`: `ES`);
  `AdminLanguageBadgeTest` checks each locale file shipped in `share/locale/`.
- **Two regions of one language on one object** (an object in `eng-GB` and `eng-US`): decided to keep `EN` on both
  badges. A sighted user tells them apart by the name: written next to the badge in the Translations tab, in the
  language choice of the edit page and the "Edit in" menu, and as the tooltip (`title`) of each badge in the node
  view's meta line and the sub-items table; a screen reader hears the full name with the state ("English (United
  Kingdom) – outdated, older than English (American)"), because the state badge's name is the content language's
  name, not the code. The order is the site's language order, so the two badges keep their places. The region is not
  added back to the code: a row would again mix `DE` with `EN-GB`, which is what #231 removed.
- **The name** (the locale's international name, e.g. German) is written next to the badge wherever there is room:
  the Translations tab, the translation lists of versions, drafts, URL aliases, states and classes, and the language
  choice of the edit page. Where there is no room, in the node view's meta line and the sub-items table's
  Translations column, it is the badge's title and its accessible name; there the badges are the links to view or
  edit a translation, and the language shown carries `aria-current`.
- Where the name and the locale code are both shown already (the language cards of Setup > Languages, the state
  editors' legends), the flag is simply gone.

Every badge comes from `design:parts/language_badge.tpl` (parameter `locale`; optional `badge_name`, `badge_named`
when the name is written next to it, `badge_plain` for the bare code, `badge_class`), styled by `.a4-lang` / `.a4-langs` in both copies of
`components.css`. The code is taken from `eZLocale`'s new attribute `content_language_code`, the locale file's
`[HTTP] ContentLanguage`: unlike `http_locale_code` it is not overridden by `site.ini [RegionalSettings] HTTPLocale`,
which sets one tag for a whole site. The optional parameters carry the prefix `badge_` because an include sees the
variables of the template that includes it: a parameter called `class` would read the content class of
`class/view.tpl` when it is not passed. The classic admin design has an identical copy of the include and of
`ezajaxsubitems_expdatatable.js`, so its sub-items table shows the code as well (the badge style is in both copies of
`components.css`). The flags in `share/icons/flags/` and the `flag_icon` operator stay for its other templates and the
other designs. `tests/tests/design/AdminLanguageBadgeTest.php` fails for a flag in an admin4 template or script, or in
either design's sub-items table.

## Visibility and account status

Whether visitors of the site see an item, and whether a user can log in, shows at a glance as a small status mark
(`.a4-mark` in both copies of `components.css`, 10 px). The shape tells the state as well as the color, so it
reads in gray scale, for color-blind users and in forced colors (where the marks take the system text color):

| Mark | Shape | Color token | Meaning |
|---|---|---|---|
| `is-visible` | filled disc | `--a4-ok` | visible |
| `is-hidden` | ring | `--a4-bad` | hidden (`is_hidden`) |
| `is-hidden-by-parent` | ring with a bar | `--a4-bad` | hidden because an item above it is hidden (`is_invisible` without `is_hidden`) |
| `is-disabled` | crossed ring | `--a4-muted` | user account disabled (Setup > Users, `ezuser_setting.is_enabled`) |
| `is-locked` | filled square | `--a4-warn` | user account locked after more failed logins than `site.ini [UserSettings] MaxNumberOfFailedLogin` (`eZUser::isLocked()`) |

Each color reaches 3:1 against the card, the zebra and hovered rows and its own badge tint, in light and dark mode.
Where the mark stands alone it is `role="img"` with an `aria-label` and a `title` that explains it; next to its own
text it is `aria-hidden`. A visible item's mark is only seen: a screen reader hears the exceptions.

Where it shows:

- **Node view header:** a badge beside the class (Visible, Hidden, or Hidden by a parent, each with its mark and the
  explanation as title); for a user object also "Disabled" or "Locked". The badge comes from
  `design:parts/visibility_status.tpl` (parameters `status_node`, `status_badge`, `status_quiet`).
- **Sub-items table:** the mark before every name (all three states, so a hidden item stands out among green ones),
  and a "Disabled" or "Locked" badge after the name of a user account. The Visibility column, when chosen in the
  table options, shows the mark beside its text. The rows of `ezjscnode::subtree` and `expsubitems::rows` carry
  `is_hidden` and `is_invisible` (`ezjscAjaxContent`) and, for user accounts, `user_status`.
- **Content tree:** only what differs, so the tree stays quiet: a ring or a ring with a bar after the name of a
  hidden node (decorative: the "(Hidden)" text after it, which admin4 used to remove with `display: none`, is now
  kept for screen readers), and a crossed ring after a disabled user account. A lock after failed logins is not shown there: the
  tree is cached by the nodes' `modified_subnode`, which enabling and disabling an account now renews
  (`eZUserOperationCollection::setSettings()` and `activation()`), but a failed login does not.
- **Browse dialog** (list view): the mark before a hidden node.

The account status comes from `expUserAccountStatus` (`kernel/classes/user/`), which reads it for a whole page with at
most two queries (`ezuser_setting`, then `ezuservisit` for the enabled users among them), never one per row; templates
ask `fetch( 'user', 'account_status', hash( 'user_id', ... ) )` (`enabled`, `disabled`, `locked` or false). It is shown
to users with `role/read`, the same who may see the sub-items columns "Account enabled" and "Locked out". Tests:
`AdminVisibilityStatusTest` (shapes, contrast, templates, the scripts through a node harness, the texts in German and
American English) and `expUserAccountStatusTest` (the bundled reads, the order disabled before locked, the access
check).
### The state of a language

For one object a badge also shows the state of its language, by its shape and never by a color per language (green,
amber and red belong to visibility and states):

| State | Shape | Title and accessible name |
|---|---|---|
| main language (the object's initial language) | filled in the ink (`.is-main`) | "German – main language" |
| translation exists | outlined (`.is-present`, the plain badge) | "English – translation exists" |
| outdated translation | outlined with a small dot at the top right (`.is-outdated`) | "Spanish – outdated, older than German" |
| missing (a language of the site the object lacks) | dashed edge, muted text (`.is-missing`) | "French – missing", or a link "Translate into French" |

Where: the node view's meta line shows every language of the site (`prioritized_languages`) plus any other language
the object has; a present one links to the view in it, a missing one is a link to `content/edit/<object>/f/<locale>`
(the GET way the Edit in menu takes, which checks the right again) when the user may create that translation
(`can_create_languages`), plain text otherwise. The Translations tab shows the state on each translation and lists
the missing ones under the table. The sub-items table's Translations column shows main and present and, on a site
with up to six languages (`expContentLanguageStates::LIST_MISSING_LIMIT`), the missing ones as text: with more, the
badges of a row would wrap and bury the ones it has, and the Missing translations column of the table options lists
them anyway. Whether the user may create a translation is not worked out per row, so the list has no translate link.

Outdated: the newest published or archived version edited in a language (`ezcontentobject_version.initial_language_id`,
as content/edit records it) is older than the newest edited in the main language. Both must exist: an object whose
languages were all written in one version (an import, a copy) or whose older versions the history limit removed is
never marked. `expContentLanguageStates::lastEdited()` reads any number of objects with one grouped query (SQL; one
`find` on MongoDB); the mark is shown for the one object of the node view and its Translations tab only, the list
rows get main, present and missing from the row they already have (`initial_language` in the node JSON), no query
per row.

The fetch function `fetch( 'content', 'language_states', hash( 'object', $object ) )` returns a list of `locale`,
`name`, `state`, `outdated` and `can_translate`; `design:parts/language_state_badge.tpl` draws one (`state_entry`,
`state_main_name`, optional `state_object_id`, `state_href`, `state_current`). Under forced colors the fill and the
dot are drawn in system colors. `tests/tests/design/AdminLanguageStatesTest.php`.

Cost, measured on alpha with a folder of 50 articles: the sub-items request (`ezjscnode::subtree`, 50 rows) takes
the same number of queries before and after the states (357 for 50 rows, 42 for 5, the same both times: nothing per
row is added); the node view adds the one grouped query of `lastEdited()`, which the SQL query cache answers on the
second use in the same page (meta line and Translations tab). Since the fetch function belongs to a kernel class, a
template that uses it must not go live before the workers know the class: deploy with `exp:velocity deploy
--kernel`; a compiled `fetch()` of a class a worker cannot load renders nothing instead of a fatal error.

## Context menus

The menus of the content tree, the sub-items table, the class icon and the bookmarks (`ezpopupmenu.js` with the
templates in `templates/popupmenu/`) look like the other admin4 popovers and work from the keyboard.

- **Look:** the card colour, edge, radius and shadow of the admin tokens in the light and the dark mode; text at the
  body size (14 px) in compact rows of about 27 px (4 px above and below, line height 1.35, at least 24 px, the
  WCAG 2.2 target size; separators 2 px apart); the menu as wide as its longest item (13.5 to 22 rem) instead of a
  fixed 14em that wrapped long items, an item longer than that cut with an ellipsis; the node or class name as a quiet title row; the hovered, focused and opened
  row in the accent tint; disabled items in the muted text colour. Under Windows high contrast the rows keep a
  visible outline.
- **Keyboard:** the tree's menu button (the class icon in front of a node) and the sub-items table's row button
  open the menu with Enter or Space; Shift+F10 or the context menu key open the menu of the focused tree item or
  table row (in a form field of the row they keep the browser's own menu); also on a scrolled page, a menu opened
  from the keyboard opens under its button. The first item takes the focus. Arrow Down and Up, Home and End move between items, Arrow Right,
  Enter or Space open a submenu, Arrow Left closes it, Enter or Space run an item, Escape closes the menu and
  returns the focus to its button, Tab closes the menu and moves on.
- **Screen readers:** `ezpopupmenu.js` gives each menu `role="menu"` labelled by its title row, its items
  `role="menuitem"` (`aria-disabled` when disabled, `aria-haspopup`/`aria-expanded` for a submenu) and its rules
  `role="separator"` when it shows the menu, so menus that extensions add through `admininterface.ini` get them too.
  The menu's button carries `aria-haspopup="menu"` and `aria-expanded`; the tree's button is named after its node.
- The functions extensions call (`ezpopmenu_showTopLevel`, `ezpopmenu_showSubLevel`, `ezpopmenu_mouseOver`,
  `ezpopmenu_submitForm`, `ezpopmenu_hideAll` ...) are unchanged. `design/admin` keeps an identical copy of the menu
  script and of the two scripts and the template that open it; change both copies together.

## Landmarks and names

admin4's frame tells screen readers and keyboards how the page is built:

- **Skip link.** The first stop of the Tab key is "Skip to main content", hidden until it has the focus. It jumps to
  `#maincontent`, which takes the focus (`tabindex="-1"`), or to `#a4-main` (below).
- **Landmarks.** The header is the banner, the top menu (`#a4-navbar-menu`), the side menu (`#leftmenu`) and the
  breadcrumb (`#path`) are named navigation landmarks, the right column (`#rightmenu`) is a complementary landmark,
  the footer the content info, and `#maincontent` the main landmark. The edit-type pages (content edit, history,
  version view, upload ...) bring their own `#maincontent` and side column; theirs carry the same roles. A page
  without the left column whose template brings no `#maincontent` (remove, copy, move ...) is wrapped by
  `pagelayout.tpl` in `#a4-main`, a main landmark without styles, so its layout does not change. The roles sit on
  the existing elements, so every selector of the stylesheets and extensions still matches.
- **Names and state.** The breadcrumb and the top menu mark the current page (`aria-current="page"`), a disabled top
  menu entry is `aria-disabled` and out of the Tab order, the logo and "Open the site" links have a name, the
  phone-width menu button is a button for keyboards and screen readers (Enter and Space, `aria-expanded`), and
  selects, checkboxes and radios that had only a `title` or their table position have a name (the row's language or
  location where a list repeats them). The duplicate image link before the current user's name is hidden from both.
- **Tables.** Every header cell of admin4's templates has `scope="col"` (its tables have header rows only).
- **Form fields.** Every field has a name: its visible label (`<label for>`, or a `<label class="a4-inline-label">`
  around the text next to a radio or checkbox), the column header and the row's name in a list, the card heading in
  the Setup > RAD wizards, and an `aria-label` only where nothing is shown. Radio groups are named groups (version
  preview, template creation, package export). `tests/tests/design/AdminFormLabelsTest.php` checks every
  `input`, `select` and `textarea` of admin4's templates and overrides.

`tests/tests/design/AdminLandmarksTest.php` checks the skip link, the landmarks, one main landmark per
`#maincontent`, the `#a4-main` wrapper, the scopes, the frame titles and the named controls, in admin4's templates
and its overrides.

## Keyboard

- **Node tabs** (View, Details, Translations ... and the tabs extensions add) follow the WAI-ARIA tabs pattern:
  `node_tabs.js` gives the list `role="tablist"`, each link `role="tab"` with `aria-selected` and `aria-controls`,
  each panel `role="tabpanel"`. Only the selected tab is in the Tab order; Arrow Left and Right (the list is horizontal, so Up and Down stay with the page), Home and End move to
  another tab and open it, as a click does; with no tab selected the first one is the Tab stop. A click on an
  element inside a tab's link opens the tab too. While the tabs are switched off (every panel shown) the roles are
  taken away.
- **Show/hide tabs** is a button with one name, "Show tabs", and `aria-pressed` (the tabs are shown or not); its
  title says what a press does. Space presses it like Enter.
- **Content tree** is a tree (the WAI-ARIA tree view pattern, as in its navigation tree example): the list has
  `role="tree"` and the name "Content tree", each node's name link is a `treeitem` with `aria-level`,
  `aria-posinset`, `aria-setsize` and, for a node with sub items, `aria-expanded` (`aria-busy` while they load);
  the list of sub items is a `group` the item owns (`aria-owns`), the current node's item has
  `aria-current="page"`. One item is in the Tab order (the current node's once it is shown, else the first; then the
  last one focused). Keys: Up and Down move through the shown items; Right opens a node (loading its items) or moves
  to its first sub item; Left closes it or moves to its parent; Home and End go to the first and last item; a letter
  moves to the next item that starts with it (letters typed within half a second form one search); Enter follows
  the link; Shift+F10 or the context menu key opens the node's own menu (each tree item says so in `aria-keyshortcuts`). A key that does nothing there (Right on an
  item without sub items) keeps its default. The open/close button before a node ("Items below <node>",
  `aria-expanded`) and the node icon stay for the mouse but are out of the Tab order and hidden from screen readers,
  since the item carries their state; a focus the keyboard or a closing menu gives them goes to the item (a click on
  the node icon keeps it there, so the menu's keys work). A node whose items cannot be loaded says why through
  `aria-describedby`; a node whose sub items are all hidden (`hideNodes`) has no `aria-expanded`. A node closed with
  the mouse takes the Tab stop when it sat on one of its sub items. The tree's script and settings template are the
  same file in `design/admin` and `design/admin4`; the tree of `show_content_structure.tpl` (no dynamic menu) is
  unchanged.
- **Date fields:** the calendar opens from a button (`aria-haspopup="dialog"`) named after its field ("Choose a date for Publish date"); `exp::datepicker` gives the focus back
  to it when the calendar closes. The classic designs keep their calendar image.

`tests/tests/design/AdminKeyboardTest.php` holds the markup and the hooks the scripts rely on, that the tree's
files are the same in both designs, and that no admin4 template has a clickable image. `tests/tests/design/AdminTreeViewTest.php` holds the
tree's roles, states, Tab stop and keys.

## Reordering sub items by drag and drop

Where a node sorts its children by priority and the user may change the priorities (`$node.can_edit`: the same
condition as the classic priority fields), the sub items table has a column of grips before the names. It shows while
the table is sorted as the node sorts (by priority, in the node's direction), with no name filter and more than one
row; sorted by another column the column is hidden. `design/admin4/javascript/subitems_priority_drag.js` brings it;
the table script (`ezajaxsubitems_expdatatable.js`, the same file in `design/admin`) adds the column only when the
template sets `confObj.priorityDrag`, so the other admin designs are unchanged.

- **Pointer** (mouse, touch, pen; pointer events, no HTML5 drag and drop, no library): drag the grip; the row is
  its own placeholder (dashed, tinted), a ghost with the name follows the pointer, the window scrolls when the pointer
  nears its top or bottom edge. Escape or a cancelled touch puts the row back.
- **Keyboard**: the grip is a button named "Move <name>" with the instructions as its description. Space or Enter
  picks the row up (`aria-pressed`), the arrow keys move it, Home and End to the first and last place, Space or Enter
  drops it, Escape or Tab cancels. Alt+Up and Alt+Down move it by one place at once. Each step is announced
  through the table's status region.
- **Saving** happens right after the drop, through `ezjscnode::updatepriority`. Only the current page is reordered:
  its own priorities are given out again to its rows in the new order, so nothing moves to another page. When the page
  has equal priorities, or shares one with the last row of the page before or the first of the page after, the rows
  get distinct values strictly between those two neighbours (steps of 10 where there is room, smaller steps where
  not; the first page may go below 0, the last page above the page before). When the neighbours leave no room (equal
  priorities across a page border), the server numbers all children again in steps of 10, the page's rows at their
  place. The priorities the table showed and the neighbours' are sent along; when one changed in the meantime nothing
  is written, the table loads again and a toast says so. "Order saved" offers Undo for 10 seconds; it posts the old
  priorities, checked the same way. A failed save puts the rows back and shows the error.
- **The server side** (`ezjscnode::updatepriority`): the lists go as one text each (`PriorityList`,
  `ExpectedPriorityList`, "node_id:priority,..."), so a page of 500 rows stays far below `max_input_vars`; the check
  and the write run in one transaction (rows locked on MySQL, PostgreSQL and Oracle). Every node to be written must
  come with the priority the table showed, and the lists are refused for a node that does not sort by priority or that
  the user may not edit (the classic form's rule); the write goes through the kernel's `content/updatepriority`
  operation, so its workflows, cache clearing and search index updates run. Lists sorted by priority order equal
  priorities by node id, the order the renumbering takes. While a new order is being saved, the pagers, the sorting
  and the filter wait (`inert`); a save that ends after the table loaded other rows does not draw over them.
- **Limits**: a row moves within its page only (to move it to another page, change the rows per page or type its
  priority); a page of one row has no grip; a key that moves a row while the previous move is still being saved is
  refused with "Please wait until the order is saved."
- **The priority cells stay** and can still be edited by hand; they show the new values after a move. A move does
  not start while a priority is being edited with a changed value (a toast asks to save or cancel it first) or while
  it is being saved, and no cell can be edited while a new order is being saved.
- Light and dark, forced colours (the placeholder's outline and the pressed grip in Highlight), reduced motion (no
  tilt of the ghost, no fade), print (no grips). Styles: `modules/content.css` (`.a4-content-drag-*`) and
  `.a4-toast-action` in `components.css`. Tests: `AdminSubitemsPriorityDragTest`,
  `ezjscServerFunctionsNodePriorityTest`.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/design.ini` | `AdminDesignSettings` | `ShowCommunityLinks` | `disabled` | siteaccess |
| `settings/design.ini` | `StylesheetSettings` | `AdminComponentCSSFileList[]` | `components.css` and the five `modules/*.css` | siteaccess |
| `settings/menu.ini` | `MenuAccessSettings` | `CheckViewAccess` | `enabled` | siteaccess |
| `settings/menu.ini` | `MenuAccessSettings` | `NoAccessLinks` | `hidden` | siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `HiddenTabs[]` | empty | siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `SetupTabs[]` | empty (see [Moving a tab into Setup](#moving-a-tab-into-setup)) | siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `MegaMenuTabs[]` | empty; `setup` and `users` have a template (see [Mega menu](#mega-menu-prototype-opt-in)) | siteaccess |
| `settings/menu.ini` | `Topmenu_<tab>` | `MegaMenuTemplate` | `design:parts/setup/mega_menu.tpl` (setup), `design:parts/user/mega_menu.tpl` (users) | siteaccess |
| `settings/menu.ini` | `Leftmenu_user_mega` | `MegaMenuLinks[]` | eight links of the Users tab (see [Mega menu](#mega-menu-prototype-opt-in)) | siteaccess |
| `settings/menu.ini` | `Leftmenu_setup` | `MegaMenuLinks[]` | twelve frequent links, at most 12 shown (see [Mega menu](#mega-menu-prototype-opt-in)) | siteaccess |
| `settings/menu.ini` | `Topmenu_<tab>` | `Name`, `Tooltip`, `TranslationContext` | the default tab names, context `design/admin/pagelayout` | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `Groups[]`, `GroupNames[<group>]`, `GroupLinks_<group>[]` | the nine groups of `Leftmenu_setup` (Design and Audit for moved tabs, AI and Updates for extensions' links) | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `LeadLinks[]` | `overview` in `Leftmenu_setup` | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `TrailLinks[]` | `change_password`, `edit_profile` in `Leftmenu_my` | siteaccess |
| `settings/menu.ini` | `Leftmenu_setup` | `GroupTabs[<group>]`, `GroupSections[<group>]` | `design` and `audit` (see [Moving a tab into Setup](#moving-a-tab-into-setup)) | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `LinkIcons[<link>]`, `LinkDescriptions[<link>]`, `LinkFigureTemplates[<link>]` | the core Setup links | siteaccess |
| `settings/design.ini` | `AdminDesignSettings` | `IconList[]` | every `.a4-bi-*` icon of `components.css` | siteaccess |
| `settings/menu.ini` | `Topmenu_setup` | `URL[default]` | `setup/menu` (the Setup overview) | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `PolicyList_<link>[]` | a node id, `<module>/<function>`, or `<module>/*` for a module without policy functions | siteaccess |
| `settings/site.ini` | `SiteAccessSettings` | `FrontSiteAccess` | empty (`DefaultAccess`) | siteaccess |
| `settings/site.ini` | `AdminEnvironment` | `Level`, `Name`, `ShortName`, `Release`, `ShowVersion`, `ShowInTitle` | empty (no badge), empty, empty, empty, `disabled`, `enabled` | siteaccess |

`ShowCommunityLinks=enabled` shows the footer line "Evaluate Exponential / Become a member of the Exponential
Community". A template can also set `$show_community_links`.

- `AdminComponentCSSFileList[]`: the shared `.a4-*` components and the module stylesheets, loaded after the design's
  own stylesheets and before `BackendCSSFileList` ([Styling the administration](../../bc/6.0/admin4-styling.md)).
- `CheckViewAccess=enabled` shows a top tab or a left menu link only to a user who can open its address, checked like
  the request itself; `NoAccessLinks=hidden` leaves such a link out (`disabled` shows its name without a link).
- `HiddenTabs[]` leaves top tabs out whatever `Tabs[]` says, also tabs an extension appends. Design and Audit then
  show in the Setup menu, and without the Dashboard tab the logo leads to the dashboard (see
  [Moving a tab into Setup](#moving-a-tab-into-setup)).
- `Name`, `Tooltip` and `TranslationContext` of a `[Topmenu_<tab>]` give the tab's label and tooltip in English and the
  translation context they are looked up in.
- `Groups[]`, `GroupNames[<group>]` and `GroupLinks_<group>[]` group a long left menu (see "Left menu" above).
- `LeadLinks[]` names links of a grouped left menu that stand before the groups and in none of them (the Setup
  overview); `TrailLinks[]` names links drawn after all others, also after the links extensions append and after the
  groups (the Dashboard menu's Change password and Edit profile), in every design; `URL[default]` of `[Topmenu_setup]` is the page the Setup tab opens (see [Setup overview](#setup-overview)).
- `PolicyList_<link>[]` names a node id that must be readable, a policy function, or `<module>/*` for the modules that
  have no policy functions (visual, class, role, workflow, trigger, url, search, settings, collaboration);
  `tests/tests/design/AdminMenuPolicyListTest.php` fails for a function a module does not have. The Design tab opens
  the design top node by its id (`content/view/full/58`, `content.ini [NodeSettings] DesignRootNode`).
- `FrontSiteAccess` is the site an admin siteaccess belongs to: its "Open the site" and "View on site" links go there.
  Both show the globe (Bootstrap Icons `globe2`, `.a4-bi-globe2`), so a link to the site does not look like a link
  that leaves the administration for something else (`box-arrow-up-right`).
- `[AdminSiteAccessMenu]`: the site menu (see "Site menu" above), read from the admin siteaccess's settings.
  `Show=disabled` turns it off. `List=related` (the default) lists `[SiteAccessSettings] RelatedSiteAccessList[]` of
  the admin siteaccess, the siteaccesses that share its database, or `AvailableSiteAccessList[]` where it is empty;
  `List=available` lists `AvailableSiteAccessList[]`; a siteaccess that is not in `AvailableSiteAccessList[]` is never
  listed; an installation that serves several sites with separate databases sets `RelatedSiteAccessList[]` for each
  administration, so that it lists its own site only. `HiddenSiteAccessList[]` leaves siteaccesses out (the site the
  globe opens stays the first entry). `AdminSiteAccessList[]` names the administrations; empty,
  a siteaccess whose `SiteDesign` begins with `admin` or that sets `FrontSiteAccess` is one. `ListAdministrations`
  (`disabled` by default) lists them as a group of their own when `enabled`. Besides the site the globe opens, only
  the siteaccesses the signed-in user may log in to are listed (`eZUser::canLoginToSiteAccess()`, as the login
  checks it). The header's cache-blocks are keyed by `siteaccess_menu_key()` (the list, `ListAdministrations` and
  the request) and the user's role hash, so a changed list shows on the next request; a
  changed `SiteName`, `Locale` or `SiteDesign` of a listed siteaccess shows once the template caches of the
  administration siteaccess are cleared.
  The template operators are `siteaccess_menu( $current_user )` and `siteaccess_menu_key()`.
- `[AdminEnvironment]`: the environment badge (see "Environment badge" above). `Level` is `development`, `test`,
  `staging` or `production`, or `auto` for the level of `EXP_ENV` (`dev`, `development`; `test`; `stage`, `staging`;
  `prod`, `production`, `live`; another name or none: no badge). An empty `Name` takes the level's translated name.
  Set it per environment, for example in `settings/override/site.ini.prod.append.php`
  ([Settings per environment](environment-settings.md)); the setting is read on every request. The header's and the
  sign-in page's cache-blocks are keyed by the badge, so a changed setting shows on the next request without clearing
  the template cache; where `EZP_INI_FILEMTIME_CHECK` is off, clear the INI cache
  (`php bin/php/ezcache.php --clear-id=global_ini`). `Release` and the version are shown to signed-in users only.
  The template operator is `exp_environment_badge()`.
- The logo's name in the header carries the release only for signed-in users; the anonymous pages of the admin
  (the 401 page, `user/register`, `user/forgotpassword`) say "Exponential".

## For developers

- **Styling rules: [Styling the administration](../../bc/6.0/admin4-styling.md).** No `<style>` block or `style=""`
  attribute in a template; the shared `.a4-*` components and `--a4-*` tokens (light and dark) of
  `stylesheets/components.css`; module CSS in `stylesheets/modules/`, loaded through `design.ini`
  `[StylesheetSettings] AdminComponentCSSFileList`; checked by `tests/tests/design/AdminStylingRulesTest.php`.
- `design/admin4/stylesheets/admin4.css` is loaded after the base stylesheets and before the extensions' own. It uses
  the base rules' own selectors, so what beat a base rule before still beats it. Shared defaults carry no weight at all
  (`:where`), so extensions' rules win. There are no cascade layers.
- The base stylesheets admin4 took over from admin3 (`core.css`, `content.css`, `pagelayout.css`, `theme/rounded.css`,
  `theme/admin_datatable.css`, `theme/modalwindow.css`) take every colour from the tokens as well (since 2026-10-14,
  `AdminStylingRulesTest::testBaseStylesheetsUseOnlyColourTokens`): links are `--a4-accent-text`, the diff marks
  `--a4-ok`/`--a4-bad` on the warn tint. admin4 has no `responsive.css` any more (its one live rule is in
  `pagelayout.css`) and no `setup-extensions.css` (no kernel template loaded it; exp_adminui carries its own copy).
- admin4 keeps its own copy of `ezajaxsubitems_expdatatable.js`: it must work with no other admin design present, and
  a single copy in `design/standard` would be shadowed by any design that has its own. Change both copies together.
- The CSS packer used to remove the space before a colon in selectors (`a :hover` became `a:hover`). Fixed.

## Changes to the other designs on the same day

- The classic grey administration design (`classic`) got the Exponential logo, a left menu that collapses and
  resizes, and Layouts pages in its own look.
- The header search scope popup of the old `design/admin` now works in both copies (each has its own element ids) and
  closes on an outside click.

## Related pages

- [Styling the administration: components, tokens, no inline CSS](../../bc/6.0/admin4-styling.md)
- [Icons from extensions and icon themes](../../bc/6.0/ICONS.md)
- [The responsive admin design (admin3)](admin3-responsive-admin.md), [left sidebar width and font size](admin3-sidebar-width-and-font-size.md)
- [Setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md), [jQuery 4 and YUI removal](jquery4-and-yui-removal.md)
- [Admin links follow permissions](admin-links-follow-permissions.md), [order list sorting](order-list-sorting.md)
- [Sub-items list: columns, presets and CSV export](subitems-table-options.md), [copy selected sub-items](subitems-copy-selected.md), [hide and unhide selected](../../bc/6.0/SUBITEMS_MENU_MORE_ACTIONS_MENU_EXPANSION_HIDE_UNHIDE.md)
- [admin4l: the admin page built from layouts](admin4l.md), [its behaviour change](../../bc/6.0/admin4l.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- History: [October 2026](../../history/2026/2026-10.md), [June 2026, second half](../../history/2026/2026-06b.md), [June 2025](../../history/2025/2025-06.md), [January 2025](../../history/2025/2025-01.md), [November 2024](../../history/2024/2024-11.md), [October 2024](../../history/2024/2024-10.md), [August 2024](../../history/2024/2024-08.md)
