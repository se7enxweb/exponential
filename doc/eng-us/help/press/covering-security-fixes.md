---
title: Covering security fixes
audience: press
category: press-media
order: 110
summary: How to report on security fixes in Exponential responsibly - the documented security process, where fixes are recorded, how to describe them without exaggeration or alarm, what advice to give readers, and what never to publish.
keywords: security, security fix, vulnerability, hardening, patch, SECURITY.md, 6.0.13, August 2026, responsible reporting, upgrade, disclosure
type: help
---
# Covering security fixes

This page is for reporters, security writers and creators who cover security fixes in Exponential. Security stories
are read by people who must decide whether to act, so precision matters more than drama. Report what the fix does,
which versions it concerns and what readers should do, with the project's documents as your source.

## Contents

1. [The documented security process](#the-documented-security-process)
2. [Where fixes are recorded](#where-fixes-are-recorded)
3. [Describing a fix accurately](#describing-a-fix-accurately)
4. [Advice you can give readers](#advice-you-can-give-readers)
5. [What never to publish](#what-never-to-publish)

## The documented security process

Everything below is from [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

| Point | What SECURITY.md says |
|---|---|
| Maintained line | "Exponential 6.0 is the maintained line." |
| Where fixes are made | "Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`) and published with the next 6.0.x release." |
| Older tags | Released tags `v6.0.0` to `v6.0.14` are "fixed by upgrading to the newest 6.0.x" |
| Unsupported | "5.x, 4.x, 3.x and older" |
| Reporting | "privately by e-mail to security@se7enx.com, not in the public issue tracker or the discussions" |
| First answer | "within one business day" |
| After a report | The reporter is "told whether the report is accepted or declined, and kept informed while a fix is prepared" |

SECURITY.md does not describe advisories, severity scores, CVE identifiers or a disclosure calendar. If you need
those for a story, ask 7x in writing ([Requesting an interview](requesting-an-interview.md)) and print "the project
does not publish this" if the answer is no.

## Where fixes are recorded

| Record | Example |
|---|---|
| The changelog of the release | [6.0.13](../../../changelogs/6.0/6.0.13.md): "Security hardening (seven findings fixed)", findings SEC-01 to SEC-06 and others |
| A specification page per hardening round | [The 6.0.13 hardening](../../specifications/6.0/security-hardening-6.0.13.md), [Security patches of August 2026](../../specifications/6.0/security-hardening-2026-08.md), [Security defaults of September 2026](../../specifications/6.0/security-defaults-2026-09.md) |
| The month page of the history | [August 2026](../../history/2026/2026-08.md): "Six security patches and jQuery 3.7.1 landed on 16 August" |
| The development line | The [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md) lists later fixes by date; they are not released until the 6.0.15 tag exists |

Note the status: the August 2026 patches are on the 6.0.15 development line. A story must not say they are in a
released version until a tag contains them ([Covering 6.0.15 while it is in development](covering-6-0-15-while-in-development.md)).

## Describing a fix accurately

| Instead of | Write |
|---|---|
| "Critical flaw found in Exponential" | Only what the source says; the sources do not grade severity |
| "Exponential was hacked" | Nothing of the kind, unless a named source documents an incident |
| "Now fully secure" | "Version X fixes the issues its changelog lists" |
| "Exponential has never had a vulnerability" | Not true and not claimable ([Claims you must not make](../advocates/claims-you-must-not-make.md#security-and-compliance)) |
| "A patched 6.0.14 is available" | "The fix arrives in the next 6.0.x release" ([Published versions never change](published-versions-never-change.md)) |

Use the words of the changelog for what was fixed, for example the August 2026 month page names "unpredictable
REST/OAuth tokens", "header injection in the After the Deadline RPC" and "shell escaping in the PDF and Word text
extractors". Quote them; do not add attack scenarios the sources do not describe.

## Advice you can give readers

```text
If you run Exponential: the project fixes security problems on its main branch and publishes them with
the next 6.0.x release. Versions 5.x and older are not supported. Read the changelog and the upgrade
notes before you upgrade, and follow the hardening guide for production sites:
- SECURITY.md: https://github.com/se7enxweb/exponential/blob/main/SECURITY.md
- Upgrading: <link to the upgrading chapter>
- Hardening: <link to chapter 13 of the installation book>
```

The links: [Upgrading](../../install/11-upgrading.md), [Security hardening, chapter 13](../../install/13-security-hardening.md),
and the shorter [Security hardening](../security-hardening.md) help page.

## What never to publish

- **Details of an unfixed vulnerability**, including steps to reproduce, proof-of-concept code or the affected
  file and parameter. If you learn of one, follow [If you find a vulnerability](if-you-find-a-vulnerability.md).
- **Names of sites you believe are vulnerable.** You cannot know their version or configuration, and naming them
  puts their users at risk.
- **Passwords, tokens or keys** from screenshots, logs or demos ([Security reports stay private](../advocates/security-reports-stay-private.md)).
- **The name of a reporter** who has not agreed to be named.

## Related pages

- [If you find a vulnerability](if-you-find-a-vulnerability.md)
- [Talking points: security](../advocates/talking-points-security.md)
- [Common errors in coverage](common-errors-in-coverage.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
