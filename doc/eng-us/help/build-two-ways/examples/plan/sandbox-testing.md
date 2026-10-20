---
title: "Plan a sandbox for risky changes"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1028
summary: Try changes that must be published to be seen on a copy of the installation, never on the live site.
keywords: sandbox, test copy, staging, safe testing, copy of the database
type: help
---
# Plan a sandbox for risky changes

Publishing a test layout, mapping or menu never touches the live site.

*Life cycle: Plan, example 29 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [On a copy of the installation](#on-a-copy-of-the-installation)
2. [Which to choose](#which-to-choose)
3. [Check](#check)

## On a copy of the installation

1. A copy needs the code, a copy of `settings/` and of the database, its own `var/` directory and a web server on another address.
2. These pages were written that way: every publish was on such a copy.

## Which to choose

Drafts are enough for looking; a copy is needed for anything that must be published to be seen.

## Check

- The copy's database is not the live one (compare a test user that exists only in the copy).

## Related pages

- [Measured steps and times](../../measured-steps-and-times.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
