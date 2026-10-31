---
title: Crediting Netgen, Mugo Web and CJW Network
audience: press, advocate
category: press-media
order: 35
summary: Which parts of Exponential build on the open source work of other companies - the concept of the upstream layouts packages, Netgen's AdminUI, Mugo Web's LoveStack, CJW Network's newsletter and request-shield, the Qbix server, Zeta Components - how the project credits them, and how coverage should name them.
keywords: credit, attribution, Netgen, AdminUI, Mugo Web, LoveStack, CJW Network, cjw_newsletter, request-shield, Qbix, Zeta Components, AdminNeo, open source credit
type: help
---
# Crediting Netgen, Mugo Web and CJW Network

This page is for journalists and creators who write about features of Exponential that started in another company's
open source work. The project credits these companies by name; good coverage does the same.

## Contents

1. [Who built what](#who-built-what)
2. [How the project words the credit](#how-the-project-words-the-credit)
3. [How to write it](#how-to-write-it)
4. [What not to imply](#what-not-to-imply)

## Who built what

| Company or project | What Exponential uses | Source |
|---|---|---|
| **Netgen** | Exponential Layouts brings "the concept of the upstream layouts packages to the Exponential kernel"; the Nexus distribution builds on Netgen Media Site and the upstream layouts packages; AdminUI is "a feature complete port of the legendary Netgen AdminUI"; the repository `se7enxweb/exponential` is a GitHub fork of `netgen/ezpublish-legacy` | README; [Contributors and friends](../who-is-7x.md#contributors-and-friends-of-the-project) |
| **Mugo Web** | The LoveStack improvements, rebuilt in 6.0.15 "with full credit to Mugo Web" | README, "Exponential Distribution Integration: LoveStack" |
| **CJW Network** | `cjw_newsletter`; a CJW flavor of Nexus; request-shield, documented as a companion in front of the HTTP cache ("a project of CJW Network ... not of the Foundation") | [Companion projects](../who-is-the-exponential-foundation.md#companion-projects) |
| **Qbix** | Exponential Velocity "began as a fork of the Qbix web server engine" | Velocity README; [Exponential Velocity](../who-is-7x.md#exponential-velocity) |
| **Zeta Components** | The kernel's libraries; 7x maintains forks and submitted security pull requests upstream | [The PHP stack it stands on](../who-is-the-exponential-foundation.md#the-php-stack-it-stands-on-and-gives-back-to) |
| **AdminNeo** | The database source editor `sevenx_dse` | [sevenx_dse](../../features/6.0/extensions/sevenx_dse.md) |
| **eZ Systems** and hundreds of contributors | The code base itself, 1999 to 2021 | COPYRIGHT.md; [The eZ Publish years](../who-is-7x.md#the-ez-publish-years-1999-to-2021) |

## How the project words the credit

- "with full credit to Mugo Web" (README).
- "brings the concept of the upstream layouts packages to the Exponential kernel" (README).
- "It began as a fork of the Qbix web server engine, and the two projects have since drifted far apart" (Velocity README).

## How to write it

```text
"Exponential Layouts, a page builder that ports the concept of the upstream layouts packages to Exponential, ..."
"The 6.0.15 line rebuilds the LoveStack improvements of Mugo Web, with credit to the company."
"Velocity began as a fork of the Qbix web server and has since developed separately."
```

## What not to imply

| Avoid | Why |
|---|---|
| "Netgen's page builder for Exponential" | Exponential Layouts is a port of the concept with its own name and code |
| "built by Netgen" for Exponential | Netgen contributed pull requests and its work is ported; 7x leads Exponential |
| "Mugo Web joins Exponential" | The sources document reuse of open source work, not a business relationship |
| "request-shield is part of Exponential" | It is a CJW Network project, documented as a companion |
| Any endorsement by these companies | None is documented; ask them for comment if your story needs it |

## Related pages

- [Brand and naming for media](brand-and-naming-for-media.md)
- [Story: Exponential Layouts, the page builder](story-exponential-layouts.md)
- [Respecting other projects and companies](../advocates/respecting-other-projects-and-companies.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
