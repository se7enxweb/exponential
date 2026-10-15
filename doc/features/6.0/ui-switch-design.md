# Choosing the administration design for your session

This page is for editors and administrators who work in the administration interface. A signed-in user who holds the
policy `uiswitch/design` can choose which administration design they work in, for example **Admin 4 (layouts)**
(`admin4l`), **Admin 4** (`admin4`), **AdminUI** (`adminui`), **Admin 3** (`admin3`), the **Classic** design (`admin`)
or **Classic (server-rendered)** (`admin_classic`), from the right toolbar.
The choice changes the interface for that user only, and only for the siteaccess where it was made. Since 15 October
2026.

The design choice is the second provider of the [UI switch](ui-switch.md), after the interface language. The
settings are explained in [the guide](../../guides/ui-switch-design.md), the code changes in
[the developer notes](../../bc/6.0/ui-switch-design.md).

## In short

- **Where:** the right toolbar of an administration siteaccess, in the **Interface** tool (`admin_ui_switch`), below
  the interface language. Not on public siteaccesses, and not while editing or browsing for an item.
- **Who:** a signed-in user whose role has the policy `uiswitch/design`. The permission is checked again on every
  request: a user who loses it is back in the siteaccess's own design at the next page.
- **What changes:** for that user's requests only, `site.ini [DesignSettings] SiteDesign` and
  `AdditionalSiteDesignList[]` are set in memory to the chosen design and its fallback chain. Templates, stylesheets
  and scripts follow, because each design names its own.
- **What does not change:** other users, the content, the settings files, other siteaccesses and the public site.
- **Caches:** every design gets caches of its own. Nobody's caches are cleared when a user switches.
- **How long:** until logout. With `RememberPreference=enabled` the choice is also stored in the user's preferences and
  comes back at the next sign-in.
- **Choosing the siteaccess's own design** clears the switch.

## Using it

1. Sign in to the administration (for example `/admin`).
2. In the right toolbar, find the **Interface** tool. It lists the interface language first, then **Administration
   design** with one button per design you may choose. The current design is marked.
3. Choose a design. The page reloads in that design, on the same page.
4. To go back, choose the first design in the list: the siteaccess's own design. The switch is cleared.

Every design that can be offered renders the same tool in its right toolbar, so you can always switch again: admin4
and admin4l show it in the right sidebar, the classic `admin` design (and admin2 and admin3, which fall back to it)
shows it as a classic box that works without JavaScript. The classic design shows it in its right menu; for a user
who came to it through the switch, that menu opens even if it is collapsed by default, unless the user closed it
(`admin_right_menu_show=0`), so the way back stays in reach.

The tool appears only when there is something to choose: at least two designs, and a role with `uiswitch/design`.

## Which designs are offered

The list comes from `uiswitch.ini [UISwitch_design] AllowedValues[]`, in that order, after the siteaccess's own design,
which always comes first. A design is offered only when:

- its name is a design name (lower case letters, digits and `_`, starting with a letter);
- it has a fallback chain, `DesignChain[<design>]` (an empty chain counts);
- the design is installed: `design/<design>` exists, or `design/<design>` of an active design extension
  (`design.ini [ExtensionSettings] DesignExtensions[]`).

So AdminUI is offered on a siteaccess only when the `exp_adminui` extension is active there
(`ActiveAccessExtensions[]=exp_adminui`). On a stock installation that is only the `adminui` siteaccess, where AdminUI
is the default design and the other designs are offered. An administrator who wants AdminUI offered on the `admin`
and `editor` siteaccesses activates the extension there (see
[the guide](../../guides/ui-switch-design.md#offering-adminui-on-other-siteaccesses)). Likewise `admin_classic` is
offered only where `sevenx_themes_admin_classic` is active. A design that is uninstalled later disappears from the
list, and a user who had chosen it is back in the siteaccess's own design at the next request.

The shipped list, with the labels shown in the toolbar:

| Design | Label | Fallback chain |
|---|---|---|
| `admin4l` | Admin 4 (layouts) | admin4, admin3, admin2, admin |
| `admin4` | Admin 4 | admin3, admin2, admin |
| `adminui` | AdminUI | admin4l, admin4, admin3, admin2, admin |
| `admin3` | Admin 3 | admin2, admin |
| `admin` | Classic | none (the classic design and the standard design) |
| `admin_classic` | Classic (server-rendered) | admin (needs `sevenx_themes_admin_classic`) |

The labels are translated in the context `kernel/uiswitch`. A design without a `DesignLabel[]` shows its name.

## What exactly changes

When the user has chosen a design, early in every request (after the session starts, before translations, the
locale, the design and any template are set up), the kernel:

1. checks that the user is signed in, still holds `uiswitch/design`, and that the stored design is still offered;
2. sets `SiteDesign` to the chosen design and `AdditionalSiteDesignList[]` to its chain, on the request's own copy of
   `site.ini`. No settings file is written, and nothing that lives longer than the request is changed;
3. forgets the design information it may already have built in memory, so the new list is used throughout.

Everything else stays as configured for the siteaccess: its content languages, its toolbars (`toolbar.ini`), its
policies, its menus, its module settings. A choice made on one administration siteaccess does not apply on another;
the session keeps one set of choices per siteaccess.

The setup wizard is never switched.

## Caches

Each design renders different markup, so each one has caches of its own: the cache of design locations, compiled
templates, `{cache-block}` content, the content view cache and the template override cache. Users who did not switch
use exactly the cache files they used before; nothing is cleared for them.

A page rendered with a switch is not stored in the role-aware HTTP page cache or in Velocity's response cache, so a
colleague never gets a page in someone else's design.

The extension `sevenx_themes_admin_classic` before 0.2.5 cleared the template and template block caches of the whole
installation on every switch. That is gone.

## Security and the audit trail

- A switch is a POST to `uiswitch/set` with the form token. A request from another site, a design that is not
  offered (400) or a user without the policy (403) is refused. The page to return to is followed only when it is a
  local path.
- Every change is written to the [audit trail](audit-trail.md) as `access.uiswitch.change`, with the provider
  `design`, the value before and after, and the siteaccess.

## Turning it off

- For everyone: in `settings/override/uiswitch.ini.append.php`

  ```ini
  [UISwitch_design]
  Enabled=false
  ```

  No design is offered or applied, and every page and cache file is as it was without the feature. The interface
  language stays available. `[UISwitchSettings] Enabled=false` turns off the whole UI switch.
- For a group of users: do not give their role `uiswitch/design`.
- For one design: leave it out of `AllowedValues[]`, or remove its `DesignChain[]` entry.

See [the guide](../../guides/ui-switch-design.md#turning-it-off-and-on) for the details.

## Related pages

- [UI switch](ui-switch.md): the framework and the interface language
- [Guide: the administration design switch](../../guides/ui-switch-design.md)
- [Developer notes: the administration design switch](../../bc/6.0/ui-switch-design.md)
- [The admin4 design](admin4-design.md), [admin4l](admin4l.md), [Exponential Admin UI](exp-adminui.md)
