---
title: Working with the project and 7x
audience: hosting
category: hosting
order: 80
summary: How a hosting company works with the Exponential project day to day: following releases, reporting bugs and security issues, contributing fixes upstream, sponsoring, and buying services from 7x when needed.
keywords: project, 7x, Exponential Foundation, releases, GitHub, Packagist, issues, security report, SECURITY.md, contribute, pull request, sponsor, support contract, upstream
type: help
---
# Working with the project and 7x

This page is for system administrators and managers at a hosting company. It answers: how do you learn about new
releases, where do you report problems, how do you give fixes back, and how do you get paid help when your own team
is not enough?

## Contents

1. [Following releases](#following-releases)
2. [Reporting bugs](#reporting-bugs)
3. [Reporting security issues](#reporting-security-issues)
4. [Contributing fixes upstream](#contributing-fixes-upstream)
5. [Sponsoring](#sponsoring)
6. [Buying services](#buying-services)

## Following releases

| Where | What you see |
|---|---|
| The kernel repository, `github.com/se7enxweb/exponential` | tags, release notes, issues, pull requests |
| Packagist, `se7enxweb/exponential` | the Composer versions |
| The changelogs in `doc/changelogs/` | what each release changed, also in this help |
| Each extension's repository under `github.com/se7enxweb` | its own releases and changelog |

Rules that matter to a host, from `SECURITY.md` and the release rules:

- **A published version never changes.** "A published tag is never changed: a fix for a problem in `v6.0.14` or an
  earlier tag reaches you as the next release, not as a corrected copy of the old one" (`SECURITY.md`). You can
  pin versions safely.
- **Only the 6.0 line is maintained.** 5.x, 4.x, 3.x and older are not supported (`SECURITY.md`); customers on
  those need a migration ([Migrating customers](../migrations/README.md)).
- **Version numbers count on in the last position.** After `.9` comes `.10`; sort versions with
  `git tag -l --sort=version:refname` or `sort -V`, not by name
  ([Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md#releases-and-version-numbers)).

```bash
# list the newest kernel tags in version order
git ls-remote --tags https://github.com/se7enxweb/exponential.git | awk '{print $2}' | sort -V | tail -5
```

## Reporting bugs

Follow [When something goes wrong](../../when-something-goes-wrong.md): it explains which tracker fits (software,
project or community) and what a good report contains. For a host, include:

- the version and PHP version;
- the engine (Velocity, FrankenPHP, Apache or nginx with PHP-FPM) and the database;
- the steps to reproduce on a clean installation, if you can;
- log lines with secrets and personal data removed.

## Reporting security issues

Privately, never in a public issue. `SECURITY.md` in the repository gives the address and says: "You can expect a
first answer within one business day". Include the version, the PHP version, the engine and database, what an
attacker can do and the steps to reproduce, and "leave out passwords, secrets and personal data". Details:
[Security updates and disclosure](../security/security-updates-and-disclosure.md).

## Contributing fixes upstream

A fix you keep only on your servers must be re-applied at every update. A fix merged upstream is maintained by the
project. The project's practice: every pull request is merged, and contributors' commits are kept
([Contributors and pull requests](../../who-is-the-exponential-foundation.md#contributors-and-pull-requests)).

1. Fork the repository and create a branch ([Develop with Git](../../develop-with-git.md)).
2. Make the fix, with a test where possible.
3. Open a pull request that explains the problem and how you verified the fix.
4. Code is contributed under the GPL v2 or later (`CONTRIBUTING.md`).

> **Tip**
> Hosting experience is valuable documentation too. A correction to one of these hosting pages is a pull request
> like any other; every help page has an "Edit on GitHub" link.

## Sponsoring

Both 7x and the Foundation accept sponsorship; the links are on
[How to support or join](../../who-is-the-exponential-foundation.md#how-to-support-or-join). A host that earns money
with Exponential has a direct interest in a well-funded project: new features, documentation, extensions and
security work are what keep its customers on the platform.

## Buying services

7x sells "support and maintenance contracts for existing sites", migrations, development and training; "Prices and
terms are on the 7x website, not here" ([Who is 7x?](../../who-is-7x.md#services-and-hosting)). Other companies
may offer the same services. Typical reasons for a host to buy help:

- a customer migration from an old major version;
- a performance problem you cannot reproduce;
- a custom extension your team cannot maintain.

## Related pages

- [Partner programs for hosts](partner-programs.md)
- [Security updates and disclosure](../security/security-updates-and-disclosure.md)
- [Upgrading customer sites](../operations/upgrading-customer-sites.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
