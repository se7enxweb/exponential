---
title: License costs - what the GPL means for your budget
audience: government, project-manager, sales-marketing
category: public-sector
order: 40
summary: What a public body pays, and does not pay, for the license of Exponential under the GNU GPL version 2 or later - no license fee, no per-user or per-site charge - and which costs remain, with how to read license and subscription lines in bids.
keywords: license cost, license fee, GPL, GNU General Public License, open source, free software, subscription, per-user license, no license fee, budget, extensions, third-party services
type: help
---
# License costs - what the GPL means for your budget

This page is for budget holders and procurement officers who see "license: none" in a bid and need to know what
that means. Exponential is free software under the GNU General Public License, version 2 or (at your option) any
later version. The facts come from [Licensing questions](../../../project/licensing-faq.md) and the
[GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html) itself.

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [What you do not pay](#what-you-do-not-pay)
2. [What you still pay](#what-you-still-pay)
3. [Reading license lines in bids](#reading-license-lines-in-bids)
4. [Extensions and third-party services](#extensions-and-third-party-services)
5. [What the license gives you](#what-the-license-gives-you)
6. [The documentation license](#the-documentation-license)

## What you do not pay

- **No license fee** for the kernel: `"license": "GPL-2.0-or-later"` in `composer.json`.
- **No per-user, per-editor, per-site or per-server charge.** The GPL allows any use, including running as many sites
  as you need and installing on as many servers as you like.
- **No fee to see or change the source.** The code is public on GitHub ([Community channels](../../../project/community-channels.md)).
- **No fee for the documentation**, which is free documentation under the GNU FDL 1.3 or later.

## What you still pay

| Cost | Why it remains |
|---|---|
| Implementation | someone builds your content model, design and integrations |
| Hosting | servers or a hosting service ([Hosting costs](hosting-costs.md)) |
| Support and maintenance | free community help promises no response time ([Getting support](../../../project/getting-support.md)) |
| Upgrades and security releases | someone applies them ([Upgrade and maintenance costs](upgrade-and-maintenance-costs.md)) |
| Staff | editors, administrators, web team ([Staff and skills](staff-and-skills.md)) |
| Training | the course material is free; trainers' time is not ([Training costs](training-costs.md)) |
| Compliance work | accessibility audit, security testing, data protection |

A bid with no license fee is therefore not a bid with no cost. Compare total cost, as in
[The structure of a TCO model](tco-model-structure.md).

## Reading license lines in bids

| The bid says | Ask |
|---|---|
| "License: free / none" | correct for the kernel; are any paid extensions or services included? |
| "Annual subscription" for Exponential | what exactly is the subscription for: support, updates, hosting? The software itself has no license fee |
| "Enterprise edition license" | which code is in it, and under which license? Code distributed to you that builds on Exponential is distributed under the GPL |
| "Proprietary module, source not delivered" | the licensing FAQ explains that extensions are derivative works; whoever receives one also receives its source |

The GPL allows a supplier to charge for copies, installation, support and updates; it only fixes the license
under which you receive the code ([Licensing questions](../../../project/licensing-faq.md#can-i-sell-exponential-or-an-extension-or-design-for-it)).

## Extensions and third-party services

- Extensions published by the project are free software; check each one's license on its page
  ([Package licenses and versions](../../../../features/6.0/package-licenses-and-versions.md)).
- Some extensions call third-party services when activated (analytics, captcha, social sign-in, maps). The extension
  is free; the service may have its own terms and costs ([Privacy standards](../../../../standards/privacy.md#third-party-services-in-shipped-extensions)).
- An AI writing assistant needs a provider account; its usage is paid to the provider (see the Exponential
  Intelligence course, [Exponential Intelligence](../../../trainers/exponential-intelligence/README.md)).

## What the license gives you

- The right to run, study, change and pass on the software, which is what makes a change of supplier possible.
- No obligation to publish the code of your own site: visiting a website is not distribution in the GPL's sense.
- Your own extensions need not be shared unless you distribute them.

These rights reduce long-term cost risk: no supplier can withdraw the license or raise its price.

## The documentation license

The documentation, this help included, is free documentation under the
[GNU FDL 1.3](https://www.gnu.org/licenses/fdl-1.3.html) or later: you may copy it into your own manuals and
training material ([The license of this documentation](../../../../README-LICENSE.md)).

## Related pages
- [Total cost of ownership: overview](README.md)
- [Licensing questions](../../../project/licensing-faq.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
