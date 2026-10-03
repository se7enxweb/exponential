# birthday: a birthday datatype

`birthday` ("eZ Birthday") provides a datatype (`ezbirthday`) for a person's date of birth, stored so it can be sorted and searched, with the
usual class and object edit and view templates. It also registers an export handler (`eZBirthdayCsvHandler`) in `csv.ini`, which the export tool
[xrowextract](xrowextract.md) uses.

## What changed

* 1.3.0 (28 January 2024): package vendor.
* 1.3.1 (14 September 2026): the **CSV export handler no longer ends any request that loads it**. `eZBirthdayCsvHandler` extended the base handler
  of `bccie`, which declares `exportAttribute( &$attribute, $separationChar )`, while the export tool calls `exportAttribute( $attribute )` with
  one argument; the declaration was incompatible with its parent and PHP refused the class the moment anything autoloaded it (not a missing column:
  the request ended). The class now extends `XrowBaseHandler` like every other handler registered through that ini. The value still comes from
  `data_text`, because `objectAttributeContent()` returns an `eZBirthday` object.
* 1.3.2 (2 October): `ezinfo.php`, which the extension lacked, was added and `extension.xml` corrected, so the about page lists it.

## Languages

The extension carries translation files in `translations/<locale>/translation.ts`: fre-FR, ger-DE, nor-NO. The German file holds 24 messages (count `<message` in
`translations/ger-DE/translation.ts`). The texts are looked up in the context(s) `design/standard/class/datatype`, `design/standard/content/datatype` and `kernel/classes/datatypes`. `./console exp:ezchecktranslation ger-DE` prints statistics of the kernel's
`share/translations/ger-DE/translation.ts` (not of this extension's file). After editing a file, refresh the compiled translation cache with `./console exp:ezgeneratetranslationcache`
and clearing the template and content caches.

## Related

* [xrowextract](xrowextract.md), [bccie](bccie.md)
* [Chronicle](../../../history/extensions/birthday.md) and [release notes](../../../changelogs/extensions/birthday.md)
* [Change ledger](../../../history/ledger/birthday.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
* [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
