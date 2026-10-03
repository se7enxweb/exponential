# Specification: INI override directories and placements

Reference for how `eZINI` finds and orders the files of one INI setting, with the
multi-site extension directories added in January 2026 (commits `124186d096`,
`fc1a2f0a91`). The task-oriented page is
[Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md).

## Scopes and order

`eZINI` keeps override directories in four scopes. `eZINI::overrideDirs()` merges
them as `sa-extension`, `siteaccess`, `extension`, `override`; files are parsed in
that order and a later file overrides an earlier one. Inside a scope, a directory
registered later with `prependOverrideDir()` stands **earlier** in the list and
therefore has **lower** priority.

`eZExtension::prependExtensionSiteAccesses()`
(`lib/ezutils/classes/ezextension.php`) registers, for an active siteaccess
that an extension owns, in this call order:

| Call order | Directory | Identifier | Scope | Resulting priority inside the scope |
|---|---|---|---|---|
| 1 | `extension/<ext>/settings/override/` | `ext-siteaccess-override:<ext>` | `siteaccess` | highest of the three |
| 2 | `extension/<ext>/settings/override__<group>/` (only if the siteaccess name contains `__`; `<group>` is the part before it) | `ext-siteaccess-override:<ext>:__group:<group>` | `siteaccess` | middle |
| 3 | `extension/<ext>/settings/siteaccess/<siteaccess>/` | the extension identifier | `siteaccess` | lowest of the three |

All three are registered only if `extension/<ext>/settings/siteaccess/<siteaccess>`
exists (one `file_exists` call per active extension siteaccess).

## Placements

`eZINI::findSettingPlacement()` names where a setting came from, by the number of
path segments of the file relative to the installation root:

| Segments | Example path | Placement |
|---|---|---|
| 2 | `settings/site.ini` | `default` |
| 3 | `settings/override/site.ini.append.php` | `override` |
| 4 | `extension/<ext>/settings/site.ini.append.php` | `extension:<ext>` |
| 4 | `settings/siteaccess/<sa>/site.ini.append.php` | `siteaccess` |
| 5 | `extension/<ext>/settings/override/site.ini.append.php` | `ext-siteaccess-override:<ext>` |
| 5 | `extension/<ext>/settings/override__<group>/site.ini.append.php` | `ext-siteaccess-override__<group>:<ext>` |
| 6 | `extension/<ext>/settings/siteaccess/<sa>/site.ini.append.php` | `ext-siteaccess:<ext>` |
| (injected) | settings injected at run time | `injected` |

## Constants and globals

| Name | Where | Effect |
|---|---|---|
| `$GLOBALS['eZINI_CONFIG_CACHE_DIR']` | read by `eZINI::loadCache()` | Directory of the compiled INI cache. When unset, `eZINI` sets it to its default, `var/cache/ini/` relative to the installation. Set it before the first INI is loaded to share one cache between projects of one installation. |
| `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST` | `define()` it as `true` | After the directories above are registered, every override directory of the scope `extension` that starts with `extension/site_` and does not belong to the extension of the active siteaccess is removed from the list for this request. |

## Compatibility

Without these directories and without the global nothing changes: the call that
registered `settings/siteaccess/<sa>/` before is now one of three, in the same
position relative to the other scopes.

## Related

[Console: exp:ini](../../features/6.0/exp-ini-command.md) reads and writes settings
with the same files; [INI preserves comments](../../bc/6.0/eZINI_PRESERVES_COMMENTS.md).

## See also

[Changelog 6.0.12](../../changelogs/6.0/6.0.12.md); [Chronicle: January 2026](../../history/2026/2026-01.md); feature page [Per-site settings inside extensions](../../features/6.0/multi-site-ini-overrides.md).
