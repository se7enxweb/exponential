---
title: Running a team on Git
audience: project-manager, developer
category: leadership
order: 130
summary: How a project team works on an Exponential site with Git: repositories, branches, pull requests and reviews, commit messages, settings in version control, deployments, and why a published tag is permanent.
keywords: git, team, branch, branching, pull request, review, merge, tag, release, version, immutable, commit message, hooks, manifest, deploy, ManagedByDeployment, composer, worktree, git_manager, workflow
type: help
---
# Running a team on Git

This page is for project managers who lead a team that builds or maintains an Exponential site, and for the
developers in that team. It turns the project's own Git practice into a team workflow: how the repositories are laid
out, how work moves from a branch to the live site, who reviews and who releases, and which rules protect the team
from the mistakes that cannot be undone.

You do not need to type Git commands to use this page. Where a command matters, it is shown so that you can ask for
it by name. The developer's tutorial is [Develop with Git](../develop-with-git.md).

## Contents

1. [Why Git, and why clones](#why-git-and-why-clones)
2. [How an Exponential site is laid out in Git](#how-an-exponential-site-is-laid-out-in-git)
3. [A branching model for a site team](#a-branching-model-for-a-site-team)
4. [Pull requests and reviews](#pull-requests-and-reviews)
5. [Commit messages](#commit-messages)
6. [The checks every commit passes](#the-checks-every-commit-passes)
7. [Settings and secrets](#settings-and-secrets)
8. [From branch to live site](#from-branch-to-live-site)
9. [Tags are permanent](#tags-are-permanent)
10. [Following upstream](#following-upstream)
11. [Roles in the Git workflow](#roles-in-the-git-workflow)
12. [Team setup checklist](#team-setup-checklist)

## Why Git, and why clones

"Building websites with Exponential? The recommended way is to get the kernel and every se7enxweb extension as Git
clones with their full history" (README, quoted on [Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#git-first)).

What a manager gets from that decision, in the words of [Develop with Git](../develop-with-git.md#why-git-clones-and-not-archives):

| Question you will ask | With release archives | With Git clones |
|---|---|---|
| What did we change? | `composer status -v` names changed files | `git status` and `git diff` show every change, line by line |
| Why is this line here? | Not possible | `git log` and `git blame` show who changed it, when and why |
| Which change broke it? | Not possible | `git bisect` finds it in a few steps |
| Can we use a fix before it is released? | Copy files by hand | `git cherry-pick` |
| Are our own changes safe at the next update? | Lost at the next update | Kept on our own branch; Composer refuses to overwrite them |
| Can we send a fix back? | Not possible | Push a branch to a fork and open a pull request |

## How an Exponential site is laid out in Git

An installation is **several repositories**, not one:

- **The kernel**, [se7enxweb/exponential](https://github.com/se7enxweb/exponential), in the installation root.
- **Each extension**, in `extension/<name>`, with its own repository and history. On `main`, `composer install`
  clones 67 repositories ([Develop with Git](../develop-with-git.md#a-new-site)).
- **Your own work**: your own extensions and designs, and the settings of your site.

`git status` in the installation root lists the extensions as untracked folders, because they are separate
repositories, and that is expected.

Decide early, and write into the charter, where your **own** code lives:

| Option | Fits | Watch out for |
|---|---|---|
| A branch of the kernel (`my-site`) that holds your own extension and design folders | A small team, one site | Your branch also receives every upstream merge; keep your commits separate from merges |
| Your own extension in its own repository, installed by Composer | Several sites, or code you reuse | Release it with versions like any extension ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)) |
| Both: site settings on the kernel branch, reusable code in extensions | Most agency projects | Document which repository holds what in the handover document |

## A branching model for a site team

The project itself works on `main` and cuts release tags from it ([Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches)).
A site team adds one level on top:

| Branch | Holds | Who writes to it |
|---|---|---|
| `my-site` (name it after your site) | The state that is deployed, on top of a release tag or `main` | Only by merging reviewed pull requests |
| `feature/<topic>`, `fix/<topic>` | One piece of work each | The developer doing it |
| Upstream branches and tags | The project's `main` and its tags | Nobody in your team; you merge from them |

The first step of every new site is in [Develop with Git](../develop-with-git.md#a-new-site):
`git switch -c my-site`, on top of `main` or of a release tag such as `v6.0.14`.

> **Tip**
> For production, build on a **release tag**. "You can pin a tag and rely on getting the same files every time, which
> is what makes a pinned version the right choice for production" ([Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches)).
> Choose `main` only when you need something that is not in a tag yet, and write that decision down.

## Pull requests and reviews

Every change reaches `my-site` through a pull request that a second person has read. This is the cheapest
protection against the "knowledge in one head" risk ([Risk management](risk-management.md)).

What a reviewer checks, at least:

- The change does what its ticket says, and the ticket says how it was tested.
- The commit subjects follow the format (below).
- No secrets, customer data or host names in code, settings, logs or screenshots.
- Templates of an administration design follow the
  [code review checklist of Styling the administration](../../bc/6.0/admin4-styling.md#code-review-checklist).
- Settings changes are in files under version control, not made on the server.

The project's own practice, worth copying (from [CI](../../bc/6.0/ci.md)): a pull request is tested **as merged
into `main`**, so a branch that was green can turn red when `main` moves; the answer is to merge `main` into the
branch and push again.

## Commit messages

The project's rule (`CONTRIBUTING.md`, [Quality checks](../../features/6.0/quality-checks.md)): every commit subject
starts with what the change does.

| Prefix | Use for |
|---|---|
| `Added:` | A new feature, file, class or capability |
| `Updated:` | Changed or improved behavior |
| `Fixed:` | A bug fix |
| `Removed:` | Something deleted |
| `Renamed:` | Something renamed |

Example from CONTRIBUTING.md: `Fixed: The redirect keeps the query string on the own host`.

Why a manager should care: a changelog can be assembled from such subjects, and a reader of the history understands
each change without opening it. Adopt the same rule for your own repositories.

## The checks every commit passes

`make hooks` once in a kernel clone sets Git's hooks path to `.githooks`. From then on
([Quality checks](../../features/6.0/quality-checks.md)):

| When | Check |
|---|---|
| `git commit` | PHP syntax of staged files (also with PHP 8.0 when it is installed), JavaScript syntax, the file manifest, the code style of the change |
| `git commit` | The subject format |
| `git push` | PHPStan against its baseline |

**The file manifest** `share/filelist.md5` lists the checksum of every file of the distribution. The upgrade check
of the administration compares the installed files with it, so a stale line shows up as a "modified" file on every
site. Every commit that adds, changes or removes a file updates its line (`make manifest-fix`).

`make check` runs lint, manifest, phpcs and phpstan as CI does. Your own repositories can reuse the idea even if not
the tools: one command that every developer runs before pushing, the same one that CI runs.

## Settings and secrets

Two rules from the documentation, worth turning into team policy:

1. **Never push the secrets.** "The files in `settings/override/` and `settings/siteaccess/` hold database passwords:
   never push them to a public repository" ([Develop with Git](../develop-with-git.md#the-kernel)). Agree where the
   real values live (a private repository, the deployment tool, the server only) and write it into the handover
   document.
2. **Configuration comes from version control.** With `[ConfigurationSettings] ManagedByDeployment=enabled` on a
   siteaccess, the administration does not change its configuration on the server: the settings editor, the toolbar
   and menu editors, the template editor, the setup wizard, extension activation and package installs are closed,
   and `eZINI::save()` refuses ([ManagedByDeployment](../../features/6.0/managed-by-deployment.md)). Without it,
   these editors write files into the release directory "where version control does not see them and the next
   deployment drops them".

A team that deploys from Git should switch `ManagedByDeployment` on for its production siteaccesses. Agree with
editors and administrators beforehand which changes now go through a pull request.

## From branch to live site

A typical path for one change:

1. Developer branches, changes, commits, pushes, opens a pull request.
2. Reviewer reads and approves; checks pass.
3. The pull request is merged into `my-site`.
4. The administrator deploys the merged commit to staging, then to production.

For step 4 the product provides one command: `exp:velocity deploy`. It runs the extension autoloads, the INI cache,
the template, override, translation and design caches, the PHP-FPM reload, the Velocity restart, then the content,
HTTP and template-block caches and Velocity's response cache, prints PASS, FAIL or SKIP for each, and stops at the
first failure ([Deploying, section 7](../../guides/deploying.md#7-ship-a-php-change-with-one-command)). `--dry-run`
shows what it would do. The order matters: a cache of rendered pages cleared before PHP runs the new code fills up
again with pages of the old code ([11.6](../../install/11-upgrading.md#116-from-any-60x-to-today)).

**Without a shell.** The [git_manager](../../features/6.0/extensions/git_manager.md) extension gives a Git dashboard in
the administration (see where the installation stands against its branch, switch branches, pull, push) and a backup
manager. Use it where nobody on the team has shell access, and make a backup before every change.

> **Warning**
> Keep other working copies of the code (for example Git worktree folders) **outside** the installation root, or list
> them in `.autoloadignore`. Otherwise regenerating the kernel class map can map kernel classes into those copies
> ([11.6 step 1](../../install/11-upgrading.md#step-1-put-the-new-code-in-place)).

## Tags are permanent

This is the one rule a team must never break, and the project states it in several places:

- "A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag reaches you as the next
  release, not as a corrected copy of the old one" ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
- "If a published version is wrong, do not move or delete its tag. Publish the next version with the fix and say in
  its notes what was wrong with the previous one. Packagist caches what it read, so a moved tag produces two
  different packages under one version number" ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).

What it means for your team when you release your own extensions:

| Rule | Why |
|---|---|
| Choose the number from what is published: `git fetch --tags`, then `git tag -l 'v*' --sort=version:refname` | Memory and the working tree are not reliable; a number may already be taken |
| Sort by version, never by name | By name `0.1.10` sorts before `0.1.8` |
| After `.9` comes `.10` | Only the last position counts up unless you decide otherwise |
| Write the number into `ezinfo.php` and `extension.xml` (and `composer.json`), commit, **then** tag that commit | A tag made first would carry the old number |
| Check before you push the tag | A tag is cheap to get right and impossible to correct |
| A wrong release is followed by the next release | Never by moving the tag |

Make **one person** responsible for tagging each repository, and give them the checklist in
[Extensions, "Release it"](../../guides/extensions.md#7-release-it).

## Following upstream

The kernel on your branch moves to a new release by merging its tag ([Develop with Git](../develop-with-git.md#keep-up-to-date)):

```bash
git fetch origin --tags
git merge v6.0.14
composer install
```

Extensions move one at a time, after reading their release notes: `composer update se7enxweb/<package>`. On a live
site, never run a plain `composer update`, which moves every package at once
([Getting the code](../../install/03-getting-the-code.md#35-extensions-are-composer-packages)).

When Composer refuses to update a package, it is protecting your work: uncommitted changes, or commits that exist
only on one machine. The table in [Develop with Git, "When Composer refuses"](../develop-with-git.md#when-composer-refuses)
says what to do. A manager's takeaway: **no work may live only on one developer's machine**. Push branches.

Moving the code is half of an upgrade; the database is the other half. See
[Release and upgrade planning](release-and-upgrade-planning.md).

## Roles in the Git workflow

| Activity | Developer | Reviewer | Release owner | Administrator | Project manager |
|---|---|---|---|---|---|
| Branch and commit | Does | | | | |
| Open pull request | Does | | | | Links it to the ticket |
| Review | | Does | | | Ensures it happens |
| Merge into `my-site` | | Does, after approval | | | |
| Tag an own extension | | | Does, with the checklist | | Approves the version |
| Deploy to staging and production | | | | Does | Schedules |
| Merge an upstream release | Does, on a branch | Reviews | | Deploys | Plans the upgrade |
| Send a fix upstream | Does | Reviews | | | Tracks it |

## Team setup checklist

```text
Git setup for <project name>

[ ] Every developer installs with Git clones (preferred-install for se7enxweb/* set to source)
[ ] The site branch is named: <branch>, based on <tag or main>; the decision is written down
[ ] Where own code lives is decided: <kernel branch / own extension repositories>
[ ] Repositories for own code created: <list>
[ ] Branch protection: no direct pushes to the site branch; review required
[ ] Commit subject format agreed (Added: / Updated: / Fixed: / Removed: / Renamed:)
[ ] Kernel clones: make hooks run by every developer
[ ] Secrets: settings/override and settings/siteaccess never pushed to a public repository; real values kept in <place>
[ ] ManagedByDeployment decided per siteaccess: <list>
[ ] Deploy command and order agreed (exp:velocity deploy, or the manual steps)
[ ] Release owner per repository named: <names of roles>
[ ] Tagging checklist from "Release it" copied into the repository
[ ] Upstream fixes go as pull requests; local patches listed in the handover document
```

## Related pages

- [Develop with Git](../develop-with-git.md): the developer's step-by-step tutorial
- [Getting the code](../../install/03-getting-the-code.md), [Extensions](../../guides/extensions.md)
- [Quality checks](../../features/6.0/quality-checks.md), [Continuous integration](../../bc/6.0/ci.md)
- [Deploying](../../guides/deploying.md), [ManagedByDeployment](../../features/6.0/managed-by-deployment.md)
- [Working with the community and contributing back](working-with-the-community.md), [Quality gates and acceptance](quality-gates-and-acceptance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
