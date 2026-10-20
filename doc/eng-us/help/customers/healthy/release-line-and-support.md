---
title: Release line and support
audience: customer, businesses, project-manager
category: customer-success
order: 4
summary: Which Exponential versions are maintained, how to find the version your site runs, how security fixes reach you, which PHP versions matter, and what to put on your calendar as an owner.
keywords: release line, supported versions, 6.0.x, SECURITY.md, PHP version, version number, end of life, changelog
type: help
---
# Release line and support

This page answers the owner's three questions about versions: which version do we run, is it still maintained, and
what must we do to stay maintained. The full policy for businesses is in
[Release line and support policy](../../businesses/reliability/release-line-and-support-policy.md); this page is the
short version with the things to check.

## What is maintained

From `SECURITY.md` at the root of the installation:

| Version | Supported |
|---|---|
| 6.0.x, the current line (`main`, and the next tag cut from it) | Yes |
| 6.0.x, released tags (`v6.0.0` to `v6.0.14`) | Fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | No |

In plain words: **be on the newest 6.0.x release.** An older 6.0.x is not cut off, but a fix only reaches you when
you upgrade within the line. A site on 5.x or older needs an upgrade project; [Upgrading](../../../guides/upgrading.md)
has a part for each starting version.

## Find the version your site runs

- In the administration: **Setup > System information** names the version
  ([System information](../../../guides/system-information.md)).
- **Info > About** lists the version of Exponential and of every extension
  ([About and Copyright pages](../../../features/6.0/about-and-package-pages.md)).
- Ask your provider: a provider who keeps an inventory of customer sites can answer at once
  ([Keeping customers current](../../partners/keeping-customers-current.md)).

Write the version down with the date. Compare it with the newest release in the changelog
([Changelog 6.0.15](../../../../changelogs/6.0/6.0.15.md)).

## How security fixes reach you

`SECURITY.md` sets the process: a problem is reported privately, an accepted issue "is fixed on `main` and released
with the next 6.0.x version", and "a published tag is never changed". So the plan is simple: **upgrade within the
line, on a regular rhythm, and quickly when a release carries a security fix.** Agree with your provider how quickly
that is (see [Contracts and SLAs](../partners-and-hosts/contracts-and-slas.md)).

## PHP matters too

Exponential runs on PHP, and PHP versions have their own support periods. From the release line page for businesses:
PHP 7.4 only up to 6.0.7; PHP 8.0 is the oldest supported; 8.1 to 8.5 are supported, and Exponential Velocity needs
8.1 or later. **Setup > System information** warns when the security support of the running PHP branch has ended
([System information](../../../guides/system-information.md)). Ask your host which PHP versions they offer and when
they retire one.

## Extensions have versions of their own

Each extension carries its own version, shown on **Setup > Extensions** and **Info > About**
([Extension list](../../../features/6.0/extension-list-and-downloads.md)). An extension that is no longer maintained
can hold back an upgrade. Ask your provider once a year which extensions the site uses and whether each one is
still maintained.

## What the project does not publish

No release calendar, no end-of-life date for 6.0, and no service level agreement
([Release line and support policy](../../businesses/reliability/release-line-and-support-policy.md)). The only
response commitment in the project's own files is a first answer to a private security report "within one business
day" (`SECURITY.md`). Any other promise, such as how fast your site is upgraded, comes from your contract with your
provider or with 7x.

## On your calendar

- [ ] Each quarter: compare the site's version with the newest 6.0.x release.
- [ ] When a release carries security fixes: ask your provider for the date it will be applied.
- [ ] Each year: ask which PHP version the site runs and until when it is supported.
- [ ] Each year: review the list of extensions and their maintainers.

## Related pages

- [How updates work](how-updates-work.md)
- [Upgrades with your provider](upgrades-with-your-provider.md)
- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- [Who is 7x?](../../who-is-7x.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
