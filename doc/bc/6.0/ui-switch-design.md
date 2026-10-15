# The administration design switch: what changed for developers

Read this page if you maintain an administration design, an extension that overrides kernel classes for the
administration, code that reads the design settings or the design-base cache, or an installation that uses
`sevenx_themes_admin_classic`. It lists the new class, the cache key change, the behavior on Velocity's persistent
workers, and what replaces the kernel overrides of `sevenx_themes_admin_classic`. Since 15 October 2026.

The user side is in [the feature page](../../features/6.0/ui-switch-design.md), the settings in
[the guide](../../guides/ui-switch-design.md). The framework this builds on (registry, module `uiswitch`, policies,
session storage, cache variant key) is described in [UI switch: developer notes](ui-switch.md).

## In short

- New kernel class `expUISwitchDesignProvider` (`kernel/classes/uiswitch/expuiswitchdesignprovider.php`), the UI
  switch provider `design`. It sets `site.ini [DesignSettings] SiteDesign` and `AdditionalSiteDesignList[]` in memory
  for the requests of a user who chose another administration design.
- New static `eZTemplateDesignResource::designBaseCachePath( $siteAccess = false )`. The design-base cache file name
  of the current siteaccess includes the UI switch variant key **only when a choice is applied**. Without a choice
  the file name is byte-identical to before. A siteaccess asked for by name is never switched.
- Compiled templates, `{cache-block}` and the content view cache are separated by the UI switch variant key of
  [the framework](ui-switch.md). The template override cache was already keyed by the design list.
- A switch clears no cache: no `eZCache::clearByTag()`.
- `apply()` and `reset()` clear the design statics and globals, so a persistent Velocity worker never carries one
  user's design into the next request.
- The kernel overrides of `sevenx_themes_admin_classic` (`ezpKernelWeb`, `global_functions.php`) are obsolete as of
  its 0.2.5 and must be removed.

## The provider class

`expUISwitchDesignProvider extends expUISwitchProvider` (identifier `design`, policy `uiswitch/design`, settings group
`uiswitch.ini [UISwitch_design]`).

| Method | Behavior |
|---|---|
| `__construct( $identifier, array $settings = array(), $siteDefault = null, $root = '', ?array $designExtensions = null )` | The last three arguments exist for tests: the siteaccess's design, the installation root and the design extensions. In the kernel they are read from the settings. |
| `defaultValue()` | The siteaccess's `[DesignSettings] SiteDesign`, read once before any `apply()`. |
| `allowedValues()` | The default first, then each `AllowedValues[]` entry that is a design name (`/^[a-z][a-z0-9_]{0,63}$/`), has a `DesignChain[]` entry and exists as `design/<design>` or `design/<design>` of an active design extension (`design.ini [ExtensionSettings] DesignExtensions[]`). |
| `chain( $design )` | `DesignChain[<design>]` split on `;`, trimmed, de-duplicated, without the design itself and without names that are not design names; `array()` for an empty chain, `null` when the design has no entry. |
| `isValid( $value )` | Strict membership of `allowedValues()` (inherited). Nothing from the request is used as a path. |
| `label( $value )` | `DesignLabel[<design>]` through `ezpI18n::tr( 'kernel/uiswitch', ... )`; the design name without one. |
| `title()` | "Administration design", context `kernel/uiswitch`. |
| `apply( $value )` | Refuses an invalid value or the default. Otherwise sets `SiteDesign` and `AdditionalSiteDesignList` on `eZINI::instance()` (the request's instance) with `setVariable()`, then clears the design state (below). Writes no file and changes nothing process-wide. |
| `reset()` | Clears the design state and forgets the applied value. |
| `currentValue()` | The value applied in this request, else the default (inherited). |
| `cacheVariantKey( $value )` | `''` for the default, else `design-<design>` (inherited). |

Clearing the design state means `eZTemplateDesignResource::clearInMemoryCache()` (the `eZTemplateDesignSetting`
global, the static override array and match-file caches, the `eZOverrideTemplateCacheMap` global) and unsetting
`$GLOBALS['eZTemplateDesignResourceBases']`. The bases of siteaccesses asked for by name
(`eZTemplateDesignResourceSiteAccessBases`) are not touched: they never follow a switch.

The registry calls `apply()` from `ezpKernelWeb::requestInit()`, after the session start and before the login pre-check,
the request/input event, `eZLocale` and any template, so `$site.design` and every template see the chosen design.
It is not called in the setup wizard (`validity-check-required`), and `eZTemplateDesignResource::designSetting( 'site' )`
still returns `site-design-override` first where that is set.

## The design-base cache key

`eZTemplateDesignResource::allDesignBases()` caches the list of design directories in
`<cache>/<DESIGN_BASE_CACHE_NAME><md5>.php` when `site.ini [DesignSettings] DesignLocationCache=enabled`. The file name
now comes from the new public static method:

```php
static function designBaseCachePath( $siteAccess = false )
{
    $siteAccessName = $siteAccess ? (string)$siteAccess : ( $GLOBALS['eZCurrentAccess']['name'] ?? '' );
    if ( !$siteAccess && !empty( $GLOBALS['expUISwitchVariantKey'] ) )
        $siteAccessName .= '-' . $GLOBALS['expUISwitchVariantKey'];

    return eZSys::cacheDirectory() . '/' . self::DESIGN_BASE_CACHE_NAME . md5( $siteAccessName ) . '.php';
}
```

- No choice applied (the variant key is `''`): `md5( <siteaccess> )`, the same file as before.
- A choice applied: `md5( <siteaccess> . '-' . <variant key> )`, for example `md5( 'admin-design-admin4' )`.
- `$siteAccess` given: `md5( $siteAccess )`, whatever the current user chose. That list is read from the siteaccess's
  settings on disk, which a switch never changes.

The variant key is the request's whole key, providers sorted by identifier and joined with `.`
(`design-admin4.language-ger-DE`). A user who changed only the interface language therefore also gets a design-base
cache file of their own; its content equals the default one.

Code that built the file name itself (`DESIGN_BASE_CACHE_NAME . md5( $siteaccess )`) still finds the default file;
call `designBaseCachePath()` to get the file of the current request.

Before this change `sevenx_themes_admin_classic` set `DesignLocationCache=disabled` in memory for its siteaccess,
because a per-session design list cannot share one cache file per siteaccess. The kernel keeps the cache on.

## The other caches

| Cache | Key | Change |
|---|---|---|
| Design-base cache | siteaccess | + variant key when a choice is applied (above) |
| Template override cache (`override/override_<md5>.php`) | md5 of `overrideKeys()`: siteaccess, standard design, `SiteDesign`, `AdditionalSiteDesignList[]`, design extensions | none: already one file per design list |
| Compiled templates (`eZTemplateCompiler::compilationFilename()`) | template, charset, locale, access, page layout, index file | + `-<variant key>` when one is applied ([UI switch](ui-switch.md)). Needed because compiled code holds resolved includes. |
| `{cache-block}` (`eZTemplateCacheFunction`) | placement, siteaccess, keys | siteaccess + `-<variant key>`, read at run time from `$GLOBALS['expUISwitchVariantKey']` |
| Content view cache (`eZNodeviewfunctions`) | hash with the siteaccess | + hash entry `uiswitch` |
| Role-aware HTTP page cache, Velocity response cache | | a page rendered with a choice is not stored |
| ezjscore packer | file list | none: each design's `page_head_style.tpl` names its own files |

A switch calls no `eZCache::clearByTag()`. The extension cleared the `template` and `template-block` caches of the
whole installation on every switch and on every reset; with the keys above every design has caches of its own, and
the caches of users without a choice are untouched.

## Velocity and other persistent workers

A Velocity worker serves many requests in one process. Per request, `index.php` builds a new `ezpKernelWeb`, whose
`eZINI::resetAllInstances()` gives fresh INI instances, so an applied design never stays in the settings. What stays
in a process is static and global state:

- `expUISwitch::beginRequest()`, at the start of every `requestInit()`, calls `reset()` of every provider that applied
  a value earlier in the process and sets the variant key to `''`.
- `expUISwitchDesignProvider::reset()` and `apply()` clear the design statics and globals listed above. The static
  override array is the one that once served a compiled admin page layout with a public header (24 September 2026).
- The registry skips a provider whose class a worker does not know (`class_exists`), so naming the class in the
  settings before the workers were restarted does not end requests. Regenerate autoloads and deploy before the
  settings name a new provider class.

## Replacing the kernel overrides of sevenx_themes_admin_classic

Up to 0.2.4, `sevenx_themes_admin_classic` replaced two kernel files through `classes/kerneloverride/` and
`EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE`:

- `kernel/private/classes/ezpkernelweb.php`: an old copy of `ezpKernelWeb` with a block at the end of `__construct()`
  that switched the design. Everything else in the copy predates months of kernel changes (security headers, the
  form token 403 path, the audit header, Velocity fixes), so installing it rolls the kernel back.
- `kernel/private/classes/global_functions.php`: an old copy without design logic, shipped only because the copied
  `ezpKernelWeb` included it from its own directory.

Version 0.2.5 removes both, the module `switchadmindesign`, its toolbar tool and its toolbar resets, and registers
`admin_classic` with the kernel switch instead (`settings/uiswitch.ini.append.php`). On an installation that used the
overrides:

1. Update the extension to 0.2.5 or later. Make sure `extension/sevenx_themes_admin_classic/classes/kerneloverride/`
   is gone.
2. Regenerate the autoloads **without** `-o` / `--kernel-override`:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   ```

   If `var/autoload/ezp_override.php` still maps `ezpKernelWeb` into the extension, regenerate it with `-o` after the
   files are gone, or move it aside; leave `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` off in `config.php` unless another
   extension needs it.
3. Remove a `PolicyOmitList[]=switchadmindesign` entry from your own settings if you added one, and replace the policy
   `switchadmindesign/switch` in your roles by `uiswitch/design`.
4. Deploy (`./console exp:velocity deploy --allow-root-user`).

| Old extension behavior | Kernel behavior now |
|---|---|
| Design switched in `ezpKernelWeb::__construct()`, reading the session before the session was started | `expUISwitch::applyRequest()` in `requestInit()`, after the session start; never starts a session |
| Only one siteaccess (`SharedAdminSiteaccessName`) | Every switch siteaccess (`uiswitch.ini [UISwitchSettings] SiteAccessList[]`, empty: every siteaccess with an administration design) |
| `SiteDesign=admin3` hard-coded | `SiteDesign` = the chosen design |
| `AdditionalSiteDesignList` taken from the session as an array | The session holds only the design name; the chain always comes from `DesignChain[]` |
| Allow-list hard-coded in the view (`admin_classic`, `admin3`) | `AllowedValues[]`, filtered by installed designs with a chain |
| No permission check when applying | `uiswitch/design` checked on every request; a choice that lost its policy or left the list is dropped |
| `DesignLocationCache=disabled` for the siteaccess | Cache kept; file name includes the variant key when a choice is applied |
| `eZCache::clearByTag( 'template' )` and `( 'template-block' )` on every switch | No cache cleared; caches keyed per design |
| Module `switchadmindesign/switch` (POST) and `reset` (GET, state-changing) | `uiswitch/set` (POST only, form token, cross-site refused, 400/403/404/405); choosing the default resets |
| No audit record | `access.uiswitch.change` (provider `design`, before, after, siteaccess) |
| Session only | Session, or also the user's preferences with `RememberPreference=enabled` |
| Toolbar tool `admin_switch_design` in `Toolbar_admin_developer`; extension `toolbar.ini` reset `AvailableToolArray[]` and `Toolbar_admin_right` | Kernel tool `admin_ui_switch` in `Toolbar_admin_right`, in admin4 and in the classic `admin` design; no toolbar reset |
| `global_functions.php` copy | Nothing: the kernel file is used |

## Checking an installation

- `grep -rn "kerneloverride" var/autoload/ezp_override.php` prints nothing (or the file does not exist).
- Without a choice, the design-base cache file of a siteaccess is still `<cache>/<DESIGN_BASE_CACHE_NAME>` followed by
  `md5( '<siteaccess>' )`.
- `php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchDesignProviderTest.php` passes (no database
  needed): allow-list, chains, labels, apply and reset, the design-base cache file name with and without a choice,
  and the registry setting the variant key.

## Related pages

- [Feature: choosing the administration design](../../features/6.0/ui-switch-design.md)
- [Guide: the administration design switch](../../guides/ui-switch-design.md)
- [UI switch: developer notes](ui-switch.md)
- [Velocity engines and deploying a PHP change](velocity-engines.md)
- [Extensions: behavior changes](extensions-behaviour-changes.md)
