---
title: Operating systems in the ecosystem
audience: ecosystem, admin, hosting
category: ecosystem
order: 34
summary: The operating systems the Exponential ecosystem runs on, by the four tiers of the operating system support page - tested here, known to work, expected to work, not possible today - and how users can move a system up a tier.
keywords: operating systems, Linux, AlmaLinux, RHEL, Debian, Ubuntu, Alpine, FreeBSD, macOS, Windows, Docker, Podman, tiers, support
type: help
---
# Operating systems in the ecosystem

Exponential runs on "almost any [operating system] that runs PHP 7.4 or later" (`README.md`, "Requirements"). The
help sorts them into four tiers ([Operating system support](../../os-support.md)).

## Contents

1. [The four tiers](#the-four-tiers)
2. [Examples by tier](#examples-by-tier)
3. [Moving a system up](#moving-a-system-up)

## The four tiers

| Tier | Meaning |
|---|---|
| 1. Tested here | "The project runs Exponential on it every day, or its CI runs the test suites on it on every push" |
| 2. Known to work | Covered by the documentation, packages or Velocity's own CI, or the same PHP on the same kernel as tier 1 |
| 3. Expected to work | A maintained PHP 8.x exists and nothing stands in the way; "a reasoned expectation, not a test result" |
| 4. Not possible today | No PHP 8 exists, or the hardware cannot run it |

## Examples by tier

| Tier | Systems (selection) |
|---|---|
| 1 | AlmaLinux 9 (the project's development server); Ubuntu in GitHub Actions (the CI) |
| 2 | RHEL 9 and 10 and its rebuilds, Debian 12 and 13, Ubuntu 22.04 and 24.04, Alpine, Plesk hosts, FreeBSD 14, DragonFly BSD, macOS, Windows, Docker and Podman containers |
| 3 and 4 | See the full page |

Velocity publishes `.deb` packages, a macOS binary and container images on `ghcr.io/se7enxweb/exponential-velocity`
([Operating system support](../../os-support.md)).

## Moving a system up

"If you run Exponential on a tier 3 system, please tell the project on the community tracker or the software tracker,
and the row moves up" ([Operating system support](../../os-support.md)). That is a contribution any user can make
([Filing a bug](../../community/contributing/filing-a-bug.md)).

## Related pages

- [The PHP stack](README.md)
- [Operating system support](../../os-support.md)
- [Operating systems for hosts](../../hosting/basics/operating-systems-for-hosts.md)
- [Velocity packages and binaries](../../../features/6.0/velocity-packages-and-binaries.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
