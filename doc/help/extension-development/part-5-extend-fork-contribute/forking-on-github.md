---
title: Forking on GitHub, step by step
audience: developer, designer, app-creator
category: extension-development
order: 30
summary: Fork an extension's repository on GitHub, clone it, add the original as upstream, work on a branch and install your fork into an Exponential site with Composer, with the commands and what you should see.
keywords: fork, GitHub, clone, upstream, remote, branch, composer, repositories, vcs, install fork, step by step
type: help
---
# Forking on GitHub, step by step

Part 5, chapter 3. This chapter walks through forking an extension on GitHub and using your fork in a development
site. It assumes a GitHub account and Git on your computer ([Develop with Git](../../develop-with-git.md) covers
Git itself). The example forks `se7enxweb/expchangeclass`; replace it with the repository you need, and
`your-account` with your GitHub user or organization.

## 1. Fork on GitHub

1. Open the repository's page, for example https://github.com/se7enxweb/expchangeclass.
2. Click **Fork** (top right).
3. Choose your account as the owner. Keep the repository name.
4. Leave "Copy the main branch only" ticked unless you need other branches.
5. Click **Create fork**.

You now have `https://github.com/your-account/expchangeclass`, with the whole history of the original and a note
"forked from se7enxweb/expchangeclass" under its name. That note is part of the credit: keep the fork connected,
do not "detach" it.

## 2. Clone your fork

```bash
git clone git@github.com:your-account/expchangeclass.git
cd expchangeclass
git remote add upstream https://github.com/se7enxweb/expchangeclass.git
git remote -v
```

You should see two remotes: `origin` (your fork, where you push) and `upstream` (the original, where you fetch
from).

## 3. Work on a branch

Never work on `main` of your fork. A branch per change keeps each pull request clean:

```bash
git switch -c fix-menu-entry-csp
# edit files
git add design/admin/templates/popupmenu/classcontextmenu.tpl
git commit -m "Updated: The context menu entry carries no inline handler"
git push -u origin fix-menu-entry-csp
```

Name the branch after the change, not after yourself.

## 4. Use your fork in a development site

A site installed with Composer can take the extension from your fork instead of Packagist. In the site's
`composer.json`:

```json
{
    "repositories": [
        { "type": "vcs", "url": "https://github.com/your-account/expchangeclass" }
    ],
    "require": {
        "se7enxweb/expchangeclass": "dev-fix-menu-entry-csp"
    }
}
```

Then:

```bash
composer update se7enxweb/expchangeclass
```

`dev-` plus the branch name installs that branch. Composer reads the package name from the fork's `composer.json`,
which still says `se7enxweb/expchangeclass`, so the requirement keeps that name. Clear the caches and regenerate the
autoloads afterwards, as for any extension change.

> **Warning**
> Do not run `composer update` on a live site to test a fork. Use a development copy. A Composer run can also replace
> extensions you have checked out with Git by hand; know how your site was installed before you run it.

Without Composer, clone the fork straight into `extension/`:

```bash
cd extension
git clone -b fix-menu-entry-csp git@github.com:your-account/expchangeclass.git expchangeclass
```

The folder name must be the extension's name (`expchangeclass`), not the repository's if they differ.

## 5. From here

- To send your change to the original: [Contributing back with pull requests](contributing-back-with-pull-requests.md).
- To keep your fork current while you wait: [Keeping a fork in sync with its source](keeping-a-fork-in-sync.md).
- If the fork becomes a lasting one with its own releases, change the package `name` in its `composer.json` to your
  own and register it on Packagist ([Composer and Packagist](../part-7-releasing/composer-and-packagist.md)).

## What can go wrong

| Problem | Cause | Fix |
|---|---|---|
| `Permission denied (publickey)` on clone | no SSH key on GitHub | use the https URL, or add your key to GitHub |
| Composer: "could not find a matching version" | the branch name in `dev-...` is wrong, or the fork's `composer.json` has another name | check `git branch -r` and the `name` field |
| Your change is not visible on the site | the site still uses the original | `composer show se7enxweb/expchangeclass` shows the source; clear the caches |

## Related pages

- Previous: [When to fork, and when not to](when-to-fork.md)
- Next: [Keeping a fork in sync with its source](keeping-a-fork-in-sync.md)
- [Develop with Git](../../develop-with-git.md)
- [Fork a repository](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/working-with-forks/fork-a-repo) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
