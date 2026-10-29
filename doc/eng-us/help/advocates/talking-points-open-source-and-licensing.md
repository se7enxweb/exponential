---
title: "Talking points: open source and licensing"
audience: advocate
category: advocacy
order: 15
summary: How to talk correctly about Exponential's licenses, the GPL v2 or later for the code and the GNU FDL 1.3 or later for the documentation, what they allow and require, how contributions are licensed, and the mistakes to avoid.
keywords: talking points, open source, free software, GPL, GPLv2, GNU General Public License, GFDL, GNU Free Documentation License, license, copyright, contributions, freedom, lock-in
type: help
---
# Talking points: open source and licensing

This page is for advocates who talk about why Exponential is free software and what that means in practice. Licenses
are legal texts; say what they are and point to them, and leave legal advice to lawyers.

> **Warning**
> This page is not legal advice. Quote the license texts and the project's own statements; for a legal question,
> send the person to the license text and their own adviser.

## Contents

1. [The two licenses](#the-two-licenses)
2. [The points](#the-points)
3. [Contributions and their license](#contributions-and-their-license)
4. [Mistakes to avoid](#mistakes-to-avoid)
5. [Questions you will hear](#questions-you-will-hear)

## The two licenses

| What | License | Source |
|---|---|---|
| The code of Exponential | GNU General Public License, version 2 or any later version | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE), [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| The documentation in `doc/` | GNU Free Documentation License, version 1.3 or any later version | [The license of this documentation](../../README-LICENSE.md) |
| Copyright | "Exponential Foundation and 7x" (with "eZ Systems AS" for 1999 to 2014) | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| Velocity | MIT license (its own `composer.json`) | [Exponential Velocity](../who-is-the-exponential-foundation.md#exponential-velocity) |

Extensions each carry their own license; read the extension's files ([Extensions](../../guides/extensions.md)).

## The points

| Say | Source |
|---|---|
| "Exponential is free software under the GNU GPL version 2 or later. You may run, study, change and share it." | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) |
| "The 'or later' clause lets the project move to future GPL versions." | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |
| "The documentation is free too: you may copy, translate, adapt and share it under the GNU FDL." | [Free documentation](../free-documentation.md) |
| "There is no license fee for the software." | [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) |
| "Because the code is free, no single company can take it away. That is how it outlived its original vendor." | [Handling objections, Longevity](../business/objection-handling.md#longevity-will-it-still-be-here-in-ten-years) |
| "The full history since 2002 is public: anyone can read who changed what and why." | [Principles you can see in the code](../who-is-7x.md#principles-you-can-see-in-the-code) |
| "Contributions stay free: changes are released under the same GPL." | [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) |

## Contributions and their license

- **Code**: "By contributing, you agree that your contributions will be licensed under the GNU General Public License,
  version 2.0 or later" ([CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md)).
- **Documentation**: "A change to a page under the notice above is accepted under the same license, the GNU Free
  Documentation License 1.3 or any later version" ([Contributing to the documentation](../../README-LICENSE.md#contributing-to-the-documentation)).
- **Text copied from elsewhere** with a different license has to say so in its pull request (same source). Never copy
  a blog post or a page from another site into the help without checking its license
  ([Citing sources and licensing your writing](citing-sources-and-licensing-your-writing.md)).

## Mistakes to avoid

| Do not say | Say instead | Why |
|---|---|---|
| "It is freeware" or "public domain" | "It is free software under the GNU GPL" | The GPL has conditions ([Brand and naming rules, Do and Don't](../business/brand-and-naming-rules.md#do-and-dont)) |
| "You can do anything with it" | "You may run, study, change and share it under the GPL's conditions" | Sharing changed versions has conditions |
| "You must publish your site's code" | "Read the license; ask a lawyer for your case" | That is a legal question about distribution |
| "7x owns Exponential" | "The Exponential Foundation and 7x hold the copyright; the license is GPL" | [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) |
| "Licence" | "License" | American English ([Naming rules quick card](naming-rules-quick-card.md)) |

## Questions you will hear

| Question | Answer |
|---|---|
| "Can we use it commercially?" | The GPL allows commercial use. Conditions apply when you distribute. Read the license; ask your adviser for your case. |
| "Can we build closed extensions?" | That is a legal question about the GPL; point to the license text and an adviser. Do not answer it yourself. |
| "Can I copy a help page into our intranet?" | Yes, under the GNU FDL's conditions: keep the notice and the license ([Free documentation](../free-documentation.md#copy-it-and-share-it-with-other-users)). |
| "Can I translate the help?" | Yes, and contributing the translation back helps everyone ([Free documentation](../free-documentation.md#translate-it)). |

## Related pages

- [Advocating for open source](advocating-for-open-source.md)
- [Advocating for free documentation](advocating-for-free-documentation.md)
- [Licensing for buyers](../business/licensing-for-buyers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](../copyright-and-copyleft.md).
