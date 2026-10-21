---
title: Documentation to ship with a bundle
audience: bundler, partner
category: bundling
order: 150
summary: Which documentation a bundle of Exponential carries - the doc/ tree with the built-in help, its GNU FDL 1.3 license and notices, your own guides - what the GFDL lets you do with the help (copy, adapt, translate, sell) and what it asks in return, and a list of pages worth pointing recipients to.
keywords: documentation, GFDL, GNU Free Documentation License, doc/, help, LICENSE-GFDL-1.3.txt, README-LICENSE.md, manual, adapt, translate, bundle documentation, first steps
type: help
---
# Documentation to ship with a bundle

This page is for bundlers who decide what documentation goes into a bundle and what they write themselves. The help
you are reading is free documentation, so you may ship it, adapt it and build your own manual on it.

## Contents

1. [What Exponential already ships](#what-exponential-already-ships)
2. [What the GFDL lets you do](#what-the-gfdl-lets-you-do)
3. [What the GFDL asks](#what-the-gfdl-asks)
4. [What you should write yourself](#what-you-should-write-yourself)
5. [Pages to point recipients to](#pages-to-point-recipients-to)

## What Exponential already ships

| Part | Where | Notes |
|---|---|---|
| The documentation tree | `doc/` (English in `doc/eng-us/`) | installation guide, features, upgrade notes, examples, this help |
| The built-in help | the help dashboard in the administration | reads the pages of `doc/`; "Only git-tracked files below doc/ ... are pages" for the command-line tool, and an installation without `.git` uses the manifest `share/filelist.md5` as its list |
| The license | `doc/eng-us/LICENSE-GFDL-1.3.txt` | GNU Free Documentation License 1.3 |
| Copyright and exceptions | `doc/eng-us/README-LICENSE.md` | who holds the copyright, older pages with other terms |

The help's file list comes from the Git index, `git ls-files`, or `share/filelist.md5` "for an installation without
.git" (`kernel/classes/help/exphelpallowlist.php`). A bundle built without `.git` therefore needs an up-to-date
manifest for the built-in help to list its pages ([Integrity checks](integrity-checks.md)).

## What the GFDL lets you do

From [Free documentation](../free-documentation.md), in short:

- read, copy and share the pages, "for free or for a fee, in any medium";
- translate them;
- adapt them into your own manual, course material or customer guide.

## What the GFDL asks

When you pass copies on, keep with every copy:

- the **copyright notices** of the pages;
- the **license notice**;
- a **copy of the license** (`doc/eng-us/LICENSE-GFDL-1.3.txt`).

"Do not add conditions of your own, and do not use technical measures ... that stop the people you give copies to
from reading or copying them further." More than 100 copies in a hard-to-edit format need an editable copy as well;
an adapted version is released under the GFDL again. The details are in the license and on
[Free documentation](../free-documentation.md). Not legal advice.

## What you should write yourself

The project documents Exponential; only you can document your bundle:

| Document | Contents |
|---|---|
| Getting started | how to start the bundle, where the site and the administration are, how the first password reaches the recipient |
| What is inside | the versions, the extensions, the site package, the runtime; your third-party list |
| Configuration | where the settings of your bundle live, which ones the recipient may change |
| Updates | how the recipient updates, and how you announce updates ([Updates and upgrade paths](updates-and-upgrade-paths.md)) |
| Support | what you support and where to ask ([Support boundaries](support-boundaries.md)) |
| Licenses | your notice file and the source offer ([License texts and notices](license-texts-and-notices-to-ship.md)) |

Building on this help for those documents is welcome; keep the notices.

## Pages to point recipients to

| Need | Page |
|---|---|
| Sign in and change the password | [The default administrator password](../default-administrator-password.md), [Change the administrator password](../change-the-admin-password.md) |
| Harden the installation | [Security hardening](../security-hardening.md), [Chapter 13](../../install/13-security-hardening.md) |
| Back up and restore | [Chapter 10.9 Backups and restore](../../install/10-after-installing.md#109-backups-and-restore) |
| Update | [Update with Git](../update-with-git.md), [Chapter 11: Upgrading](../../install/11-upgrading.md) |
| Databases | [Database support](../database-support.md) |
| When something goes wrong | [When something goes wrong](../when-something-goes-wrong.md) |

## Related pages

- [The license of this documentation](../../README-LICENSE.md)
- [Contributing docs and translations](../partners/contributing-docs-and-translations.md)
- [License texts and notices to ship](license-texts-and-notices-to-ship.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
