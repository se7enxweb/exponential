# owsimpleoperator: write a template operator in a few lines

Template operators are powerful, but sometimes you want a simple one and writing a full operator class is more work than the operator
deserves. `owsimpleoperator` (from Open Wide) lets you write each operator as a small class with plain methods, with less boilerplate and
more readable code, and also lets templates call **PHP functions and class methods as operators** that you list explicitly.

## What you get

* Call any permitted PHP function as a template operator.
* String manipulation, eZ object attribute manipulation, object type control and output manipulation utility methods for your PHP code.

Constraints: an optional operator argument must default to `null`; for more than 10 arguments override `$max_operator_parameter`.

## Allow a PHP function or class method (`owsimpleoperator.ini`)

Nothing is callable unless you list it:

```ini
[PHPFunctions]
PermittedFunctionList[]
PermittedFunctionList[]=time
PermittedFunctionList[]=mktime
PermittedFunctionList[]=getdate
PermittedFunctionList[]=str_replace
PermittedFunctionList[]=str_rot13
PermittedFunctionList[]=file_get_contents
[ClassOperators]
PermittedClassOperatorList[]
[OWSimpleOperatorSettings]
DebugOutput=enabled
```

The list above is what ships. `file_get_contents` in that list lets a template read any file the web server can read; remove it from
production lists you do not need it on (the other entries are harmless). A function that is not permitted cannot be called from a template.

## What changed in the Exponential 6 releases

* 1.2.0 (7 January 2024): `composer.json`, `ezinfo.php` and `extension.xml` versions.
* 1.2.1: the extension name is lowercase to prevent errors.
* 1.2.2 to 1.2.5 (27 to 30 September 2026): the extension states its version, license and website; the license is named in the metadata; the
  description names Exponential.

## Related

* [Swark](swark.md): ready-made operators in the same spirit
* [Chronicle](../../../history/extensions/owsimpleoperator.md) and [release notes](../../../changelogs/extensions/owsimpleoperator.md)
* [Change ledger](../../../history/ledger/owsimpleoperator.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2024-01 (all extensions)](../../../history/extensions/months/2024-01.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
