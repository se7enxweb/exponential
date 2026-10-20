# sevenx_themes_admin_classic: switch between admin3 and the classic fallback

This page is for administrators with legacy workflows that need the classic server-rendered admin templates.
`sevenx_themes_admin_classic` lets an administrator switch, per session, between:

- the **admin3** design alone: `AdditionalSiteDesignList` is `admin3, admin`;
- **admin3 with the classic admin design as a fallback**: `AdditionalSiteDesignList` is
  `admin3, admin_classic, admin`.

`SiteDesign` stays `admin3` in both cases. The extension applies the session value through a **kernel override**, so
global configuration is never changed.

## Set it up

1. Activate the extension: `ActiveExtensions[]=sevenx_themes_admin_classic`.
2. Allow kernel overrides in `config.php`:

```php
define( 'EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE', true );
```

3. Generate the kernel override autoloads:

```bash
php ./bin/php/ezpgenerateautoloads.php --kernel-override
```

4. In the admin siteaccess, add the cached view preferences the extension's sub-items controls need (the README gives
   the exact `CachedViewPreferences` block).
5. Bind the shared admin siteaccess name in `sevenxthemesadminclassic.ini` (see below). Keep environment specific
   values in `settings/override`, not in the extension's defaults.

## Use it

Click the switch button in the admin toolbar, under the developer controls, directly beneath **Clear cache**. A
module endpoint toggles the mode in the session, and the template cache is cleared after each switch or reset.

In classic mode a window control enables or disables the **Sub items** display. Paging uses a preference-driven limit
(clickable presets, and a custom input with **Set** for larger lists) instead of a hardcoded low limit.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `sevenxthemesadminclassic.ini` | `SevenXThemesAdminClassicSettings` | `SharedAdminSiteaccessName` | `sevenx_site_admin` | Admin siteaccess the switch applies to |
| `sevenxthemesadminclassic.ini` | `SevenXThemesAdminClassicSettings` | `SwitchAdminDesignSessionVariableName` | `SevenXThemesAdminClassicDesignEnabled` | Session variable of the mode |
| `sevenxthemesadminclassic.ini` | `SevenXThemesAdminClassicSettings` | `SubitemsDefaultLimit`, `SubitemsMaxLimit` | `200`, `5000` | Sub items per page in classic mode |

## Releases

| Version | Date | Change |
|---|---|---|
| 0.2.0 | 20 June 2026 | First public release: design switching, cache-safe runtime, classic sub-items controls; release notes in the repository. |
| 0.2.1 | | GPL v2 license files. |
| 0.2.2 | | Documentation of the required `CachedViewPreferences` for admin siteaccesses. |
| 0.2.3 | | The `switchadmindesign` policy omit list stays commented in the extension's defaults. |
| 0.2.4 | | The switch redirect keeps the current URI and is hardened; documentation of toolbar and policy behaviour. |
| | 21 June 2026 | Small toolbar position improvements. |

## Limits

The release notes (`RELEASE_NOTES_v0.2.0.md` to `RELEASE_NOTES_v0.2.4.md`) and the tags go to 0.2.4, but `ezinfo.php`
still states `'Version' => '0.1.0'`, and `extension.xml` is in an older format (`version="0.1.0"` as an attribute, no
`<metadata>`). The about page (`/ezinfo/about`) therefore shows 0.1.0 whatever release you installed. Read the release
from the tag (`git describe --tags` in the clone) until the metadata is brought in line (see the
[extension metadata specification](../../../specifications/6.0/extension-metadata.md)).

## Related pages

- [admin3: the responsive admin](../admin3-responsive-admin.md)
- [Sub items table options](../../../bc/6.0/subitems-table-options.md)
- [Chronicle](../../../history/extensions/sevenx_themes_admin_classic.md) and [release notes](../../../changelogs/extensions/sevenx_themes_admin_classic.md)
- [Change ledger](../../../history/ledger/sevenx_themes_admin_classic.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-06 (all extensions)](../../../history/extensions/months/2026-06.md)
