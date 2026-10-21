---
title: Using Git as a student
audience: student
category: learning
order: 45
summary: How learners use Git with Exponential - three repositories every learner keeps (the kernel clone on your own branch, your own extensions, your notes), daily habits, reading the project history to learn, commit messages in the project's style, keeping secrets out, and where Develop with Git and Update with Git go further.
keywords: git, version control, clone, branch, commit, history, log, blame, diff, repository, commit message, secrets, gitignore, student, develop with git
type: help
---
# Using Git as a student

Git is how Exponential is developed, installed (as clones) and improved. This page shows how a learner uses it every
day. The full guide is [Develop with Git](../develop-with-git.md); the commands are collected in the
[Git quick reference for learners](git-quick-reference-for-learners.md).

## Contents

1. [Three repositories a learner keeps](#three-repositories-a-learner-keeps)
2. [Daily habits](#daily-habits)
3. [Learn from the history](#learn-from-the-history)
4. [Commit messages](#commit-messages)
5. [Keep secrets out](#keep-secrets-out)
6. [Staying up to date](#staying-up-to-date)

## Three repositories a learner keeps

| Repository | What it holds | Started with |
|---|---|---|
| The kernel clone | Exponential with its full history, on your own branch `my-site` | [Installing from Git clones for study](installing-from-git-clones-for-study.md) |
| Your extensions | One repository per extension or design you write, in `extension/<name>` | `git init` inside it ([project 8](project-08-your-first-extension.md)) |
| Your notes | Your study journal, reports and hand-ins | `git init` in a folder outside the installation ([Keeping a study journal](keeping-a-study-journal.md)) |

The extensions Composer installs are separate repositories too; `git status` in the root lists them as untracked
folders, which is expected ([Develop with Git](../develop-with-git.md#check-that-it-worked)).

## Daily habits

1. Start the session with `git status` in the repository you will change.
2. Work on a branch, never directly on someone else's tag or on `main` of the project.
3. Commit small steps with clear messages; one commit per idea.
4. End the session with `git status` and `git log --oneline -5`: is everything committed?
5. Push your own repositories to your own account on a Git host, so a broken laptop does not take your work.

## Learn from the history

The kernel's history is a textbook written by its developers. From [Develop with Git](../develop-with-git.md#find-out-why-and-when):

```bash
git log --oneline -10 -- kernel/content/view.php   # the last ten changes to one file
git blame kernel/content/view.php                   # who last changed each line
git log -S "CheckValidity" --oneline                # every commit that added or removed a word
```

Exercise: pick a setting you met in a study project, find the commit that introduced it with `git log -S`, and
explain in your journal why it was added.

## Commit messages

The project's commit subjects start with what the change does: `Added: `, `Updated: `, `Fixed: `, `Removed: ` or
`Renamed: ` ([Contributors guidelines](../../../../CONTRIBUTING.md#making-changes)). Use the same style in your own
repositories; it makes your history readable and your future pull requests familiar:

```text
Added: The club event class and three sample events
Fixed: The event template escapes the title
Updated: The search test plan after the index rebuild
```

## Keep secrets out

- Never commit `settings/override/` from an installation, `kickstart.ini` (it holds the database password,
  [Getting started](../../guides/getting-started.md#33-from-a-file-repeatable-installs)), API keys or passwords.
- Use a `.gitignore` in your own repositories for files that must stay local.
- If a secret was committed, treat it as public: change the password or revoke the key, then remove it from the
  repository.

## Staying up to date

Git first (the kernel), then Composer (the extensions):

```bash
git fetch origin --tags
git merge origin/main
composer update
```

`composer update`, not `composer install`, because the kernel's `composer.json` changed
([Develop with Git](../develop-with-git.md#keep-up-to-date)). The whole routine, with the database half of an
upgrade, is [Update with Git](../update-with-git.md). If Composer refuses because of your own changes, read
[When Composer refuses](../develop-with-git.md#when-composer-refuses).

## Related pages

- [Git quick reference for learners](git-quick-reference-for-learners.md)
- [Develop with Git](../develop-with-git.md)
- [Your first contribution, step by step](your-first-contribution-step-by-step.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
