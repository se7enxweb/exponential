---
title: "The first push and the default branch"
audience: designer
category: theme-distribution
order: 60
summary: Push a theme for the first time with main as its default branch, protect main from force pushes, and decide how work branches and pull requests flow into it.
keywords: first push, default branch, main, branch protection, force push, pull request, git push -u, dev-main
type: help
---
# The first push and the default branch

This page is for designers publishing a repository for the first time.

## Why main

The Exponential repositories use `main` as their default branch. Composer users who want the newest unreleased code
of your theme require `dev-main`; a different branch name means a different constraint (`dev-master`, `dev-trunk`)
and confusion. Use `main` from the start.

## Step by step

1. Check the branch name: `git branch --show-current` prints `main`. If it prints something else:
   `git branch -m main`.
2. Push and set the upstream:

   ```bash
   git push -u origin main
   ```

   Expected: `branch 'main' set up to track 'origin/main'`.
3. Make sure GitHub uses it as the default:

   ```bash
   gh repo view yourvendor/mytheme --json defaultBranchRef --jq .defaultBranchRef.name
   ```

   Expected: `main`.
4. Protect it against force pushes and deletion (**Settings > Branches** or **Rules**). A history that others have
   fetched must never be rewritten.

## How changes flow

| Way | Fits |
|---|---|
| Commit to `main` directly | One maintainer, small fixes |
| A branch per change, merged by pull request | Several people, reviews wanted |
| Pull requests from forks | Outside contributors |

Whatever you choose, every release is a tag on `main` ([GitHub releases](github-releases.md)).

## Related pages

- [Creating the GitHub repository](creating-the-github-repository.md)
- [Pull requests to someone else's theme](../lifecycle/pull-request-etiquette.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
