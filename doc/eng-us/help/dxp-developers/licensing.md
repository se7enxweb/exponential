---
title: Licensing
audience: dxp-developer
category: dxp-development
order: 45
summary: The license of Exponential Platform DXP as its packages declare it - GPL-2.0-or-later or proprietary - what the GPL means for a project you build and deliver, the licenses of the bundled third-party packages, and how to produce a license report.
keywords: license, GPL, GPL-2.0-or-later, proprietary, dual license, open source, composer licenses, copyleft, delivering to customers, third-party packages
type: help
---
# Licensing

This page is for developers and project leads who must state the license of a DXP project or deliver one to a
customer. It reports what the packages declare; it is not legal advice.

## Contents

1. [What the packages declare](#what-the-packages-declare)
2. [What the GPL means for your project](#what-the-gpl-means-for-your-project)
3. [Third-party packages](#third-party-packages)
4. [A license report](#a-license-report)
5. [Documentation](#documentation)

## What the packages declare

| Package | `license` in `composer.json` |
|---|---|
| `se7enxweb/exponential-platform-dxp` | `(GPL-2.0-or-later or proprietary)` |
| `se7enxweb/exponential-platform-dxp-skeleton` | `(GPL-2.0-or-later or proprietary)` |
| `se7enxweb/admin-ui` and the other forks | GPL 2.0 or later (the forks changed the wording from "GPL 2.0 only", [Step 6](../../bc/6.0/platform-package-forks-and-command-renames.md#step-6-license-wording)) |

The skeleton's README carries the badge "GPL v2 (or any later version)". The copyright lines name 7x and, for the
upstream code, Ibexa AS (formerly eZ Systems AS).

## What the GPL means for your project

- You may use, study, change and run the DXP for any purpose, including commercial projects.
- When you **distribute** the software (hand a copy to a customer, publish it), the GPL code and your changes to it
  go with their source under the GPL.
- Running a site for visitors is not distribution of the code to them under GPL version 2.
- Your own code in `src/` that works with the DXP is part of a combined work when you distribute it; decide its
  license with that in mind, and ask a lawyer for a contract that depends on it.

The designer audience's pages explain the same rules for themes in plain words:
[GPL for theme code](../designers/distribution/gpl-for-theme-code.md),
[GPL and selling](../designers/sales/gpl-and-selling.md).

## Third-party packages

The metapackage brings Symfony (MIT), Doctrine (MIT), Twig (BSD-3-Clause), the Lexik JWT bundle (MIT) and many more.
Each keeps its own license. A delivered project lists them.

## A license report

```bash
composer licenses --no-dev                          # every PHP package with its license
composer licenses --format=json > var/licenses.json
yarn licenses list                                  # Node packages used by the builds
```

Read the report for packages without a license or with a license your customer does not accept, before the contract,
not after.

## Documentation

These help pages are free documentation under the GNU FDL 1.3 ([Free documentation](../free-documentation.md)): you
may copy them into project documentation for a customer, under the same license.

## Related pages

- [Reading the metapackage](reading-the-metapackage.md)
- [Licensing choices overview (for themes)](../designers/distribution/licensing-choices-overview.md)
- [Contributing to the forks](contributing-to-the-forks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
