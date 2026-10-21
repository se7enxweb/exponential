---
title: What a bundle is
audience: bundler, partner
category: partnership
order: 10
summary: What counts as a bundle of Exponential, the parts every bundle is made of, what changes legally and practically the moment you hand a bundle to someone else, and the questions to answer before you build one.
keywords: bundle, bundling, distribution, redistribution, image, installer, starter kit, appliance, parts of a bundle, recipient, conveying, GPL
type: help
---
# What a bundle is

This page is for anyone who is about to put Exponential together with other things and give the result to someone
else. It defines the word "bundle" as this section uses it, lists the parts a bundle is made of, and explains why the
moment of handing it over matters.

## Contents

1. [A working definition](#a-working-definition)
2. [The parts of a bundle](#the-parts-of-a-bundle)
3. [When a bundle becomes a distribution](#when-a-bundle-becomes-a-distribution)
4. [What a bundle is not](#what-a-bundle-is-not)
5. [Questions to answer first](#questions-to-answer-first)

## A working definition

A **bundle** is Exponential, combined with other software, configuration or content, prepared by you and handed to
someone outside your own organization, ready to install or ready to run. The recipient may be a customer, a hosting
customer, a student, a distribution's user or the public.

The word covers very different shapes ([Kinds of bundles](kinds-of-bundles.md)): a container image, a cloud server
image, a hosting panel's one-click installer, a Debian or RPM package, an agency starter kit in a Git repository, a
Platform distribution, a site package, an extension bundle, a hardware or virtual appliance, a classroom image or a
public demo. What they share is that **you decide what is inside, and someone else receives it**.

## The parts of a bundle

| Part | Typical content | Where it comes from | Page |
|---|---|---|---|
| The kernel | `se7enxweb/exponential` at a pinned version | Git tag or Composer | [Getting the code](getting-the-code-for-a-bundle.md) |
| Required extensions | the `require` list of the kernel's `composer.json` | Composer | [Exponential 6 as a base](exponential-6-as-a-base.md) |
| Your extensions | your own code, third-party extensions | your repositories, Packagist | [Adding and removing extensions](adding-and-removing-extensions.md) |
| A site package | content classes, content, roles, settings, install scripts (`.ezpkg`) | the package server or your own | [Site packages in a bundle](site-packages-in-a-bundle.md) |
| Installer configuration | `kickstart.ini` template, `exp:install` options | you | [The three installers](the-three-installers.md) |
| Runtime | PHP and its extensions, a web server or Velocity, a database | the operating system, images, Composer | [Web server and engine choices](web-server-and-engine-choices.md) |
| Operating system and tooling | base image, users, cron, backups, monitoring | your image or package | [Server and virtual machine images](server-and-virtual-machine-images.md) |
| Documentation and notices | license texts, copyright notices, this help, your own guides | the repository and you | [License texts and notices to ship](license-texts-and-notices-to-ship.md) |

Write the list for your own bundle down once, in the [Bundle worksheet](bundle-worksheet.md). Every later decision
(updates, support, licensing) depends on it.

## When a bundle becomes a distribution

Exponential's code is under the GNU General Public License, version 2 or later (`composer.json`
`"license": "GPL-2.0-or-later"`, `COPYRIGHT.md`). The license lets everyone run, study and change the software
without conditions. Its conditions apply when you **distribute** copies: from then on you must pass on the license,
the notices and the source code, and you may not add restrictions
([Licensing for partners](../partners/licensing-for-partners.md#what-partners-must-do-when-they-distribute)).

| Situation | Distribution? |
|---|---|
| You run a site for a customer on your own servers | No copy is handed over: your duties come from your contract, not from distribution. |
| You hand the customer an image, a container, a package or a repository | Yes. |
| A hosting panel downloads and installs Exponential into the customer's account | Ask counsel: this depends on who makes the copy and from where. |
| You publish a demo download for anyone | Yes. |

This table is a reminder, not legal advice. The details are on
[GPL obligations when you distribute](gpl-obligations-when-distributing.md).

## What a bundle is not

- **Not a fork.** A bundle uses released Exponential and adds to it. If you change kernel files, you carry a fork;
  send the change upstream instead ([Contributing fixes back](contributing-bundle-fixes-back.md)).
- **Not a new product name for Exponential.** A bundle is "based on Exponential"
  ([Naming and branding a bundle](naming-and-branding-a-bundle.md)).
- **Not a license.** Nobody can sell an "Exponential license"; you sell the bundle, your work and your service
  ([Licensing explained for buyers](../business/licensing-for-buyers.md)).
- **Not an installed site.** A bundle should install the site at first start, not carry someone's live database
  and secrets ([First boot and install once](first-boot-and-install-once.md)).

## Questions to answer first

- [ ] Who receives the bundle, and how (download, image registry, panel, package repository)?
- [ ] Which Exponential version, and how will it be updated after release?
- [ ] Which extensions and which site package go in, and under which licenses?
- [ ] Which database and which web server or engine does it use?
- [ ] How does the recipient get the administrator password, and how is it changed?
- [ ] Where do you publish the corresponding source code?
- [ ] Who supports what: you, 7x, the community?

## Related pages

- [Kinds of bundles](kinds-of-bundles.md)
- [Bundlers: start here](bundlers-start-here.md)
- [Distribution partners and resellers](../partners/distribution-partners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
