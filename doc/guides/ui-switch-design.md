# The administration design switch: settings, designs and roles

This guide is for administrators and developers who set up the per-session choice of the administration design
([the feature](../features/6.0/ui-switch-design.md)). You will read the settings, add an installed design to the
list, make sure every offered design lets a user switch back, give the permission to a role, test the result and
solve the usual problems. It takes about twenty minutes.

Everything below was checked against the code: `kernel/classes/uiswitch/expuiswitchdesignprovider.php`, the UI switch
registry `kernel/classes/uiswitch/expuiswitch.php` and `eZTemplateDesignResource::designBaseCachePath()` in
`kernel/common/eztemplatedesignresource.php`. The framework and the interface language are in
[UI switch settings](ui-switch-settings.md).

[Guides](README.md) · Related guides: [UI switch settings](ui-switch-settings.md),
[Templates and design](templates-and-design.md), [Roles and policies](roles-and-policies.md), [Caches](caches.md)

## In short

- The design switch is the provider `design` of `settings/uiswitch.ini`: `[UISwitchSettings] ProviderList[]=design`
  (after `language`) and the group `[UISwitch_design]`.
- `AllowedValues[]` lists the designs in toolbar order; `DesignChain[<design>]` gives each its fallback designs;
  `DesignLabel[<design>]` its name in the toolbar. A design without a chain, or that is not installed, is not offered.
- Every offered design must render the right toolbar tool `admin_ui_switch`, or a user who chose it cannot switch
  back.
- Users need the policy `uiswitch/design`.
- No cache needs clearing for a switch. After changing the settings, clear the INI cache and deploy as usual.

## 1. The settings

Never edit `settings/uiswitch.ini`. Override it in `settings/override/uiswitch.ini.append.php`, in a siteaccess's
`settings/siteaccess/<siteaccess>/uiswitch.ini.append.php`, or in an extension's `settings/uiswitch.ini.append.php`.

The shipped group:

```ini
[UISwitchSettings]
ProviderList[]=design

[UISwitch_design]
Class=expUISwitchDesignProvider
Enabled=true
AllowedValues[]
AllowedValues[]=admin4l
AllowedValues[]=admin4
AllowedValues[]=adminui
AllowedValues[]=admin
DesignChain[]
DesignChain[admin4l]=admin4;admin3;admin2;admin
DesignChain[admin4]=admin3;admin2;admin
DesignChain[adminui]=admin4l;admin4;admin3;admin2;admin
DesignChain[admin]=
DesignLabel[]
DesignLabel[admin4l]=Admin 4 (layouts)
DesignLabel[admin4]=Admin 4
DesignLabel[adminui]=AdminUI
DesignLabel[admin]=Classic
RememberPreference=disabled
```

| Setting | Meaning |
|---|---|
| `Class` | The provider class. Leave it. |
| `Enabled` | `true` (default) or `false`. `false` (also `disabled` or `0`) switches the design provider off; the interface language stays. |
| `AllowedValues[]` | The designs a user may choose, in toolbar order. The siteaccess's own `SiteDesign` is always offered, first, whether it is listed or not; choosing it clears the switch. Each design is listed once. |
| `DesignChain[<design>]` | The fallback designs the choice runs with, separated by `;`, most specific first. They become `AdditionalSiteDesignList[]` for the user's requests. The design itself and names that are not design names are left out, and a name listed twice counts once. `DesignChain[admin]=` (empty) is a valid chain: the design plus the standard design only. **A design without a `DesignChain[]` entry is not offered.** |
| `DesignLabel[<design>]` | The name in the toolbar, translated in the context `kernel/uiswitch`. Without it the toolbar shows the design name. Add one for your siteaccess's own design too (for example `DesignLabel[editor]=Editor`), as it is always the first choice. |
| `RememberPreference` | `disabled` (default): the choice ends with the session, at logout. `enabled`: it is also stored in the user's preferences (`exp_uiswitch_design`) and applied at the next sign-in while the session holds no choice. |

Where the switch works at all (which siteaccesses) is set by `[UISwitchSettings] SiteAccessList[]`; empty means every
siteaccess whose design list contains an administration design. See [UI switch settings](ui-switch-settings.md).

A design is offered only when all of these hold:

1. it is listed in `AllowedValues[]` (or it is the siteaccess's own design);
2. its name matches `[a-z][a-z0-9_]*` (at most 64 characters);
3. it has a `DesignChain[]` entry;
4. `design/<design>` exists, or `design/<design>` of an extension listed in
   `design.ini [ExtensionSettings] DesignExtensions[]`.

Nothing a user sends is used as a path: a value is accepted only when it is exactly one of the offered designs.

## 2. Adding an installed design

Take the classic server-rendered design `admin_classic` of the extension `sevenx_themes_admin_classic` (0.2.5 or
later) as the example. The extension ships the entry itself, in
`extension/sevenx_themes_admin_classic/settings/uiswitch.ini.append.php`:

```ini
[UISwitch_design]
AllowedValues[]=admin_classic
DesignChain[admin_classic]=admin
DesignLabel[admin_classic]=Classic (server-rendered)
```

and registers its design directory in `settings/design.ini.append.php`
(`DesignExtensions[]=sevenx_themes_admin_classic`). Activating the extension is all that is needed.

For a design of your own:

1. Put it in `design/<name>` or in `extension/<extension>/design/<name>` with the extension in
   `DesignExtensions[]`.
2. Add `AllowedValues[]=<name>`, `DesignChain[<name>]=...` and `DesignLabel[<name>]=...` in an override or in your
   extension's `settings/uiswitch.ini.append.php`. End the chain with a design that has the switch tool (section 3);
   `admin` ends every shipped chain.
3. Add the label to your translations in the context `kernel/uiswitch` if it should be translated.
4. Clear the INI cache and deploy (section 6).

Do not write `AllowedValues[]` without a value in an extension: an empty `AllowedValues[]` line clears the list that
was read before it, so the kernel's designs would disappear.

## 3. Every offered design needs the switch tool

A user who switches to a design must find the switch again in that design, or they cannot go back until they log
out. The tool is the toolbar tool `admin_ui_switch`, template `toolbar/full/admin_ui_switch.tpl`:

| Design | Where the template comes from |
|---|---|
| `admin4`, `admin4l` | `design/admin4/templates/toolbar/full/admin_ui_switch.tpl` (right sidebar) |
| `adminui` | through its chain (`admin4l`, `admin4`) |
| `admin`, and `admin2`, `admin3` through their chains | `design/admin/templates/toolbar/full/admin_ui_switch.tpl` with `design/admin/templates/uiswitch/choices.tpl`: one form per choice, works without JavaScript |
| an extension design | through its chain; every shipped chain ends in `admin` |

The design's page layout must also render the right toolbar (`{tool_bar name='admin_right' view='full'}`; the classic
page layout does so while the right menu is shown), and the siteaccess's `toolbar.ini` must place the tool there:

```ini
[Tool]
AvailableToolArray[]=admin_ui_switch

[Toolbar_admin_right]
Tool[]=admin_ui_switch
```

The shipped admin, editor and adminui siteaccesses have it. A siteaccess whose own design is the classic one (for
example one with `SiteDesign=admin`) needs these lines in its `settings/siteaccess/<siteaccess>/toolbar.ini.append.php`
to show the switch. The toolbar comes from the siteaccess, not from the chosen design, so one entry serves every
design.

The tool is hidden while editing and browsing, and it is empty for a user without any `uiswitch/*` policy.

## 4. Giving the permission

1. Go to **Users > Roles and policies** and open the role (for example **Editor**).
2. **Edit**, **New policy**, module **uiswitch**, function **design**, and save.
3. Assign the role as usual.

The function `uiswitch/design` exists because `design` is in `ProviderList[]`; it takes no limitations. A role with
`uiswitch/*` (or `*/*`, as Administrator has) already includes it. The permission is checked on every request, so
removing the policy takes effect at the user's next page.

## 5. Testing it

1. Sign in with a test user that holds `uiswitch/design` (not the shared administrator account, whose preferences
   are someone's work environment).
2. In the right toolbar, **Interface**, **Administration design**: choose **Admin 4**. The page reloads in admin4.
3. Choose **Classic**. The page reloads in the classic design, and the **Interface** box is in its right menu.
4. Choose the first design (the siteaccess's own). The switch is cleared.
5. In a second browser, sign in as another user: the interface is the siteaccess's own design throughout.
6. Open the **Audit** tab (`/audit/dashboard`) and look for `access.uiswitch.change`: one record per change, with provider
   `design` and the values before and after.
7. Log out and in again: the siteaccess's own design (unless `RememberPreference=enabled`).

From a shell, `uiswitch/state` (GET, signed in) returns the providers the user may use, their designs and the
current one as JSON.

The developer tests run without a database:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchDesignProviderTest.php
```

## 6. Deploying a settings change

A switch itself needs no cache clearing: every design has caches of its own. After changing `uiswitch.ini` or
`toolbar.ini`, clear the INI cache and deploy:

```bash
./console exp:velocity deploy --allow-root-user
```

or, by hand, `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, a reload of the PHP-FPM that serves the
site, and `exp:velocity restart` for Velocity. A new provider class needs regenerated autoloads before the settings
name it (`deploy --kernel` for a kernel class). A global cache clear is not needed.

## 7. Turning it off and on

| Goal | Setting |
|---|---|
| No design switch anywhere | `[UISwitch_design] Enabled=false` in `settings/override/uiswitch.ini.append.php` |
| No design switch on one siteaccess | the same in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` |
| No UI switch at all (also no language) | `[UISwitchSettings] Enabled=false` |
| Not for some users | do not give their role `uiswitch/design` |
| One design fewer | leave it out of `AllowedValues[]` (rewrite the list starting with an empty `AllowedValues[]`) |

When it is off, no setting is changed in memory, no cache variant is used and every cache file name is the one it was
without the feature. A stored choice is ignored; with `[UISwitch_design] Enabled=false` it is also dropped from the session at the next request.

## 8. Troubleshooting

| Problem | Cause and fix |
|---|---|
| A design is not offered | It has no `DesignChain[<design>]` entry, its directory is missing (`design/<design>` or `design/<design>` of an extension in `DesignExtensions[]`), the extension is not active, or the name has upper case letters or characters other than `[a-z0-9_]`. Check the resolved settings on **Setup > Ini settings** (`uiswitch.ini`, `design.ini`). |
| The **Administration design** choice is missing entirely | The user lacks `uiswitch/design`; fewer than two designs are offered; `design` is not in `ProviderList[]`; `Enabled=false`; the siteaccess is not a switch siteaccess; or the class is missing from the autoloads (debug warning `UI switch provider 'design': class 'expUISwitchDesignProvider' not found`). |
| The **Interface** tool is missing | `admin_ui_switch` is not in `[Toolbar_admin_right] Tool[]` of the siteaccess, or the right menu is collapsed in the classic design, or you are editing or browsing. |
| A user cannot switch back | The chosen design has no `toolbar/full/admin_ui_switch.tpl` in its chain. Add `admin` to the end of its `DesignChain[]`. To rescue the user: log out (the choice ends with the session), or remove the design from `AllowedValues[]` (the stored choice is dropped at the next request). With `RememberPreference=enabled`, choose the default design after signing in. |
| A page looks half old, half new after a settings change | Stale INI cache or a PHP process that kept the old settings: deploy as in section 6. A global cache clear is not needed. |
| Switching returns 400 | The design is not offered (see the first row). 403: the user lacks the policy, the form token is missing, or the request came from another site. 404: the siteaccess has no switch. |
| A page in the wrong design for a colleague | Should not happen: pages rendered with a switch are not stored in the HTTP page cache or Velocity's response cache, and every other cache is separate per design. Report it with the URL and both users' designs. |

## Related pages

- [Feature: choosing the administration design](../features/6.0/ui-switch-design.md)
- [Developer notes: the administration design switch](../bc/6.0/ui-switch-design.md)
- [UI switch](../features/6.0/ui-switch.md) and [its settings](ui-switch-settings.md)
- [Roles and policies](roles-and-policies.md), [Caches](caches.md), [Templates and design](templates-and-design.md)
