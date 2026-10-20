---
title: Keeping a fork in sync with its source
audience: developer
category: extension-development
order: 40
summary: Bring the original's new commits into your fork with git fetch and merge or rebase, resolve conflicts, and keep your own changes small so syncing stays easy - with the commands and a routine.
keywords: fork, sync, upstream, git fetch, git merge, git rebase, conflict, divergence, routine
type: help
---
# Keeping a fork in sync with its source

Part 5, chapter 4. A fork starts identical to its original and drifts apart with every commit on either side. The
further it drifts, the harder it becomes to take the original's fixes. This chapter shows how to bring the original's
changes into your fork, and how to keep that easy.

## The commands

With the remotes from [Forking on GitHub, step by step](forking-on-github.md) (`origin` your fork, `upstream` the
original):

```bash
git fetch upstream
git switch main
git merge --ff-only upstream/main
git push origin main
```

`--ff-only` succeeds only when your `main` has no commits of its own, which is how it should be if you work on
branches. If it refuses, you committed to `main` by mistake: move those commits to a branch first.

GitHub offers the same as a button: **Sync fork** on your fork's page.

## Bringing the original into a branch

Your working branch needs the original's new commits too. Two ways:

```bash
# merge: keeps your branch's history as it is, adds a merge commit
git switch fix-menu-entry-csp
git merge upstream/main

# rebase: replays your commits on top of the original's; history stays linear
git switch fix-menu-entry-csp
git rebase upstream/main
git push --force-with-lease origin fix-menu-entry-csp
```

| Use | When |
|---|---|
| merge | the branch is shared with others, or already reviewed |
| rebase | the branch is yours alone and not yet reviewed; many projects prefer pull requests rebased on their current main |

Never rebase a branch others have based work on, and never force-push `main`.

## Conflicts

A conflict means the original and you changed the same lines. Git stops and marks them:

```text
<<<<<<< HEAD
<a id="menu-class-change" href="#" data-menu-form="menu-form-class-change">
=======
<a id="menu-class-change" href="#" class="menu-item" onclick="...">
>>>>>>> upstream/main
```

1. Read both sides and decide what the line must be. Often it is a combination.
2. Remove the markers, save, `git add` the file.
3. `git merge --continue` (or `git rebase --continue`).
4. Run the extension's tests and open the pages concerned before you push.

## A routine for a lasting fork

- **Sync often.** Weekly, or on every release of the original. Ten small merges are easier than one large one.
- **Keep your changes few and separate.** One commit per change, with a clear message, so you can see in `git log`
  what is yours.
- **Send your changes back.** Every change the original accepts is one less change you carry
  ([Contributing back with pull requests](contributing-back-with-pull-requests.md)).
- **Watch the original.** On GitHub: **Watch > Custom > Releases** tells you when to sync.
- **Write down what differs.** A short section in your README, "Differences from the original", saves the next
  maintainer hours.

## See how far you have drifted

```bash
git fetch upstream
git log --oneline upstream/main..main    # your commits the original does not have
git log --oneline main..upstream/main    # the original's commits you do not have
```

Two short lists mean a healthy fork. A long first list means you carry a lot: time to send some of it back.

## Related pages

- Previous: [Forking on GitHub, step by step](forking-on-github.md)
- Next: [Rebranding ethically](rebranding-ethically.md)
- [Update with Git](../../update-with-git.md)
- [Syncing a fork](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/syncing-a-fork) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
