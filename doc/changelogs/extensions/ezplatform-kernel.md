# ezplatform-kernel: release notes

Read this page before you install or update `ezplatform-kernel`, or to find out which release brought a change.

Releases made by the se7enxweb team, newest first. Each release lists the team's changes since the previous tag, grouped as Added, Updated, Removed and Renamed. Upstream history carried by the fork is not repeated here; it is in the [repository page](../../history/ecosystem/ezplatform-kernel.md).

## v1.3.45 (2026-04-12)

**Added**

- Unescape backslash-escaped quotes and newlines in SQLite cleandata.sql (`f1970779c`)
- Remove autoincrement from ezcontentobject_attribute.id for SQLite composite PK compatibility (`eac6c8b77`)
- Skip doctrine:database:create for SQLite in checkCreateDatabase() (`b32c2d6ae`)
- Recreate composite-PK tables after Doctrine schema generation (`a5a32b66f`)

**Updated**

- Switch ezsystems/doctrine-dbal-schema to se7enxweb/doctrine-dbal-schema (`77524ca4e`)

**Renamed**

- Rename commands to exponential:* prefix, keep ibexa:* and ezplatform:* as deprecated aliases (`4b90bbf4d`)

## v1.3.44 (2026-03-29)

**Added**

- Add branch-alias for 1.3-se7enx (`9b341b163`)

**Updated**

- Append 73 legacy Exponential schema tables to ibexa-oss cleandata.sql (`efaf3e785`)

**Removed**

- Remove se7enx branch-alias: prevent auto-resolution by external projects (`7c7b12577`)

## v1.3.43 (2026-03-26)

**Added**

- Add missing configuration parsers for Content and User Settings views (`9ab6f7357`)
- Add replace for ezsystems/ezplatform-kernel + ibexa/core, add PHP 8.5 support (`021397cff`)

**Renamed**

- Fix vendor paths: Replace ezsystems with se7enxweb paths (`933f42eaf`)

## v1.3.40 (2025-09-27)

**Updated**

- Change 'eZ Platform' to 'Exponential Platform'. Rebranding (`a613f1c18`)
- Update PHP version requirements in composer.json (`9621fd420`)
- Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding. (`36a12daf1`)
- Replace 'Ibexa Platform' with 'Exponential Platform'. Rebranding. (`a7016bf01`)
- Correct spelling of 'ibexa' to 'exponential' in SQL. Rebranding. (`c61b18361`)

**Renamed**

- Update package details for se7enxweb integration (`f54fd6f66`)

## Related pages

- [SQLite for the platform](../../features/6.0/platform-sqlite-install.md)
- [package map](../../specifications/6.0/platform-package-map.md)
- [upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md)
