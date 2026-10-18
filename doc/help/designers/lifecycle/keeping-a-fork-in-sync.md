---
title: "Keeping a fork in sync"
audience: designer, developer
category: theme-lifecycle
order: 360
summary: Bring the original theme's fixes into your fork regularly - an upstream remote, fetch and merge, resolving conflicts in templates, and keeping your own changes small so merges stay easy.
keywords: fork sync, upstream remote, git fetch, git merge, conflicts, rebase, downstream, merge upstream
type: help
---
# Keeping a fork in sync

This page is for designers maintaining a fork ([Forking a theme](forking-a-theme.md)). A fork that never merges from
its origin misses every fix made there, including security fixes.

## Step by step

1. **Add the original as a remote**, once:

   ```bash
   git remote add upstream https://github.com/originalvendor/theme.git
   git fetch upstream --tags
   ```

2. **Merge regularly** (each upstream release, or monthly):

   ```bash
   git fetch upstream --tags
   git switch main
   git merge upstream/main
   ```

3. **Resolve conflicts**: in templates, keep upstream's fixes (escaping, accessibility) and re-apply your change on
   top. Read each conflict; never take one side blindly.
4. **Test** as in [Upgrade testing, step by step](upgrade-test-step-by-step.md).
5. **Release** your fork with a changelog entry: `Updated: merged <original> <version>`.

Merge rather than rebase a branch others have fetched: rebasing rewrites published history.

## Keep merges easy

- Keep your changes in separate files where possible (an override of a template rather than an edit of it).
- Send general fixes upstream ([Contributing back upstream](contributing-back-upstream.md)); every accepted one is a
  conflict you will never have.

## Related pages

- [Develop with Git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
