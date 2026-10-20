---
title: "Objection: PHP and modern technology"
audience: advocate
category: advocacy
order: 32
summary: How to answer "PHP is old", "it needs an old PHP", "the interface looks dated" or "we need headless" with sourced facts about PHP 8 support, the current stack, admin4, Velocity, the REST API and the Symfony distributions.
keywords: objection, PHP, old technology, legacy, modern, PHP 8, PHP 8.5, interface, dated, admin4, dark mode, headless, REST, GraphQL, Symfony, jQuery 4, TinyMCE 8
type: help
---
# Objection: PHP and modern technology

This page is for advocates who hear "PHP is old", "this is legacy software", "the admin looks like 2010" or "we need
headless". Do not argue about languages or taste. Describe what Exponential runs on today and what it offers, with
sources, and let people judge.

## Contents

1. [The variants of the objection](#the-variants-of-the-objection)
2. [The facts](#the-facts)
3. [The word "legacy"](#the-word-legacy)
4. [Reply templates](#reply-templates)
5. [Do not say](#do-not-say)

## The variants of the objection

| They say | The real question |
|---|---|
| "PHP is old." | "Is the language a risk?" |
| "It needs an old PHP." | "Will it run on our current servers?" |
| "The interface looks dated." | "Will our editors like it?" |
| "It is legacy software." | "Is it maintained?" (see [Is it still alive?](objection-is-it-still-alive.md)) |
| "We need headless / an API." | "Can other systems read the content?" |

## The facts

| Fact | Source |
|---|---|
| PHP 8.0 is the oldest supported version; 8.1 to 8.5 are supported; `composer.json` allows up to 8.8 | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| The project aims at "PHP 8.2, 8.3, 8.4, 8.5, 8.6 and beyond php9" | same |
| CI runs the tests on PHP 8.1 to 8.5 and checks that 8.0 still boots | same |
| PHP 7.4 support ended with 6.0.7 | same |
| The admin4 design, with light and dark mode, keyboard support and accessibility tests | [The admin4 design](../../features/6.0/admin4-design.md) |
| jQuery 4 and TinyMCE 8 in the 6.0.15 line | [Handling objections, PHP versions and modern technology](../business/objection-handling.md#php-versions-and-modern-technology) |
| AdminUI as an alternative administration | [AdminUI](../../features/6.0/exp-adminui.md) |
| A REST API, versions 1 and 2 | [REST API v1 and v2](../../guides/rest-api-v1-and-v2.md) |
| Exponential Velocity, a PHP web server with HTTP/2, HTTPS and persistent workers | [Velocity](../../features/6.0/velocity-persistent-worker-server.md) |
| For Symfony with GraphQL: Exponential Platform DXP | [Which product is for you](../who-is-the-exponential-foundation.md#which-product-is-for-you) |
| A Content Security Policy and no inline scripts in new administration templates | [Content Security Policy](../../features/6.0/content-security-policy.md) |

## The word "legacy"

"Legacy" has a precise meaning in the product family: "The Exponential 6 kernel when it runs beside a Symfony
platform" ([Glossary](../who-is-the-exponential-foundation.md#glossary)). It does not mean "old" or "unmaintained".
When someone uses it loosely, explain the meaning once, kindly, and move on. Do not use it yourself to mean "old"
([Brand and naming rules](../business/brand-and-naming-rules.md#the-product-family)).

## Reply templates

```text
PHP IS OLD
Exponential runs on current PHP: 8.0 to 8.5 are supported, its tests run on 8.1 to 8.5 on every push,
and composer.json already allows later 8.x versions.
Source: (link to the support commitment)

DATED INTERFACE
Have a look at the admin4 design: light and dark mode, keyboard navigation, accessibility tests in CI.
There is also AdminUI as an alternative. Screenshots are better on your own install: one command,
./console exp:install, gives you a site on SQLite in minutes.

HEADLESS
There's a REST API (v1 and v2) for apps and other systems. If you want a Symfony stack with GraphQL,
the family has Exponential Platform DXP. (links)
```

## Do not say

- "PHP is better than language X." Language wars help nobody.
- "It is the most modern CMS." No source supports a superlative.
- "It does GraphQL." Exponential 6 offers REST; GraphQL belongs to the Symfony distributions
  ([Exponential Platform DXP](../who-is-the-exponential-foundation.md#exponential-platform-dxp)).
- "It will support PHP 9." The sources state an aim, not a promise.

## Related pages

- [Talking points for developers](talking-points-for-developers.md)
- [Knowing the product family](knowing-the-product-family.md)
- [Answering objections as an advocate](answering-objections-as-an-advocate.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
