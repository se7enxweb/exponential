---
title: The extensions bundled with Exponential
audience: ecosystem, admin, developer, hosting
category: ecosystem
order: 21
summary: The se7enxweb extensions that the kernel's composer.json brings to every installation - what they cover, which are switched off until you enable them, how to see the current list, and how to add or drop one.
keywords: bundled extensions, default extension distribution, composer.json, require, suggest, se7enxweb, eztags, cjw_newsletter, expai, explayouts, ezprestapi, two-factor, inactive extensions
type: help
---
# The extensions bundled with Exponential

"`composer.json` of this release requires 66 `se7enxweb/*` packages, so every installation brings them", counted on
18 October 2026; "Many are inactive until you switch them on"
([The extension ecosystem](../../who-is-the-exponential-foundation.md#the-extension-ecosystem)).

## Contents

1. [See the current list](#see-the-current-list)
2. [What they cover](#what-they-cover)
3. [Active or not](#active-or-not)
4. [Adding or dropping one](#adding-or-dropping-one)

## See the current list

```bash
grep -n '"se7enxweb/' composer.json
```

"The first block of the output is `suggest`, the second `require`. The current `require` and `suggest` sections of
`composer.json` are the authority" ([The default extension distribution](../../../features/6.0/default-extension-distribution.md)).

## What they cover

| Area | Examples | Page |
|---|---|---|
| Page building | `explayouts` and the `explayouts-*` packages | [Exponential Layouts](../products/exponential-layouts.md) |
| Writing assistance | `expai` | [Exponential Intelligence](../products/exponential-intelligence.md) |
| Background work | `expqueue` | [expqueue](../../../features/6.0/extensions/expqueue.md) |
| Administration | `exp_adminui`, `expui` | [AdminUI and the admin designs](../products/adminui-and-admin-designs.md) |
| Taxonomy | `eztags` | [eztags](../../../features/6.0/extensions/eztags.md) |
| Newsletters | `cjw_newsletter` | [cjw_newsletter](../../../features/6.0/extensions/cjw_newsletter.md) |
| Security | `sevenx_authentication_2fa`, `recaptcha`, `hcaptcha` | [Two-step sign-in](../../../features/6.0/extensions/sevenx_authentication_2fa.md) |
| Shop | `ezpaypal` | [ezpaypal](../../../features/6.0/extensions/ezpaypal.md) |
| APIs | `ezprestapi` | [ezprestapi](../../../features/6.0/extensions/ezprestapi.md) |
| Databases | `ezoracle`, `sevenx_dse` | [Database support](../../database-support.md) |
| Themes | `sevenx_themes_simple`, `sevenx-themes-media` | [Themes and designs](themes-and-designs.md) |
| Operations | `git_manager` | [git_manager](../../../features/6.0/extensions/git_manager.md) |
| SEO and statistics | `bcgooglesitemaps`, `bcwebsitestatistics`, `xrowmetadata` | [Extensions](../../../features/6.0/extensions/README.md) |

The full table with one line each: [Who is 7x?](../../who-is-7x.md#the-extension-ecosystem).

## Active or not

Being installed is not being active. Optional extensions "that `composer.json` brings but leaves inactive, such as
Exponential Intelligence and the job queue, are switched on afterwards" (`README.md`, "Installation"); the
installation book says how ([After installing](../../../install/10-after-installing.md)).

## Adding or dropping one

The steps are in [The default extension distribution](../../../features/6.0/default-extension-distribution.md).
Version constraints use `~`, which "lets the last position float".

Each extension has its own repository, releases and changelog
([extension changelogs](../../../../changelogs/extensions/README.md)).

## Related pages

- [Packages](README.md)
- [Extensions](../../../features/6.0/extensions/README.md)
- [Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)
- [Compatibility and dependencies](compatibility-and-dependencies.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
