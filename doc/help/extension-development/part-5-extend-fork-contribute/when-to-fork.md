---
title: When to fork, and when not to
audience: developer, app-creator
category: extension-development
order: 20
summary: A fork is a copy you promise to maintain. When a fork is the right answer - an abandoned original, a fix waiting for review, a different direction - and when an override, an issue or a pull request is better.
keywords: fork, when to fork, abandoned, pull request, maintenance, upstream, downstream, divergence, decision
type: help
---
# When to fork, and when not to

Part 5, chapter 2. On GitHub a fork is one click. Its cost comes later: every change the original makes from then on
has to be brought into your copy, and every user of your copy depends on you to do it. This chapter helps you decide
whether that cost is worth paying.

## Three kinds of fork

The word "fork" is used for three different things, with very different costs:

| Kind | What it is | Cost |
|---|---|---|
| A **contribution fork** | your copy on GitHub, used only to prepare a pull request to the original | almost none: you delete or reset it after the pull request is merged |
| A **waiting fork** | your copy with a fix you need now, used until the original releases the same fix | small, if it is short: you go back to the original afterwards |
| A **lasting fork** | a copy that goes its own way, published under your name | permanent: you are now a maintainer |

Most of the time you want the first kind. The second kind is fine when it is temporary and you say so. The third
kind needs a good reason.

## Good reasons for a lasting fork

- **The original is abandoned.** No commits for years, issues unanswered, the author unreachable or saying they
  stopped. Read [Adopting an abandoned extension](adopting-an-abandoned-extension.md) first: sometimes the author is
  glad to hand it over, and no fork is needed.
- **The original cannot run where you need it** and its maintainers do not want the change, for example because they
  support a different platform.
- **You want a different direction**, and the original's maintainers said so clearly. Disagreement is allowed; the
  license gives you the right to take the code your own way.

se7enxweb/expchangeclass is an example of the first two at once: its README explains that two existing forks of an
old extension were in circulation and "neither was safe to run here" on PHP 8, and that the new fork takes the best
of both and fixes what neither had.

## Bad reasons

- **"I need to change one template."** Use an override ([Extending without forking](extending-without-forking.md)).
- **"I need a setting the original does not have."** Ask for it, or send a pull request that adds it.
- **"The author is slow to answer."** Wait a reasonable time, then ask again politely. Weeks are normal for volunteer
  projects.
- **"I want my company's name on it."** That is rebranding, and it has its own rules
  ([Rebranding ethically](rebranding-ethically.md)).

## A decision in five questions

1. Can I do it from my own extension (settings, override, event, subclass)? If yes, do that.
2. Is the change useful to others? If yes, send it as a pull request, and fork only to prepare it.
3. Do I need it before the original can release it? If yes, use a waiting fork and plan the day you leave it.
4. Is the original maintained? If no, see adopting.
5. Am I ready to answer issues and merge upstream changes for years? If no, do not make a lasting fork.

## If you do fork for good

- Use a new package name and a display name that does not pretend to be the original.
- Keep every copyright line and the license; add yours ([Attribution and copyright notices](attribution-and-copyright-notices.md)).
- Write in the README why the fork exists and what it changes, as expchangeclass's README does.
- Keep the Git history: fork on GitHub, or clone and push, instead of copying files. The history is part of the
  credit, and it lets you merge from the original later ([Keeping a fork in sync with its source](keeping-a-fork-in-sync.md)).
- Offer your fixes back anyway. Even a lasting fork can send pull requests to the original.

## Related pages

- Previous: [Extending without forking](extending-without-forking.md)
- Next: [Forking on GitHub, step by step](forking-on-github.md)
- [Evaluating an old extension](../part-1-from-user-to-developer/evaluating-an-old-extension.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
