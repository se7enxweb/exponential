---
title: Embargoes and timing
audience: press
category: press-media
order: 139
summary: Timing coverage of Exponential when no embargo policy is published - releases that appear as public Git tags, development work visible in the open repository, security fixes that must wait for the release, how to agree an embargo in writing, and how to time a story on a development line.
keywords: embargo, timing, release timing, publication time, pre-briefing, security embargo, coordinated disclosure, open development, tags, scheduling
type: help
---
# Embargoes and timing

This page is for reporters and editors who plan when to publish. Exponential is developed in the open: most news is
public the moment it happens, in the repository. The project publishes no embargo policy. This page explains what
that means for timing, and the one case where waiting is required.

## Contents

1. [Open development means no secret dates](#open-development-means-no-secret-dates)
2. [Releases](#releases)
3. [Work in development](#work-in-development)
4. [Security: the one case where you wait](#security-the-one-case-where-you-wait)
5. [Agreeing an embargo](#agreeing-an-embargo)

## Open development means no secret dates

Every commit to the main branch is public on GitHub, and the history pages describe the work month by month
([The history pages as a source](the-history-pages-as-a-source.md)). A journalist who follows the repository sees
features as they are built. There is usually nothing to embargo.

## Releases

- A release happens when its Git tag is published ([Covering a release](covering-a-release.md)). It can be reported
  from that moment.
- No release dates are announced in advance in the sources this help uses. Do not publish a date you inferred.
- Packagist shows the new version shortly after the tag; Composer users can install it from then.

## Work in development

You may report on work in the development line; it is public. Make clear that it is unreleased:

```text
"The changelog of the coming 6.0.15 release, which is still in development, lists ..."
```

Features in development can change before release. Re-check them when the release is tagged
([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).

## Security: the one case where you wait

Vulnerabilities are reported privately and fixed in the next release ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)).
If you learn about an unfixed vulnerability, from a source, a reader or your own testing:

1. Do not publish technical details or a way to exploit it.
2. Make sure it has been reported privately to security@se7enx.com ([If you find a vulnerability](if-you-find-a-vulnerability.md)).
3. Publish after the fixed release is out, and point readers to the update ([Covering security fixes](covering-security-fixes.md)).

This protects the people who run sites, not the project's image.

## Agreeing an embargo

If 7x or the Foundation offers you information before it is public (an announcement, an interview about something
coming), agree the terms in writing first:

```text
I agree not to publish information from <briefing / document> before <date, time, time zone>, unless it
becomes public earlier through <the repository / the website>, in which case I may publish from then.
```

The second clause matters in an open project: once the code is public, it is public.

## Related pages

- [Covering a release](covering-a-release.md)
- [Covering security fixes](covering-security-fixes.md)
- [Requesting an interview](requesting-an-interview.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
