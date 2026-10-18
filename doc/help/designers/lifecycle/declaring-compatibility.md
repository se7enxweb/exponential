---
title: "Declaring compatibility"
audience: designer, developer
category: theme-lifecycle
order: 80
summary: Tell users and tools which Exponential and PHP versions a theme release works with - composer.json require, the README's requirements, extension.xml dependencies and the release notes - consistently.
keywords: declare compatibility, requirements, composer require, php constraint, README requirements, release notes, extension.xml
type: help
---
# Declaring compatibility

This page is for designers releasing a theme. Compatibility is stated in several places; they must agree.

## Where it goes

| Place | Read by | Example |
|---|---|---|
| `composer.json` `require.php` | Composer, which refuses an install on another PHP | `"php": "^8.0"` |
| `composer.json` `require` / `suggest` | Composer | Extensions the theme needs or works better with ([require and suggest](../extensions/composer-require-and-suggest.md)) |
| `extension.xml` `<dependencies>` | The kernel's loading order | `<requires>` an extension ([Declaring dependencies](../extensions/dependencies-in-extension-xml.md)) |
| README "Requirements" | People | "Exponential 6.0 (tested with 6.0.15), PHP 8.1 to 8.5" |
| Release notes | People updating | "Tested with ..." when it changed |
| Compatibility matrix | People choosing a version | [A compatibility matrix](compatibility-matrix.md) |

## Step by step for a release

1. Take the versions from your upgrade test ([Upgrade testing, step by step](upgrade-test-step-by-step.md)).
2. Update `require.php` if the range changed. Raising the minimum is a change users must notice: say it in the notes,
   and consider whether it deserves a higher position in the version number ([Choosing a version number](../distribution/choosing-a-version-number.md)).
3. Update the README and the matrix.
4. Run `composer validate`.

## Related pages

- [PHP version compatibility](php-version-compatibility.md)
- [Breaking changes](breaking-changes.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
