# exp_enhanced_link: the enhanced link datatype

This page is for site builders who install Netgen based content classes or demo data. `exp_enhanced_link`
("Exponential Enhanced Link") provides the `ngenhancedlink` field type of the Netgen packages for Exponential 6, so
that content classes and demo data that use it can be installed and rendered.

## What one attribute holds

- an **internal** target (a content or location id) or an **external** URL;
- a **target mode**: `link`, `link_new_tab`, `embed` or `modal`;
- an optional **label** and an optional URL **suffix**.

The value is stored as JSON in `data_text` with a `sort_key_string` reference, which matches the Netgen persistence
format. The datatype implements `fromString()` and `toString()` for package import and export, supports search
indexing (`metaData()`, `sortKey()`, `title()`), and ships basic admin edit and view templates.

## Set it up

It needs the PHP `json` extension. After activating the extension, run:

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

Then add an attribute of the enhanced link type to a class in **Setup > Classes**.

## What changed

| Version | Date | Change |
|---|---|---|
| 1.0.0 | 7 August 2026 | First import. |
| 1.0.1 | | An `object_serialize_map` declares `data_text` as `text`, so link values are kept when content is exported, duplicated or copied (before, they were lost during serialization). |
| 1.0.2 | | `serializeContentClassAttribute()` and `unserializeContentClassAttribute()` keep the class setting `data_text5` when classes are exported and imported with the package system. |
| 1.0.3, 1.0.4 | | Translations with German; the about page names the extension "Exponential Enhanced Link" (it used the directory name). |

## Related pages

- [Chronicle](../../../history/extensions/exp_enhanced_link.md) and [release notes](../../../changelogs/extensions/exp_enhanced_link.md)
- [Change ledger](../../../history/ledger/exp_enhanced_link.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-08](../../../history/extensions/months/2026-08.md), [2026-09](../../../history/extensions/months/2026-09.md) (all extensions)
