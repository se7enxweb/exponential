---
title: License texts and notices to ship
audience: bundler, partner
category: bundling
order: 120
summary: The exact files and notices a bundle of Exponential carries - LICENSE, LICENSE.md, COPYRIGHT.md, the documentation license, extension license files and your own notices - where recipients can see them in a running installation (Info > About, Info > Copyright), and a notice file template for your bundle.
keywords: license text, notices, LICENSE, LICENSE.md, COPYRIGHT.md, LICENSE-GFDL-1.3.txt, README-LICENSE.md, Info About, Info Copyright, ezinfo, notice file, attribution
type: help
---
# License texts and notices to ship

This page is for whoever assembles the files of a bundle release. It lists the license and notice files that must
travel with Exponential, shows where an installed site displays them, and gives a template for the notice file of
your own bundle. Not legal advice; the license texts decide.

## Contents

1. [Files from the kernel](#files-from-the-kernel)
2. [Files from the documentation](#files-from-the-documentation)
3. [Files from the extensions](#files-from-the-extensions)
4. [Where a running site shows them](#where-a-running-site-shows-them)
5. [A notice file for your bundle](#a-notice-file-for-your-bundle)
6. [Checklist](#checklist)

## Files from the kernel

| File | What it holds |
|---|---|
| `LICENSE` | the copyright lines of 7x and the Exponential Foundation and of the original developer |
| `LICENSE.md` | the text of the GNU General Public License, version 2 |
| `COPYRIGHT.md` | the copyright and the license terms ("either version 2 of the License, or (at your option) any later version") |
| headers in the source files | `@copyright` and `@license` lines, for example "GNU General Public License v2.0 (or any later version)" |

Ship these files unchanged and keep the headers. If your bundle removes the root files from the web root (for
example in a distribution package that moves documentation to `/usr/share/doc`), put them where your platform keeps
license files and say so in your notice.

## Files from the documentation

| File | What it holds |
|---|---|
| `doc/eng-us/LICENSE-GFDL-1.3.txt` | the GNU Free Documentation License 1.3 |
| `doc/eng-us/README-LICENSE.md` | who holds the copyright of the documentation, and which older pages keep different terms |

Details: [Documentation to ship](documentation-to-ship.md) and [Free documentation](../free-documentation.md).

## Files from the extensions

Every extension carries its own metadata and usually its own license file:

- `ezinfo.php`: `Name`, `Version`, `Copyright`, `License`, `Info_url`;
- `extension.xml`: `<license>`, `<copyright>`, `<info_url>`;
- a `LICENSE` file in many repositories.

Source: [Extension metadata](../../specifications/6.0/extension-metadata.md). Do not strip extension directories down
to "just the code" in a bundle: the metadata and license files go with it.

## Where a running site shows them

| Page | Shows | Source |
|---|---|---|
| **Info > About** (`/ezinfo/about`) | the version, the license, the copyright notice, a **Third-party software** table and every extension with its license and website | [About and Copyright pages](../../features/6.0/about-and-package-pages.md) |
| **Info > Copyright** (`/ezinfo/copyright`) | the copyright, the GPL version 2 or later, the warranty disclaimer and the attribution to the original developers "that the license requires" | the same page |

Do not remove or hide these pages in a bundle; they are how a recipient learns their rights. If your bundle has its
own admin design, keep a way to reach them.

## A notice file for your bundle

Put a short, plain notice at the top level of the bundle (for example `NOTICE.txt` or a section of your README):

```text
<Bundle name> <version>
Copyright (C) <year> <your company>.

This bundle contains Exponential <version>, free software under the
GNU General Public License, version 2 or (at your option) any later version.
Copyright (C) 1998 - 2026 7x & Exponential Foundation.
Copyright (C) 1999 - 2014 eZ Systems AS.
See LICENSE, LICENSE.md and COPYRIGHT.md.

It also contains the third-party components listed in THIRD-PARTY.txt,
each under its own license.

The corresponding source code of the GPL components of this bundle is
available at <URL>  (or: see the written offer in SOURCE-OFFER.txt).

The documentation in doc/ is under the GNU Free Documentation License 1.3
or any later version; see doc/eng-us/LICENSE-GFDL-1.3.txt.
```

Copy the copyright lines from `LICENSE` and `COPYRIGHT.md` of the exact version you ship; they change with the years.

## Checklist

- [ ] `LICENSE`, `LICENSE.md`, `COPYRIGHT.md` are in the bundle, from the version you ship.
- [ ] `doc/eng-us/LICENSE-GFDL-1.3.txt` and `doc/eng-us/README-LICENSE.md` are in it if the documentation is.
- [ ] Every extension directory keeps its `ezinfo.php`, `extension.xml` and license file.
- [ ] Your notice file names the bundle, Exponential's license, the third-party list and the source offer.
- [ ] **Info > About** and **Info > Copyright** work in the installed bundle.

## Related pages

- [GPL obligations when you distribute](gpl-obligations-when-distributing.md)
- [Third-party components and their licenses](third-party-components-and-their-licenses.md)
- [Licensing for partners](../partners/licensing-for-partners.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
