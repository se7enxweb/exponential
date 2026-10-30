---
title: PHP developer FAQ
audience: php-developer, developer
category: php-development
order: 490
summary: Short answers to the questions PHP developers ask most about Exponential - PHP versions, PSR-4 and namespaces, type declarations, class not found, settings not applied, edits that do nothing under Velocity, databases, tests without a database, CI failures, and where to start.
keywords: FAQ, PHP developer questions, PSR-4, namespaces, strict_types, class not found, settings cache, Velocity restart, PHP 8.0, databases, PHPUnit, CI, phpstan baseline, Symfony, Composer, short array syntax
type: help
---
# PHP developer FAQ

This page is for PHP developers who want a quick answer. Each answer links to the page with the details.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../examples/web-servers/nginx.md), [LiteSpeed](../../examples/web-servers/litespeed.md),
> [Caddy](../../examples/web-servers/caddy.md), [IIS](../../examples/web-servers/iis.md), [Velocity](../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../hosting/panels/plesk.md), [cPanel](../hosting/panels/cpanel.md), [DirectAdmin](../hosting/panels/directadmin.md), [ISPConfig](../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../guides/litespeed-page-cache.md).

## Contents

1. [Platform and language](#platform-and-language)
2. [Classes and settings](#classes-and-settings)
3. [Velocity and deploys](#velocity-and-deploys)
4. [Databases](#databases)
5. [Tests and CI](#tests-and-ci)
6. [Where to start](#where-to-start)

## Platform and language

**Which PHP versions must my code support?**
PHP 8.0 to 8.5. PHP 8.0 is the oldest because it is the stock PHP of RHEL 9
([Supported PHP versions: 8.0 to 8.5](supported-php-versions.md)).

**Can I use enums, readonly properties or `foo(...)`?**
Not in code that ships with or for Exponential: PHP 8.0 cannot parse them
([Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md)).

**Why no `declare( strict_types=1 )` and no return types?**
The coding standard forbids them in new code, for consistency with the existing code, compatibility of subclasses and
PHP 8.0 ([Why the code has no type declarations](why-no-type-declarations.md)).

**Why `array()` and not `[]`?**
The house style, measured on the existing code and checked by `phpcs.xml.dist` ([The coding standard](the-coding-standard.md)).

**Does Exponential follow PSR-4 or PSR-12?**
No. Classes are found through generated class maps, and the code style is the kernel's own
([PHP, PHP-FIG and Composer standards](../../standards/php-and-composer.md)).

**May I use namespaces?**
Yes. The kernel uses them for commands, cronjob parts, views and services
(`Exponential\Command\...`); other classes are global with a prefix ([Class naming and the exp prefix](class-naming-and-the-exp-prefix.md)).

**Is there a Symfony layer?**
Exponential 6 runs on its own kernel. The legacy bridge runs it inside a Symfony platform installation
([Symfony and the legacy bridge](../creators/app-creators/symfony-and-the-legacy-bridge.md)).

## Classes and settings

**My new class is "not found".**
Run `php bin/php/ezpgenerateautoloads.php -e`; under Velocity also restart the workers (`exp:velocity deploy`)
([Autoloads and class maps](autoloads-and-class-maps.md)).

**I changed a setting and nothing happened.**
Clear the INI cache with `--clear-tag=ini` (not `--clear-id=ini`), check the layer that wins with `exp:ini where`, and
restart Velocity's workers ([Reading settings with eZINI](reading-settings-with-ezini.md)).

**How do I create `.htaccess` and `config.php`?**
`./console exp:setup:htaccess --apply` and `./console exp:setup:config --apply` copy them from `.htaccess_root` and
`config.php-RECOMMENDED`; an existing file that differs is shown as a diff and kept unless `--force`
([Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md)).

**How do I change a `config.php` setting without an editor?**
`./console exp:config:set <NAME> <value> [--type=bool|int|octal|float|string|null] --apply`. The file is read as PHP
tokens, never run; only the value of that one `define()` changes, and the result must pass `php -l`. A constant of your
own extension needs `--allow-unknown`. Under Velocity restart the workers afterwards
([Setting up .htaccess and config.php from the console](../../guides/setup-files-from-the-console.md#6-set-a-value)).

**Should I rename `eZContentObject` and the other `eZ` classes?**
Never. They are real identifiers that every extension uses; new code uses `exp` or your own prefix.

**How do I change one kernel view without copying it?**
Subclass its class and register the subclass in `site.ini [RunnableSettings] Implementation[]`
([Module views as classes](module-views-as-classes.md#re-implementing-a-kernel-view)).

## Velocity and deploys

**My edit to a PHP file does nothing.**
Under Velocity the workers keep loaded classes: `exp:velocity deploy`. With the engine archive on, a kernel edit needs a
rebuild, which the deploy does ([The engine archive (phar) for PHP developers](the-engine-phar-for-php-developers.md)).

**A page shows another user's or siteaccess's data, only under Velocity.**
State kept between requests: a function static, a cache keyed by too little, a register-once flag
([Static state traps](static-state-traps.md)).

**My download view prints the page after the file.**
`eZExecution::cleanExit()` is inside a `catch ( Exception )`. Under Velocity it ends the request with an exception; do not
catch it ([Velocity persistent workers for PHP developers](velocity-persistent-workers-for-php-developers.md)).

**Does FrankenPHP keep state between requests?**
No. It runs in classic mode, a clean state per request ([FrankenPHP for PHP developers](frankenphp-for-php-developers.md)).

## Databases

**Which database should I develop on?**
SQLite 3 is the default and needs nothing; test on a server database too before you release
([Code for every supported database](code-for-every-supported-database.md)).

**How do I avoid SQL injection?**
`escapeString()` for strings, `(int)` for numbers, or `eZPersistentObject` conditions ([SQL with eZDB, safely](sql-with-ezdb.md)).

**My SQL returns nothing on MongoDB.**
The adapter translates the kernel's SQL shapes only; untranslatable SQL returns an empty result and logs `MONGO TODO`.

## Tests and CI

**Do I need a database to run the tests?**
No. `vendor/bin/phpunit` runs the DB-less suites; database tests run separately with `tests/runtests.php --dsn ...`
([PHPUnit without a database](phpunit-without-a-database.md), [Database and live tests](database-and-live-tests.md)).

**PHPStan fails on code I did not touch.**
Only findings missing from `phpstan-baseline.neon` fail; check whether your change moved or changed that code
([Static analysis with PHPStan](static-analysis-with-phpstan.md)).

**CI is red but everything passes locally.**
Usually a test reads your installation's settings, or CI runs an older PHPUnit or PHP
([CI and reproducing a red check](ci-and-reproducing-a-red-check.md)).

**The commit-msg hook refuses my commit.**
Start the subject with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `
([Git hooks and make targets](git-hooks-and-make-targets.md)).

## Where to start

Read [Exponential for PHP developers](exponential-for-php-developers.md), set up a checkout, then do
[Write your first module view](write-your-first-module-view.md). The full order is
[The PHP developer's reading path](php-developer-reading-path.md).

## Related pages

- [PHP Developers: start here](php-developers-start-here.md)
- [Application creators: frequently asked questions](../creators/app-creators/app-creator-faq.md)
- [Software Engineers: start here](../software-engineers/software-engineers-start-here.md),
  [Web Framework Developers: start here](../web-framework-developers/web-framework-developers-start-here.md),
  [the extension developer book](../extension-development/README.md)
- [Glossary for PHP developers](glossary-for-php-developers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
