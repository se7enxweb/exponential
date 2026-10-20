---
title: "Lesson 13.3: A pull request"
audience: trainer, developer
category: training
order: 133
summary: Learners prepare a small fix to the kernel or an extension as a pull request: a fork, a branch, a focused commit with the right subject, the checks and the manifest, a push to the fork, and a description reviewers can act on. 50 minutes.
keywords: developers course, pull request, GitHub, fork, branch, remote, commit message, Fixed:, CONTRIBUTING.md, make hooks, manifest-fix, gh pr create, code review, GPL, documentation fix
type: help
---
# Lesson 13.3: A pull request

The last lesson closes the circle of the course: a fix learners make for themselves goes back to the project, so it
is in the next release and nobody has to carry it alone. In this lesson learners prepare a small, real change (often
a fix to these help pages) as a pull request, with every check a reviewer expects.

> **Note**
> A GitHub account is needed to open the pull request. Learners without one stop before the push; everything before
> it is the same.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 13: RAD, Velocity and contributing](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 12.3](../module-12-tests-and-quality/checks-before-you-share-a-change.md) |

## Learning objectives

After this lesson, learners can:

- fork a repository and add the fork as a remote of their clone;
- make a focused change on a branch of its own;
- write a commit subject with the right prefix and run the checks and the manifest;
- push the branch to the fork and open a pull request with a useful description;
- respond to review comments with further commits on the same branch.

## Trainer's notes

### How to present it

Sources: [Develop with Git, Contribute a change with a pull request](../../../develop-with-git.md#contribute-a-change-with-a-pull-request)
and `CONTRIBUTING.md` in the installation root.

1. **Why** (2 minutes). A fix kept only on your site has to be re-applied after every update; a merged fix is in the
   next release for everyone.
2. **The license** (2 minutes). Exponential is under the GNU GPL, version 2 or later; contributions are released under
   the same license (`CONTRIBUTING.md`). Documentation is free under the GNU FDL 1.3.
3. **The steps** (5 minutes). Fork on GitHub; `git remote add fork https://github.com/<you>/<repository>.git`; a
   branch per change; the change; the checks of lesson 12.3; for the kernel, the manifest (`make manifest-fix`); a
   commit `Fixed: ...`; `git push -u fork <branch>`; open the pull request from the link GitHub prints (or
   `gh pr create`).
4. **A good description** (3 minutes). What was wrong, what the change does, how it was tested, and anything the
   reviewer should look at. One topic per pull request.
5. **Review** (2 minutes). Answer comments, push further commits to the same branch; the pull request updates itself.

### Common questions

**Can I contribute to an extension the same way?** Yes: each extension is its own repository with its own
`origin`; the steps are the same inside `extension/<name>`.

**What if CI fails on my pull request?** Read the failing step; its summary names the problem (a stale manifest line,
a commit subject, a syntax error). Fix it with another commit on the branch.

**Do I need write access to the project?** No. You push to your fork; the pull request asks the project to take the
change.

### Pitfalls

- **Working on `main`.** Always a branch; one change per branch.
- **Secrets in the commit.** `settings/override/` and `settings/siteaccess/` may hold passwords; never commit them.
  Check `git diff --cached --name-only` before every commit.
- **A pull request that does many things.** Split it; small pull requests are reviewed faster.

## Hands-on exercise

In the kernel clone of your development installation (or in the clone of an extension you used in the course).

1. Find something small to improve: a typo or an unclear sentence in a help page you read during the course, or a
   missing test.
2. Create a branch: `git switch -c fix-<short-name>`.
3. Make the change. For a documentation page, check its links with the help checker:
   `php bin/php/console exp:help check --below=help/<folder>/`.
4. Run the checks of lesson 12.3 that apply; for the kernel, update the manifest: `make manifest-fix`.
5. Review what will be committed: `git status` and `git diff --cached --name-only` after `git add`. Nothing from
   `settings/override/` or `var/`.
6. Commit: `git commit -m "Fixed: <what the change does>"`.
7. If you have a GitHub account: fork the repository on GitHub, add your fork and push:

   ```bash
   git remote add fork https://github.com/<you>/exponential.git
   git push -u fork fix-<short-name>
   ```

8. Open the pull request from the link GitHub prints. Describe what was wrong, what you changed and how you checked
   it.
9. Without an account: show the trainer `git log --oneline -1` and `git show --stat` instead.

## Expected result

- Step 3: the help checker reports no errors for the folder of the changed page.
- Step 6: one focused commit whose subject starts with `Fixed:` (or another allowed prefix).
- Step 8: an open pull request on GitHub with a clear description, and CI starting on it.
- The trainer reviews one learner's pull request description with the group.

## Quiz

1. Where do you push your branch: to `origin` or to your fork?
2. Which file explains how to contribute to the kernel?
   A. `README.md` B. `CONTRIBUTING.md` C. `composer.json` D. `share/filelist.md5`
3. What belongs in a pull request description?
4. How do you answer a review comment that asks for a change?
5. Under which license are code contributions to Exponential released?

<details>
<summary>Answers</summary>

1. To your fork; `origin` points at the project, where you have no write access.
2. **B.** `CONTRIBUTING.md` in the installation root.
3. What was wrong, what the change does, how it was tested, and what the reviewer should look at.
4. With another commit on the same branch, pushed to the fork; the pull request updates itself.
5. The GNU GPL, version 2 or later, like the project.

</details>

## Further reading

- [Develop with Git](../../../develop-with-git.md): clones, branches, pull requests and keeping up to date.
- [Source control and releases](../../../../standards/source-control-and-releases.md).
- [Free documentation](../../../free-documentation.md): improving these pages.
- [Pull request etiquette](../../../designers/lifecycle/pull-request-etiquette.md) and
  [Contributing back upstream](../../../designers/lifecycle/contributing-back-upstream.md).

## Related pages

- [Module 13: RAD, Velocity and contributing](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
