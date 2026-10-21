---
title: "Contributing back upstream"
audience: designer, developer
category: theme-lifecycle
order: 340
summary: Send fixes and improvements back to the themes, extensions and the Exponential project you build on - what to contribute, how to prepare a change, and why it pays off in less maintenance for you and shared growth for the community.
keywords: contribute back, upstream, pull request, community, shared growth, fixes, Exponential project, extensions
type: help
---
# Contributing back upstream

This page is for designers who changed someone else's theme or extension, or the kernel's templates, for their own
work. Every change you keep private, you must carry through every upgrade yourself. Every change accepted upstream is
maintained by everyone.

## What to contribute

| Contribute | Keep private |
|---|---|
| Bug fixes, security fixes | Client-specific branding |
| Accessibility and CSP improvements | Content and data |
| Translations | Anything with credentials or customer details |
| Compatibility with new PHP or Exponential versions | |
| Documentation | |

## Where

- **Themes and extensions of 7x**: the repositories under `github.com/se7enxweb`.
- **Exponential itself**: `github.com/se7enxweb/exponential`; its documentation is in `doc/` and free under the GFDL,
  so documentation improvements are welcome there too.
- **Other authors' themes**: their repositories; read their contribution notes first.

The Exponential project's practice is to merge every pull request and fix what needs fixing in its own commits on
top, so a contribution is not lost to a long review; that is no reason to send less careful work.

In Exponential and in any other project, a change that alters deliberate behavior other sites depend on, a
[negative feature](../../project/getting-support.md#negative-features), needs a clear justification and may go through
several rounds of review before it is accepted. Offer the new look or behavior as an option and keep the old default.

## Step by step

1. Make the change on a branch of your fork, one topic per branch.
2. Test it on a current installation.
3. Write a commit message with a prefix and the reason ([Changelog entries](../distribution/changelog-prefixes.md)).
4. Open a pull request ([Pull requests to someone else's theme](pull-request-etiquette.md)). A change to
   Exponential itself, including the kernel's templates and designs, is merged only once you have signed the
   Contributor Licensing Agreement that 7x and the Exponential Foundation require for every kernel contribution.
   **By signing and submitting, you assign the copyright in your contribution to 7x, which becomes its owner, and give up control over it:** 7x may use, change, relicense and distribute it in any way, also under other licenses and in commercial products, without asking or
   paying you, and you cannot withdraw it or set conditions on it
   ([Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla)); your own themes are
   not affected.
5. After it is merged, drop your private copy of the change at your next upgrade.

## Related pages

- [Develop with Git](../../develop-with-git.md)
- [Working with the community](../../leadership/working-with-the-community.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
