---
title: Settings in an extension
audience: developer, app-creator
category: extension-development
order: 70
summary: Settings an extension ships: registration lines in kernel INI files, your own INI file with documented defaults, reading it safely with eZINI, per-siteaccess settings and secrets.
keywords: settings, ini, extension settings, own ini file, eZINI, variable, hasVariable, defaults, override, ini.append.php, registration, design.ini, module.ini
type: help
---
# Settings in an extension

Part 3, chapter 7. Almost every extension has a `settings/` folder. It does two things: it **registers** the
extension's parts with the kernel (modules, designs, datatypes...), and it ships the extension's **own options** with
sensible defaults. This chapter covers both.

## Registration settings

These are lines in kernel INI files that point the kernel at your extension. A typical set:

`settings/module.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ModuleSettings]
ExtensionRepositories[]=mysite_hello

*/ ?>
```

`settings/design.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[ExtensionSettings]
DesignExtensions[]=mysite_hello

*/ ?>
```

`settings/site.ini.append.php` (template operators):

```php
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=mysite_hello

*/ ?>
```

The full table of registration settings is in [The extension directory layout](extension-directory-layout.md). They
are read only while the extension is active, because only an active extension's `settings/` is an override directory.

## Your own settings file

Give your options their own file named after the extension, `settings/mysite_hello.ini.append.php`, with every
option documented and given a safe default:

```php
<?php /* #?ini charset="utf-8"?

[HelloSettings]
# The greeting shown when no name is given.
DefaultName=world
# How many greetings the list view shows at most.
ListLimit=10
# Names that may not be greeted (one per line).
BlockedNames[]

*/ ?>
```

In a settings directory the kernel reads `<file>`, `<file>.php`, `<file>.append.php` and the deprecated
`<file>.append` (`eZINI` in `lib/ezutils/classes/ezini.php`). Use `<file>.append.php`, as every shipped extension
does; it protects the file from being served to a browser.

An installation changes your options without touching your extension, in its override or siteaccess directory:

```bash
./console exp:ini set mysite_hello.ini/HelloSettings/ListLimit 25 global
```

## Reading settings in PHP

```php
<?php
$ini = eZINI::instance( 'mysite_hello.ini' );

$name  = $ini->variable( 'HelloSettings', 'DefaultName' );
$limit = $ini->hasVariable( 'HelloSettings', 'ListLimit' )
       ? (int)$ini->variable( 'HelloSettings', 'ListLimit' )
       : 10;
$blocked = $ini->variable( 'HelloSettings', 'BlockedNames' );   // an array
```

| Rule | Why |
|---|---|
| Use `hasVariable()` before `variable()` for optional settings | a missing variable or block is written to the error log ("Undefined variable ... in group ...") and `variable()` returns `false` |
| Cast numbers (`(int)`) | values are strings |
| Compare switches with the exact string (`=== 'enabled'`) | there is no boolean type in INI |
| Never cache an `eZINI` value in a static property of your own | under a persistent server (Velocity) a static outlives the request; `eZINI` handles its own cache |

`eZINI::instance( $fileName = 'site.ini', $rootDir = 'settings', ... )` is the signature; the defaults are what you
want almost always. The full API is in eZINI: reading settings.

## Reading settings in templates

```text
{ezini( 'HelloSettings', 'DefaultName', 'mysite_hello.ini' )|wash}
```

## Per-siteaccess settings in an extension

An extension can carry settings for a siteaccess in `settings/siteaccess/<sa>/`, and group settings in
`settings/override__<group>/`. See [Settings scopes and the order they win in](../part-2-builder/settings-scopes-and-their-order.md).

## Secrets

An extension that needs an API key ships the setting **empty** with a comment, and the installation fills it in
`settings/override/`. Name it so it is masked (`...Key`, `Token`, `Secret`, `Password`). Never commit a real key; the
package download refuses to pack an extension that holds one
([the secret check](../../../bc/6.0/extension-package-secret-check.md)).

## Settings the RAD tools write

The **Settings extension** wizard (`/setup/settingsextension`) writes a settings-only extension, including event
listeners and siteaccess customization points; `exp:ini move-all ... --create-extension` moves existing settings into
one. See [With RAD: a settings-only extension](tutorials/rad-settings-extension.md).

## Related pages

- Previous: [composer.json for an extension](composer-json-for-extensions.md)
- Next: [Autoloads and class naming](autoloads-and-class-naming.md)
- [How INI settings work](../part-2-builder/how-ini-settings-work.md)
- [Environment settings](../../../features/6.0/environment-settings.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
