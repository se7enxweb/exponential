# exp_enhanced_link: the enhanced link datatype

`exp_enhanced_link` ("Exponential Enhanced Link") is the `ngenhancedlink` field type of the Netgen packages for Exponential 6, so that content
classes and demo data that use it can be installed and rendered. One attribute holds a link with:

* an **internal** target (a content or location id) or an **external** URL;
* a **target mode**: `link`, `link_new_tab`, `embed` or `modal`;
* an optional **label** and an optional URL **suffix**.

The value is stored as JSON in `data_text` with a `sort_key_string` reference, which matches the Netgen persistence format. The datatype
implements `fromString()` and `toString()` for package import and export, wires search indexing (`metaData()`, `sortKey()`, `title()`) and
ships basic admin edit and view templates. It needs the PHP `json` extension.

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all --allow-root-user
```

## What changed

* 1.0.0 (7 August 2026): first import.
* 1.0.1: an `object_serialize_map` declares `data_text` as `text`, so link values are kept when content is exported, duplicated or copied
  (before, they were lost during serialization).
* 1.0.2: `serializeContentClassAttribute()` and `unserializeContentClassAttribute()` preserve the class setting `data_text5` when classes are
  exported and imported with the package system.
* 1.0.3 to 1.0.4: translations with German; the about page names the extension "Exponential Enhanced Link" (it used the directory name).

## Related

* [Chronicle](../../../history/extensions/exp_enhanced_link.md) and [release notes](../../../changelogs/extensions/exp_enhanced_link.md)
