---
title: Distribution obligations checklist
audience: reseller, partner, developer
category: partnership
order: 450
summary: The checklist a reseller works through before handing copies of Exponential to anyone - images, appliances, downloads, OEM products, files a customer takes away - covering license texts, copyright notices, source code, marked changes, extension and component licenses, documentation and naming. Not legal advice.
keywords: distribution, GPL obligations, license, LICENSE, COPYRIGHT.md, source code, copyright notices, modified files, extensions, third-party components, GFDL, documentation, image, appliance, OEM
type: help
---
# Distribution obligations checklist

This page is for technical leads and product managers at resellers who hand copies of Exponential to others. Running
a site for a customer is not distribution; giving someone a copy is
([What reselling means for free software](../basics/what-reselling-means-for-free-software.md#running-a-site-for-a-customer-is-not-distributing)).
This checklist is a reminder, **not legal advice**: read the licenses and ask counsel for your packaging.

## Contents

1. [When this applies](#when-this-applies)
2. [The checklist](#the-checklist)
3. [Component licenses](#component-licenses)
4. [Source code offers](#source-code-offers)
5. [Record of a release](#record-of-a-release)

## When this applies

| You give someone | Applies |
|---|---|
| A preinstalled server image, container or appliance | Yes |
| A downloadable OEM product or bundle | Yes |
| The site's files at the end of a project or a contract | Yes |
| A copy of your changed version to a customer's developer | Yes |
| Access to a site you host, through a browser | No |

## The checklist

**License and notices**

- [ ] `LICENSE`, `LICENSE.md` and `COPYRIGHT.md` included and unchanged.
- [ ] Every copyright notice in the files kept.
- [ ] No term added that restricts the recipient's GPL rights (no "may not redistribute", no "source not included").

**Source code**

- [ ] The corresponding source of everything distributed is provided, including your changes. Exponential is PHP,
      so the files are the source.
- [ ] Changed files carry a notice that you changed them and the date (GPL section 2a).
- [ ] Generated or minified files are accompanied by their sources.

**Extensions and components**

- [ ] Each extension's license read from its `ezinfo.php` and `extension.xml`, or on **Info > About**.
- [ ] Third-party components checked in the **Third-party software** table of the About page
      ([About and Copyright pages](../../../features/6.0/about-and-package-pages.md)).
- [ ] Your own extensions licensed so they may be distributed with GPL code (ask counsel).
- [ ] Fonts and images in themes licensed for redistribution.

**Documentation**

- [ ] Help pages you include keep their notices and come with the GNU FDL text
      ([Documentation you can give your customers](../customers/documentation-for-your-customers.md)).

**Naming**

- [ ] The product named correctly; "based on Exponential" where it is your own product
      ([Naming and brand for resellers](../sales/naming-and-brand.md)).

Source of the core list: [Distribution partners and resellers](../../partners/distribution-partners.md#obligations-of-a-distributor)
and [Licensing for hosts](../../hosting/business/licensing-for-hosts.md#images-templates-and-one-click-installers).

## Component licenses

| Component | License | Where to check |
|---|---|---|
| The kernel | GPL v2 or later | `COPYRIGHT.md`, `composer.json` (`GPL-2.0-or-later`) |
| Each extension | Its own | `ezinfo.php`, `extension.xml`, **Info > About** |
| Exponential Velocity | MIT | Its `composer.json` |
| Third-party components | Their own | The About page's third-party table |
| This documentation | GNU FDL 1.3 or later | [The license of this documentation](../../../README-LICENSE.md) |

Check the About page of the exact installation you ship before you write "everything is GPL".

## Source code offers

The simplest way to meet the source requirement is to ship the source with the copy. Build images and installers from
the public repositories and packages, keep them unchanged where you can, and put your own configuration in files that
belong to the installation, such as `settings/override` ([Licensing for hosts](../../hosting/business/licensing-for-hosts.md)).
Contributing your changes upstream removes most of the burden at the next release
([Contributing code back](../../partners/contributing-code-back.md)).

## Record of a release

```text
Product / image: ______________   Version: ______   Date: ________
Exponential version: 6.0.__   Extensions (versions, licenses): ______________
Changed files (with notices): ______________
Source provided: with the copy / at <location>
License files included: LICENSE, LICENSE.md, COPYRIGHT.md, GFDL text (if docs included)
Reviewed by: ______________   Counsel consulted: yes / no
```

## Related pages

- [OEM product reseller](../models/oem-product-reseller.md)
- [Licensing for partners](../../partners/licensing-for-partners.md)
- [Bundlers: start here](../../bundlers/bundlers-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
