---
title: Contributing PHP changes
audience: php-developer, developer
category: php-development
order: 470
summary: How a PHP change reaches the Exponential kernel - the contributor license agreement, fork and branch, the checks to run before a pull request, commit messages, tests, the file manifest, review rounds, squashing and rebasing, and what to contribute as an extension instead.
keywords: contributing, pull request, CLA, contributor license agreement, copyright assignment, fork, branch, commit message, Added, Updated, Fixed, Removed, Renamed, make check, file manifest, code review, squash, rebase, CONTRIBUTING.md
type: help
---
# Contributing PHP changes

This page is for PHP developers who want a fix or a feature in the Exponential kernel itself, the repository
`se7enxweb/exponential`. The binding text is `CONTRIBUTING.md` at the root of the repository; this page puts its steps
in the order a PHP developer meets them and links to the checks.

## Contents

1. [Kernel or extension?](#kernel-or-extension)
2. [Before your first pull request: the CLA](#before-your-first-pull-request-the-cla)
3. [Fork, clone, branch](#fork-clone-branch)
4. [Write the change](#write-the-change)
5. [Check it](#check-it)
6. [Commit it](#commit-it)
7. [The pull request and the review](#the-pull-request-and-the-review)
8. [After the merge](#after-the-merge)

## Kernel or extension?

| Contribute to the kernel | Keep in an extension |
|---|---|
| a bug fix in kernel or library code | a feature only your sites need |
| a PHP compatibility fix | a module, datatype or operator of your own |
| a new extension point the kernel lacks | code that uses existing extension points |
| documentation of existing behavior | |

Your own extensions are not affected by the kernel's contribution terms. Publishing an extension of your own is
described in [Contribute extensions back](../contribute-extensions-back.md) and the
[extension developer book, part 5](../extension-development/part-5-extend-fork-contribute/README.md).

## Before your first pull request: the CLA

7x and the Exponential Foundation require a signed Contributor Licensing Agreement for every code contribution to the
core kernel, before it can be merged. Read the agreement and the summary in `CONTRIBUTING.md` ("Copyright assignment
(CLA)") before you sign: by signing and submitting a contribution to the core kernel, you assign the copyright in that
contribution to the Exponential Foundation and 7x. You sign once for all your future contributions; a pull request without a signed CLA on file is
reviewed but not merged until it is received.

## Fork, clone, branch

```bash
git clone https://github.com/<your-account>/exponential.git
cd exponential
composer install
make devtools && make hooks
git checkout -b fix/acme-short-description
```

Set up an installation to test on ([Setting up a PHP development checkout](a-php-development-checkout.md)).

## Write the change

- Follow the code style of `phpcs.xml.dist` and the files around your change ([The coding standard](the-coding-standard.md)).
- Keep PHP 8.0 compatibility ([Writing code that runs on PHP 8.0](writing-code-that-runs-on-php-8-0.md)).
- Name new code with the `exp` prefix; never rename existing `eZ*` identifiers
  ([Class naming and the exp prefix](class-naming-and-the-exp-prefix.md)).
- Admin templates and stylesheets follow [Styling the administration](../../bc/6.0/admin4-styling.md): no `<style>`
  blocks or `style=""` attributes, shared components and tokens.
- Add a test that fails without your change and passes with it ([PHPUnit without a database](phpunit-without-a-database.md)).
- Update the documentation the change affects.

## Check it

```bash
make check                                   # syntax, manifest, code style, PHPStan
make lint80                                  # PHP 8.0 syntax of changed files (needs a PHP 8.0 binary)
php vendor/bin/phpunit <your test files>
make manifest-fix                            # refresh share/filelist.md5 for added and changed files
```

[CI and reproducing a red check](ci-and-reproducing-a-red-check.md) lists what CI runs and how to reproduce each step.

## Commit it

The subject starts with what the change does, and the message says why:

```text
Fixed: The redirect keeps the query string on the own host

The query string was dropped when the target host was the current one, so
paged lists lost their page after a save.
```

Prefixes: `Added: `, `Updated: `, `Fixed: `, `Removed: `, `Renamed: `. The commit-msg hook checks them
([Commit messages](../extension-development/part-5-extend-fork-contribute/commit-messages.md)). Commit
`share/filelist.md5` with the change.

## The pull request and the review

1. Push your branch to your fork and open a pull request to `main`; tick the CLA checkbox of the template.
2. **Expect many review rounds.** The project reviews more than usual before it accepts a change; each round makes the
   change easier to accept. Answer every comment in the pull request, with a change or a reason. A change to a
   [negative feature](../project/getting-support.md#negative-features), behavior that is deliberate or open to
   interpretation and that other sites depend on, needs a clear justification and goes in more easily as an option
   than as a new default.
3. **Keep it small and focused**: a fix and a refactoring are two pull requests.
4. **Squash and rebase while it is open.** Fold the fixups of each round into logical commits and rebase onto the current
   `main`; force-push only your pull request branch (`git push --force-with-lease`).
5. A pull request is tested as merged into `main`; when `main` moves, merge or rebase and push again.

## After the merge

`main`'s history is never rewritten after a merge: a later fix comes as a new pull request. Releases and tags are made
by the project; a published version never changes.

## Related pages

- [Develop with Git](../develop-with-git.md)
- [Contributing back with pull requests](../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)
- [Source control and releases standard](../../standards/source-control-and-releases.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
