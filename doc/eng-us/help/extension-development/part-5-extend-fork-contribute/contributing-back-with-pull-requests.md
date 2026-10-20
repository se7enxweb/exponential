---
title: Contributing back with pull requests
audience: developer, designer, app-creator
category: extension-development
order: 80
summary: Send a fix or feature to the kernel or someone else's extension as a pull request: one change per branch, tests, a description of what was broken and how you checked it, and what to do after.
keywords: pull request, contribute, GitHub, branch, fork, description, tests, review, kernel, extension, upstream, CONTRIBUTING
type: help
---
# Contributing back with pull requests

Part 5, chapter 8. A pull request asks the maintainers of a repository to take your commits. It is how a fix you made
for yourself becomes a fix for everyone, and how you stop carrying it alone. This chapter goes through one from
start to end. It applies to the kernel (`se7enxweb/exponential`) and to any extension on GitHub.

## Before you start

1. **Read the project's rules.** The kernel's are in `CONTRIBUTING.md` at the root of the repository: the license
   your contribution goes under (GPL 2 or later), the commit message format, the file manifest, the admin styling
   rules. An extension may have its own `CONTRIBUTING.md`, or none; then look at how its recent commits are written.
   **For the kernel: expect many review rounds.** 7x reviews a commit or pull request in many more rounds than
   usual, sometimes hundreds of revisions; be ready to revise your change until it is simple to accept
   ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews)).
   **For the kernel: copyright assignment (CLA).** 7x and the Exponential Foundation merge a
   code contribution to the core kernel, sent in any form (pull request, patch, issue or mail), only after you have
   signed the Contributor Licensing Agreement. **By signing and submitting, you assign the copyright in your contribution to 7x, which becomes its owner, and give up control over it:** 7x may use, change, relicense and distribute it in any way, also under other
   licenses and in commercial products, without asking or paying you, and you cannot withdraw it or set conditions on it.
   How to sign:
   [Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla). It does not apply to the
   extensions you write and keep for yourself.
2. **Look for an existing issue or pull request** about the same thing. If there is one, comment there instead of
   starting a second.
3. **For a large change, ask first.** Open an issue that describes what you want to do and why. A maintainer may
   already have a plan, or a reason it cannot work.

## 1. One change, one branch

Fork the repository, clone your fork and create a branch named after the change
([Forking on GitHub, step by step](forking-on-github.md)). Keep the branch to one subject. Two unrelated fixes are two
pull requests: each can be reviewed and merged on its own, and a problem with one does not hold back the other.

## 2. Make the change the project's way

- Follow the code style of the files you touch, even where you would write it differently.
- Write commit messages the project can build a changelog from
  ([Commit messages the project can build a changelog from](commit-messages.md)). The kernel's commit-msg hook (run
  `make hooks` once in your clone) checks the subject for you.
- In the kernel, keep `share/filelist.md5` current (`make manifest-fix`); its pre-commit hook checks it.
- Add or change a test that fails without your change and passes with it.
- Update the documentation the change affects.

## 3. Check it

Run the project's checks before you push. For the kernel, `CONTRIBUTING.md` and
[Quality checks](../../../features/6.0/quality-checks.md) list them; for an extension, its `tests/` or its CI
workflow show what it runs. Then use the feature by hand.

## 4. Write the description

The description is what the reviewer reads first. A good one answers four questions:

| Question | Example |
|---|---|
| What was broken, or what is missing? | "Every sign-in from a real browser was answered with 403." |
| Why? | "With `Referrer-Policy: no-referrer`, browsers send `Origin: null` with the form POST." |
| What does the change do? | "Sends `same-origin` instead. One line." |
| How was it checked? | "A new test fails without the change and passes with it; the full suite has the same results as main." |

That example is a real pull request, told in full in
[Case study: a pull request to an upstream author](case-study-a-pull-request-to-an-upstream-author.md).

Also say what you did **not** check and why, and what you saw fail that is not yours (a test that fails on main too).
It saves the reviewer from finding it themselves and wondering.

## 5. Open it

```bash
git push -u origin fix-signin-origin
```

GitHub then offers **Compare & pull request**. Check that the base repository is the original (not your fork) and the
base branch is the one the project develops on (`main` or `master`). Paste the description. Open it as a **draft**
if you still want to change things.

## 6. After you open it

- **CI runs.** If a check fails, read its log, fix it on the same branch, push again; the pull request updates
  itself.
- **Answer review comments** with changes or with reasons. Mark conversations resolved only when the reviewer agrees,
  or when the project's habit is that the author resolves them.
- **Keep it current.** If the base branch moves on and conflicts appear, bring it in
  ([Keeping a fork in sync with its source](keeping-a-fork-in-sync.md)).
- **For the kernel, squash and rebase.** 7x requires the author to squash the pull request into logical commits
  (usually one) and rebase it onto `main` while it is open, so the merge is clean:
  `git fetch origin`, `git rebase -i origin/main`, then `git push --force-with-lease` to your pull request branch
  only. Once it is merged, `main`'s history is never rewritten; a later fix is a new pull request
  ([Code reviews](../../../../../CONTRIBUTING.md#code-reviews)).
- **Be patient.** Volunteer maintainers answer when they can. A friendly reminder after two or three weeks is fine.

## 7. After it is merged

- Delete the branch (GitHub offers a button).
- Sync your fork's `main`.
- If you used a waiting fork in a site, switch the site back to the original's release that contains your change.

## Related pages

- Previous: [Attribution and copyright notices](attribution-and-copyright-notices.md)
- Next: [Pull request etiquette](pull-request-etiquette.md)
- [Contribute extensions back](../../contribute-extensions-back.md)
- [Develop with Git](../../develop-with-git.md)
- [About pull requests](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/about-pull-requests) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
