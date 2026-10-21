---
title: Staying informed
audience: community, admin, developer, hosting
category: community
order: 16
summary: How community members keep up with Exponential - releases and changelogs, the history chronicle, GitHub notifications, Packagist, the commit feed on the developers' landing page, and where announcements are discussed.
keywords: news, releases, changelog, history, GitHub watch, notifications, Packagist, commit feed, announcements, staying current, upgrade
type: help
---
# Staying informed

Exponential has no mailing list named in the sources. These are the documented ways to follow what happens.

## Contents

1. [What to follow](#what-to-follow)
2. [Releases and version numbers](#releases-and-version-numbers)
3. [A routine that takes ten minutes a month](#a-routine-that-takes-ten-minutes-a-month)

## What to follow

| Source | What it tells you | How |
|---|---|---|
| The changelogs | What each 6.0.x release changed | [6.0.14 changelog](../../../../changelogs/6.0/6.0.14.md) and the others in `doc/changelogs/6.0/` |
| The history chronicle | Month by month what happened in the kernel and its ecosystem | [The history of Exponential](../../../history/README.md) |
| The ecosystem map | The repositories around the kernel and their changes | [The Exponential platform ecosystem](../../../history/ecosystem.md) |
| GitHub | New issues, pull requests and releases | "Watch" the [se7enxweb/exponential](https://github.com/se7enxweb/exponential) repository, or only its releases |
| The commit feed | The latest commits of the kernel | The developers' landing page of the help shows them; the feed is `github.com/se7enxweb/exponential/commits/main.atom` (`settings/exphelp.ini`, `[CommitFeedSettings]`) |
| Packagist | Released versions of every package | the list of the vendor's packages, <https://packagist.org/packages/list.json?vendor=se7enxweb> |
| The forums | Discussion of releases and plans | [share.exponential.earth/forums](https://share.exponential.earth/forums) |
| The roadmap page | What is planned | [Roadmap](../../project/roadmap.md) |

## Releases and version numbers

- Security fixes are made on `main` and published with the next 6.0.x release; "A published tag is never changed"
  (`SECURITY.md`). To get a fix, you upgrade to the newest release.
- Only the 6.0 line is supported; 5.x and older are not (`SECURITY.md`, "Supported Versions").
- How a site moves to a new release is explained in [Update with Git](../../update-with-git.md) and in
  [How updates work](../../customers/healthy/how-updates-work.md).

## A routine that takes ten minutes a month

1. Read the release notes of any release since last month.
2. Scan the forum's newest threads for problems that match your setup.
3. Check whether your site runs the newest 6.0.x release; plan the upgrade if not.
4. Note one thing you could contribute: a confirmation of a bug, a translation, a help page.

## Related pages

- [Joining the community](README.md)
- [How releases happen](../how-it-works/how-releases-happen.md)
- [Testing releases](../contributing/testing-releases.md)
- [Writing about a release](../../advocates/writing-about-a-release.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
