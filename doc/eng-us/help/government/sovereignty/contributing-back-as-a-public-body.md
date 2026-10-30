---
title: Contributing back as a public body
audience: government, project-manager, developer
category: public-sector
order: 170
summary: Why and how a public body gives code, translations, documentation and bug reports back to the Exponential project: what is worth sharing, the steps for publishing an extension, contributing fixes to the kernel, and the internal approvals to arrange.
keywords: contribute, give back, public code, open source policy, extensions, pull request, translations, documentation, bug reports, shared development, public money public code
type: help
---
# Contributing back as a public body

This page is for managers and developers in public bodies that build on Exponential. Sharing what you build means
other bodies can use it and maintain it with you; it also keeps your own code in step with new releases. The
project's guide for publishing extensions is [Contribute extensions back](../../contribute-extensions-back.md).

## Contents

1. [What is worth sharing](#what-is-worth-sharing)
2. [Internal approvals](#internal-approvals)
3. [Publishing an extension](#publishing-an-extension)
4. [Fixes to the kernel](#fixes-to-the-kernel)
5. [Translations and documentation](#translations-and-documentation)
6. [Bug reports](#bug-reports)
7. [Requiring it from suppliers](#requiring-it-from-suppliers)

## What is worth sharing

| Share | Keep private |
|---|---|
| Generic extensions: datatypes, workflow events, import tools, accessibility fixes | settings with credentials, keys, host names |
| Translations of the interface into your official languages | personal data of any kind |
| Fixes to bugs you found | code that encodes security measures specific to your network |
| Documentation and training material you wrote | contracts and internal procedures |
| Content classes for common public sector content (as packages) | your content itself, unless it is open data |

## Internal approvals

Decide once, in writing: who may publish code on behalf of the body, under which license (the project recommends
GPL 2 or later for extensions), under which account (an organization account, not a personal one), and the review
step that checks for secrets and personal data before publishing.

## Publishing an extension

The steps in [Contribute extensions back](../../contribute-extensions-back.md): clean it up; add the license file;
write `ezinfo.php` and `extension.xml` with version, license and website; write `composer.json`; write a README; put
it on GitHub; release a version and register it on Packagist; offer it to the community; keep it alive.

## Fixes to the kernel

Kernel fixes go to the project as pull requests; the workflow from fork to merged pull request, commit message rules
and the CI checks are in [Contributing code back](../../partners/contributing-code-back.md). Contributions to the
kernel are released under the same license, GPL v2 or later.

**Copyright assignment (CLA).** Before a kernel contribution can be merged, 7x and the
Exponential Foundation require a signed Contributor Licensing Agreement from its copyright owner. **By signing and
submitting, the body assigns the copyright in the contribution to the Exponential Foundation and 7x, which become its owners, and gives up control over it:** The Exponential Foundation and 7x may use, change, relicense and distribute it in any way, also under other licenses and in commercial products, without asking or paying the body,
which cannot withdraw it or set conditions on it. Your internal approvals must accept this, and settle who may sign:
when the body has the rights to its staff's work, the body signs or gives its permission (CLA, section 6). Extensions the body builds and keeps for its own sites are not affected. See
[Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla).

## Translations and documentation

Interface translations are `.ts` files ([Languages in Exponential](../../../languages.md)); several shipped
translations are machine-assisted and the project welcomes review by native speakers. Documentation is free
documentation under the GFDL ([Contributing docs and translations](../../partners/contributing-docs-and-translations.md)).
A public body with official translators is well placed to review its own languages.

## Bug reports

Report on a clean installation where possible, with steps to reproduce, and without your content, personal data or
secrets ([Filing bugs as a partner](../../partners/filing-bugs-as-a-partner.md); the same rules suit everyone). Security
problems go privately to the address in the project's SECURITY.md, not to a public issue.

Before filing, consider whether the behavior is deliberate or open to interpretation: other sites, including other
public bodies, may depend on it. A request to change such a
[negative feature](../../project/getting-support.md#negative-features) needs a clear justification and may take
several rounds of review; a new option is easier to accept than a new default.

## Requiring it from suppliers

Write into contracts that generic improvements a supplier makes for you are offered back to the project, with your
approval, and that the supplier does the publishing work (see "Open source procurement").

## Related pages

- [Contribute extensions back](../../contribute-extensions-back.md)
- [Publishing an extension](../../partners/publishing-an-extension.md)
- [Working with the community](../../leadership/working-with-the-community.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
