---
title: Support boundaries for bundles
audience: bundler, partner, hosting
category: bundling
order: 460
summary: Who supports what in a bundle - you, 7x, the community, the authors of extensions - the three kinds of support the project names, how partner support levels fit, how to route bugs and security reports, and a support statement template for your bundle.
keywords: support, boundaries, responsibility, community support, partner support, 7x, paid support, tickets, response time, bug report, security report, support statement, bundle
type: help
---
# Support boundaries for bundles

This page is for bundlers who write the support terms of their bundle. Recipients of a bundle call **you** first,
whatever broke. Decide in advance what you handle, what you pass on and where.

## Contents

1. [The parties](#the-parties)
2. [The support the project names](#the-support-the-project-names)
3. [Drawing the line](#drawing-the-line)
4. [Routing problems](#routing-problems)
5. [A support statement](#a-support-statement)

## The parties

| Party | Supports |
|---|---|
| You, the bundler | the bundle: build, installer, configuration, your extensions, your site package, updates you deliver |
| 7x | Exponential, through partner support and paid support |
| The community | anyone, without obligation |
| Extension authors | their extensions, as they choose |
| Your upstream vendors | the operating system, the database, the panel |

## The support the project names

| Kind | Response promise |
|---|---|
| Community support | "None: 'its developers are not obligated to provide free support...'" |
| Partner support | response times by level |
| Paid support from 7x | agreed with 7x |

Source: [Support levels and response times](../partners/support-levels-and-response-times.md#three-kinds-of-support).
Partner levels are Student, Now, Starter, Basic, Professional, Silver, Gold and Platinum; the tickets and response
times per level are on [Partner plans and prices](../partners/partner-plans-and-prices.md#the-plans). "Not published:
whether response time means first answer or resolution, business or clock hours, the time zone": get it in writing
before you promise anything that depends on it.

## Drawing the line

| Problem | Yours | Pass on |
|---|---|---|
| The bundle does not install or start | yes | |
| A setting your bundle made | yes | |
| A bug in Exponential you can reproduce on a plain installation | triage, workaround | report upstream ([Contributing fixes back](contributing-bundle-fixes-back.md)) |
| A security problem in Exponential | contain, inform | security@se7enx.com, privately (`SECURITY.md`) |
| A third-party extension | triage | its author |
| The operating system, the database server | as your terms say | its vendor |
| The recipient's own changes and extensions | as your terms say | |

Reproduce on a plain installation before reporting upstream: if it does not happen without your bundle's changes, it
is yours.

## Routing problems

- **Bugs:** the issue tracker of <https://github.com/se7enxweb/exponential>, or of the extension's repository. Include
  the version (`composer show se7enxweb/exponential`), PHP, the engine and the database, as `SECURITY.md` asks for
  security reports. Partners: [Filing bugs as a partner](../partners/filing-bugs-as-a-partner.md).
- **Security:** never in public; "You can expect a first answer within one business day" (`SECURITY.md`).
- **Questions:** forums <https://share.exponential.earth/forums>, Telegram <https://t.me/exponentialcms>.
- **Paid help:** <https://se7enx.com/contact>.

## A support statement

```text
Support for <Bundle> <version>
Supported by <your company>: installation, configuration, updates of <Bundle>, <your extensions>.
Contact: <address>, response <time> on <days>, <time zone>.
Exponential itself: we report reproducible problems to the Exponential project and deliver its fixes
in our next bundle release.
Not supported: <your exclusions>.
Supported versions: <Bundle> <versions> until <date>.
```

Publish it next to the bundle and in its documentation.

## Related pages

- [Updates and upgrade paths](updates-and-upgrade-paths.md)
- [Escalations and security issues](../partners/escalations-and-security-issues.md)
- [Support tiers for hosts](../hosting/business/support-tiers.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
