---
title: Caches and autoloads after building
audience: bundler, developer, hosting
category: bundling
order: 400
summary: What a bundle build and an update must refresh - the autoload arrays (ezpgenerateautoloads.php -e, -k, -o, .autoloadignore), the caches (ezcache.php --clear-tag, --clear-id, --clear-all, cache.php), Velocity's workers and response cache, and the documented order after a code change.
keywords: autoload, ezpgenerateautoloads.php, -e, -k, -o, .autoloadignore, cache, ezcache.php, --clear-tag, --clear-id, --clear-all, --list-tags, cache.php, exp:velocity deploy, response cache, OPcache, order, bundle
type: help
---
# Caches and autoloads after building

This page is for bundlers who build images and packages, and for anyone who updates a bundled installation. A build
that forgets the autoload arrays ships a site that cannot find its classes; one that forgets the caches ships stale
settings and templates.

## Contents

1. [Autoload arrays](#autoload-arrays)
2. [Caches](#caches)
3. [The order after a code change](#the-order-after-a-code-change)
4. [In a bundle build](#in-a-bundle-build)
5. [In a bundle update](#in-a-bundle-update)

## Autoload arrays

"Exponential finds classes through generated autoload arrays in `autoload/` (the kernel) and `var/autoload/` (the
extensions). A class that is not in an array is not found, even if its file is in place"
([10.7 Class autoloads](../../install/10-after-installing.md#107-class-autoloads)).

```bash
php bin/php/ezpgenerateautoloads.php -e        # extensions: after adding, renaming or removing a class
php bin/php/ezpgenerateautoloads.php -k        # the kernel: only when kernel/ or lib/ changed
php bin/php/ezpgenerateautoloads.php -o        # kernel overrides
php bin/php/ezpgenerateautoloads.php -e -n     # dry run: report, write nothing
```

The options of the script (`kernel/private/classes/commands/ezpgenerateautoloads.php`) also include `-q`, `-v`,
`-p` (progress), `-s` (tests), `-t` (target), `--exclude` and `--allow-root-user`. "An option it does not know stops
it with exit status 1 and nothing written", so a build script can rely on its status.

`.autoloadignore` in the installation root limits which directories are walked; "The shipped file excludes `vendor`
..., `ai`, `var` and `.claude`". Add your own working directories to it if your build tree has any. Composer's
`post-install-cmd` of the kernel already runs `php bin/php/ezpgenerateautoloads.php`; a bundle with its own root
`composer.json` adds the same script ([Composer-based bundles](composer-based-bundles.md#scripts-after-install)).

## Caches

`bin/php/ezcache.php` takes `--clear-tag`, `--clear-id`, `--clear-all`, `--list-tags`, `--list-ids`, and `--purge` with
its `--iteration-sleep`, `--iteration-max` and `--expiry` options (`kernel/private/classes/commands/ezcache.php`).

| You changed | Clear |
|---|---|
| an `.ini` setting | `--clear-tag=ini` (not `--clear-id=ini`, which misses the global INI cache) |
| a `.tpl` file | `--clear-tag=template` |
| a new template, override rule, extension or design | `--clear-id=template-override,design_base` |
| a stylesheet or script | `--clear-id=ezjscore-packer,template-block`, then the Velocity cache |
| content looks stale | `--clear-tag=content` |
| unsure | `--clear-all` |

Source: [Which to clear](../../install/10-after-installing.md#which-to-clear). `php bin/php/cache.php` (console
`exp:cache`) reaches the caches in front of PHP as well: static, HTTP, Velocity's response cache, OPcache
([The caches in front of PHP](../../install/10-after-installing.md#the-caches-in-front-of-php)).

## The order after a code change

"A page rendered by the old code between a cache clear and a PHP reload is cached again and looks as if the change had
not worked. The order is: INI and template caches, then reload PHP-FPM and restart Velocity, and **only then** the
content, HTTP, template-block and response caches." Under Velocity, `exp:velocity deploy` does it in that order, with
PASS or FAIL per step ([The order after a code change](../../install/10-after-installing.md#the-order-after-a-code-change)).

## In a bundle build

1. `composer install` from the lock file (its script regenerates the arrays), or run `ezpgenerateautoloads.php`
   yourself.
2. `php bin/php/ezpgenerateautoloads.php -e` after adding your extensions.
3. Do **not** ship `var/cache/` from the build; the site builds its caches on first use.
4. Check: `php bin/php/console exp:checkclasses` "loads every declared class and reports those PHP refuses to load".

## In a bundle update

1. Put the new code in place (image, package, Git and Composer).
2. Regenerate the arrays (`-e`; `-k` when kernel files changed).
3. Apply the database update if the release needs one ([Updates and upgrade paths](updates-and-upgrade-paths.md)).
4. Clear in the documented order, or run `exp:velocity deploy` (`--kernel` when a kernel class was added or renamed).

## Related pages

- [Cache clearing and warming](../hosting/performance/cache-clearing-and-warming.md)
- [10.6 Caches](../../install/10-after-installing.md#106-caches)
- [Velocity in a bundle](velocity-in-a-bundle.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
