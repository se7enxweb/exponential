---
title: "Licensing explained for buyers: GPL code, GFDL documentation"
audience: sales-marketing, project-manager
category: business
order: 110
summary: What a buyer may do with Exponential under the GNU GPL v2 or later, and with its documentation under the GNU FDL 1.3, in plain words; what the licenses ask in return, where other licenses apply, and how to check it all in the product. Not legal advice.
keywords: license, licensing, GPL, GNU General Public License, GPL-2.0-or-later, GFDL, GNU Free Documentation License, copyright, buyer, procurement, open source, free software, warranty, MIT, extensions, third-party, vendor lock-in
type: help
---
# Licensing explained for buyers: GPL code, GFDL documentation

This page is for people in sales and marketing who answer licensing questions from buyers, and for project managers
who must explain the license to procurement, legal or management. It answers: under which licenses does Exponential
come, what may a buyer do with it, what do the licenses ask in return, and where can the buyer check it?

> **Warning**
> This page is a summary in plain words, not legal advice. The license texts are what count: the GNU General Public
> License version 2 in `LICENSE.md` (and `LICENSE`) of the repository, and the GNU Free Documentation License 1.3 in
> [doc/en/us/LICENSE-GFDL-1.3.txt](../../LICENSE-GFDL-1.3.txt). A buyer with specific plans (selling a product built on
> Exponential, distributing it to third parties, combining it with proprietary code) should ask their own legal
> adviser.

## Contents

1. [In short](#in-short)
2. [The licenses at a glance](#the-licenses-at-a-glance)
3. [The software: GNU GPL v2 or later](#the-software-gnu-gpl-v2-or-later)
4. [What a buyer may do with the software](#what-a-buyer-may-do-with-the-software)
5. [What the GPL asks in return](#what-the-gpl-asks-in-return)
6. [Running a website is not distributing](#running-a-website-is-not-distributing)
7. [The documentation: GNU FDL 1.3 or later](#the-documentation-gnu-fdl-13-or-later)
8. [Extensions, related products and third-party parts](#extensions-related-products-and-third-party-parts)
9. [Copyright holders](#copyright-holders)
10. [No license fees, and what buyers do pay for](#no-license-fees-and-what-buyers-do-pay-for)
11. [Warranty](#warranty)
12. [Where to check it in the product](#where-to-check-it-in-the-product)
13. [Buyer FAQ](#buyer-faq)
14. [A license summary to hand to procurement](#a-license-summary-to-hand-to-procurement)

## In short

- **The software** is free software under the **GNU General Public License, version 2 or any later version**
  (`COPYRIGHT.md`, `composer.json`: `"license": "GPL-2.0-or-later"`).
- **Using it costs no license fee**: "Using Exponential under the terms of the GNU GPL is free of charge. The
  Professional License is no longer available." (README, "License").
- **The documentation**, including this page, is under the **GNU Free Documentation License 1.3 or any later
  version** ([The license of this documentation](../../README-LICENSE.md)).
- **Buyers may** use, study, change and self-host the software, and pass it on, under the license's conditions.
- **When they pass it on** (distribute copies, changed or not), the recipients get the same freedoms and the source
  code.

## The licenses at a glance

| What | License | Source |
|---|---|---|
| Exponential 6 (the kernel) | GNU GPL v2 or later (`GPL-2.0-or-later`) | `COPYRIGHT.md`, `LICENSE.md`, `composer.json` |
| Contributions to the kernel | The same: "any modifications made to the project are also released under the same GPLv2 or later license" | `CONTRIBUTING.md` |
| The documentation in `doc/` (6.x) | GNU FDL 1.3 or later, no Invariant Sections, no Front-Cover Texts, no Back-Cover Texts | [The license of this documentation](../../README-LICENSE.md) |
| Older documentation inherited from eZ Systems | The terms it came with, the GPL v2 of the repository | [The license of this documentation](../../README-LICENSE.md#older-pages-with-their-own-terms) |
| Exponential Velocity | MIT | its `composer.json` ([Who is 7x?](../who-is-7x.md#open-source-licenses)) |
| Exponential Platform DXP (meta package) | "GPL-2.0-or-later or proprietary" | its `composer.json` ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-platform-dxp)) |
| Exponential Basic | GNU GPLv2+ | repository description ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-basic)) |
| Each extension | Stated in its own `ezinfo.php` and `extension.xml`, shown on Info > About | [About and Copyright pages](../../features/6.0/about-and-package-pages.md) |

## The software: GNU GPL v2 or later

The GPL is a free software license written by the Free Software Foundation. "Free" refers to freedom, not price:
"When we speak of free software, we are referring to freedom, not price" (preamble of `LICENSE.md`).

"Or any later version" means the recipient may follow the terms of version 2 or of any later version the Free
Software Foundation publishes (`COPYRIGHT.md`). `CONTRIBUTING.md` explains why: "The 'or later' clause allows the
project to upgrade to future versions of the GPL".

The README states that the old dual license is gone: "You can no longer choose between the GNU GPLv2-or-later and
the eZ Systems' eZ Publish Professional Licenses ... This effectively makes Exponential GNU GPLv2-or-later ONLY."

## What a buyer may do with the software

Paraphrased from `LICENSE.md`; the section numbers are those of the license.

| A buyer may | License basis |
|---|---|
| Run it, for any purpose, on as many servers and sites as they like | Section 0: "The act of running the Program is not restricted" |
| Read and study the complete source code | The whole code is published; section 3 defines source code as "the preferred form of the work for making modifications to it" |
| Change it, for their own needs | Section 2 |
| Self-host it, or have any provider host it | Running is not restricted (section 0); no clause ties the software to one host |
| Hire any company or developer to work on it | The rights belong to every licensee; nothing names a required supplier |
| Copy and pass on unchanged copies | Section 1 |
| Pass on changed copies | Section 2, with its conditions |
| Charge for copies, and offer warranty for a fee | Section 1: "You may charge a fee for the physical act of transferring a copy, and you may at your option offer warranty protection in exchange for a fee" |

This is the core of the "no vendor lock-in" argument: the license itself gives the buyer the right to take the code
to another supplier. Together with self-hosting on many operating systems and databases ([Database support](../database-support.md),
[Operating system support](../os-support.md)) and the full Git history ([Develop with Git](../develop-with-git.md)),
the buyer keeps control.

## What the GPL asks in return

The conditions apply when someone **distributes** the software (gives or sells copies to others), changed or not.

| Condition | Plain words | License basis |
|---|---|---|
| Keep the notices | Copyright notices, the license notice and the warranty disclaimer stay intact, and recipients get a copy of the license | Section 1 |
| Mark changes | Changed files carry "prominent notices stating that you changed the files and the date of any change" | Section 2a |
| Same license for the whole | A distributed work that contains or is derived from Exponential is licensed as a whole under the GPL, at no charge to third parties | Section 2b |
| Provide the source | Distributed copies come with the complete source code, or a written offer for it | Section 3 |
| No extra restrictions | Recipients get the same rights; anyone who breaks the license loses their rights under it | Sections 4 and 6 |

Independent works that are only stored or shipped next to Exponential ("mere aggregation") are not brought under
the GPL by that alone (section 2). Whether a particular piece of code is "a work based on the Program" is a legal
question; send it to a legal adviser.

## Running a website is not distributing

Most buyers run a website. Section 0 of the license says: "Activities other than copying, distribution and
modification are not covered by this License ... The act of running the Program is not restricted". Running
Exponential on your own server to serve your site is running it.

What this means in practice, as questions to settle with your adviser:

1. Will we give copies of the software (or our changed version) to anyone outside our organization? If no, the
   distribution conditions are not triggered by our use.
2. Will an agency build the site and hand the code to us? Then the agency distributes it to us, and we receive the
   GPL rights, including the source.
3. Will we sell or give a product built on Exponential to our own customers? Then we distribute it, and the
   conditions above apply to us.
4. Do we combine Exponential with code we want to keep proprietary? Ask a legal adviser before you start.

> **Note**
> The GPL v2 does not have a clause about software used over a network. The license list of the package system
> also offers the `AGPL-3.0` licenses for your own packages, which do ([Package licenses](../../features/6.0/package-licenses-and-versions.md));
> Exponential itself is not under the AGPL.

## The documentation: GNU FDL 1.3 or later

The documentation in `doc/`, including all help pages, is free documentation. The full explanation is on
[Free documentation](../free-documentation.md); for buyers the essentials are:

| A buyer may | Conditions |
|---|---|
| Read and study it for any purpose, including training | None |
| Copy and share it, on paper or as files, free or for a fee | Keep the copyright notices, the license notice and a copy of the license; add no conditions or copy protection |
| Translate it | A translation is a modified version; keep the original English license text with it |
| Adapt it for their own team or site, and share the adapted version | Same license, keep the notices, say that it was changed and by whom, give it a title of its own unless the publisher agrees |
| Put it beside their own documents | The license does not extend to independent works |

Large print runs or more than 100 copies in a hard-to-edit format have extra conditions (section 3 of the FDL; see
[Free documentation](../free-documentation.md#copy-it-and-share-it-with-other-users)).

For a buyer this means: the help is not a closed manual. Their editors' handbook can start from these pages,
translated and adapted, as long as it stays free documentation.

## Extensions, related products and third-party parts

- Each **extension** states its license in `ezinfo.php` and `extension.xml`; the About page lists them all in one
  sortable table, with a **Third-party software** table of included components, their versions and licenses
  ([About and Copyright pages](../../features/6.0/about-and-package-pages.md)).
- **Packages** created in the administration choose their license from a list that includes GPL, LGPL, AGPL, MIT,
  GFDL, Creative Commons, public domain and a proprietary option; the default is `GPL-2.0-or-later`
  ([Package licenses and Semantic Versioning](../../features/6.0/package-licenses-and-versions.md)).
- **Exponential Velocity** is MIT licensed, a permissive license.
- **Exponential Platform DXP** declares "GPL-2.0-or-later or proprietary" in its `composer.json`. The sources do not
  explain the proprietary option; if a buyer needs it, they must ask 7x.

Before you tell a buyer that "everything is GPL", check the About page of the installation they will run.

## Copyright holders

| Notice | Where |
|---|---|
| "Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved." | `COPYRIGHT.md` |
| "Copyright (C) 1999 - 2014 eZ Systems AS. All rights reserved." | `COPYRIGHT.md` |
| "Copyright (C) 1998 - 2026 7x & Exponential Foundation and the Exponential contributors" | the documentation ([The license of this documentation](../../README-LICENSE.md)) |

"All rights reserved" in a notice does not take away the GPL's permissions; the license grants them on top of
the copyright. Copy these notices exactly when you quote them.

## No license fees, and what buyers do pay for

- The license costs nothing: "Using Exponential under the terms of the GNU GPL is free of charge" (README).
- Buyers pay for **work and services**, if they want them: design, development, migration, support, hosting,
  training. 7x offers these; "Prices and terms are on the 7x website, not here"
  ([Who is 7x?](../who-is-7x.md#services-and-hosting)). Any other company may offer them too.
- Sponsorship of 7x or the Foundation is voluntary ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#how-to-support-or-join)).

## Warranty

The GPL comes with no warranty. Sections 11 and 12 of `LICENSE.md` ("NO WARRANTY") disclaim warranty and liability
to the extent the law allows, and the Copyright page of the administration shows the warranty disclaimer
([About and Copyright pages](../../features/6.0/about-and-package-pages.md#read-the-copyright-page)).
A buyer who needs a warranty, a guaranteed response time or liability coverage gets it through a contract with a
supplier; section 1 of the GPL explicitly allows offering "warranty protection in exchange for a fee". The only
response commitment in the project's own files is in `SECURITY.md`: a first answer to a private security report
"within one business day".

## Where to check it in the product

| What | Where |
|---|---|
| The license and copyright of the installation | **Info > Copyright** (`/ezinfo/copyright`) |
| Extensions and third-party software with their licenses | **Info > About** (`/ezinfo/about`) |
| The documentation license | The notice at the end of every help page and its PDF; [Free documentation](../free-documentation.md) |
| The license files | `LICENSE.md`, `LICENSE`, `COPYRIGHT.md` in the installation root; `doc/en/us/LICENSE-GFDL-1.3.txt` |
| The package's declared license | `composer.json` (`"license": "GPL-2.0-or-later"`) and [Packagist](https://packagist.org/packages/se7enxweb/exponential) |

## Buyer FAQ

| Question | Answer | Source |
|---|---|---|
| Do we pay a license fee? | No. | README, "License" |
| Can we still buy a commercial license? | No: "The Professional License is no longer available." | README |
| Can we change the code? | Yes. | `LICENSE.md` section 2 |
| Must we publish our changes? | Not because you use them on your own site. If you distribute the changed software, recipients get the source under the GPL. | `LICENSE.md` sections 0, 2, 3 |
| Can we switch suppliers? | Yes; the license gives the rights to every licensee. | `LICENSE.md` |
| Can we host it anywhere? | Yes, running is not restricted. | `LICENSE.md` section 0 |
| Is there a warranty? | Not from the license; get one by contract. | `LICENSE.md` sections 1, 11, 12 |
| Can we copy the help into our manual? | Yes, under the GNU FDL 1.3 conditions. | [Free documentation](../free-documentation.md) |
| Who holds the copyright? | 7x & Exponential Foundation, with eZ Systems AS for 1999 to 2014. | `COPYRIGHT.md` |
| Does a contribution we make stay ours? | Not under your control. A contribution to the kernel needs a signed CLA, and with it you give up control: 7x may use, change, relicense and distribute it without asking or paying you, and you cannot withdraw it (formally a license, §4). It is also released under GPL v2 or later. | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md#copyright-and-control-of-your-contribution-cla) |
| Is every part GPL? | The kernel is; check extensions and related products on Info > About. Velocity is MIT. | [About and Copyright pages](../../features/6.0/about-and-package-pages.md) |

## A license summary to hand to procurement

```text
LICENSE SUMMARY: Exponential                      Prepared: <date>   For: <buyer>

Software           Exponential <6.0.x>, package se7enxweb/exponential
License            GNU General Public License v2 or any later version (GPL-2.0-or-later)
License fee        None ("Using Exponential under the terms of the GNU GPL is free of charge")
Commercial license Not available ("The Professional License is no longer available")
Copyright          7x & Exponential Foundation (1998-2026); eZ Systems AS (1999-2014)
Documentation      GNU Free Documentation License 1.3 or later
Extensions in use  <list from Info > About, with each license>
Other components   <list from Info > About, Third-party software>
Warranty           None under the license; by contract with: <supplier, if any>
Security reports   Private, as described in SECURITY.md
Notes              This summary is not legal advice. License texts: LICENSE.md, doc/en/us/LICENSE-GFDL-1.3.txt
```

## Related pages

- [Sales and marketing: start here](sales-and-marketing-start-here.md)
- [Handling objections: security, longevity and support](objection-handling.md)
- [Comparing Exponential factually](comparing-exponential-factually.md)
- [Brand and naming rules](brand-and-naming-rules.md)
- [Risk management for Exponential projects](../leadership/risk-management.md)
- [The license of this documentation](../../README-LICENSE.md), [About and Copyright pages](../../features/6.0/about-and-package-pages.md)
- The license texts on the web: [GNU GPL v2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html), [GNU FDL 1.3](https://www.gnu.org/licenses/fdl-1.3.html)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
