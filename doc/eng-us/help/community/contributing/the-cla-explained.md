---
title: The CLA explained for community members
audience: community, developer, supporter, partner
category: community
order: 25
summary: What the 7x Contributor Licensing Agreement means for a community member - a copyright assignment for code contributed to the Exponential kernel - who needs it, how to sign, and what it does not cover, in plain language with the exact sources.
keywords: CLA, contributor licensing agreement, copyright assignment, 7x, kernel, pull request, extensions, employer, GPL, sign, community@se7enx.com
type: help
---
# The CLA explained for community members

Before reading further, the most important sentence, quoted from `CONTRIBUTING.md`:

> **By signing the CLA and submitting a contribution to the core kernel, you assign (transfer) the copyright in that
> contribution to 7x. 7x becomes the copyright owner.** You **give up control**: 7x may use, change, relicense and
> distribute your contribution in any way, including commercially and under other licenses, **without asking you and
> without paying you**. You **cannot withdraw it or set conditions on it**. **Your own custom extensions are not
> affected.**

This page is a summary, not legal advice. Read the agreement itself before you sign it.

## Contents

1. [When you need it](#when-you-need-it)
2. [When you do not need it](#when-you-do-not-need-it)
3. [How to sign](#how-to-sign)
4. [Questions people ask](#questions-people-ask)

## When you need it

"7x and the Exponential Foundation require a signed Contributor Licensing Agreement (CLA) for every code
contribution to the core kernel of Exponential, before that contribution can be merged." The core kernel is the
repository se7enxweb/exponential: `kernel/`, `lib/`, `bin/`, `design/`, `settings/`, `doc/`, the extensions shipped
inside it, "and everything else in this repository" (`CONTRIBUTING.md`).

- Every form of submission counts: a pull request, a patch, code in an issue, code sent by mail.
- A pull request without a signed CLA "is reviewed, but it is not merged until the CLA has been received".
- "The CLA will be signed once for all your future contributions"; you sign again only for a new legal entity.

## When you do not need it

- **Your own extensions, site packages, designs and themes** that you keep and license yourself, privately or in your
  own repository.
- **A site you build and run.** Using, changing and running Exponential is governed by the GPL, not the CLA.
- **Anything you mark "Not a Contribution"** in writing (CLA, §3).
- **Bug reports, questions and forum answers** without code.

Source: `CONTRIBUTING.md`, "What it does not cover".

## How to sign

1. Read the [Contributor Licensing Agreement page](https://share.se7enx.com/community-project/contributor-licensing-agreement-cla)
   and download the agreement (*7X CONTRIBUTOR LICENSING AGREEMENT*, version 1.0.1, PDF).
2. Fill in section 2 and the signature block, and sign.
3. Deliver it to 7x "by regular mail, by emailing a scan of the signed copy, or by fax"; the agreement gives
   community@se7enx.com. If you send it by e-mail, make sure it arrived.
4. Mention in your pull request that your CLA is signed; the template has a checkbox.

## Questions people ask

| Question | Answer, from `CONTRIBUTING.md` |
|---|---|
| Do I give away my copyright? | Yes, for the contribution: you assign it to 7x |
| Can I take it back? | No |
| Can 7x relicense it? | Yes, including in commercial products |
| Is a one-line fix a contribution? | For small fixes "in most cases we do not need you to sign a CLA", but sign anyway for pull requests to the kernel repository: a merge needs a CLA on file |
| I work for a company | The company usually owns the copyright: it signs, or confirms permission or a waiver (§6) |
| Does the CLA replace the GPL? | No. The contribution is released under the GPL like the rest; the CLA additionally assigns copyright to 7x |

The code of conduct adds: "Be honest about authorship. Sign the CLA only for contributions you have the right to
assign, and do not submit someone else's work as your own" (`CODE_OF_CONDUCT.md`).

## Related pages

- [Contributing code](contributing-code.md)
- [Licenses in the community](../how-it-works/licenses-in-the-community.md)
- [Licensing FAQ](../../project/licensing-faq.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->

## Copyright and copyleft

Since 29 October 2026 the copyright of Exponential is held by the Exponential Foundation and 7x together, the
Foundation named first: "Exponential Copyright © 1998-2026 Exponential Foundation and 7x", in files
"Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved." Who holds it and why, what the
GPL lets you do, the third-party notices that stay and how to write the notice in new files and extensions:
[Copyright and copyleft](../../copyright-and-copyleft.md).
