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

## Step by step

1. Make the change on a branch of your fork, one topic per branch.
2. Test it on a current installation.
3. Write a commit message with a prefix and the reason ([Changelog entries](../distribution/changelog-prefixes.md)).
4. Open a pull request ([Pull requests to someone else's theme](pull-request-etiquette.md)). A change to
   Exponential itself, including the kernel's templates and designs, is merged only once you have signed the
   Contributor Licensing Agreement that 7x and the Exponential Foundation require for every kernel contribution.
   **By signing and submitting, you give up control over your contribution:** 7x and the recipients of its software
   receive a perpetual, worldwide, irrevocable, royalty-free license, patents included, to use, change, sublicense,
   relicense and distribute it in any way, also under other licenses and in commercial products, without asking or
   paying you, and you cannot withdraw or restrict it later. Under the agreement's wording you remain the nominal
   copyright holder and may still use your own work elsewhere
   ([Copyright and control of your contribution (CLA)](../../../../../../CONTRIBUTING.md#copyright-and-control-of-your-contribution-cla)); your own themes are
   not affected.
5. After it is merged, drop your private copy of the change at your next upgrade.

## Related pages

- [Develop with Git](../../develop-with-git.md)
- [Working with the community](../../leadership/working-with-the-community.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
