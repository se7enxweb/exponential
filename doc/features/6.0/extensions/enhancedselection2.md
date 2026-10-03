# enhancedselection2: a selection that stores identifiers

`enhancedselection2` is a drop-down (or multiple-select) datatype like the kernel's `ezselection`, with one important difference: it
stores the **identifier** of the option that was chosen, not its position (ID). With `ezselection`, changing the options of the class
can make stored IDs point to the wrong option. With this datatype a stored value keeps meaning the same option as long as identifiers
do not change.

In the class editor each option has a human-readable label and an identifier (generated the way Exponential generates identifiers
if you leave it empty), and up and down buttons reorder the options; the drop-down uses the same order. Information collection is
supported. It started as the SCK-CEN extension (version 1.0) and was extended with more class-level features.

## What changed in the Exponential 6 releases

* 2.1.1, 2.1.2 (March 2024): package vendor switched to se7enxweb.
* 2.1.3 (27 September 2026): `ezinfo.php` states the extension's name, version, copyright, license and website.
* 2.1.4 (28 September): selections of an attribute that has **no id yet** (a new object) are not looked up, and ids are compared as
  integers, so **PostgreSQL** installs the content; every visible text is a translation string, with German; the templates translate
  with literal contexts instead of a context held in a variable.
* 2.1.5: the description names Exponential.
* 2.1.6, 2.1.7 (2 October): the edit and collect templates of a **multiple selection** parse again. A stray closing parenthesis made the
  template parser reject the checkbox list, so the edit form of a class using it showed no fields. Commands and cronjob parts list a
  description and use the shared command helpers.

If a class that uses a multiple selection showed an empty edit form before 2.1.6, update to 2.1.6 or later and clear the template caches.

## Related

* [Chronicle](../../../history/extensions/enhancedselection2.md) and [release notes](../../../changelogs/extensions/enhancedselection2.md)
* [xrowextract](xrowextract.md) exports and imports this datatype (`ezenhancedselection`)
