---
title: Installing from Git clones for study
audience: student
category: learning
order: 13
summary: How a developer student installs Exponential as Git clones with their full history, following Develop with Git - the one Composer setting, cloning the kernel, your own branch, installing the site, checking that every extension is a clone, and why this is the best way to learn from the code.
keywords: git, clone, composer, preferred-install, source, history, my-site branch, develop with git, study, developer, install, extensions
type: help
---
# Installing from Git clones for study

This page is for learners on the [developer path](learning-path-developer.md), and for anyone who wants to read the
code they run. It follows [Develop with Git](../develop-with-git.md), which the project recommends "for building a
website", and explains why a clone is also the best way to study.

## Contents

1. [Why clones help you learn](#why-clones-help-you-learn)
2. [What you need](#what-you-need)
3. [The steps](#the-steps)
4. [Check that it worked](#check-that-it-worked)
5. [First things to explore](#first-things-to-explore)

## Why clones help you learn

From the comparison in [Develop with Git](../develop-with-git.md#why-git-clones-and-not-archives):

| With a clone you can | Command |
|---|---|
| See every change you made, line by line | `git status`, `git diff` |
| Find out why a line is there, who changed it and when | `git log`, `git blame` |
| Find which change broke something | `git bisect` |
| Keep your own changes on your own branch | `git switch -c my-site` |
| Send a fix back to the project | push to your fork, open a pull request |

A release archive gives you the files and nothing else. A clone gives you the history, which is a record of how
experienced developers solved real problems. Reading it is studying.

## What you need

- Git 2.28 or later (`git --version`);
- Composer 2 (`composer --version`) and PHP as listed in the requirements;
- a GitHub account only when you want to send changes back. Installing needs none.

Source: [Develop with Git](../develop-with-git.md#what-you-need).

## The steps

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
git switch -c my-site
composer install
php bin/php/console exp:install --random-password --url=http://localhost:8087
php bin/php/console exp:velocity start --engine=php
```

What each line does, as [Develop with Git](../develop-with-git.md#step-2-install-with-git) explains:

1. The Composer setting installs every `se7enxweb/*` package as a Git clone, and the libraries as archives. Keep the
   quotes, so your shell does not expand the `*`.
2. `git clone` fetches the kernel with its whole history; the page measured more than 27,000 commits and about
   370 MB, two to three minutes on a normal connection.
3. `git switch -c my-site` starts your own branch for your changes, on top of `main`, the current development line.
4. `composer install` clones every extension the kernel requires into `extension/<name>`, each with its history.
5. The last two lines install and serve the study site, as in
   [Installing a study site in one command](installing-a-study-site-in-one-command.md).

To study a published release instead of the development line, name its tag:
`git switch -c my-site v6.0.14`. Read
[Versions, tags and branches](../../install/03-getting-the-code.md#31-versions-tags-and-branches) first, because older
tags need a newer PHP for their test tools.

## Check that it worked

```bash
composer status -v
ls -d extension/*/.git | wc -l
git -C extension/eztags log --oneline -3
git -C extension/eztags status
```

`composer status -v` should say `No local changes`; the second line counts the extensions that are clones.
In an extension, **"HEAD detached" is normal**: Composer checks out the exact release tag that `composer.json` asks
for. Start a branch before you change anything there
([Develop with Git](../develop-with-git.md#check-that-it-worked)).

`git status` in the root lists the extensions as untracked folders; they are separate repositories, and that is
expected.

## First things to explore

| Exercise | Command or page |
|---|---|
| The last ten changes to one file | `git log --oneline -10 -- kernel/content/view.php` |
| Who changed each line of it | `git blame kernel/content/view.php` |
| Every commit that added or removed a word | `git log -S "CheckValidity" --oneline` |
| How commit messages are written here | `git log --oneline -20` (subjects start with `Added:`, `Updated:`, `Fixed:` and so on) |
| What an extension holds | [What an extension is](../extension-development/part-1-from-user-to-developer/what-an-extension-is.md) |

Note in your [study journal](keeping-a-study-journal.md) one commit you understood and why it was made.

> **Warning**
> Never `git clone` by hand into a folder Composer installed as an archive. At the next update Composer replaces it
> without asking, and your commits are gone. Let Composer make the clone with `composer reinstall <package>`
> ([When Composer refuses](../develop-with-git.md#when-composer-refuses)).

## Related pages

- [Using Git as a student](using-git-as-a-student.md)
- [Git quick reference for learners](git-quick-reference-for-learners.md)
- [Update with Git](../update-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
