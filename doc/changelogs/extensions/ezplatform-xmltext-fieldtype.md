# ezplatform-xmltext-fieldtype: release notes

Read this page before you install or update `ezplatform-xmltext-fieldtype`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/ezplatform-xmltext-fieldtype.md).

## v2.0.3 (2026-03-25)

**Renamed**

- ezsystems/ -> se7enxweb/; expand PHP to ^8.5; add replace (`26123b1`)

## After the last tag

**Updated**

- Ibexa 4.6 compat - FieldTypeParentCompatibilityPass, dual extension support, ezrichtext aliases (`7a26416`)
- Update persistence converter DI tag to ibexa.field_type.storage.legacy.converter (`f7680a9`)
- Update all DI service tags from ezplatform. to ibexa. namespace (`ee23086`)
- Update ezurl storage gateway service ID to Ibexa FQCN (`9da623f`)
- Update all service IDs to Ibexa 4.6 naming conventions (`2730518`)
- Use single quotes for FQCN service ID in YAML (backslash escape) (`d28c30d`)

## Related pages

- [Package map and upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
