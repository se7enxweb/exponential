---
title: Covering 6.0.15 while it is in development
audience: press
category: press-media
order: 106
summary: How to report on the Exponential 6.0.15 line before it has a release tag - what the sources say about it, how to label the work as in development, which phrasing is accurate, and why no date may be given.
keywords: 6.0.15, development line, unreleased, main branch, dev-main, preview, in development, no release date, Velocity, admin4, audit trail, Exponential Layouts, Exponential Intelligence
type: help
---
# Covering 6.0.15 while it is in development

This page is for reporters and creators who want to write about the work in the Exponential 6.0.15 line now, before
it is released. That is allowed and the work is documented in detail. What matters is to say clearly that it is in
development and to give no release date, because no source gives one.

## Contents

1. [What the sources say about 6.0.15](#what-the-sources-say-about-6015)
2. [What is in the line](#what-is-in-the-line)
3. [Labeling it correctly](#labeling-it-correctly)
4. [Phrases: accurate and inaccurate](#phrases-accurate-and-inaccurate)
5. [When the tag arrives](#when-the-tag-arrives)

## What the sources say about 6.0.15

| Fact | Source |
|---|---|
| "Not released yet. The 6.0.15 line follows v6.0.14 (2026-06-07); no tag `v6.0.15` existed on 2026-10-12." | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |
| "Date not released yet" | [release overview](../../release-overview) |
| The `main` branch is "the current development of the 6.0 line, today 6.0.15" and identifies itself as `6.0.15stable` in `lib/version.php` | [Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches) |
| "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x release." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| On 18 October 2026 the newest tag was still `v6.0.14` | `git tag -l 'v6.0.*' --sort=version:refname` |

Note the trap in `6.0.15stable`: it is the version string of the development branch, not a release. A release is a
tag ([Covering a release](covering-a-release.md#what-counts-as-a-release)).

## What is in the line

The changelog describes "four months of work", organized by period. The highlights named in
[Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) are "Velocity, admin4, audit trail, Exponential
Layouts, Exponential Intelligence, the help system and much more". The history adds the months:

| Period | What happened, per the history | Month page |
|---|---|---|
| July 2026 | "The command line grew up": `ezpm` in the console, the Kickstarter, a password reset tool | [July 2026](../../history/2026/2026-07.md) |
| August 2026 | Template debugging, six security patches, tests on every change | [August 2026](../../history/2026/2026-08.md) |
| September 2026 | Paged long lists, Velocity (from 22 September), a one-command install, a hardening sweep | [First half](../../history/2026/2026-09a.md), [second half](../../history/2026/2026-09b.md) |
| October 2026 | "jQuery 4 and admin4, an audit trail, `exp:ini`, a debug bar, content jobs and TinyMCE 8" | [October 2026](../../history/2026/2026-10.md) |

Exponential Intelligence and the job queue arrive in the distribution "inactive" and are switched on by the site
owner ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)).
Mention that if you describe them.

## Labeling it correctly

Put the status in the headline or the first sentence, not in a footnote:

```text
In development: what the next Exponential version, 6.0.15, is set to include
Exponential's development line adds an audit trail (not released yet)
```

In the body:

```text
These changes are in the 6.0.15 development line of Exponential. As of <date you checked>, no 6.0.15
release had been published; the newest release is 6.0.14 of 7 June 2026. The project has not announced a
release date. Source: <link to the 6.0.15 changelog>.
```

## Phrases: accurate and inaccurate

| Inaccurate | Accurate |
|---|---|
| "Exponential 6.0.15 is out." | "Exponential 6.0.15 is in development." |
| "6.0.15, due this autumn" | "6.0.15 has no announced release date." |
| "The new release includes ..." | "The development line includes ..." |
| "6.0.15 stable" | "the 6.0.15 line" |
| "Everyone gets Velocity in the next update." | "Velocity is a separate package that Exponential suggests" ([Getting the code](../../install/03-getting-the-code.md#34-the-optional-velocity-package)) |
| "Feature X will be in 6.0.15 for sure." | "Feature X is in the development line today"; lines can still change before a tag |

If you install the development line for a review, say so and follow
[Testing the development line](../advocates/testing-the-development-line.md). Do not show a test site's addresses
or sign-in data.

## When the tag arrives

1. Confirm the tag `v6.0.15` ([Verifying a release yourself](verifying-a-release-yourself.md)).
2. Read the released changelog; the "Not released yet" line will be replaced, and contents may differ from what you
   saw in development.
3. Update or correct earlier stories that described the line ([Corrections and updates](corrections-and-updates.md)).
4. Use the [Release coverage checklist](release-coverage-checklist.md).

## Related pages

- [Covering a release](covering-a-release.md)
- [Embargoes and timing](embargoes-and-timing.md)
- [Claims you must not make, The future](../advocates/claims-you-must-not-make.md#the-future)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
