# cjw-exponential-media-site-data: installer data of the media site

This page is for developers who install the CJW Exponential Platform Nexus starter project. The package
`cjw-exponential-media-site-data` holds the full content of the media site (schema and content), used by the
installer type `cjw-exponential-media`.

## Use it

In the starter project, run:

```bash
php bin/console ezplatform:install cjw-exponential-media
```

## What the package contains

```
cjw-exponential-media/
  data.sql        full content data (schema and content)
  storage/        binary file storage placeholder
schema/
  schema.sql      database schema only
```

Released as 1.0.0 on 3 July 2026. The same day `composer.json` gained the package type, `php >= 8.1` and the branch
alias `dev-main` for Packagist. Licensed GPL-2.0-only.

This package belongs to the platform side of the project (Symfony and Ibexa); see the
[ecosystem overview](../../../history/ecosystem.md). It is listed here because it is a data package of an extension
suite.

## Related pages

- [Platform Nexus starter](../platform-nexus-starter.md)
- [Chronicle](../../../history/extensions/cjw-exponential-media-site-data.md) and [release notes](../../../../changelogs/extensions/cjw-exponential-media-site-data.md)
- [Change ledger](../../../history/ledger/cjw-exponential-media-site-data.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- [Month: 2026-07 (all extensions)](../../../history/extensions/months/2026-07.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../help/partners/partners-start-here.md) and [How to apply](../../../help/partners/how-to-apply.md).
<!-- exp-partner-block:end -->
