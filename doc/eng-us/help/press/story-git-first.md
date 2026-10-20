---
title: "Story: Git first"
audience: press
category: press-media
order: 81
summary: A feature story about Exponential's "Git first" approach - installing the kernel and extensions as Git clones with full history, updating with Git, a repository history back to 2002, and a month-by-month account of every change since December 2023 - with sources and the claims to avoid.
keywords: story, Git, Git first, version control, history, clone, update with Git, transparency, open development, commits, ledger
type: help
---
# Story: Git first

This page is for technical journalists who cover development practice and transparency. Exponential recommends that
sites run from Git clones, and keeps its full history in the open.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

"Building websites with Exponential? The recommended way is to get the kernel and every se7enxweb extension as Git
clones with their full history" (README, "Installation"; [Develop with Git](../develop-with-git.md)). The repository's
history goes back to 2 September 2002 and holds every commit since; every change since December 2023 is told month by
month in [the history](../../history/README.md) and listed in the ledgers, with a coverage page that shows none is
missing ([Who is 7x?, Principles you can see in the code](../who-is-7x.md#principles-you-can-see-in-the-code)).

## Facts you can print

| Fact | Source |
|---|---|
| Git clones with full history are the recommended installation | README, "Installation" |
| Sites can be updated with Git | [Update with Git](../update-with-git.md) |
| The oldest commit is from 2 September 2002 | `git log --reverse`; [Exponential by the numbers](exponential-by-the-numbers.md) |
| Every change since December 2023 is in the history and the ledgers | [The history](../../history/README.md), [The ledgers](../../history/ledger/README.md), [Coverage](../../history/coverage.md) |
| Published versions are never changed; a fix is the next release | [Published versions never change](published-versions-never-change.md) |
| Administrators can update checkouts from the administration with `git_manager` | [git_manager](../../features/6.0/extensions/git_manager.md) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Composer is not supported" | Every package is also a Composer package on Packagist |
| "You must know Git to use it" | Editors never need Git; it is the recommended way to install and update |

## Angles and headline ideas

- "A CMS whose every change since 2023 is explained in prose."
- "Twenty-four years of history, one `git clone` away."

## Related pages

- [The history pages as a source](the-history-pages-as-a-source.md)
- [Story: every pull request is merged](story-every-pull-request-merged.md)
- [Develop with Git](../develop-with-git.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
