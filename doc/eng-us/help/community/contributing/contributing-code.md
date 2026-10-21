---
title: Contributing code as a community member
audience: community, developer, supporter
category: community
order: 24
summary: The workflow for a code contribution to the Exponential kernel from a community member's point of view - fork, branch, coding standards and naming prefixes, prefixed commit messages, the file manifest, tests, the CLA, the pull request and the squash-and-rebase rule.
keywords: code, pull request, fork, branch, commit message, Added Updated Fixed Removed Renamed, manifest, filelist.md5, make hooks, coding standards, PSR, exp prefix, tests, CLA, squash, rebase
type: help
---
# Contributing code as a community member

Code contributions go to the kernel repository [se7enxweb/exponential](https://github.com/se7enxweb/exponential) as
GitHub pull requests. The binding rules are in `CONTRIBUTING.md`; this page puts them in the order you meet them.

## Contents

1. [Before you write code](#before-you-write-code)
2. [While you write code](#while-you-write-code)
3. [Before you open the pull request](#before-you-open-the-pull-request)
4. [The pull request](#the-pull-request)
5. [Code that is not kernel code](#code-that-is-not-kernel-code)

## Before you write code

1. **Check for an issue.** Search the tracker; if your change fixes a known issue, mention it. For a larger feature,
   open an issue first and describe the idea, so you do not build something that cannot be merged.
2. **Fork and clone**: `git clone https://github.com/your-username/exponential.git`, then `composer install`
   (`CONTRIBUTING.md`, "Getting Started").
3. **Install the hooks** with `make hooks`: the pre-commit hook checks the manifest, the PHP syntax and the code
   style; the commit-msg hook checks the message.
4. **Branch**: `git checkout -b feature/your-feature`.

## While you write code

| Rule | Source |
|---|---|
| Follow the PSR standards and the project's [coding standards](../../../standards/coding-standards.md) | `CONTRIBUTING.md`, "Making Changes" |
| Name new code by [the prefix rule](../../../standards/naming-prefixes-sevenx-and-exp.md): new kernel code uses `exp`; existing identifiers such as `eZContentObject` or `eZINI` are never renamed | same |
| Templates and stylesheets of the administration designs follow [Styling the administration](../../../bc/6.0/admin4-styling.md): no `<style>` block or `style=""` attribute, shared components and tokens | same |
| Add tests that fail without your change and pass with it | `CONTRIBUTING.md`, "Code Reviews" |
| One change per pull request: "A fix and a refactoring are two pull requests" | same |

## Before you open the pull request

- **Commit messages** start with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `, for example
  `Fixed: The redirect keeps the query string on the own host` (`CONTRIBUTING.md`).
- **The manifest**: "every commit that adds, changes or removes a file updates its line in `share/filelist.md5`
  (`make manifest-fix`)".
- **Tests pass**, and the documentation is updated if behavior changed.
- **Your CLA is on file** with 7x ([The CLA explained](the-cla-explained.md)).

The checks that CI runs are described in [Quality checks](../../../features/6.0/quality-checks.md).

## The pull request

1. Push your branch to your fork and open a pull request against `main`.
2. Fill in the template and tick the CLA checkbox.
3. Expect review rounds: "7x does a much larger than usual number of review rounds before it accepts a commit or pull
   request, sometimes hundreds of revisions" (`CONTRIBUTING.md`, "Code Reviews").
4. **Squash and rebase** your pull request onto `main` while it is open; force-push only to your own branch.
5. Once merged, `main`'s history is never rewritten; a later fix is a new pull request.

[Reviewing and being reviewed](reviewing-and-being-reviewed.md) has advice for the review rounds.

## Code that is not kernel code

| Your code | Where it goes | CLA? |
|---|---|---|
| A fix to an extension that has its own repository | That repository's pull requests | Ask its maintainer; the CLA covers code "you want 7x to include" in the project |
| Your own extension, theme or site package | Your own repository, Projects, Packagist | No ([Sharing extensions and themes](sharing-extensions-and-themes.md)) |
| A fix to a library Exponential depends on | The library's own project | Its rules |

## Related pages

- [Contributing as a community member](README.md)
- [Contributing code (supporters)](../../supporters/contributing-code.md)
- [Develop with Git](../../develop-with-git.md)
- [Contributing back with pull requests](../../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)
- [Pull request etiquette](../../extension-development/part-5-extend-fork-contribute/pull-request-etiquette.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
