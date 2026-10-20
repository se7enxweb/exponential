---
title: Contributing extensions back upstream
audience: partner
category: partnership
order: 640
summary: How partners improve existing extensions together with their authors - finding the upstream, upgrading an extension to current Exponential and PHP, sending changes back, forks and rebranding done respectfully, and taking over maintenance of an abandoned extension.
keywords: extension, upstream, fork, rebrand, maintainer, pull request, upgrade, PHP compatibility, abandoned, original author, credit
type: help
---
# Contributing extensions back upstream

This page is for partner developers who use, fix or extend existing extensions. The aim is shared growth: improve the
extension where it lives, so everyone benefits and the original author is credited.

## 1. Find the upstream

- The extension's `ezinfo.php` and `extension.xml` name its info URL and license
  ([Extensions](../../guides/extensions.md)).
- The Composer package name (`vendor/name`) leads to its repository on Packagist.
- Many extensions in the ecosystem are maintained by se7enxweb as continuations of earlier work; others live with
  their original authors or on <https://projects.exponential.earth>.

## 2. Upgrading an extension to current Exponential

Most upgrades are small: PHP 8 compatibility, renamed settings, template adjustments. Work in a branch, keep each
change in its own commit, and describe why in the message (`Fixed: ...`, `Updated: ...`). Test on the PHP versions
the kernel supports (8.0 to 8.5 for 6.0.15, `doc/en/us/install/02-requirements.md`).

## 3. Send it back

| Upstream state | Do |
|---|---|
| Active | Open a pull request; follow the project's rules |
| Slow but alive | Open the pull request and an issue explaining it; wait a reasonable time |
| Abandoned (no answer for a long time) | Ask publicly whether you may take over; otherwise fork |

A change to the **core kernel** (`se7enxweb/exponential`) also needs a signed Contributor Licensing Agreement before
it is merged; 7x and the Exponential Foundation require it for every kernel code contribution, in any form. Your
own custom extensions, and fixes you keep for your customers, are not affected. See
[Copyright assignment (CLA)](../../../../../CONTRIBUTING.md#copyright-assignment-cla).

## 4. Forks and rebranding, done respectfully

- Keep the full Git history, the original copyright and the license.
- Credit the original author in the README ("based on [name] by [author]").
- Rename only if needed (for example to publish on Packagist under your vendor), and say where it came from.
- Offer your fork's improvements back if the original revives.

The project does the same: Exponential Layouts "brings the Netgen Layouts concept to the Exponential kernel"
(README), and the LoveStack improvements were rebuilt "with full credit to Mugo Web" (README).

## 5. Taking over maintenance

If you become maintainer:

- Publish releases by the rules: version in `ezinfo.php` and `extension.xml`, tag after the bump, never move a tag
  ([Extensions, "Release it"](../../guides/extensions.md#7-release-it)).
- Answer issues, or say in the README that you only maintain it for your own needs.
- Tell the community in the forums.

## Related pages

- [Extension and theme partners](extension-and-theme-partners.md)
- [Publishing an extension as a partner](publishing-an-extension.md)
- [Contributing code back](contributing-code-back.md)
- [Contribute extensions back](../contribute-extensions-back.md): publish an extension and what the GPL requires when you distribute it
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
