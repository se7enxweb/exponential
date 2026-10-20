---
title: "eZINI: reading settings"
audience: developer
category: extension-development
order: 20
summary: The eZINI methods an extension uses - instance(), variable(), hasVariable(), variableArray(), group(), hasSection() - with their signatures, what they return when a setting is missing, and the template operator ezini().
keywords: eZINI, instance, variable, hasVariable, variableArray, variableMulti, group, hasSection, hasGroup, ezini operator, settings, ini
type: help
---
# eZINI: reading settings

Part 8, reference 2. `eZINI` (`lib/ezutils/classes/ezini.php`) reads INI files with all their overrides merged: the
default in `settings/`, the extensions' `settings/`, the siteaccess, `settings/override`. How the files and scopes
combine is explained in [How INI settings work](../part-2-builder/how-ini-settings-work.md) and
[Settings in an extension](../part-3-extension-developer/settings-in-an-extension.md).

## Getting an instance

```php
static function instance( $fileName = 'site.ini', $rootDir = 'settings', $useTextCodec = null, $useCache = null,
                          $useLocalOverrides = null, $directAccess = false, $addArrayDefinition = false )
```

Always use `eZINI::instance( 'myext.ini' )` with only the file name. The instance is shared for the request and has
every override applied. Do not use `new eZINI()`.

## Reading

| Method | Returns |
|---|---|
| `variable( $blockName, $varName )` | the value (a string, or an array for `Name[]=` settings); `false` and an error in the debug output if the group or setting is missing |
| `hasVariable( $blockName, $varName )` | `true` if the setting exists; use it before `variable()` for optional settings |
| `hasSection( $sectionName )`, `hasGroup( $blockName )` | `true` if the section exists |
| `group( $blockName )` | all settings of a section as an array |
| `variableArray( $blockName, $varName )` | each value of an array setting split at `;` into an array |
| `variableMulti( $blockName, $varNames, $signatures = array() )` | several settings of a section at once |
| `groups()` | every section with its settings |

`variable()` writes "Undefined variable: ... in group ..." to the error log when the setting is missing. For an
optional setting, check first:

```php
<?php
$ini = eZINI::instance( 'myext.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'Limit' ) ? (int)$ini->variable( 'ListSettings', 'Limit' ) : 10;
$enabled = $ini->hasVariable( 'ListSettings', 'Enabled' ) && $ini->variable( 'ListSettings', 'Enabled' ) === 'enabled';
$types = $ini->hasVariable( 'ListSettings', 'Types' ) ? (array)$ini->variable( 'ListSettings', 'Types' ) : array();
```

Settings are strings: cast numbers, compare switches with the string (`'enabled'`, `'true'`), and cast arrays.

## Static helpers

| Method | Use |
|---|---|
| `eZINI::exists( $fileName = "site.ini", $rootDir = "settings" )` | whether the file exists in that directory |
| `eZINI::isLoaded( $fileName, ... )` | whether an instance was already created |
| `eZINI::fetchFromFile( $fileName )` | read one file alone, without overrides |
| `eZINI::getSiteAccessIni( $siteAccess, $iniFile )` | the settings as another siteaccess sees them |

## Writing

Extensions should not write settings at run time. `setVariable()` changes the instance in memory for the current
request only. To change stored settings, use the console command `exp:ini`
([The exp:ini command](../part-2-builder/the-exp-ini-command.md)) or the settings pages of the administration; the
RAD catalogue lists "writing settings from code" as its own extension point for the rare case that needs it.

## In templates

```text
{ezini( 'ListSettings', 'Limit', 'myext.ini' )}
{if ezini_hasvariable( 'ListSettings', 'Limit', 'myext.ini' )} ... {/if}
```

`ezini()` behaves like `variable()`; check optional settings with `ezini_hasvariable()` first.

## Caches

Settings are cached. After changing an INI file, clear the INI cache: `php bin/php/ezcache.php --clear-tag=ini`.

## Related pages

- Previous: [Extension points at a glance](extension-points-at-a-glance.md)
- Next: [eZDebug and logging](ezdebug-and-logging.md)
- [Settings in an extension](../part-3-extension-developer/settings-in-an-extension.md)
- [INI files an extension author meets](ini-files-for-extension-authors.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
