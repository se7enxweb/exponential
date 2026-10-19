---
title: Evaluating the health of the open source project
audience: government, project-manager, admin
category: public-sector
order: 130
summary: How a public body judges whether the Exponential open source project behind a bid is maintained and likely to last - releases and tags, changelogs, the security policy, continuous integration, documentation, community channels and governance - with the commands and pages to check each signal yourself.
keywords: open source health, project health, maintenance, sustainability, release cadence, changelog, SECURITY.md, continuous integration, community, governance, due diligence, longevity
type: help
---
# Evaluating the health of the open source project

This page is for the technical lead or architect on an evaluation panel. When you buy services around free software,
you also depend on the project that maintains the software. This page lists the signals you can check yourself in
the public repository and documentation, without relying on the bidder's word. Every signal links the page that
describes it.

## Contents
1. [Who maintains Exponential](#who-maintains-exponential)
2. [Releases and version policy](#releases-and-version-policy)
3. [Changelogs and history](#changelogs-and-history)
4. [Security policy](#security-policy)
5. [Tests and continuous integration](#tests-and-continuous-integration)
6. [Documentation](#documentation)
7. [Community and roadmap](#community-and-roadmap)
8. [Weak signals to note](#weak-signals-to-note)
9. [A health checklist](#a-health-checklist)

## Who maintains Exponential

Exponential is maintained, developed, supported and hosted by 7x, with the Exponential Foundation as the nonprofit
behind the open source project ([Who is 7x?](../../../who-is-7x.md),
[Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md)). Exponential continues the code
base of eZ Publish, a product its creator discontinued after almost eighteen years of service. Read both pages for
the history and for what the sources do not say.

## Releases and version policy

Check the tags yourself in a clone:

```bash
git fetch --tags
git tag -l 'v*' --sort=version:refname | tail -5
```

- Published tags `v6.0.0` to `v6.0.14` exist; 6.0.15 is in development
  ([Source control and releases](../../../../standards/source-control-and-releases.md#the-kernels-versions)).
- Release dates of recent tags are listed in [The roadmap](../../../project/roadmap.md#what-has-already-been-delivered).
- Published tags are never changed; a fix arrives as the next version
  ([Tags are permanent](../../../../standards/source-control-and-releases.md#tags-are-permanent)). For you this means a
  version number always identifies the same code.

## Changelogs and history

- Each kernel version has a changelog in `doc/changelogs/6.0/`, for example the
  [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md).
- Extensions have their own changelogs in `doc/changelogs/extensions/`.
- Every change since December 2023 is told in [the history](../../../../history/README.md).

A long, detailed changelog with security and accessibility entries is a sign of active maintenance; read a sample
of entries rather than counting them.

## Security policy

`SECURITY.md` in the repository root names the supported line (6.0.x), a private reporting address, and a first
answer "within one business day". Older lines (5.x and before) are not supported. The
[Security standards](../../../../standards/security.md) page documents three hardening passes and states its gaps.

## Tests and continuous integration

- The test suites run on PHP 8.1 to 8.5 on every push and pull request, and PHP 8.0 is checked to boot
  ([Who is the Exponential Foundation?](../../../who-is-the-exponential-foundation.md#the-support-commitment),
  [Testing and CI](../../../../standards/testing-and-ci.md)).
- Design tests hold accessibility rules of the admin4 design ([Accessibility standards](../../../../standards/accessibility.md)).

## Documentation

- The documentation lives in the repository, under the GNU Free Documentation License 1.3, and is shown as help in
  the administration ([The license of this documentation](../../../../README-LICENSE.md)).
- Standards pages state compliance honestly, with "Partly complies" and "Unverified" where that is the truth
  ([Standards](../../../../standards/README.md)).

## Community and roadmap

- Free channels: forums, Telegram, Discord, Slack ([Community channels](../../../project/community-channels.md)).
- Issue trackers on GitHub for the software and for the project.
- A public roadmap and a community roadmap ([The roadmap](../../../project/roadmap.md)). The roadmap page itself notes
  that some version numbers on it are out of date; the changelog is the authoritative record.
- A partner program with published levels ([Partners: start here](../../../partners/partners-start-here.md)).

## Weak signals to note

The documentation names these itself; weigh them against your risk appetite:

- `main` has no branch protection; merge and push rules are policy only.
- Commits and tags are not signed.
- No dependency vulnerability scanning in CI.
- Maintenance is concentrated in one company; ask the bidder how it would continue if that changed. The GPL lets
  anyone continue the code, which is your fallback.

## A health checklist

```text
[ ] Latest tag and its date (git tag --sort=version:refname): <...>
[ ] Commits on main in the last 90 days: git log --since=90.days --oneline | wc -l -> <...>
[ ] Changelog of the current line read: security and accessibility entries present
[ ] SECURITY.md: supported versions and reporting address present
[ ] CI workflows present in .github/workflows/ and passing on main
[ ] Documentation covers install, upgrade, security, accessibility, privacy
[ ] Community channels active (a recent forum thread or issue answered)
[ ] Weak signals recorded and judged
```

## Related pages
- [Evaluation: overview](README.md)
- [Procurement: overview](../README.md)
- [Getting support](../../../project/getting-support.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
