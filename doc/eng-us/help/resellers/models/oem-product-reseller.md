---
title: OEM product reseller
audience: reseller, partner, developer
category: reselling
order: 100
summary: How to sell your own product built on Exponential - a packaged solution, a hosted product, an appliance or image - under the OEM resale opportunity of the partner program, with the GPL duties of distributing it, how to name it, how to keep it on supported releases, and the questions for the partner team.
keywords: OEM, OEM product, resale, packaged solution, SaaS, appliance, image, site package, distribution, GPL, source code, product name, support of the core
type: help
---
# OEM product reseller

This page is for software companies and agencies that turn their Exponential work into a product they sell many
times. "OEM options are also available to resell your own products based on Exponential", and "OEM Resale
Opportunities" is a benefit at every partner level (<https://exponential.earth/partner/benefits>, read 18 October
2026). The program page puts it as: "Partners can also resell their own products based on Exponential (OEM options)".

## Contents

1. [Product shapes](#product-shapes)
2. [Building the product on Exponential](#building-the-product-on-exponential)
3. [The GPL and your product](#the-gpl-and-your-product)
4. [Naming the product](#naming-the-product)
5. [Questions for the partner team](#questions-for-the-partner-team)
6. [Keeping the product healthy](#keeping-the-product-healthy)

## Product shapes

| Shape | Example | Distribution? |
|---|---|---|
| Packaged solution | An intranet, a school site or a newsroom set up from your site package and extensions | Yes, when the customer receives the files |
| Hosted product (SaaS) | You run the solution for many customers; they use it in the browser | Not while you only run it |
| Appliance or image | A preinstalled server image or container | Yes |
| Extension bundle | Your extensions with installation and support | Yes, when you deliver the code ([Extension and theme bundles](extension-and-theme-bundles.md)) |

## Building the product on Exponential

| Need | What exists | Page |
|---|---|---|
| A repeatable starting point | A **site package**: an Exponential package of type `site`, installed by the setup wizard, the kickstarter or `exp:install --package=<name>` | [Site packages for hosts](../../hosting/provisioning/site-packages.md) |
| Unattended installs | The kickstarter and `exp:install` | [The kickstarter for hosts](../../hosting/provisioning/kickstarter.md), [exp:install](../../hosting/provisioning/exp-install.md) |
| Your own features | Extensions, loaded per installation | [Extensions](../../../guides/extensions.md) |
| Several sites from one product | Siteaccesses on one installation | [Multi-site offers](../offers/multi-site-offers.md) |
| An offline or controlled install | A local package repository below `var/storage/packages/` | [Site packages for hosts](../../hosting/provisioning/site-packages.md#offline-and-controlled-installs) |

## The GPL and your product

Exponential is GPL v2 or later. You may charge for your product, your work and your services. When you distribute
the product, the recipients get the license, the copyright notices and the source code, and may share it on.

- [ ] Ship `LICENSE`, `LICENSE.md` and `COPYRIGHT.md` unchanged.
- [ ] Provide the source of everything you distribute, including your changes to Exponential.
- [ ] Mark changed files with a notice and the date (GPL section 2a).
- [ ] Check each extension's own license on **Info > About** before you write "everything is GPL".
- [ ] Ask counsel whether your own extensions must be GPL when you distribute them with Exponential.

Full list: [Distribution obligations checklist](../responsibilities/distribution-obligations-checklist.md). This is
not legal advice.

## Naming the product

- Give the product its own name, and say plainly that it is "based on Exponential".
- Whether the product may carry "Exponential" in its name, and how, is not published: ask the partner team
  ([Naming and brand for resellers](../sales/naming-and-brand.md)).
- Never name it after the old product name, and never suggest it is made or endorsed by 7x or the Foundation without
  their written agreement.

```text
<Product> is a <kind of solution> by <Your company>, based on Exponential, the open source content
management system led by 7x and operated by the Exponential Foundation. Exponential is free software under the
GNU General Public License, version 2 or later.
```

## Questions for the partner team

The OEM benefit is a program promise; its details are not published. Ask <partners@exponential.earth>:

- [ ] May the product carry "Exponential" in its name or description, and how?
- [ ] Does 7x list or promote OEM products?
- [ ] Do OEM sales count as revenue for our partner level?
- [ ] Who supports the Exponential core inside our product: we alone, or 7x as our back-up under our plan?

## Keeping the product healthy

- **Stay on the maintained line.** Only the newest 6.0.x receives fixes (`SECURITY.md`); a product frozen on an old
  tag ages fast ([Keeping customers current](../customers/keeping-customers-current.md)).
- **Contribute generic fixes upstream** so your product does not carry a private fork forever
  ([Contributing code back](../../partners/contributing-code-back.md)).
- **Version your product separately** and publish which Exponential release each version is built on.
- **Test every Exponential release** before your customers receive it ([Testing releases](../../partners/testing-releases.md)).

## Related pages

- [OEM and reselling](../../partners/oem-and-reselling.md)
- [Distribution partners and resellers](../../partners/distribution-partners.md)
- [Bundlers: start here](../../bundlers/bundlers-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
