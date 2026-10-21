---
title: PHP versions in the ecosystem
audience: ecosystem, developer, admin, hosting
category: ecosystem
order: 31
summary: Which PHP versions the Exponential ecosystem supports - PHP 8.0 to 8.5 for Exponential 6 and Velocity, what the CI tests, PHP 7 only in old releases, the aim for 8.6 and later, and the platform line's own PHP requirements.
keywords: PHP versions, PHP 8.0, PHP 8.5, PHP 8.6, PHP 7, RHEL 9, CI, Velocity, platform line, composer.json php requirement
type: help
---
# PHP versions in the ecosystem

## Contents

1. [Exponential 6](#exponential-6)
2. [What the CI tests](#what-the-ci-tests)
3. [The platform line](#the-platform-line)
4. [Choosing a PHP version](#choosing-a-php-version)

## Exponential 6

| PHP | Exponential 6 | Source |
|---|---|---|
| 7.4 | Up to 6.0.7 only | `README.md` |
| 8.0 | The oldest supported version, so the stock PHP of Red Hat Enterprise Linux 9 runs it, under Velocity too from 0.0.4.54 | `README.md`, [PHP 8.0 support](../../../bc/6.0/php-8.0-support.md) |
| 8.1 to 8.5 | Supported, Velocity included | `README.md` |
| 8.6 and later | Allowed by `composer.json` (up to 8.8); the project aims at "PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9" | `composer.json`, `README.md` |

Source table: [PHP versions](../../who-is-the-exponential-foundation.md#php-versions).

## What the CI tests

"The continuous integration runs the test suite on PHP 8.1 to 8.5 and checks that PHP 8.0 still boots, on every push
to `main` and every pull request" ([PHP versions](../../who-is-the-exponential-foundation.md#php-versions);
[CI](../../../bc/6.0/ci.md)).

## The platform line

The Symfony distributions have their own PHP ranges, for example Nexus "Symfony 7.4/5.4/3.4 PHP 8.0-8.5
Compatibility", the DXP "PHP 8.3 and later", and Platform Legacy 2.5 "on PHP 8.1 and 8.2"
([The products one by one](../../who-is-the-exponential-foundation.md#the-products-one-by-one),
[The Exponential platform ecosystem](../../../history/ecosystem.md)). Check the distribution's `composer.json`.

## Choosing a PHP version

"please use the latest version of PHP available on your OS" (`README.md`). 7x "stronly recommends you to upgrade and
leverage the latest PHP 8.x security improvements" rather than staying on PHP 7 (same). For hosting details see
[PHP versions and extensions](../../hosting/basics/php-versions-and-extensions.md) and
[PHP settings for Exponential](../../hosting/basics/php-settings-for-exponential.md).

## Related pages

- [The PHP stack](README.md)
- [Requirements](../../../install/02-requirements.md)
- [Compatibility and dependencies](../packages/compatibility-and-dependencies.md)
- [Objection: PHP and modern technology](../../advocates/objection-php-and-modern-technology.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
