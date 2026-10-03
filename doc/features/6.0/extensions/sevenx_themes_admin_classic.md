# sevenx_themes_admin_classic: switch between the admin3 design and the classic fallback

`sevenx_themes_admin_classic` lets an administrator switch, per session, between the **admin3** design alone and **admin3 with the classic admin design as a fallback**, for
legacy workflows that need the classic server-rendered templates. The switch is a button in the admin toolbar (under the developer controls, directly beneath
**Clear cache**) and a module endpoint that toggles the mode in the session.

Runtime behaviour: with classic **enabled**, `AdditionalSiteDesignList` is `admin3, admin_classic, admin`; with it **disabled**, `admin3, admin`. `SiteDesign` stays `admin3`
in both cases. The extension uses a **kernel override** to apply the session value, so global configuration is never changed destructively.

## Set it up

1. Activate: `ActiveExtensions[]=sevenx_themes_admin_classic`.
2. Allow kernel overrides in `config.php`: `define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );`
3. `php ./bin/php/ezpgenerateautoloads.php --kernel-override`
4. In the admin siteaccess add the cached view preferences the extension's subitems controls need (the README gives the exact `CachedViewPreferences` block), and bind
   the shared admin siteaccess name in `sevenxthemesadminclassic.ini`.

| Key (block `SevenXThemesAdminClassicSettings`) | Default | Meaning |
|---|---|---|
| `SharedAdminSiteaccessName` | `sevenx_site_admin` | Admin siteaccess the switch applies to |
| `SwitchAdminDesignSessionVariableName` | `SevenXThemesAdminClassicDesignEnabled` | Session variable of the mode |
| `SubitemsDefaultLimit`, `SubitemsMaxLimit` | `200`, `5000` | Sub items per page in classic mode |

Keep environment specific values in `settings/override`, not in the extension's defaults. The template cache is cleared after each switch or reset.

## Classic sub items controls

In classic mode a window control enables or disables the **Sub items** display, and the paging uses a preference-driven limit (clickable presets and a custom input with **Set**
for larger lists) instead of a hardcoded low limit.

## Releases

* 0.2.0 (20 June 2026): the first public release: design switching, cache-safe runtime, classic sub items controls; release notes in the repository.
* 0.2.1: GPL v2 license files. 0.2.2: documentation of the required `CachedViewPreferences` for admin siteaccesses. 0.2.3: the `switchadmindesign` policy omit list stays commented
  in the extension's defaults. 0.2.4: the switch redirect preserves the current URI and is hardened; documentation of toolbar and policy behaviour.
* 21 June 2026: small toolbar position improvements.

Version check: the release notes (`RELEASE_NOTES_v0.2.0.md` to `RELEASE_NOTES_v0.2.4.md`) and the tags go to 0.2.4, but `ezinfo.php` still states `'Version' => '0.1.0'` and the
`extension.xml` is an older format (`version="0.1.0"` as an attribute, no `<metadata>`), so the about page (`/ezinfo/about`) shows 0.1.0 whatever release you installed. Read the release from the tag (`git describe --tags` in the clone) until
the metadata is brought in line with the release (see the [extension metadata specification](../../../specifications/6.0/extension-metadata.md)).

## Related

* [Sub items table options](../../../bc/6.0/subitems-table-options.md)
* [Chronicle](../../../history/extensions/sevenx_themes_admin_classic.md) and [release notes](../../../changelogs/extensions/sevenx_themes_admin_classic.md)
* [Change ledger](../../../history/ledger/sevenx_themes_admin_classic.md)
* [Month: 2026-06 (all extensions)](../../../history/extensions/months/2026-06.md)
