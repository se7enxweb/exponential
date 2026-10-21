---
title: Reading settings with eZINI
audience: php-developer, developer
category: php-development
order: 170
summary: The eZINI API for PHP developers - instance(), variable(), hasVariable(), variableArray(), group() and hasGroup(), the order of the settings layers, environment variants, arrays and empty resets, defaults for missing keys, writing settings from code, caches, and settings under persistent workers.
keywords: eZINI, instance, variable, hasVariable, variableArray, group, hasGroup, setVariable, settings layers, siteaccess, override, EXP_ENV, environment variant, ini cache, clear-tag=ini, exp:ini, append.php
type: help
---
# Reading settings with eZINI

This page is for PHP developers who read configuration in their code. Every setting in Exponential is an INI value,
read through `eZINI` (`lib/ezutils/classes/ezini.php`), which merges the kernel's defaults, the extensions, the
siteaccess and the installation's overrides into one view. The extension-oriented reference is
[eZINI](../extension-development/part-8-reference/ezini.md); this page focuses on correct use in PHP code.

## Contents

1. [The calls](#the-calls)
2. [The layers](#the-layers)
3. [Environment variants](#environment-variants)
4. [Arrays](#arrays)
5. [Defaults for missing keys](#defaults-for-missing-keys)
6. [Your own settings file](#your-own-settings-file)
7. [Writing settings](#writing-settings)
8. [Caches and persistent workers](#caches-and-persistent-workers)

## The calls

```php
$ini = eZINI::instance();                          // site.ini
$ini = eZINI::instance( 'content.ini' );           // another file
$value  = $ini->variable( 'DebugSettings', 'DebugOutput' );      // a string
$has    = $ini->hasVariable( 'DebugSettings', 'DebugOutput' );   // bool
$list   = $ini->variable( 'ExtensionSettings', 'ActiveExtensions' ); // an array for Key[] settings
$parts  = $ini->variableArray( 'SomeBlock', 'SemicolonList' );   // a value split on ';'
$block  = $ini->group( 'DatabaseSettings' );                     // the whole block as an array
$exists = $ini->hasGroup( 'AcmeReportSettings' );
```

`eZINI::instance()` returns one shared object per file and directory, so calling it again is cheap.

## The layers

A value is read in this order; the last layer that sets it wins:

1. `settings/<file>.ini`, the kernel's defaults (or your extension's own `settings/<file>.ini` for a file of yours)
2. active extensions' `settings/<file>.ini.append.php`, in extension order
3. `settings/siteaccess/<siteaccess>/` and the extensions' `settings/siteaccess/<siteaccess>/`
4. extensions' `settings/override/`
5. `settings/override/<file>.ini.append.php` of the installation

The order of extensions matters when two extensions set the same key
([Extension load order](../extension-development/part-3-extension-developer/extension-load-order.md),
[Settings scopes and their order](../extension-development/part-2-builder/settings-scopes-and-their-order.md)).

## Environment variants

When `config.env.php` defines `EXP_ENV` (for example `dev`, `test`, `prod`), `eZINI` also reads a variant of each file
with the environment name after `.ini`, after the standard file of the same layer:

```text
settings/override/site.ini.append.php
settings/override/site.ini.test.append.php     read after it when EXP_ENV is 'test'
```

The variant overrides its own layer only, and each environment has its own INI cache. `eZINI::environment()` returns the
name; templates read it with `{exp_environment()}` ([Environment settings](../../features/6.0/environment-settings.md)).

## Arrays

```ini
[AcmeReportSettings]
ClassList[]
ClassList[]=article
ClassList[]=event
```

- `Key[]=value` lines add up across layers.
- An empty `Key[]` line clears everything set before it, in earlier layers too. Ship it as the first line of a list in
  your defaults, so an override can replace the list cleanly.
- `Key[name]=value` gives an associative array.

## Defaults for missing keys

`variable()` on a missing key writes an error ("Undefined variable" or "Undefined group") to the debug output and the
error log, and returns `false`. Code that reads optional or new keys must check:

```php
$ini = eZINI::instance( 'acmereport.ini' );
$limit = $ini->hasVariable( 'ListSettings', 'DefaultLimit' )
    ? max( 1, min( 100, (int)$ini->variable( 'ListSettings', 'DefaultLimit' ) ) )
    : 10;
```

Always cast and bound values from settings, like any input: settings are written by people.

## Your own settings file

Ship `extension/acmereport/settings/acmereport.ini` with every key and a comment for each. Changes to the kernel's
files go into `*.ini.append.php`, wrapped in a PHP comment so the web server never shows them:

```ini
<?php /* #?ini charset="utf-8"?
[ModuleSettings]
ExtensionRepositories[]=acmereport
*/ ?>
```

## Writing settings

`$ini->setVariable( $block, $name, $value )` changes the value in memory for this request; `save()` writes a file. For
changing settings in a file, use the `exp:ini` engine, which edits line by line and keeps comments
([Change settings from the command line](../../features/6.0/exp-ini-command.md), [exp:ini](../../bc/6.0/console-exp-ini.md)):

```bash
php bin/php/console exp:ini get acmereport.ini/ListSettings/DefaultLimit
php bin/php/console exp:ini set acmereport.ini/ListSettings/DefaultLimit 20 global
```

Never write secrets into a file your extension ships; read them from `settings/override/` of the installation.

## Caches and persistent workers

- Settings are compiled into the INI cache. After a change: `php bin/php/ezcache.php --clear-tag=ini`. `--clear-id=ini`
  misses the global INI cache.
- Under Velocity, a worker reads settings once and keeps them; `exp:velocity deploy` clears the INI cache and restarts
  the workers in the right order ([Velocity persistent workers for PHP developers](velocity-persistent-workers-for-php-developers.md)).
- Do not keep an `eZINI` value in a static property of your own across requests; read it from `eZINI::instance()`
  where you need it ([Static state traps](static-state-traps.md)).

## Related pages

- [Settings and INI files in your application](../creators/app-creators/settings-and-ini-files.md)
- [INI files for extension authors](../extension-development/part-8-reference/ini-files-for-extension-authors.md)
- [eZINI preserves comments](../../bc/6.0/eZINI_PRESERVES_COMMENTS.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
