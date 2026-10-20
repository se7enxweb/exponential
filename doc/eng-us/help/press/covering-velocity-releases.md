---
title: Covering Velocity releases
audience: press
category: press-media
order: 108
summary: How to report on releases of Exponential Velocity, the PHP web server - what it is, how its versions count, where its release notes live, how to quote its performance figures with their conditions, and what not to claim.
keywords: Velocity, Exponential Velocity, web server, release, 0.0.4.50, version numbers, performance, benchmark, Qbix, MIT, release notes, GitHub Releases
type: help
---
# Covering Velocity releases

This page is for technical press, podcasters and video creators who report on Exponential Velocity. Velocity is
released far more often than Exponential itself and has a version scheme that is easy to misread. Its performance
figures are documented with their conditions, and those conditions belong in your story.

## Contents

1. [What Velocity is](#what-velocity-is)
2. [Releases and version numbers](#releases-and-version-numbers)
3. [Where the release facts live](#where-the-release-facts-live)
4. [Quoting the performance figures](#quoting-the-performance-figures)
5. [What not to claim](#what-not-to-claim)

## What Velocity is

| Fact | Source |
|---|---|
| "Velocity is a web server written in PHP, bundled with Exponential, that keeps the application loaded between requests." | [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md), "Exponential Velocity — the fast option" |
| "It began as a fork of the Qbix web server engine, and the two projects have since drifted far apart" | The package's README, quoted in [Who is 7x?, Exponential Velocity](../who-is-7x.md#exponential-velocity) |
| The PHP application server "that Exponential recommends for every stage from development to production" | [Velocity chronicle](../../history/velocity/README.md) |
| MIT licensed | Its `composer.json`, per [Who is 7x?](../who-is-7x.md#open-source-licenses) |
| A Composer package that the kernel requires as of 6.0.15 (`~0.0.4.54`, which runs on PHP 8.0 and needs the `sockets` extension); before, the kernel only suggested it | [Getting the code](../../install/03-getting-the-code.md#34-the-velocity-package) |
| First commits 20 July 2026, first release `v0.0.1` in September 2026, part of Exponential since 22 September 2026 | [Who is 7x?](../who-is-7x.md#exponential-velocity) |

## Releases and version numbers

Velocity versions have four positions and the last one keeps counting: "`0.0.4.9` is followed by `0.0.4.10`, never
`0.0.5.0`" ([Velocity release notes](../../../changelogs/extensions/exponential-velocity.md)). On 18 October 2026 the
latest release in the release notes was `v0.0.4.50` of 12 October 2026; GitHub showed `v0.0.4.53` as the newest tag on
19 October 2026. On 20 October 2026 the kernel's `composer.json` moved Velocity from `suggest` to `require`, at
`~0.0.4.54`, which accepts every later 0.0.4.x: as of 6.0.15 Velocity is part of Exponential
([Getting the code](../../install/03-getting-the-code.md#34-the-velocity-package),
[Velocity is part of Exponential 6.0.15](../velocity-is-part-of-exponential-6-0-15.md)). Check the tags
on the day you write ([Verifying a release yourself](verifying-a-release-yourself.md)).

Many releases can fall on one day: the chronicle lists `v0.0.2.1` to `v0.0.4.14` on 22 September 2026 alone
([Velocity chronicle](../../history/velocity/README.md#month-pages)). Report the release you are writing about, with
its own notes; do not present each one as major news.

The old name survives in identifiers: "In the code you will still see `Q_WebServer`, `qbixserver.php`, `qbixctl` ...
these are identifiers and stay as they are. The product is called Velocity" ([Velocity chronicle](../../history/velocity/README.md#a-note-on-names)).

## Where the release facts live

| What | Where |
|---|---|
| Releases up to v0.0.4.42, in full | [Velocity release notes](../../../changelogs/extensions/exponential-velocity.md) |
| Releases from v0.0.4.43 on | A summary table on the same page, full notes on [GitHub Releases](https://github.com/se7enxweb/exponential-velocity/releases) |
| The story, month by month | [Velocity chronicle](../../history/velocity/README.md) |
| How to upgrade | [Velocity engine upgrade notes](../../bc/6.0/velocity-engine-upgrade-notes.md) |
| Packages (deb, rpm, Docker, binaries) | [Velocity packages and binaries](../../features/6.0/velocity-packages-and-binaries.md) |
| The Composer package | [se7enxweb/exponential-velocity on Packagist](https://packagist.org/packages/se7enxweb/exponential-velocity) |

Three tags, `v0.0.4.21`, `v0.0.4.22` and `v0.0.4.26`, have no release page because "their builds did not produce a
usable artifact" (release notes). Do not report them as releases with features.

## Quoting the performance figures

The README states the figures "currently documented for this stack": "0.36 ms per cached page, 8,941 requests per
second sustained, and repeat visits never touch the network". It also explains them, and the explanations are part of
an honest quote:

| Figure | Condition stated in the README |
|---|---|
| 0.36 ms | "the server's own work to answer a cached page", a median "over hundreds of requests" |
| 8,941 requests per second | "measured on a 12-core machine serving a real page, with zero errors at every concurrency tried, up to 384 connections in flight" |
| Repeat visits never touch the network | "refers to the optional navigation cache (a service worker)" |

The README adds that the figures are "reproducible with scripts that ship with the project" and that "Most of the
speed came from fixing Exponential and the cache". Quote the figures with their conditions and the link, or not at
all ([Verifying numbers](verifying-numbers.md)).

## What not to claim

| Do not write | Why |
|---|---|
| "N times faster than Apache / nginx / FrankenPHP" | No published comparison exists ([Claims you must not make](../advocates/claims-you-must-not-make.md#numbers)) |
| "Your site will answer in 0.36 ms" | The figure is for a cached page on the measured setup |
| "Velocity is experimental" | The project calls it the recommended engine; FrankenPHP is "the production-ready alternative" ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-velocity)) |
| "Velocity replaces Exponential's caching" | It adds a response cache; the README credits most speed to fixes in Exponential and its cache |
| "Velocity is Qbix" | It began as a fork; the projects "have since drifted far apart" |

## Related pages

- [Covering a release](covering-a-release.md)
- [How version numbers work](how-version-numbers-work.md)
- [Comparisons done fairly](comparisons-done-fairly.md)
- [Talking points: speed and Velocity](../advocates/talking-points-speed-and-velocity.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
