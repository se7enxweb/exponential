---
title: "Story: operating systems"
audience: press
category: press-media
order: 71
summary: A feature story about where Exponential runs - the four documented tiers from "tested here" to "not possible today", Linux distributions, BSD, macOS, Windows, containers and processor architectures - with the honesty of a reasoned expectation versus a test result.
keywords: story, operating systems, Linux, BSD, FreeBSD, macOS, Windows, containers, Docker, tiers, support matrix, AlmaLinux, Ubuntu, Debian
type: help
---
# Story: operating systems

This page is for journalists who cover platforms and hosting. Exponential's documentation sorts operating systems into
four tiers and says plainly which are tested and which are only expected to work.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [Facts you can print](#facts-you-can-print)
3. [What not to claim](#what-not-to-claim)
4. [Angles and headline ideas](#angles-and-headline-ideas)

## The story in one paragraph

Exponential runs on "almost any [operating system] that runs PHP 8.0 or later (PHP 7.4 usable without support)", and its documentation sorts them into
four tiers: **tested here** (the project runs it every day or its CI tests on it), **known to work**, **expected to
work** ("a reasoned expectation, not a test result") and **not possible today** ([Operating system support](../os-support.md)).
Tier 1 is the project's own development server on AlmaLinux 9 and the CI on Ubuntu; tier 2 includes the Red Hat family,
Debian, Ubuntu, Alpine, FreeBSD, macOS, Windows and containers.

## Facts you can print

| Fact | Source |
|---|---|
| Four tiers, with definitions | [The four tiers](../os-support.md#the-four-tiers) |
| Tier 1: AlmaLinux 9 (the development server) and Ubuntu on GitHub Actions (CI on PHP 8.1 to 8.5) | [Tier 1: tested here](../os-support.md#tier-1-tested-here) |
| Tier 2: RHEL 9 and 10 and relatives, Debian 12 and 13, Ubuntu 22.04 and 24.04, Alpine, Plesk hosts, FreeBSD 14, DragonFly BSD, macOS, Windows, Docker and Podman | [Tier 2: known to work](../os-support.md#tier-2-known-to-work) |
| Velocity ships `.deb` packages for Debian and Ubuntu, a macOS arm64 binary and container images for amd64 and arm64 | same |
| Users who run it on a tier 3 system are asked to report it so the row can move up | [The four tiers](../os-support.md#the-four-tiers) |

## What not to claim

| Do not say | Say instead |
|---|---|
| "Runs everywhere" (as the press kit's "Works Everywhere, On Any OS") | "Runs on many systems, sorted into documented tiers" |
| "Tested on Windows" | Windows is tier 2, known to work; the page notes what is missing |
| "Runs on shared hosting with Velocity" | Velocity usually needs its own server; classic hosting uses Apache or Nginx with PHP |

## Angles and headline ideas

- "A support matrix that admits what it has not tested."
- "From AlmaLinux to Apple Silicon: where a PHP CMS runs."

## Related pages

- [Story: hosting freedom](story-hosting-freedom.md)
- [Story: current PHP](story-current-php.md)
- [Operating system support](../os-support.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
