# cjw-exponential-media-site-data: installer data of the media site

`cjw-exponential-media-site-data` is the installation data package for the CJW Exponential Platform Nexus starter project: the full content of the media site
(schema and content) used by the installer type `cjw-exponential-media`.

```bash
php bin/console ezplatform:install cjw-exponential-media
```

Layout of the package:

```
cjw-exponential-media/
  data.sql        full content data (schema and content)
  storage/        binary file storage placeholder
schema/
  schema.sql      database schema only
```

Released as 1.0.0 on 3 July 2026; the same day `composer.json` gained the package type, `php >= 8.1` and the branch alias `dev-main` for Packagist. Licensed
GPL-2.0-only.

This package belongs to the platform side of the project (Symfony and Ibexa), see the [ecosystem overview](../../../history/ecosystem.md); it is listed here because it is a data
package of an extension suite.

## Related

* [Chronicle](../../../history/extensions/cjw-exponential-media-site-data.md) and [release notes](../../../changelogs/extensions/cjw-exponential-media-site-data.md)
* [Change ledger](../../../history/ledger/cjw-exponential-media-site-data.md)
* [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)
