---
title: Contributing bundle fixes back
audience: bundler, partner, developer
category: bundling
order: 470
summary: Why and how a bundler sends fixes and improvements upstream instead of carrying them - the contribution workflow of the kernel, the GPL and the Contributor Licensing Agreement, extensions and site packages, documentation, and what to keep private.
keywords: contribute, upstream, pull request, fork, CONTRIBUTING.md, CLA, GPL, commit message, Added, Updated, Fixed, filelist.md5, extension, site package, documentation, bundle
type: help
---
# Contributing bundle fixes back

This page is for bundlers who find and fix problems while building their bundle. A fix you keep is a fix you
re-apply at every release; a fix that is merged upstream arrives with the next release for everyone, you included.

## Contents

1. [What to send back](#what-to-send-back)
2. [The kernel workflow](#the-kernel-workflow)
3. [Licensing of contributions](#licensing-of-contributions)
4. [Extensions and site packages](#extensions-and-site-packages)
5. [Documentation](#documentation)
6. [What stays private](#what-stays-private)

## What to send back

| Found while bundling | Send to |
|---|---|
| A bug in the kernel or an installer | <https://github.com/se7enxweb/exponential>, issue or pull request |
| A missing option that would make bundling easier | an issue describing the need first |
| A bug in an extension | the extension's repository |
| An improved or new site package | the package server's repository ([Contribute your site package](../site-packages/contribute-your-site-package.md)) |
| A mistake or gap in the documentation | a pull request to `doc/` |
| A security problem | security@se7enx.com, privately, never as a public issue |

## The kernel workflow

From [Contributing code back](../partners/contributing-code-back.md#the-workflow) and `CONTRIBUTING.md`:

1. Fork the repository and create a branch for one change.
2. Make the change, following the coding style.
3. Commit with a subject that starts with what it does: `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `.
4. Keep the file manifest current: every added, changed or removed file updates its line in `share/filelist.md5`
   (`make manifest-fix`; `make hooks` installs the check).
5. Make sure the tests pass, update the documentation, push and open a pull request.
6. Have a signed Contributor Licensing Agreement on file with 7x.

## Licensing of contributions

"By contributing, you agree that your contributions will be licensed under the GNU General Public License, version
2.0 or later" and "a contribution to the core kernel needs your signed Contributor Licensing Agreement"
(`CONTRIBUTING.md`). Read the agreement before you sign; when your company holds the rights to its developers' work,
the company signs or gives its permission ([Contributing code back](../partners/contributing-code-back.md)).

## Extensions and site packages

- Find the upstream of an extension before you fork it ([Contributing extensions back upstream](../partners/contributing-extensions-back.md)).
- Generic extensions you wrote for your bundle may be worth publishing ([Publishing extensions from a bundle](publishing-extensions-from-a-bundle.md)).
- "Built a site package? 7x and the Exponential Foundation strongly encourage you to submit it by pull request to the
  repository behind the package server, so every installation can offer it" ([Site packages: start here](../site-packages/README.md)).

## Documentation

The help is free documentation under the GNU FDL 1.3; bundlers who write guides on top of it can send improvements to
the original pages ([Contributing docs and translations](../partners/contributing-docs-and-translations.md)). A page
about a bundling problem helps the next bundler.

## What stays private

- Customer names, credentials, content and business rules.
- Your bundle's commercial terms.
- Anything you are not allowed to publish; check that you hold the rights before contributing.

Partners earn points for community work, including contributions ([Earning points through community work](../partners/earning-points-through-community-work.md)).

## Related pages

- [Contribute extensions back](../contribute-extensions-back.md)
- [Develop with Git](../develop-with-git.md)
- [Integrity checks](integrity-checks.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
