# Hide admin tabs: HiddenTabs

This page is for administrators who want a simpler admin on one siteaccess, for example an editor siteaccess. Two
settings take a tab out of the admin's top menu or the node view, whatever added it. Added 2026-10-01.

Before, a siteaccess could restate the list of tabs but never remove an extension's tab: extensions append their
tabs, and extension settings are read after a siteaccess's.

## Settings

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/menu.ini` | `TopAdminMenu` | `HiddenTabs[]` | empty | siteaccess or override |
| `settings/admininterface.ini` | `WindowControlsSettings` | `HiddenTabs[]` | empty | siteaccess or override |

- `[TopAdminMenu] HiddenTabs[]` is read by the `topmenu` template operator (the top menu).
- `[WindowControlsSettings] HiddenTabs[]` is read by `window_controls.tpl` of `admin`, `admin3` and `admin4` (the
  node view's tabs).

The tab identifiers are the entries of `Tabs[]` and `AdditionalTabs[]`, for example `dashboard`, `design`, `roles`.

## Example: no Design menu and no Layouts node tab

`settings/siteaccess/<admin-like siteaccess>/menu.ini.append.php`:

```ini
<?php /*
[TopAdminMenu]
HiddenTabs[]
HiddenTabs[]=design
*/ ?>
```

`settings/siteaccess/<admin-like siteaccess>/admininterface.ini.append.php`:

```ini
<?php /*
[WindowControlsSettings]
HiddenTabs[]
HiddenTabs[]=layouts
*/ ?>
```

Then clear the INI cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Reload the admin: the Design tab and the node view's Layouts tab are gone on that siteaccess only.

Find every place that reads the settings:

```bash
grep -rn HiddenTabs kernel design settings
```

## The shipped editor siteaccess

The editor siteaccess uses both settings. It hides `explayouts_ui_dashboard`, `setup`, `design`, `gitmanager`,
`xrowextract` and `bccie_overview` in the top menu, and `layouts` in the node view. See
[the setup wizard and the editor siteaccess](setup-wizard-and-editor-siteaccess.md).

## Changed on the same day

- The node view shows each additional tab once. A tab that an extension appended twice used to appear twice.
- The dashboard, its menus and the top tabs show a link only to a user who can open it; see
  [admin links follow permissions](admin-links-follow-permissions.md).
- The Store sidebar (`[Leftmenu_shop]` in `menu.ini`) lists Dashboard, Orders, Products overview, Product statistics
  and Product categories first.

## Related pages

- [Admin links follow permissions](admin-links-follow-permissions.md)
- [The admin4 design](admin4-design.md)
- [Setup wizard and editor siteaccess](setup-wizard-and-editor-siteaccess.md)
- [Upgrade checklist of 1-2 October 2026](../../bc/6.0/behaviour-changes-2026-10.md)
- [6.0.15 changelog](../../changelogs/6.0/6.0.15.md)
- [October 2026 chronicle](../../history/2026/2026-10.md)
