---
title: "PHP version compatibility"
audience: designer, developer
category: theme-lifecycle
order: 70
summary: Keep a theme's PHP code (operators, modules, classes) working on every PHP version Exponential supports - PHP 8.0 is the oldest, PHP 7.4 usable without support - with php -l per version, what typically breaks, and how to state the range in composer.json.
keywords: PHP version, PHP 8.0, PHP 8.5, compatibility, php -l, deprecations, composer require php, operators, modules
type: help
---
# PHP version compatibility

This page is for designers whose theme contains PHP: template operators, module views, classes. A theme of only
templates and stylesheets is not affected by PHP versions directly.

## The facts

- The 6.0.15 changelog states that **PHP 8.0 is the oldest supported version** (5 October 2026), and from 29 October 2026 PHP 7.4 is usable without support ([Why Exponential supports PHP 7.4](../../why-exponential-supports-php-7-4.md)).
- The root `composer.json` allows PHP from 8.0 up (`^8.0 || ^8.1 || ... ^8.5` and later minor versions).
- The published themes state their own ranges, for example `sevenx_themes_media` `^8.0 || ^8.1 || ^8.2 || ^8.3 || ^8.4`
  and `cjw_themes_jumper` `>=8.0`.

## Step by step: check a release

1. List the PHP files of the theme: `git ls-files '*.php'`.
2. Run the syntax check with every PHP version you claim, if they are installed:

   ```bash
   for f in $(git ls-files '*.php'); do php8.0 -l "$f"; php8.5 -l "$f"; done
   ```

   (The binary names depend on your system.) Expected: `No syntax errors detected` for each file and version.
3. Run the theme's pages on the oldest and the newest PHP you claim and read the error log for deprecation notices.
4. Write the tested range into `composer.json` `require.php` and the compatibility matrix
   ([A compatibility matrix](compatibility-matrix.md)).

## What typically breaks

| Change in newer PHP | In a theme |
|---|---|
| Deprecated dynamic properties (8.2) | An operator class that sets undeclared properties: declare them |
| Stricter types in built-in functions | Passing `null` where a string is expected: cast or check first |
| Removed functions | Old helpers copied from earlier code |

## Related pages

- [Declaring compatibility](declaring-compatibility.md)
- [Template operators in a theme](../extensions/template-operators-in-a-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
