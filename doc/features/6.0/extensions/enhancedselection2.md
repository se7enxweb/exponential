# enhancedselection2: a selection that stores identifiers

This page is for site builders who need a drop-down or multiple-select field whose stored values stay correct when
the options change. `enhancedselection2` is a selection datatype like the kernel's `ezselection`, with one important
difference: it stores the **identifier** of the chosen option, not its position (ID).

With `ezselection`, changing the options of the class can make stored IDs point to the wrong option. With this
datatype a stored value keeps meaning the same option as long as identifiers do not change.

## Use it

1. Edit a content class in **Setup > Classes** and add an attribute of this datatype.
2. Give each option a human-readable label and an identifier. Leave the identifier empty to have it generated the way
   Exponential generates identifiers.
3. Reorder the options with the up and down buttons; the drop-down uses the same order.

Information collection is supported. The datatype started as the SCK-CEN extension (version 1.0) and was extended
with more class-level features.

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 2.1.1, 2.1.2 | March 2024 | Package vendor switched to se7enxweb. |
| 2.1.3 | 27 September 2026 | `ezinfo.php` states the extension's name, version, copyright, license and website. |
| 2.1.4 | 28 September 2026 | Selections of an attribute that has **no id yet** (a new object) are not looked up, and ids are compared as integers, so **PostgreSQL** installs the content. Every visible text is a translation string, with German. The templates translate with literal contexts instead of a context held in a variable. |
| 2.1.5 | | The description names Exponential. |
| 2.1.6, 2.1.7 | 2 October 2026 | The edit and collect templates of a **multiple selection** parse again: a stray closing parenthesis made the template parser reject the checkbox list, so the edit form of a class using it showed no fields. Commands and cronjob parts list a description and use the shared command helpers. |

**Upgrade note:** if a class that uses a multiple selection showed an empty edit form before 2.1.6, update to 2.1.6
or later and clear the template caches.

## Related pages

- [xrowextract](xrowextract.md) exports and imports this datatype (`ezenhancedselection`)
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/enhancedselection2.md) and [release notes](../../../changelogs/extensions/enhancedselection2.md)
- [Change ledger](../../../history/ledger/enhancedselection2.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
