---
title: Contributing code
audience: supporter
category: supporting
order: 30
summary: How individuals contribute code to Exponential - finding something to work on, setting up from Git, the branch and pull request workflow of CONTRIBUTING.md, commit messages, the file manifest, tests and CI, the merge policy and the license of contributions.
keywords: code, contribute, pull request, fork, branch, Git, GitHub, commit message, CI, tests, manifest, GPL, first contribution
type: help
---
# Contributing code

This page is for developers who want to send their first or next pull request to Exponential. The rules are in
[CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md); the Git workflow is explained
step by step on [Develop with Git](../develop-with-git.md).

## Find something to work on

- Open issues: <https://github.com/se7enxweb/exponential/issues>.
- Something that annoyed you in your own work: that is usually the best first contribution.
- Extensions: each `se7enxweb/*` extension is its own repository with its own issues.

## Set up

Install Exponential from Git with full history ([Develop with Git](../develop-with-git.md)). SQLite is the default
database, so a development installation needs no database server ([Database support](../database-support.md)).

## The workflow

1. Fork the repository and clone your fork.
2. Create a branch: `git checkout -b feature/your-feature`.
3. Follow the PSR standards and the coding style; administration templates follow the styling rules (no `<style>`
   blocks, no `style=""` attributes; shared components and tokens) (CONTRIBUTING.md).
4. Commit with a subject that starts with `Added: `, `Updated: `, `Fixed: `, `Removed: ` or `Renamed: `, for
   example `Fixed: The redirect keeps the query string on the own host` (CONTRIBUTING.md).
5. Update `share/filelist.md5` for every file you add, change or remove (`make manifest-fix`); `make hooks` installs
   a pre-commit hook that checks it, the PHP syntax and the code style.
6. Run the tests, update the documentation, push and open a pull request. "Keep your pull request focused. If you
   have multiple contributions, submit multiple pull requests."

## What happens to your pull request

- CI runs syntax, manifest, commit message, code style, PHPStan and test checks on the supported PHP versions
  (`doc/eng-us/bc/6.0/ci.md`).
- It is reviewed, and every pull request is merged. While it is open, you squash it and rebase it onto `main` as the
  reviews ask (`git fetch origin`, `git rebase -i origin/main`, `git push --force-with-lease` to your pull request
  branch only); once it is merged, `main`'s history is never rewritten (`doc/eng-us/bc/6.0/ci.md`).
- A pull request that changes deliberate behavior, a [negative feature](../project/getting-support.md#negative-features)
  other sites depend on, needs a clear justification and can take several rounds of review before it is accepted and
  merged. Adding an option instead of changing the default is the easier path.

## License

"By contributing, you agree that your contributions will be licensed under the GNU General Public License, version
2.0 or later" (CONTRIBUTING.md).

**Copyright assignment (CLA).** The Exponential Foundation and 7x merge a code contribution to
the core kernel, sent in any form (pull request, patch, issue or mail), only after you have signed the Contributor
Licensing Agreement. **By signing and submitting, you assign the copyright in your contribution to 7x, which becomes its owner, and give up control over it:** 7x may use, change, relicense and distribute it in any way, also under other licenses and in commercial
products, without asking or paying you, and you cannot withdraw it or set conditions on it. One signature covers all your
future contributions. Extensions you write for yourself are not affected. How to sign:
[Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla).

## First contribution ideas

- A test that reproduces a reported bug.
- A PHP deprecation fix for the newest PHP version.
- A small usability fix in the administration.

## Related pages

- [Develop with Git](../develop-with-git.md)
- [Testing and reporting bugs](testing-and-reporting-bugs.md)
- [Contributing code back (for partners)](../partners/contributing-code-back.md)
- [Contribute to the project](../project/contribute-to-the-project.md): every way to help build Exponential
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
