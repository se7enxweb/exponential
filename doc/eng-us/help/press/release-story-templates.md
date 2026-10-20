---
title: Release story templates
audience: press
category: press-media
order: 114
summary: Fill-in templates for release coverage of Exponential - a news brief, a technical article, a development-line preview, an extension or Velocity note, a social post and a video script outline - each with the facts to fill in and the source for each.
keywords: templates, release story, news brief, technical article, preview, social post, video script, show notes, fill in, release coverage
type: help
---
# Release story templates

This page is for reporters, editors and creators who want a quick, correct structure for release coverage. Each
template has slots in angle brackets. Fill every slot from a source; if you cannot fill one, delete the sentence
rather than guess. The templates contain no quotes, on purpose: quotes come only from published sources or from 7x
or the Foundation in writing ([Quotes policy](quotes-policy.md)).

## Contents

1. [Where the slots come from](#where-the-slots-come-from)
2. [News brief, about 150 words](#news-brief-about-150-words)
3. [Technical article outline](#technical-article-outline)
4. [Development-line preview](#development-line-preview)
5. [Extension or Velocity note](#extension-or-velocity-note)
6. [Social post](#social-post)
7. [Video or podcast segment](#video-or-podcast-segment)

## Where the slots come from

| Slot | Source |
|---|---|
| `<version>`, `<date>`, `<tag>` | The changelog header ("Released ... Tag ...") and the [release overview](../../release-overview) |
| `<headline change>` | The changelog's "In short" ([Reading a changelog](reading-a-changelog.md)) |
| `<upgrade note>` | The changelog's "Upgrading" |
| `<feature page>` | The links in the changelog |
| `<release count>` | `git tag -l 'v6.0.*'` or [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases) |
| `<who stands behind it>` | [Who is the Exponential Foundation?, The Foundation and 7x](../who-is-the-exponential-foundation.md#the-foundation-and-7x) |

## News brief, about 150 words

```text
Exponential <version> released

<Date> - Exponential <version>, a new release of the free and open source PHP content management
system, was published on <date> (tag v<version>). <Headline change, in the words of "In short".>

<Second change, one sentence.> <Upgrade note: what site owners must do, or "the changelog lists no
required action for ...".>

Exponential is developed under the GNU GPL, version 2 or later. The company 7x leads its development;
the Exponential Foundation, a nonprofit founded in 2026, operates and funds the project. The release is
the <ordinal> of the 6.0 line, which began with 6.0.0 on 1 January 2024.

Changelog: <link>   Repository: https://github.com/se7enxweb/exponential
```

## Technical article outline

```text
1. What is new in Exponential <version> (tag v<version>, <date>)         - changelog header
2. The headline change and how it works                                 - changelog + feature page
3. Two or three further changes for developers and administrators       - Added / Updated sections
4. Upgrading: what to run, what to check                                - "Upgrading" + upgrade chapter
5. Requirements: PHP versions, databases                                - Getting the code, section 3.1
6. Where to get it: Composer package, Git tag                           - Getting the code
7. Context: the 6.0 line so far                                         - Who is 7x?, The 6.0.x releases
8. Comment from 7x or the Foundation (only if given in writing)         - Quotes policy
```

## Development-line preview

```text
In development: <topic> in the next Exponential version

The development line of Exponential, 6.0.15, includes <feature>, according to the project's
changelog for the line (<link>). As of <date checked>, 6.0.15 had not been released; the newest
release is 6.0.14 of 7 June 2026, and the project has not announced a release date.

<What the feature does, from its feature page.> <Whether it is active by default, if the source says.>
```

Rules for this template: [Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md).

## Extension or Velocity note

```text
<Package> <version> for Exponential

<Package>, <what it is in one phrase>, has a new release, <version>, dated <date> (<link to its
release notes>). <One or two changes, in the words of the release notes.> It is a separate package
with its own version numbers; sites update it on its own.
```

For Velocity figures, add their conditions or leave them out ([Covering Velocity releases](covering-velocity-releases.md)).

## Social post

```text
Exponential <version> is out (tag v<version>, <date>): <headline change in under 15 words>.
Changelog: <link>
```

```text
Not released yet, but in Exponential's 6.0.15 development line: <feature>. Details: <link>
```

Keep the version and status in the post itself; a thread's first post is often shared alone.

## Video or podcast segment

```text
INTRO      "Exponential <version> was released on <date>." (show the tag or the changelog header)
WHAT       Headline change - show it on your own fresh installation
WHO        "The company 7x leads development; the Exponential Foundation, a nonprofit, operates the project."
UPGRADE    "If you run a site, read the upgrade notes first" (link in the description)
CLOSE      Links: changelog, repository, the feature page; disclosure if sponsored
```

Show notes must carry the same links; say "in development" out loud for anything unreleased.

## Related pages

- [Covering a release](covering-a-release.md)
- [Release coverage checklist](release-coverage-checklist.md)
- [Social post templates](../advocates/social-post-templates.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
