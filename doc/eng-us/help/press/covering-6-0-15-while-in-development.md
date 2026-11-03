---
title: Covering 6.0.15, before and after its tag
audience: press
category: press-media
order: 106
summary: How to report on the Exponential 6.0.15 line, the current release line of Exponential 6.0 - what the sources said about it before the tag v6.0.15, how to check whether the tag exists, which phrasing is accurate, and why a release date comes only from the tag.
keywords: 6.0.15, current release line, main branch, dev-main, tag v6.0.15, in development, release date, Velocity, admin4, audit trail, Exponential Layouts, Exponential Intelligence
type: help
---
# Covering 6.0.15, before and after its tag

This page is for reporters and creators who write about the work in the Exponential 6.0.15 line, the current release
line of Exponential 6.0, which follows 6.0.14. Until the tag `v6.0.15`, the line was in development on the `main`
branch, and coverage had to say so and give no release date, because no source gave one. Check first whether the tag
exists (`git tag -l 'v6.0.*' --sort=version:refname`), and take a release date only from the tag.

## Contents

1. [What the sources say about 6.0.15](#what-the-sources-say-about-6015)
2. [What is in the line](#what-is-in-the-line)
3. [Labeling it correctly](#labeling-it-correctly)
4. [Phrases: accurate and inaccurate](#phrases-accurate-and-inaccurate)
5. [When the tag arrives](#when-the-tag-arrives)

## What the sources say about 6.0.15

Before the tag, the sources said the following; quote them with the date they carried.

| Fact | Source |
|---|---|
| "Not released yet. The 6.0.15 line follows v6.0.14 (2026-06-07); no tag `v6.0.15` existed on 2026-10-12." | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) |
| "Date not released yet" | [release overview](../../release-overview) |
| The `main` branch is "the current development of the 6.0 line, today 6.0.15" and identifies itself as `6.0.15stable` in `lib/version.php` | [Getting the code](../../install/03-getting-the-code.md#31-versions-tags-and-branches) |
| "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x release." | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) |
| On 18 October 2026 the newest tag was still `v6.0.14` | `git tag -l 'v6.0.*' --sort=version:refname` |

Note the trap in `6.0.15stable`: it is the version string of the `main` branch, not a release. A release is a
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
| October 2026 | jQuery 4 and admin4, an audit trail, `exp:ini`, a debug bar, content jobs and TinyMCE 8 | [October 2026](../../history/2026/2026-10.md) |

Exponential Intelligence and the job queue arrive in the distribution "inactive" and are switched on by the site
owner ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#12-october-2026-exponential-intelligence-in-the-distribution)).
Mention that if you describe them.

## Labeling it correctly

Put the status in the headline or the first sentence, not in a footnote. If the tag `v6.0.15` does not exist on the
day you check, write for example:

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

Once the tag exists, call 6.0.15 a release and give the date of its tag
([Covering a release](covering-a-release.md)).

## Phrases: accurate and inaccurate

The left column was inaccurate before the tag `v6.0.15`; the right column is what was accurate then.

| Inaccurate | Accurate |
|---|---|
| "Exponential 6.0.15 is out." | "Exponential 6.0.15 is in development." |
| "6.0.15, due this autumn" | "6.0.15 has no announced release date." |
| "The new release includes ..." | "The development line includes ..." |
| "6.0.15 stable" | "the 6.0.15 line" |
| "Everyone gets Velocity in the next update." | "Velocity is a separate package that Exponential's `composer.json` requires at `~0.0.4.57`" ([Getting the code](../../install/03-getting-the-code.md#34-the-velocity-package)) |
| "Feature X will be in 6.0.15 for sure." | "Feature X is in the development line today"; lines can still change before a tag |

After the tag, "the 6.0.15 line" is still accurate, "6.0.15 stable" is still not the product's wording, and
"Exponential 6.0.15 is out" becomes accurate, with the date of the tag as the release date.

If you install the `main` branch for a review, say so and follow
[Testing the development line](../advocates/testing-the-development-line.md). Do not show a test site's addresses
or sign-in data.

## When the tag arrives

1. Confirm the tag `v6.0.15` ([Verifying a release yourself](verifying-a-release-yourself.md)).
2. Read the changelog as it stands at the tag; contents may differ from what you saw before the tag.
3. Update or correct earlier stories that described the line ([Corrections and updates](corrections-and-updates.md)).
4. Use the [Release coverage checklist](release-coverage-checklist.md).

## Related pages

- [Covering a release](covering-a-release.md)
- [Embargoes and timing](embargoes-and-timing.md)
- [Claims you must not make, The future](../advocates/claims-you-must-not-make.md#the-future)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
