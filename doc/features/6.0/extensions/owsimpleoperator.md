# owsimpleoperator: write a template operator in a few lines

This page is for developers who need a small template operator, or want templates to call a PHP function. A full
operator class is often more work than the operator deserves. `owsimpleoperator` (from Open Wide) lets you write each
operator as a small class with plain methods: less boilerplate and more readable code. It also lets templates call
**PHP functions and class methods as operators**, but only those you list explicitly.

## What you get

- Call any permitted PHP function as a template operator.
- Utility methods for your PHP code: string manipulation, object attribute manipulation, object type control and
  output manipulation.

Constraints:

- An optional operator argument must default to `null`.
- For more than 10 arguments, override `$max_operator_parameter`.

## Allow a PHP function or class method

Nothing is callable unless you list it in `owsimpleoperator.ini`. This is what ships:

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

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `owsimpleoperator.ini` | `PHPFunctions` | `PermittedFunctionList[]` | the list above | PHP functions templates may call |
| `owsimpleoperator.ini` | `ClassOperators` | `PermittedClassOperatorList[]` | empty | Class methods templates may call |
| `owsimpleoperator.ini` | `OWSimpleOperatorSettings` | `DebugOutput` | `enabled` | Debug output |

**Security:** `file_get_contents` lets a template read any file the web server can read. Remove it from the list on
production sites that do not need it (the other entries are harmless). A function that is not permitted cannot be
called from a template.

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 1.2.0 | 7 January 2024 | `composer.json`, `ezinfo.php` and `extension.xml` versions. |
| 1.2.1 | | The extension name is lowercase to prevent errors. |
| 1.2.2 to 1.2.5 | 27 to 30 September 2026 | The extension states its version, license and website; the license is named in the metadata; the description names Exponential. |

## Related pages

- [swark](swark.md): ready-made operators in the same spirit
- [String template operators](../string-template-operators.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/owsimpleoperator.md) and [release notes](../../../changelogs/extensions/owsimpleoperator.md)
- [Change ledger](../../../history/ledger/owsimpleoperator.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2024-01](../../../history/extensions/months/2024-01.md), [2026-09](../../../history/extensions/months/2026-09.md) (all extensions)
