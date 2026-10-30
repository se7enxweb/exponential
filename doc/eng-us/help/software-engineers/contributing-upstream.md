---
title: Contributing upstream
audience: software-engineer, developer
category: software-engineering
order: 910
summary: How an engineer sends a change to the Exponential kernel or one of its extensions - forking and branching, the checks to run first, the copyright assignment (CLA) required for kernel contributions and what it means, pull request expectations and many review rounds, squash and rebase, licensing under the GPL, issues and private security reports.
keywords: contributing, upstream, pull request, fork, CLA, copyright assignment, code review, review rounds, squash, rebase, GPL, license, issues, CONTRIBUTING.md, code of conduct, software engineer
type: help
---
# Contributing upstream

This page is for engineers who want a fix or a feature in Exponential itself rather than carrying it in their own
code. The authoritative text is `CONTRIBUTING.md` in the root of the repository; this page puts it in order and adds the
engineering checks.

## Contents

1. [Kernel or extension](#kernel-or-extension)
2. [The copyright assignment (CLA)](#the-copyright-assignment-cla)
3. [Preparing the change](#preparing-the-change)
4. [The pull request](#the-pull-request)
5. [Review](#review)
6. [Licensing](#licensing)
7. [Issues and security reports](#issues-and-security-reports)

## Kernel or extension

| Target | Repository | Notes |
|---|---|---|
| The kernel | `se7enxweb/exponential`: `kernel/`, `lib/`, `bin/`, `design/`, `settings/`, `doc/`, the extensions shipped inside it | the CLA is required before a merge |
| An extension | its own `se7enxweb/<name>` repository | see [Contributing back with pull requests](../extension-development/part-5-extend-fork-contribute/contributing-back-with-pull-requests.md) |
| Documentation | the files in `doc/` of the kernel | the same process ([Documentation as code](documentation-as-code.md)) |

## The copyright assignment (CLA)

7x and the Exponential Foundation require a signed Contributor Licensing Agreement for every code contribution to the
core kernel, before it can be merged, whatever form it arrives in (pull request, patch, code in an issue, mail).
`CONTRIBUTING.md` states it plainly: **by signing the CLA and submitting a contribution to the core kernel, you assign
the copyright in that contribution to the Exponential Foundation and 7x**, which may use, change, relicense and distribute it without asking or paying
you; you cannot withdraw it or set conditions on it. It is signed once for all future contributions. Your own custom
extensions are not affected. Read the agreement itself before signing; the summary is not legal advice. If your
employer has rights to what you write, you need its permission.

## Preparing the change

```bash
composer install
make hooks
make devtools
```

Then, on a branch:

- follow the [coding standards](../../standards/coding-standards.md) and the style of the files around your change;
- name new code with the `exp` prefix ([The prefix rule](../../standards/naming-prefixes-sevenx-and-exp.md));
- add a test that fails without your change and passes with it;
- update the documentation and the changelog where needed;
- refresh the file manifest: `make manifest-fix`, and commit `share/filelist.md5` with the change;
- run `make check` and the tests of your change ([CI gates](ci-gates.md)).

Use the [Code review checklist](code-review-checklist.md) on your own change before anyone else does.

## The pull request

- One change per pull request; a fix and a refactoring are two.
- Describe what changed, why, and how you tested it.
- Tick the CLA checkbox of the pull request template once your CLA is on file.
- Keep the branch current with `main`; a pull request that conflicts with `main` gets no CI checks at all.

## Review

`CONTRIBUTING.md` warns: 7x does a much larger than usual number of review rounds, sometimes hundreds of revisions,
before it accepts a change. It is how the kernel stays maintainable, not a judgment of the author. To get through
faster: keep the change small, answer every review comment in the pull request, rebase when `main` moves, and squash
the fixups of each round into logical commits.

## Licensing

Exponential is licensed under the GNU GPL, version 2 or later; contributions are licensed the same way. The "or later"
clause lets the project move to later GPL versions. Check that any code you bring in is compatible
([Licenses and compatibility](../extension-development/part-5-extend-fork-contribute/licenses-and-compatibility.md)).
The project's code of conduct is the Contributor Covenant 2.1 with Exponential additions (`CODE_OF_CONDUCT.md`).

## Issues and security reports

- Bugs, feature requests and discussions go to the issue tracker, with its templates; the help's **Report an error**
  page prepares a safe summary of the installation ([When something goes wrong](../when-something-goes-wrong.md)).
- Some reported bugs are [negative features](../project/getting-support.md#negative-features): deliberate behavior,
  or behavior open to interpretation, that other sites depend on. Changing one needs a clear justification, is best
  proposed as an option rather than a new default, and may take several rounds of review before it is merged.
- A security problem goes privately to the address in `SECURITY.md`, never into a public issue or pull request.

## Related pages

- [Git workflow](git-workflow.md)
- [Contribute extensions back](../contribute-extensions-back.md)
- [The maintainer path](../extension-development/part-5-extend-fork-contribute/the-maintainer-path.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
