# swark: template operators and workflow events that are missing

`swark` implements many template operators and a couple of workflow events that are needed very often yet are missing in the kernel. It
comes from Seeds Consulting AS (2008) and Brookins Consulting.

## Operators

`add_view_parameters`, `array_search`, `arsort`, `asort`, `charset`, `clear_object_cache`, `cookie`, `current_layout`, `current_siteaccess`,
`debug`, `debug_attributes`, `is_post_request`, `json_encode`, `krsort`, `ksort`, `ltrim`, `modify_view_parameter`, `preg_match`,
`preg_replace`, `range`, `redirect`, `remove_array_element`, `return`, `rsort`, `rtrim`, `serialize`, `server`, `set_array_element`, `shortenw`,
`shuffle`, `sort`, `split_by_length`, `str_replace`, `strpos`, `strrpos` and more. Each has a page in the extension's `doc/operators` folder.

## Workflow events

* **AutoPriority** (`swark.ini [AutoPriority] PriorityIncrement=10`): when an object is published, gives it a priority a step above its siblings.
* **DeferToCron**: defers a workflow to the cronjob.

## What changed in the Exponential 6 releases

* 1.0.2 (7 January 2024): `composer.json`.
* 1.0.3 (27 September 2026): `ezinfo.php` has a static `info()` and states the version, license and website; the documentation writes its
  line break as `<br>`.
* 1.0.4 (30 September): the description names Exponential.

## Related

* [owsimpleoperator](owsimpleoperator.md)
* [Chronicle](../../../history/extensions/swark.md) and [release notes](../../../changelogs/extensions/swark.md)
