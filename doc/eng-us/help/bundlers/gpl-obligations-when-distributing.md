---
title: GPL obligations when you distribute a bundle
audience: bundler, partner, hosting
category: bundling
order: 110
summary: What the GNU General Public License, version 2 or later, asks of a bundler who distributes Exponential - license texts, notices, corresponding source code including changes, no extra restrictions - how that applies to images, packages, installers and appliances, and a checklist. Not legal advice.
keywords: GPL, GPL-2.0-or-later, license, obligations, distribution, source code, corresponding source, notices, written offer, appliance, image, installer, not legal advice
type: help
---
# GPL obligations when you distribute a bundle

This page is for bundlers and their legal and product teams. It summarizes what the license of Exponential's code
asks when you hand copies to others. **It is not legal advice.** The license texts decide, and your counsel decides
how they apply to your bundle.

## Contents

1. [The license of the code](#the-license-of-the-code)
2. [What you may do](#what-you-may-do)
3. [What you must do when you distribute](#what-you-must-do-when-you-distribute)
4. [How it applies to each kind of bundle](#how-it-applies-to-each-kind-of-bundle)
5. [Your own code in the bundle](#your-own-code-in-the-bundle)
6. [Checklist](#checklist)

## The license of the code

| Item | Statement | Where |
|---|---|---|
| License | GNU General Public License, version 2, "or (at your option) any later version" | `COPYRIGHT.md`, `LICENSE.md` (the GPL v2 text) |
| Composer license field | `"license": "GPL-2.0-or-later"` | `composer.json` |
| Copyright | "Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." and "Copyright (C) 1999 - 2014 eZ Systems AS" | `COPYRIGHT.md`, `LICENSE` |
| Contributions | "By contributing, you agree that your contributions will be licensed under the GNU General Public License, version 2.0 or later" | `CONTRIBUTING.md`, quoted on [Licensing for partners](../partners/licensing-for-partners.md) |
| Extensions | each its own; the project's are GPL | each extension's `extension.xml`, `ezinfo.php` and license file |

## What you may do

- Run, study, change and combine Exponential for any purpose.
- Distribute copies, changed or not, as part of a bundle.
- Charge for the bundle, for distribution, for installation, support, hosting and your own work. "Using Exponential
  under the terms of the GNU GPL is free of charge" (README); what your customers pay you for is the work and the
  service, never a license to the software ([Licensing for partners](../partners/licensing-for-partners.md#what-partners-may-do)).

## What you must do when you distribute

From [Licensing for partners](../partners/licensing-for-partners.md#what-partners-must-do-when-they-distribute) and
[Distribution partners](../partners/distribution-partners.md#obligations-of-a-distributor):

1. **Pass on the license and the notices.** Include the license texts (`LICENSE`, `LICENSE.md`, `COPYRIGHT.md`) and
   keep every copyright notice in the files.
2. **Provide the source code**, including your modifications, as the GPL requires. For GPL version 2, section 3
   describes the ways: the source with the binaries, or a written offer, valid for at least three years, to provide it.
   Read the section itself before you choose.
3. **Add no restrictions** that the GPL forbids: no clause in your terms that takes away the recipients' right to
   copy, change and share the GPL parts.
4. **Keep the documentation's notices** when you ship the documentation (GFDL; see
   [Documentation to ship](documentation-to-ship.md)).

## How it applies to each kind of bundle

| Bundle | Practical way to meet the source duty | Notes |
|---|---|---|
| Container image | publish the build files and the exact sources (tag, lock file, your extensions) next to the image | an image is a distribution of every GPL component inside it |
| Server or VM image | the same; also the sources of GPL operating system parts, as their own licenses require | your OS vendor's terms apply to the OS |
| Panel installer that downloads Exponential | the recipient gets the code from the project; your installer scripts are your own code | ask counsel who distributes what |
| Distribution package | the source package of your distribution | follow the distribution's policy |
| Starter kit repository | the repository is the source | add your license file |
| Appliance | source for the GPL software on the box, and no locks that stop recipients from exercising their rights | ask counsel; appliances are where most mistakes happen |

## Your own code in the bundle

| Your part | Question to answer with counsel |
|---|---|
| An extension that uses Exponential's APIs | Must it be GPL-compatible when distributed with Exponential? The partner pages advise: "If they are distributed with or as part of GPL code, they must be GPL compatible; ask your counsel" ([Distribution partners](../partners/distribution-partners.md#what-you-can-distribute)) |
| A theme or site package | Which license for templates, styles, images and content? See [License fields in your packages](license-fields-in-your-own-packages.md) |
| Separate programs in the image (a monitoring agent, a panel) | They keep their own licenses; list them ([Third-party components](third-party-components-and-their-licenses.md)) |

## Checklist

- [ ] `LICENSE`, `LICENSE.md` and `COPYRIGHT.md` are in the bundle, unchanged.
- [ ] Copyright headers in the files are intact.
- [ ] The corresponding source (kernel tag, `composer.lock`, your extensions, your changes, build files) is
      published or offered, and the release notes say where.
- [ ] Every third-party component and its license is listed.
- [ ] Your terms add no restriction on the GPL parts.
- [ ] The documentation you ship keeps its GFDL notices and license text.
- [ ] Counsel has reviewed the bundle's licensing once, and again when its shape changes.

## Related pages

- [License texts and notices to ship](license-texts-and-notices-to-ship.md)
- [Licensing for partners](../partners/licensing-for-partners.md), [Distribution partners and resellers](../partners/distribution-partners.md)
- [Licensing for hosts](../hosting/business/licensing-for-hosts.md), [Licensing questions](../project/licensing-faq.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](../copyright-and-copyleft.md).
