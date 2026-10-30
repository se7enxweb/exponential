---
title: Contributing code back
audience: partner
category: partnership
order: 630
summary: How partner developers contribute fixes and features to the Exponential kernel - why it pays off, the workflow from fork to merged pull request, commit message rules, the file manifest and CI, the merge policy, and agreeing contributions with customers.
keywords: contribute, pull request, fork, branch, commit message, CI, manifest, filelist.md5, GPL, upstream, kernel, merge policy
type: help
---
# Contributing code back

This page is for developers in partner companies. Contributing is how partners turn private fixes into shared
maintenance: once your fix is in the kernel, every release carries it and you stop maintaining it alone.

## Why it pays off

- **Less maintenance.** A private patch must be re-applied on every upgrade; a merged fix never again.
- **Points and reputation.** Code contributions count ("contribute to open-source extensions", "code contributions on
  exponential.earth" are tracked, <https://exponential.earth/partner/requirements>).
- **Influence.** Code is the clearest feature request ([Taking part in the roadmap](roadmap-participation.md)).

## The workflow

From CONTRIBUTING.md and [Develop with Git](../develop-with-git.md):

1. Fork <https://github.com/se7enxweb/exponential> and clone your fork.
2. Create a branch for one change: `git checkout -b feature/your-feature`.
3. Make the change. Follow the PSR standards and the coding style; administration templates follow the styling rules
   (no `<style>` blocks or `style=""` attributes; shared components and tokens).
4. Commit with a subject that starts with what the change does: `Added: `, `Updated: `, `Fixed: `, `Removed: ` or
   `Renamed: `.
5. Keep the file manifest current: every added, changed or removed file updates its line in `share/filelist.md5`
   (`make manifest-fix`); `make hooks` installs the pre-commit hook that checks it.
6. Make sure the tests pass, update the documentation, push to your fork and open a pull request.
7. Make sure a signed Contributor Licensing Agreement is on file with 7x, knowing that it gives control of the
   contribution to the Exponential Foundation and 7x (see below)
   ([Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)). When the partner company
   has the rights to its developers' work, the company signs it or gives its permission (CLA, section 6); one
   signature covers all future contributions.

## What happens next

- **Copyright assignment (CLA).** A kernel contribution, sent in any form (pull request,
  patch, issue or mail), is merged only once its signed CLA has been received; 7x and the Exponential Foundation
  require it. **By signing and submitting, the partner assigns the copyright in the contribution to the Exponential Foundation and 7x, which become its owners, and gives up control over it:** The Exponential Foundation and 7x may use, change, relicense and distribute it in any way, also under other licenses and in commercial
  products, without asking or paying the partner, who cannot withdraw it or set conditions on it. It does not
  affect the partner's own custom extensions or its customers' sites.

- Every pull request is merged. While it is open, you squash it and rebase it onto `main` as the reviews ask
  (`git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease` to your pull request branch only);
  once it is merged, `main`'s history is never rewritten, and later fixes come as new pull requests
  (`doc/eng-us/bc/6.0/ci.md`).
- A fix that changes behavior your customer saw as a bug, but other installations rely on (a
  [negative feature](../project/getting-support.md#negative-features)), needs a clear justification and may go through
  several rounds of review before it is accepted and merged. Keeping today's default and adding an option is easier to
  accept.
- CI checks syntax, the manifest, commit messages, code style, PHPStan and the tests on supported PHP versions
  (`doc/eng-us/bc/6.0/ci.md`).
- "By contributing, you agree that your contributions will be licensed under the GNU General Public License, version
  2.0 or later" (CONTRIBUTING.md).

## Agreeing it with customers

Paid customer work belongs to the contract. Add a clause:

```text
Generic improvements to Exponential and its public extensions made during this project (bug fixes and
general features that contain no customer data or business rules) may be contributed to the public
projects under their open source licenses. Customer content, data, designs and confidential logic are
never published.
```

## Small contributions count

- A failing test that shows a bug.
- A documentation fix: every help page has "Edit on GitHub".
- A translation fix ([Contributing documentation and translations](contributing-docs-and-translations.md)).

## Related pages

- [Contributing extensions back upstream](contributing-extensions-back.md)
- [Develop with Git](../develop-with-git.md)
- [Filing bugs well](filing-bugs-as-a-partner.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
