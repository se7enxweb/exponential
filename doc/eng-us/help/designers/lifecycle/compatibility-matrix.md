---
title: "A compatibility matrix"
audience: designer, developer
category: theme-lifecycle
order: 60
summary: Keep a table of which theme versions were tested with which Exponential, PHP, server engine and database versions, and the browsers and widths you check - how to fill it honestly and where to publish it.
keywords: compatibility matrix, tested versions, Exponential versions, PHP versions, browsers, Velocity, FPM, database, support table
type: help
---
# A compatibility matrix

This page is for designers who want users to see at a glance whether a theme fits their installation.

## What to record

| Dimension | Values to record |
|---|---|
| Exponential | The releases or `main` commit you tested, e.g. 6.0.14, 6.0.15 (development) |
| PHP | Each version you ran the theme on (Exponential 6.0 supports PHP 8.0 and later) |
| Server engine | Exponential Velocity, PHP-FPM with Apache or nginx, FrankenPHP, if your theme has PHP code that could differ |
| Database | Only if the theme ships tables or queries |
| Browsers and widths | Current browsers; 1440, 960 at 200% zoom, 375 px |
| Modes | Light, dark, right-to-left if supported |

## A template

| Theme | Exponential | PHP | Tested on |
|---|---|---|---|
| 1.0.25 | 6.0.15 (dev) | 8.3, 8.5 | Velocity, Apache + PHP-FPM |
| 1.0.24 | 6.0.14 | 8.1, 8.3 | Apache + PHP-FPM |

Write only what you tested. "Should work" belongs in a separate, clearly marked column, or nowhere.

## Step by step

1. Keep the matrix in `doc/compatibility.md` of the theme, or in the README.
2. Add a row for every release, during the upgrade test ([Upgrade testing, step by step](upgrade-test-step-by-step.md)).
3. Reflect it in `composer.json` `require.php` and in the README's requirements ([Declaring compatibility](declaring-compatibility.md)).

## Related pages

- [PHP version compatibility](php-version-compatibility.md)
- [A support policy for your theme](support-policy-for-your-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
