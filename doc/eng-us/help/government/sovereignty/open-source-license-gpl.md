---
title: The open source license (GPL)
audience: government, project-manager, admin
category: public-sector
order: 70
summary: Under which license Exponential comes (GNU GPL version 2 or any later version), what the license lets a public body do, what it asks when copies are passed on, and where to check it in the product and the repository.
keywords: GPL, GNU General Public License, GPL-2.0-or-later, free software, open source, license, Open Source Definition, copyright, LICENSE, COPYRIGHT
type: help
---
# The open source license (GPL)

This page is for managers, legal and procurement staff, and administrators who must state under which license the
platform runs. It summarizes; the license text decides. The plain-words summary for buyers is
[Licensing explained for buyers](../../business/licensing-for-buyers.md), and the license text is the
[GNU GPL version 2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [The licenses](#the-licenses)
2. [What the license allows](#what-the-license-allows)
3. [What it asks in return](#what-it-asks-in-return)
4. [Free software and open source](#free-software-and-open-source)
5. [Where to check](#where-to-check)

## The licenses

| What | License |
|---|---|
| Exponential 6, the kernel | GNU GPL v2 or any later version (`GPL-2.0-or-later` in `composer.json`) |
| Contributions to the kernel | the same |
| The documentation (6.x) | GNU FDL 1.3 or any later version (see "The documentation license (GFDL)") |
| Exponential Velocity | MIT |
| Each extension | stated in its own `ezinfo.php` and `extension.xml`, shown on the about page |

"Using Exponential under the terms of the GNU GPL is free of charge. The Professional License is no longer
available." (README, quoted in [Licensing explained for buyers](../../business/licensing-for-buyers.md)).

## What the license allows

Run the software for any purpose, on as many servers and sites as you like; read and change the source; host it
yourself or with any provider; hire anyone to work on it; pass on copies, changed or not, under the license's
conditions.

## What it asks in return

The conditions apply when you **distribute** the software, that is give copies to others: keep the notices, mark
changes, license the whole under the GPL, provide the source, add no further restrictions. Running it on your own
server to serve your website is running it, not distributing it. The GPL v2 has no clause about software used over a
network. Details and the questions to settle: "What the GPL means for a public body".

## Free software and open source

The GPL is a free software license written by the Free Software Foundation, and it meets the
[Open Source Definition](https://opensource.org/osd) of the Open Source Initiative. Procurement rules sometimes ask
for one term or the other; both apply.

## Where to check

- `LICENSE`, `LICENSE.md` and `COPYRIGHT.md` at the root of the repository; `composer.json` `"license"`.
- In the administration: the about page lists every extension with version and license
  ([About and package pages](../../../features/6.0/about-and-package-pages.md)).
- The license list of the package system ([Package licenses and versions](../../../features/6.0/package-licenses-and-versions.md)).

## Related pages

- [Licensing explained for buyers](../../business/licensing-for-buyers.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
