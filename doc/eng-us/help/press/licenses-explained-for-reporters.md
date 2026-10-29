---
title: Licenses explained for reporters
audience: press
category: press-media
order: 40
summary: The licenses around Exponential in plain words for journalists - the GNU GPL for the software, the GNU FDL for the documentation, MIT for Velocity, the shared copyright, what "free of charge" means, what 7x sells, and the license phrases that are often reported wrongly.
keywords: license, licenses, GPL, GNU General Public License, GFDL, GNU Free Documentation License, MIT, open source, free software, copyright, free of charge, professional license, proprietary
type: help
---
# Licenses explained for reporters

This page is for journalists who must explain how Exponential is licensed. It gives the facts from the repository
and plain explanations. It is not legal advice; for a legal question about a specific use, readers should ask a
lawyer.

## Contents

1. [The licenses in one table](#the-licenses-in-one-table)
2. [The GPL in plain words](#the-gpl-in-plain-words)
3. [The GFDL in plain words](#the-gfdl-in-plain-words)
4. [Who holds the copyright](#who-holds-the-copyright)
5. [Free of charge, and what is sold](#free-of-charge-and-what-is-sold)
6. [Phrases often reported wrongly](#phrases-often-reported-wrongly)

## The licenses in one table

| What | License | Source |
|---|---|---|
| The software (Exponential 6) | GNU General Public License, version 2 or any later version | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE); README, "License" |
| Contributions to it | The same license: "any modifications made to the project are also released under the same GPLv2 or later license" | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| The documentation, including this help | GNU Free Documentation License 1.3 or any later version | [The license of this documentation](../../README-LICENSE.md) |
| Exponential Velocity | MIT | its `composer.json` |
| Exponential Platform DXP (meta package) | "GPL-2.0-or-later or proprietary" | its `composer.json` |
| Inherited eZ Systems documentation | The terms it came with (GPL v2) | [Older pages with their own terms](../../README-LICENSE.md#older-pages-with-their-own-terms) |
| Each extension | Its own license, stated in its package | the extension's `composer.json` and `ezinfo.php` |

## The GPL in plain words

Anyone may use the software for any purpose, study and change its code, and pass copies on, including changed
copies. Whoever distributes the software or a changed version must pass on the same freedoms and the source code.
Running it on your own server for your own site does not require publishing your changes under GPL v2. This is the
license that let 7x carry the code forward after its original developer stopped
([Story: open source and licenses](story-open-source-and-licenses.md)). What passing it on requires, how the license is
broken in practice, and why not giving changes back to the project is not a violation:
[License violations](../project/licensing-faq.md#license-violations).

## The GFDL in plain words

Anyone may copy, redistribute, translate and adapt the documentation, also commercially, if the copyright and license
notices are kept and changed versions stay under the same license
([Quoting and reusing the documentation](quoting-and-reusing-the-documentation.md)).

## Who holds the copyright

"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.", and "Copyright (C) 1999 - 2014 eZ Systems AS" for the
inherited code ([COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md)). Contributions are
released under the GPL, and kernel contributors assign the copyright in their contributions to the Exponential Foundation and 7x
([Copyright assignment (CLA)](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md#copyright-assignment-cla)). Write "The Exponential Foundation and 7x hold the copyright together", never
"7x owns Exponential".

## Free of charge, and what is sold

- "Using Exponential under the terms of the GNU GPL is free of charge. The Professional License is no longer
  available" (README, "License").
- 7x sells services around the software: development, support, hosting, training and migrations
  ([7x for reporters](7x-for-reporters.md#what-it-sells-and-what-stays-free)).
- The partner program has paid levels for companies ([Partner plans and prices](../partners/partner-plans-and-prices.md)).
- Sponsorship funds the work ([Writing about funding and sponsors](writing-about-funding-and-sponsors.md)).

## Phrases often reported wrongly

| Wrong | Right |
|---|---|
| "Exponential is freeware." | It is free software (open source) under the GPL; freeware usually means free of charge without the freedoms. |
| "Exponential is public domain." | It is copyrighted and licensed under the GPL. |
| "Open core: the good features cost extra." | No paid edition of the software is documented; the Professional License "is no longer available". |
| "The documentation is Creative Commons." | It is under the GNU FDL 1.3 or later. |
| "Velocity is GPL too." | Velocity is MIT licensed. |
| "Free means no one is paid." | Development is funded by 7x and sponsors ([Two organizations, one project](two-organizations-one-project.md)). |

## Related pages

- [Licensing explained for buyers](../business/licensing-for-buyers.md)
- [Talking points: open source and licensing](../advocates/talking-points-open-source-and-licensing.md)
- [Quoting and reusing the documentation](quoting-and-reusing-the-documentation.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](../copyright-and-copyleft.md).
