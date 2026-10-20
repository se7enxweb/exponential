# birthday: a birthday datatype

This page is for site builders who store people's dates of birth. `birthday` ("eZ Birthday") provides the datatype
`ezbirthday`. It stores a date of birth so that it can be sorted and searched, and ships the usual class and object
edit and view templates.

It also registers an export handler, `eZBirthdayCsvHandler`, in `csv.ini`. The export tool
[xrowextract](xrowextract.md) uses it.

## Use it

1. Activate the extension.
2. Edit a content class (**Setup > Classes**) and add an attribute of the birthday datatype.
3. Edit an object of that class and enter a date of birth.

## What changed

| Version | Date | Change |
|---|---|---|
| 1.3.0 | 28 January 2024 | Package vendor. |
| 1.3.1 | 14 September 2026 | The **CSV export handler no longer ends any request that loads it** (see below). |
| 1.3.2 | 2 October 2026 | `ezinfo.php`, which the extension lacked, was added and `extension.xml` corrected, so the about page lists it. |

About 1.3.1: `eZBirthdayCsvHandler` extended the base handler of `bccie`, which declares
`exportAttribute( &$attribute, $separationChar )`, while the export tool calls `exportAttribute( $attribute )` with
one argument. The declaration was incompatible with its parent, and PHP refused the class the moment anything
autoloaded it: the request ended. The class now extends `XrowBaseHandler`, like every other handler registered
through that INI file. The value still comes from `data_text`, because `objectAttributeContent()` returns an
`eZBirthday` object.

## Languages

The extension carries translation files in `translations/<locale>/translation.ts`: fre-FR, ger-DE and nor-NO. The
German file holds 24 messages (count `<message` in `translations/ger-DE/translation.ts`). The texts are looked up in
the contexts `design/standard/class/datatype`, `design/standard/content/datatype` and `kernel/classes/datatypes`.

After editing a translation file:

1. Refresh the compiled translation cache: `./console exp:ezgeneratetranslationcache`.
2. Clear the template and content caches.

`./console exp:ezchecktranslation ger-DE` prints statistics of the kernel's `share/translations/ger-DE/translation.ts`,
not of this extension's file.

## Related pages

- [xrowextract](xrowextract.md), [bccie](bccie.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/birthday.md) and [release notes](../../../changelogs/extensions/birthday.md)
- [Change ledger](../../../history/ledger/birthday.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
