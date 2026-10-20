---
title: "Where the facts live: sources every advocate cites"
audience: advocate
category: advocacy
order: 9
summary: A lookup table of the sources behind every common claim about Exponential (license, releases, PHP, databases, security, languages, accessibility, features, history, community), with how to check each one yourself.
keywords: sources, facts, citations, proof, README, SECURITY.md, CONTRIBUTING.md, COPYRIGHT.md, changelog, history, ledger, feature pages, verify, lookup
type: help
---
# Where the facts live: sources every advocate cites

This page is for advocates who need a source fast. Before you say something in public, find its line here, open the
source and check that it still says what you remember. Sources change with each release; your memory does not.

## Contents

1. [The repository files](#the-repository-files)
2. [Claims and their sources](#claims-and-their-sources)
3. [Check it yourself](#check-it-yourself)
4. [Public websites the documentation cites](#public-websites-the-documentation-cites)
5. [When there is no source](#when-there-is-no-source)

## The repository files

| File | What it proves |
|---|---|
| [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md) | What Exponential is, its features, engines, community places, sponsor options |
| [LICENSE](https://github.com/se7enxweb/exponential/blob/main/LICENSE) and [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md) | The GPL license and the copyright holders |
| [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) | Supported versions, private reporting, "a first answer within one business day" |
| [CONTRIBUTING.md](https://github.com/se7enxweb/exponential/blob/main/CONTRIBUTING.md) | How to contribute, commit messages, licensing of contributions |
| [composer.json](https://github.com/se7enxweb/exponential/blob/main/composer.json) | PHP versions allowed, the packages required |
| [The license of this documentation](../../README-LICENSE.md) | The GNU FDL 1.3 notice of the documentation |

## Claims and their sources

| Claim area | Source page in this help |
|---|---|
| What Exponential is | [Exponential 6, the kernel](../who-is-the-exponential-foundation.md#exponential-6-the-kernel) |
| Who leads it, who operates it | [The Foundation and 7x](../who-is-the-exponential-foundation.md#the-foundation-and-7x) |
| The product family | [The product family at a glance](../who-is-the-exponential-foundation.md#the-product-family-at-a-glance) |
| Releases and dates | [Who is 7x?, The 6.0.x releases](../who-is-7x.md#the-60x-releases), [6.0.15 changelog](../../../../changelogs/6.0/6.0.15.md) |
| PHP versions | [PHP versions](../who-is-the-exponential-foundation.md#php-versions) |
| Security process | [Security fixes](../who-is-the-exponential-foundation.md#security-fixes), [Security hardening](../security-hardening.md) |
| Content Security Policy | [Content Security Policy](../../features/6.0/content-security-policy.md) |
| Audit trail | [The audit trail](../../features/6.0/audit-trail.md) |
| Two-step sign-in | [Two-step sign-in](../../guides/two-factor-login.md) |
| Databases | [Database support](../database-support.md) |
| Operating systems, web servers | [Operating system support](../os-support.md) |
| Languages and right to left | [Languages in Exponential](../../languages.md), [Right to left](../../features/6.0/right-to-left.md) |
| Accessibility of the administration | [The admin4 design](../../features/6.0/admin4-design.md), [Styling the administration](../../bc/6.0/admin4-styling.md) |
| Velocity | [Velocity](../../features/6.0/velocity-persistent-worker-server.md) |
| Exponential Layouts | [The layouts editor](../layouts-editor.md) |
| Exponential Intelligence | [expai](../../features/6.0/extensions/expai.md) |
| The help | [The help system](../../features/6.0/help-system.md) |
| Extensions and the RAD tools | [Extensions](../../guides/extensions.md), [RAD tools](../../features/6.0/rad-tools.md) |
| Install in one command | [Installing Exponential in one command](../../features/6.0/install-in-one-command.md) |
| Upgrades and migrations | [Upgrading](../../install/11-upgrading.md), [Migrating from 4.x](../../install/14-migrating-from-4x.md) |
| History, month by month | [The history](../../history/README.md) |
| Community places | [Community places](../who-is-the-exponential-foundation.md#community-places) |
| Sponsoring | [How to support or join](../who-is-the-exponential-foundation.md#how-to-support-or-join) |
| Pull requests are merged | [How the project grows](../who-is-the-exponential-foundation.md#how-the-project-grows) |
| Every term | [The glossary](../../glossary.md) |

For business framing of the same facts: [Feature highlights mapped to customer needs](../business/feature-highlights-for-customers.md#the-map-need-feature-where-to-show-it-proof).

## Check it yourself

Some facts can be checked with a command on a clone of the repository. Offer these to skeptical listeners:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git tag -l 'v6.0.*' --sort=version:refname   # every 6.0.x release, in the right order
git log -5 --format='%ad %s'                  # the most recent changes
git log --reverse --format='%ad' | head -1    # the oldest commit
cat COPYRIGHT.md                              # the copyright holders
```

> **Note**
> Always sort versions with `--sort=version:refname`. A plain sort by name puts `0.1.10` before `0.1.9`
> ([Releases and version numbers](../who-is-the-exponential-foundation.md#releases-and-version-numbers)).

## Public websites the documentation cites

| Site | What it is |
|---|---|
| [exponential.earth](https://exponential.earth/) | The open source project's website |
| [foundation.exponential.earth](https://foundation.exponential.earth/) | The Foundation |
| [se7enx.com](https://se7enx.com/) | 7x |
| [doc.exponential.earth](https://doc.exponential.earth/) | "The Official Documentation For Exponential 6 CMS" |
| [share.exponential.earth](https://share.exponential.earth/) | The community portal and forums |
| [ezpedia.exponential.earth](https://ezpedia.exponential.earth/) | The community encyclopedia |
| [projects.exponential.earth](https://projects.exponential.earth/) | Community projects and extensions |

## When there is no source

Say so: "The documentation does not say that. I will look, and if I cannot find it, I will ask in the forum." Then
either find the source or tell the person you could not. Do not fill the gap with a guess
([Claims you must not make](claims-you-must-not-make.md)).

If you think the documentation **should** say it, propose a change ([Improving the help](improving-the-help.md)) or
open an issue on the [community tracker](https://github.com/se7enxweb/exponential-community/issues).

## Related pages

- [Honesty and accuracy](honesty-and-accuracy.md)
- [Citing sources and licensing your writing](citing-sources-and-licensing-your-writing.md)
- [One-page fact sheet](one-page-fact-sheet.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
