---
title: Registering operators
audience: developer, app-creator
category: development
order: 515
summary: Every way a template operator gets registered - eztemplateautoload.php in AutoloadPathList and ExtensionAutoloadPath, the entry keys, template.ini PHPOperatorList, owsimpleoperator.ini - how extension order is decided (ActiveExtensions, ActiveAccessExtensions, ExtensionOrdering and extension.xml dependencies), and what to clear afterwards.
keywords: register operator, eztemplateautoload.php, eZTemplateOperatorArray, eZTemplateFunctionArray, AutoloadPathList, ExtensionAutoloadPath, PHPOperatorList, ActiveExtensions, ActiveAccessExtensions, ExtensionOrdering, extension.xml, requires, uses, extends, cache
type: help
---
# Registering operators

An operator class does nothing until the template engine knows which names it answers. This page lists every
place that tells it, in `lib/eztemplate/classes/eztemplate.php` (`factory()`, `autoload()`) and the settings it
reads.

## Contents

1. [eztemplateautoload.php](#eztemplateautoloadphp)
2. [Where the engine looks: AutoloadPathList and ExtensionAutoloadPath](#where-the-engine-looks)
3. [The order of extensions](#the-order-of-extensions)
4. [PHP functions from template.ini](#php-functions-from-templateini)
5. [OW Simple Operator](#ow-simple-operator)
6. [Template functions](#template-functions)
7. [After registering](#after-registering)

## eztemplateautoload.php

The engine includes a file called `eztemplateautoload.php` from each folder on its list. The file sets two
arrays, both optional:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myextension/autoloads/myextensionoperators.php',
                                    'class' => 'myExtensionOperators',
                                    'operator_names' => array( 'myext_initials', 'myext_reading_time' ) );

$eZTemplateFunctionArray = array();
$eZTemplateFunctionArray[] = array( 'script' => 'extension/myextension/autoloads/myextensionfunction.php',
                                    'class' => 'myExtensionFunction',
                                    'function_names' => array( 'myext_box' ) );
```

The keys of an operator entry (`class`, `script`, `class_parameter`, `function`, `operator_names`,
`operator_names_function`) are explained in
[The operator class interface](operator-class-reference.md#the-registration-entry). Two rules:

- start each array with `= array();` (the engine unsets both variables before including the file, but a file that
  only appends is fragile when included elsewhere);
- the file runs on every request that creates the template engine: keep it to array definitions, no database or
  file access. (OW Simple Operator's file reads an INI file; that is cached.)

## Where the engine looks

`eZTemplate::factory()` builds the folder list from `site.ini` `[TemplateSettings]`:

```ini
[TemplateSettings]
AutoloadPathList[]
AutoloadPathList[]=lib/eztemplate/classes/
AutoloadPathList[]=kernel/common/
AutoloadPathList[]=lib/ezpdf/classes/
AutoloadPathList[]=kernel/private/eztemplate/
ExtensionAutoloadPath[]
```

- `AutoloadPathList[]` holds folders (with the trailing slash). The kernel's four come from `settings/site.ini`.
  An extension can add its folder here (extension bcwebsitestatistics does:
  `AutoloadPathList[]=extension/bcwebsitestatistics/autoloads/`), but the usual way is the next setting.
- `ExtensionAutoloadPath[]` holds extension **names**; the engine expands each to `extension/<name>/autoloads/`
  (`eZExtension::expandedPathList()`). Every extension with operators adds its own line in its
  `settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myextension

*/ ?>
```

Never start that list with an empty `ExtensionAutoloadPath[]` line in an extension: in an INI append an empty `[]`
line empties the list collected so far, and every extension merged before yours loses its operators.

A folder on either list without an `eztemplateautoload.php` gives the warning
`Path '...' does not have the file 'eztemplateautoload.php' allthough it reported it had one.` in the debug output.

## The order of extensions

The engine reads `AutoloadPathList[]` first, then `ExtensionAutoloadPath[]`, each in list order, and the last
registration of a name wins ([Registration order and overrides](registration-order-and-overrides.md)). The order
of `ExtensionAutoloadPath[]` is the order in which the extensions' `site.ini.append.php` files are merged:

1. `[ExtensionSettings] ActiveExtensions[]` (global extensions), then the siteaccess's
   `ActiveAccessExtensions[]` (access extensions);
2. with `[ExtensionSettings] ExtensionOrdering=enabled` (the default in `settings/site.ini`, and on a development installation), the
   list of active extensions is sorted by the dependencies each extension declares in its `extension.xml`
   (`eZExtension::extensionOrdering()`, `ezpExtension::getLoadingOrder()`):
   `<requires>` and `<uses>` make your extension load **after** the named one, `<extends>` **before** it.

```xml
<dependencies>
    <requires>
        <extension name="ezjscore" />
    </requires>
</dependencies>
```

So an extension that deliberately overrides another extension's operator should declare `<requires>` (or
`<uses>`) on it; overriding a kernel or template library operator needs no declaration, because extensions are
always read after `AutoloadPathList[]`.

## PHP functions from template.ini

`template.ini` `[PHP] PHPOperatorList[]` maps operator names to PHP functions that take one argument:

```ini
[PHP]
PHPOperatorList[]
PHPOperatorList[urlencode]=urlencode
PHPOperatorList[urldecode]=urldecode
PHPOperatorList[rawurlencode]=rawurlencode
PHPOperatorList[rawurldecode]=rawurldecode
```

That is the kernel's `settings/template.ini`. `kernel/common/eztemplateautoload.php` registers these names
through the functions `eZPHPOperatorNameInit()` (the names) and `eZPHPOperatorInit()` (one
`eZTemplatePHPOperator` object for all of them). Extensions add entries in their `template.ini.append.php`; on
a development installation cjw_newsletter adds `nl2br`, ezmultiupload `strip_tags`, swark `serialize` and `unserialize`,
bcgooglesitemaps `getenv`. Use key=value lines without a leading empty `PHPOperatorList[]` in an append, for the
reason above. What these operators do and their limits: [PHP function operators](php-function-operators.md).

## OW Simple Operator

Extension owsimpleoperator registers operators from `owsimpleoperator.ini` (`[PHPFunctions] PermittedFunctionList[]`
and `[ClassOperators] PermittedClassOperatorList[]`), through its own `eztemplateautoload.php`. See
[OW Simple Operator](owsimpleoperator.md).

## Template functions

Template functions use the same files and the same lists: `$eZTemplateFunctionArray` entries with `function_names`
(or `function_names_function`) instead of `operator_names`. See
[Custom template functions](../template-language/custom-template-functions.md).

## After registering

- `php -l` the autoload file and the class file.
- Clear the INI cache (the folder list comes from INI) and the template caches (compiled templates were compiled
  against the old list): `php bin/php/ezcache.php --clear-tag=ini,template --allow-root-user`.
- `php bin/php/ezpgenerateautoloads.php -e` only when you added classes that are loaded through the autoload
  arrays rather than through `script`.
- On a development installation: `./console exp:velocity deploy --allow-root-user` does all of this and restarts Velocity, whose workers
  keep the registrations they made when they started.
- Check the name on each siteaccess that uses it: `{'x'|myext_initials}` must not log
  `Operator 'myext_initials' is not registered`, and the [owner listing script](registration-order-and-overrides.md#the-names-registered-twice)
  must not show your name next to another class.

## Related pages

- [Registration order and overrides](registration-order-and-overrides.md)
- [The operator class interface](operator-class-reference.md)
- [Creating an operator, step by step](creating-an-operator-step-by-step.md)
- [Settings and INI files](../creators/app-creators/settings-and-ini-files.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
