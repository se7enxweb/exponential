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
- **Normal or compact density.** A second header button (pressed = compact) tightens the cells of `table.list` and
  `.a4-table`, cards and the height of the controls in a row (32 to 28 px), not `table.special`; the eZPreference
  `admin_density`, applied before the page is drawn. Every target stays at least 24 px, the content tree
  keeps its row pitch.
- **Remembered choices.** The color mode, the density, the side columns on a wide window and the closed groups of
  the left menus are the signed-in user's eZPreferences (`admin_color_mode`, `admin_density`,
  `admin_left_sidebar`, `admin_right_sidebar`, `admin_navgroups`), like the column width. One helper,
  `window.a4Pref` (the first script of `page_head_style.tpl`), reads them from `<html>`
  (`design:parts/a4_preference_attributes.tpl`, `data-a4-pref-*`) and saves them through
  `user/preferences/set_and_exit`. An anonymous page keeps them in the browser (`localStorage`) only; a browser
  choice made before is taken along once. `tests/tests/design/AdminPreferencesTest.php` checks it.
- **Top menu.** Every item shows at every width from 576 px, wrapping onto more lines; phones keep the hamburger menu.
  The page always starts below the header, even after a resize adds a line.
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
- **Edit form.** The button bars (Send for publishing, Store draft, Store draft and exit, Discard draft) stay in view
  while the form scrolls (CSS `position: sticky`, replacing the YUI script that stopped working). They are slim
  (42 px on a desktop) and fit a phone screen. Edit pages show one left column, not two. Required fields carry a red
  `*` (a hint above the attributes says what it means; screen readers hear "required", and the controls are
  `aria-required`). After a failed store a summary at the top takes the focus and links to each field; every invalid
  field shows its message under its label, carries a red edge, and its controls are `aria-invalid` with the message as
  description. Each attribute is a named group (`role="group"`, id `ezcca-attr-<attribute id>`). The datatype
  templates are unchanged: the marks are set on their data controls by the page's script (not on buttons, file inputs
  or remove checkboxes); a datatype template opts a control out with the class `a4-no-field-mark`. Multiedit, user
  registration and the template look editor include the same attribute template and behave the same.
- **Frame.** Side column margins are halved (a 16 px gap). The sidebar buttons sit on the window edges at 48% of the
  window height; they are named after their column ("Left column", "Right column"), say with `aria-expanded` whether
  it shows, and on a narrow window Escape closes a column open over the page and returns the focus to its tab. Long names in the side columns wrap instead of being cut off.
- **Left menu.** The left column is the "Side menu" navigation. A long menu shows in groups: Setup in Content model,
  Site and URLs, Workflows, Users and messages, System, and "More" for links an extension adds. Each group has a small
  capital title with a caret and opens and closes (the eZPreference `admin_navgroups` keeps the choice; the group of the current page always
  opens). The current page's link carries `aria-current="page"` and is marked with the accent tint and a bar. A menu
  is grouped through menu.ini (`Groups[]`, `GroupNames[<group>]`, `GroupLinks_<group>[]`); without `Groups[]`, or with
  `Groups[]` reset to no values, it is one list, named by its heading. Access is checked per link as before.
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
left menu (Content model, Site and URLs, Workflows, Users and messages, System, and "More" for links an extension adds).

- **One source.** The tiles are the left menu, drawn another way: `setup/setupmenu.tpl` includes
  `parts/setup/menu.tpl`, which hands `[Leftmenu_setup]` of `menu.ini` to `parts/ini_menu.tpl` with
  `menu_layout='tiles'`. The links, their names, their groups and the access checks (`PolicyList_<link>[]`,
  `CheckViewAccess`, `NoAccessLinks`) are the menu's: a function the user may not open has no tile, and an
  extension's link appears under "More" by adding it to `[Leftmenu_setup] Links[]`, as for the menu. The order inside
  a group is that of `Links[]`.
- **A tile** is a card (`.a4-card`) with a Bootstrap icon in an accent square, the
  function's name as its link, stretched over the card with one focus ring around it, and one line on what the
  function does as the link's description. Icon, description and figure come from the menu's own section in
  `menu.ini`, per link key (see "Extending it" below); a link without them takes the plug icon and no description.
  With `NoAccessLinks=disabled` a function the user may not open is a tile without link and without figure, as the
  menu shows its name without link.
- **Figures**, only where one cheap fetch gives them and only on a tile the user may open
  (`setup/setupmenu_figure.tpl`, named by `LinkFigureTemplates[]`): classes, sections, content languages, active
  extensions that exist (`expinfo( 'active' )`), signed-in users; one and many are two strings.
- **Grid.** One column on a phone, then two, three and four as the content column grows (container queries on
  `.a4-setup-overview`, so a wide left column counts). Styles in `stylesheets/modules/setup.css`, tokens only, light and
  dark, forced colors.
- **Left menu.** "Overview" is the first entry of the Setup menu, before the groups: menu.ini `LeadLinks[]` names
  links that stand before the groups and in none of them (`.a4-navlead`). It is marked as the current page on the
  overview, which leaves it out of its tiles.
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

The link shows under "More" in the menu and on the overview (or in a group, with `GroupLinks_<group>[]=myreports`).
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
  marks the Setup tab and draws the Setup menu, with its group open at the current link. No page marks a tab that is
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

## Languages

A flag stands for a country, not for a language: English is spoken under several flags, and one country has several
languages. admin4 therefore shows a content language as a short code badge and its name, the way most back offices
list languages:

- **The code** is the language part of the locale's language tag (`ger-DE` has `de-DE`, so `DE`; `eng-GB` gives
  `EN`). When the installation has two content languages of the same language (`eng-GB` and `eng-US`), both show the
  region as well (`EN-GB`, `EN-US`). A locale without a language tag shows its locale code.
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

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/design.ini` | `AdminDesignSettings` | `ShowCommunityLinks` | `disabled` | siteaccess |
| `settings/design.ini` | `StylesheetSettings` | `AdminComponentCSSFileList[]` | `components.css` and the five `modules/*.css` | siteaccess |
| `settings/menu.ini` | `MenuAccessSettings` | `CheckViewAccess` | `enabled` | siteaccess |
| `settings/menu.ini` | `MenuAccessSettings` | `NoAccessLinks` | `hidden` | siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `HiddenTabs[]` | empty | siteaccess |
| `settings/menu.ini` | `TopAdminMenu` | `SetupTabs[]` | empty (see [Moving a tab into Setup](#moving-a-tab-into-setup)) | siteaccess |
| `settings/menu.ini` | `Topmenu_<tab>` | `Name`, `Tooltip`, `TranslationContext` | the default tab names, context `design/admin/pagelayout` | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `Groups[]`, `GroupNames[<group>]`, `GroupLinks_<group>[]` | the five groups of `Leftmenu_setup` | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `LeadLinks[]` | `overview` in `Leftmenu_setup` | siteaccess |
| `settings/menu.ini` | `Leftmenu_setup` | `GroupTabs[<group>]`, `GroupSections[<group>]` | `design` and `audit` (see [Moving a tab into Setup](#moving-a-tab-into-setup)) | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `LinkIcons[<link>]`, `LinkDescriptions[<link>]`, `LinkFigureTemplates[<link>]` | the core Setup links | siteaccess |
| `settings/design.ini` | `AdminDesignSettings` | `IconList[]` | every `.a4-bi-*` icon of `components.css` | siteaccess |
| `settings/menu.ini` | `Topmenu_setup` | `URL[default]` | `setup/menu` (the Setup overview) | siteaccess |
| `settings/menu.ini` | `Leftmenu_<menu>` | `PolicyList_<link>[]` | a node id, `<module>/<function>`, or `<module>/*` for a module without policy functions | siteaccess |
| `settings/site.ini` | `SiteAccessSettings` | `FrontSiteAccess` | empty (`DefaultAccess`) | siteaccess |

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
  overview); `URL[default]` of `[Topmenu_setup]` is the page the Setup tab opens (see [Setup overview](#setup-overview)).
- `PolicyList_<link>[]` names a node id that must be readable, a policy function, or `<module>/*` for the modules that
  have no policy functions (visual, class, role, workflow, trigger, url, search, settings, collaboration);
  `tests/tests/design/AdminMenuPolicyListTest.php` fails for a function a module does not have. The Design tab opens
  the design top node by its id (`content/view/full/58`, `content.ini [NodeSettings] DesignRootNode`).
- `FrontSiteAccess` is the site an admin siteaccess belongs to: its "Open the site" and "View on site" links go there.
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
