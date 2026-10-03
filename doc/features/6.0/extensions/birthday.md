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

## Related

* [xrowextract](xrowextract.md), [bccie](bccie.md)
* [Chronicle](../../../history/extensions/birthday.md) and [release notes](../../../changelogs/extensions/birthday.md)
