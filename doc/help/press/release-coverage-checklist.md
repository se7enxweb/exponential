---
title: Release coverage checklist
audience: press
category: press-media
order: 112
summary: A copyable checklist to run before publishing a story, video or post about an Exponential release - the tag, the date, the version format, the contents, the names, the quotes, security and privacy, links and a correction plan.
keywords: checklist, release, coverage, before publishing, tag, version, date, names, quotes, sources, fact check, links
type: help
---
# Release coverage checklist

This page is for anyone about to publish release coverage: a news item, a review, a newsletter section, a video or a
social thread. Copy the checklist into your notes and tick each line. Every line points to the page that explains it,
so a "no" tells you where to look.

## Contents

1. [Before you start writing](#before-you-start-writing)
2. [The full checklist](#the-full-checklist)
3. [For video, audio and live formats](#for-video-audio-and-live-formats)
4. [After publishing](#after-publishing)

## Before you start writing

Answer three questions first; they decide the whole story.

| Question | Where to check |
|---|---|
| Is it released (a tag exists), or is it the development line? | [Verifying a release yourself](verifying-a-release-yourself.md) |
| Which product is it: the kernel, an extension, Velocity or a Platform package? | [Where changelogs and release notes live](where-changelogs-and-release-notes-live.md) |
| Who is my audience: general, business, technical, local or education? | [Release story templates](release-story-templates.md) |

## The full checklist

```text
RELEASE COVERAGE CHECKLIST                          story: ____________________  date checked: __________

STATUS
[ ] The tag exists (v<version>), checked today with git or GitHub Releases
[ ] Or: the story says "in development" in the headline or first sentence, and gives no release date
[ ] The release date is the tag date (or says which date it is), taken from the changelog or release overview

NUMBERS AND NAMES
[ ] Version written "Exponential 6.0.x" in text; "v6.0.x" only when naming the tag
[ ] Extension, Velocity and Platform versions are not mixed up with the kernel version
[ ] Product name "Exponential"; "eZ Publish" only as history, in the past tense
[ ] 7x = the company; the Exponential Foundation = the nonprofit; not mixed up
[ ] No statement that Exponential is associated with eZ Systems or Ibexa
[ ] Every number has a source and the date it was counted

CONTENT
[ ] Every feature named is in the changelog (or the history), with a link
[ ] Scope kept: no "rebuilt", "fully", "all" where the changelog says less
[ ] Upgrade advice links the upgrade chapter; nothing suggests running an installer over an existing site
[ ] No user counts, market share, speed multiples, compliance or "secure" claims

QUOTES AND ATTRIBUTION
[ ] Every quote is verbatim from a published source (with link) or given in writing by 7x or the Foundation
[ ] No quote composed, "cleaned up" or attributed to a person who did not say it
[ ] Opinions are marked as mine / ours

SECURITY AND PRIVACY
[ ] No details of unfixed vulnerabilities
[ ] No passwords, keys, internal host names or sign-in screens with data on screen
[ ] No customer names or personal data without consent

LINKS AND CREDIT
[ ] Links to the changelog, the feature pages and the repository work
[ ] Credit for Netgen, Mugo Web or CJW Network where the release uses their work
[ ] Disclosure line if I am paid, sponsored or a contributor

AFTERWARDS
[ ] I know how I will publish a correction if something is wrong
```

Pages behind the lines: [Covering a release](covering-a-release.md),
[Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md),
[How version numbers work](how-version-numbers-work.md), [Brand and naming rules](../business/brand-and-naming-rules.md),
[Verifying numbers](verifying-numbers.md), [Quotes policy](quotes-policy.md),
[Covering security fixes](covering-security-fixes.md), [Disclosure and conflicts of interest](../advocates/disclosure-and-conflicts-of-interest.md),
[Corrections and updates](corrections-and-updates.md). The upgrade line comes from
[Writing about a release](../advocates/writing-about-a-release.md#checklist): the installer "must never run over an
existing site".

## For video, audio and live formats

```text
[ ] On-screen text and lower thirds use "Exponential 6.0.x" and the right names
[ ] The demo installation is my own, freshly installed; no real data, no sign-in data visible
[ ] Spoken version numbers match the tag ("six point oh point fourteen")
[ ] Description or show notes carry the links to the changelog and the repository
[ ] Unreleased features are called "in development" out loud, not only in the description
```

## After publishing

- Keep your checklist with the date; it shows what you checked if a reader questions a fact.
- When the next release arrives, do not edit the old story to make it current; add an update line
  ([Corrections and updates](corrections-and-updates.md)).

## Related pages

- Fact-checking checklist
- [Common errors in coverage](common-errors-in-coverage.md)
- [Covering a release](covering-a-release.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
