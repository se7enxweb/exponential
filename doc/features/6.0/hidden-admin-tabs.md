# Hide admin tabs: HiddenTabs

Extensions append their tabs to the admin's top menu and node view, and extension settings are read after a
siteaccess's. A siteaccess could restate either list but never take an extension's tab out. Two new settings
now do exactly that, whatever adds the tab. Added 2026-10-01.

| File | Block | Key | Default | Scope |
|---|---|---|---|---|
| `settings/menu.ini` | `TopAdminMenu` | `HiddenTabs[]` | empty | siteaccess or override |
| `settings/admininterface.ini` | `WindowControlsSettings` | `HiddenTabs[]` | empty | siteaccess or override |

`[TopAdminMenu] HiddenTabs[]` is read by the `topmenu` template operator; `[WindowControlsSettings] HiddenTabs[]`
by `window_controls.tpl` of `admin3` and `admin` (the node view's tabs).

## Example: a siteaccess without the Layouts node tab and the Design menu

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

The tab identifiers are the entries of `Tabs[]` and `AdditionalTabs[]` (for example `dashboard`, `design`, `roles`). The shipped editor siteaccess hides `explayouts_ui_dashboard`, `setup`, `design`, `gitmanager`, `xrowextract` and `bccie_overview` in the top menu and `layouts` in the node view.
Clear the INI cache afterwards: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`.

The node view also shows each additional tab once now: a tab that an extension appended twice used to appear
twice. The editor siteaccess uses both settings; see
[the setup wizard and the editor siteaccess](setup-wizard-and-editor-siteaccess.md).

Same-day related changes: the dashboard, its menus and the top tabs show a link only to a user who can open it
(editors no longer saw Design, Newsletter and Export tabs they could not use), and the Store sidebar
(`[Leftmenu_shop]` in `menu.ini`) lists Dashboard, Orders, Products overview, Product statistics and Product
categories first.
