# ezprestapiprovider: the REST provider classes (deprecated)

This page is for developers and site administrators whose installation still activates `ezprestapiprovider`.

> [!WARNING]
> **Deprecated.** REST API v1 and v2 are served by one extension, [ezprestapi](ezprestapi.md) (1.2.6 or later).
> `ezprestapiprovider` 6.0.4 (15 October 2026) is its final release. Activate `ezprestapi` and remove
> `ezprestapiprovider` from `ActiveExtensions`; the steps are in
> [REST API v1 and v2: one extension](../../../guides/rest-api-v1-and-v2.md#6-migrating-from-ezprestapiprovider).

## What it is now

A compatibility shim on top of `ezprestapi`, so that old settings and old code keep working while an installation
moves:

- `ezpRestApiProvider` extends `ezp7xRestApiProvider`, and `ezpRestContentController` extends
  `ezp7xRestContentController`, so they get the v1 and v2 routes and the permission checks of `ezprestapi`.
- Its `rest.ini` maps the prefixes `ezp` and `ezpl` to `ezp7xRestApiProvider`, the same values `ezprestapi` sets, so
  the order in which the two are activated no longer matters.
- It no longer ships the five classes it duplicated (`ezpRestAtomController`, `ezpRestAtomView`,
  `ezpRestAtomDecorator`, `ezpRestContentModel`, `ezpRestApiViewController`); `ezprestapi` ships them, and the
  autoload generator no longer warns about duplicate class names.
- Its Composer package requires `se7enxweb/ezprestapi ~1.2.6`.

Before 6.0.4 it registered its own provider, and when its `ProviderClass[ezp]` won (it was listed earlier in
`ActiveExtensions`, or active alone, as the setup wizard used to activate it), every `/api/ezp/v2/...` route answered
404 and the v1 reads ran without the permission checks. The
[activation table](../../../guides/rest-api-v1-and-v2.md#7-activation-combinations-before-and-since) compares before
and since.

## Migration in short

1. Keep or add `ActiveExtensions[]=ezprestapi` in `settings/override/site.ini.append.php` and remove
   `ActiveExtensions[]=ezprestapiprovider`.
2. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`), regenerate the extension autoloads
   (`php bin/php/ezpgenerateautoloads.php -e`), reload PHP-FPM and restart Velocity.
3. Change a `rest.ini` override `ProviderClass[ezp]=ezpRestApiProvider` to `ezp7xRestApiProvider`, and code that
   extends `ezpRestContentController` to extend `ezp7xRestContentController`. Both keep working through the shim while
   it is installed.

Nothing changes for OAuth clients, tokens, personal API keys or the v1 URLs.

## What changed

| Version | Date | Change |
|---|---|---|
| 6.0.0 | 29 January 2024 | Package vendor and name switched to se7enxweb; description updated. |
| 6.0.1, 6.0.2 | 27 and 30 September 2026 | Version, license and website; the description names Exponential. |
| 6.0.3 | 2 October 2026 | Copyright notices name 1998 - 2026 7x & Exponential Foundation first. |
| 6.0.4 | 15 October 2026 | Deprecated, final release: the provider and content controller extend those of ezprestapi; `rest.ini` maps `ezp` and `ezpl` to `ezp7xRestApiProvider`; the five duplicated classes and the model tests removed (they live in ezprestapi); requires `se7enxweb/ezprestapi ~1.2.6`. |

## Related pages

- [REST API v1 and v2: one extension](../../../guides/rest-api-v1-and-v2.md)
- [ezprestapi](ezprestapi.md)
- [Chronicle](../../../history/extensions/ezprestapiprovider.md) and [release notes](../../../changelogs/extensions/ezprestapiprovider.md)
- [Change ledger](../../../history/ledger/ezprestapiprovider.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2023-12](../../../history/extensions/months/2023-12.md), [2024-01](../../../history/extensions/months/2024-01.md), [2026-03](../../../history/extensions/months/2026-03.md), [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md)
