# PHP 8.4 and 8.5 for the older Symfony stack: the framework forks

**Repositories:** `se7enxweb/symfony`, `se7enxweb/twig`, `se7enxweb/doctrine-bundle` (repository `DoctrineBundle`), `se7enxweb/doctrine-dbal-schema`, `se7enxweb/php-parser` (repository `PHP-Parser`), plus the platform packages `ezpublish-kernel`, `ezplatform-kernel`, `ezplatform-http-cache`, `ezplatform-user`, `ezplatform-richtext` that received PHP constraints.
**History:** [symfony](../../history/ecosystem/symfony.md) · [twig](../../history/ecosystem/twig.md) · [DoctrineBundle](../../history/ecosystem/DoctrineBundle.md) · [doctrine-dbal-schema](../../history/ecosystem/doctrine-dbal-schema.md) · [PHP-Parser](../../history/ecosystem/PHP-Parser.md) · [ezpublish-kernel](../../history/ecosystem/ezpublish-kernel.md).
**Related legacy page:** [PHP 8 support of the Exponential 6 kernel](../../bc/6.0/php8.md).

## What it is

Exponential Platform 1.x / 2.5 and Nexus 1.x run on the Symfony 3.4 generation of the framework. Upstream stopped maintaining it long before PHP 8.4 and 8.5 appeared, so these stacks stop with fatal errors or flood the log with deprecations on a current PHP. The forks listed here are small, targeted patches that make the old stack work on PHP 8.2 to 8.5. They are installed automatically: each fork declares the upstream package under `replace`, so Composer uses the fork wherever the upstream name is required.

## Why it helps

- You can run an older Exponential Platform site on a supported PHP version instead of staying on an unsupported one.
- Your logs show your own warnings, not thousands of framework deprecations.
- No change to your project code: the fork has the same namespaces and the same API.

## What each fork fixes

### `se7enxweb/symfony` (Symfony 3.4 monolith)

| Date | Change | Effect |
|---|---|---|
| 2025-08-24 | `ErrorHandler`, `LazyLoadingValueHolderGenerator`, `ArrayNode`, `ExceptionCaster` adapted for PHP 8.2 / 8.4 | the debug error handler, lazy services, configuration arrays and exception dumping run on PHP 8.x |
| 2026-01-28 | return types on the VarDumper `Data` class; `WebProfilerExtension` uses a method reference instead of a closure | `dump()` and the profiler toolbar work on PHP 8 |
| 2026-01-29 | explicit nullable types and a `ReflectionProperty` deprecation fix in `ErrorHandler` | no deprecation notices from the handler |
| 2026-01-30 | `composer.json` requires `se7enxweb/twig` in place of `twig/twig` | one Twig for the whole stack |
| 2026-02-10 | PHP 8.5 deprecations in `Container` and `Yaml\Inline` | clean container and YAML parsing on 8.5 |
| 2026-02-14 | profiler data collector and `Kernel` changes needed by Nexus: deprecation statistics are produced and shown more gracefully | the toolbar of a default Nexus install does not fail on deprecations |
| 2026-04-09 | `NativeFileSessionHandler` skips its `ini_set()` calls when a session is already active or headers were sent | `cache:clear` and other CLI commands no longer crash with "An unexpected error has occurred" when the legacy kernel has started a session before the handler is built |

### `se7enxweb/twig` (Twig 2.x)

| Date | Change | Effect |
|---|---|---|
| 2025-08-24 | `NameExpression` fix for PHP 8.2+ | templates compile on PHP 8.2 and later |
| 2026-01-30 | package replaces `twig/twig`; constraint and version raised to 2.16.2 | Composer installs the fork |
| 2026-04-09 | `CallExpression::reflectCallable()` detects the PHP 8.5 closure name form `{closure:Class::method():N}` | the compiler no longer emits an invalid dynamic method call for closures; template functions defined as closures work on PHP 8.5 |
| 2026-05-11 | explicit nullable parameter types in 36 files (Node, Error, Sandbox, Extension, Loader, Template, Parser) | no "implicitly nullable parameter" deprecation on PHP 8.4 and 8.5 |

### Smaller forks

| Fork | What changed |
|---|---|
| `se7enxweb/doctrine-bundle` (1.5 line) | vendor name replaced, `replace` of `doctrine/doctrine-bundle` and the Twig require rule (2026-01-30) |
| `se7enxweb/doctrine-dbal-schema` | PHP 8.1 support (2025-07-01), `replace` shim for the upstream name (2026-04-12) |
| `se7enxweb/php-parser` (0.9 line) | PHP 8 token compatibility: qualified name tokens expanded back to the sequences the old grammar expects; `match`, `enum`, `readonly` mapped to identifiers; nullsafe operator mapped to `->`; attributes and PHP 7.0+ return types and nullable markers stripped (2026-05-11), plus `replace` of `nikic/php-parser` 0.9.5 |
| `se7enxweb/ezpublish-kernel` | PHP 8.2 / 8.3 support, closure-based Twig functions replaced by named methods for PHP 8.5 (2026-01-30), cache configuration blocks that were missing (2026-02-09, 2026-02-10) |

## Check what is installed

```bash
composer show -i | grep -E "se7enxweb/(symfony|twig|doctrine-bundle|php-parser)"
composer why twig/twig          # shows the fork that satisfies the upstream name
php -v
```

(Read-only commands; they change nothing.)

## Limits

- These are patches to keep an old stack alive, not a Symfony upgrade. New projects should start on [Platform v5](platform-dxp-skeleton.md) (Symfony 7.4, PHP 8.3+).
- `se7enxweb/twig` follows the Twig 2.16 line; the v5 stack uses Twig 3.x from upstream.

## Related

[Package map](../../specifications/6.0/platform-package-map.md) · [Upgrade notes](../../bc/6.0/platform-package-forks-and-command-renames.md) · [Nexus starter](platform-nexus-starter.md)

## Platform ecosystem pages

- Features: [Platform administration interface](platform-admin-ui-fork.md); [DXP skeleton](platform-dxp-skeleton.md); [Layouts on the platform](platform-layouts-core-fork.md); [Nexus starter](platform-nexus-starter.md); [Site bundles](platform-site-bundles.md); [SQLite for Exponential Platform](platform-sqlite-install.md); [Legacy bridge](legacy-bridge.md); [AdminNeo database manager](adminneo-database-manager.md).
- Specifications: [Platform console command names](../../specifications/6.0/platform-console-commands.md); [Platform package map](../../specifications/6.0/platform-package-map.md); [Platform SQLite installer](../../specifications/6.0/platform-sqlite-installer.md); [Legacy bridge bundle specification](../../specifications/6.0/legacy-bridge-bundle.md).
- Upgrade notes: [Package forks and command renames](../../bc/6.0/platform-package-forks-and-command-renames.md).
- Changelog: [Platform changelog](../../changelogs/extensions/exponential-platform.md).
- History: [ecosystem overview](../../history/ecosystem.md), with a page for every month from 2018-11 in [ecosystem months](../../history/ecosystem/months/2026-04.md), and the [change ledger](../../history/ledger/README.md).

## Related pages

- [Steps to upgrade your Exponential 6.0.13 site to use PHPUnit 10 — what broke, how we fixed it, and how you run tests now](../../bc/6.0/phpunitv10.md)
- [PHPUnit 13 support for Exponential 6.0.x — what broke in the jump from 10 → 13, how we fixed it, and the new eZTemplateStringOperator test suite](../../bc/6.0/phpunitv13.md)
- [PHPUnit 13 / PHP 8.4.23 test suite cleanup](../../bc/6.0/phpunitv13forPHP841.md)
- [`ezpSessionHandlerDB` PHP 8 compatibility bugfixes and PHPUnit 13 test suite](../../bc/6.0/ezpsessionhandlerdb-php8-bugfix-and-tests.md)
- [April 2025](../../history/2025/2025-04.md)
- [September 2025](../../history/2025/2025-09.md)
- [December 2025](../../history/2025/2025-12.md)
- [February 2026](../../history/2026/2026-02.md)

## Related pages

- [March 2026](../../history/2026/2026-03.md)
