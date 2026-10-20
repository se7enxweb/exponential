# swark: template operators and workflow events that are missing

This page is for template authors and site builders. `swark` adds many template operators and two workflow events
that are needed often but are missing in the kernel. It comes from Seeds Consulting AS (2008) and Brookins
Consulting.

## Operators

`add_view_parameters`, `array_search`, `arsort`, `asort`, `charset`, `clear_object_cache`, `cookie`,
`current_layout`, `current_siteaccess`, `debug`, `debug_attributes`, `is_post_request`, `json_encode`, `krsort`,
`ksort`, `ltrim`, `modify_view_parameter`, `preg_match`, `preg_replace`, `range`, `redirect`,
`remove_array_element`, `return`, `rsort`, `rtrim`, `serialize`, `server`, `set_array_element`, `shortenw`,
`shuffle`, `sort`, `split_by_length`, `str_replace`, `strpos`, `strrpos` and more.

Each operator has a page in the extension's `doc/operators` folder.

## Workflow events

| Event | What it does | Setting |
|---|---|---|
| **AutoPriority** | When an object is published, gives it a priority one step above its siblings. | `swark.ini [AutoPriority] PriorityIncrement=10` |
| **DeferToCron** | Defers a workflow to the cronjob. | none |

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 1.0.2 | 7 January 2024 | `composer.json`. |
| 1.0.3 | 27 September 2026 | `ezinfo.php` has a static `info()` and states the version, license and website; the documentation writes its line break as `<br>`. |
| 1.0.4 | 30 September 2026 | The description names Exponential. |

## Related pages

- [owsimpleoperator](owsimpleoperator.md)
- [String template operators](../string-template-operators.md)
- [CLI, cronjob and view abstractions](../../../bc/6.0/cli_cronjob_view_abstractions.md)
- [Extension metadata specification](../../../specifications/6.0/extension-metadata.md)
- [Chronicle](../../../history/extensions/swark.md) and [release notes](../../../changelogs/extensions/swark.md)
- [Change ledger](../../../history/ledger/swark.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
