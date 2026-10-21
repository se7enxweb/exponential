---
title: Standards and tooling in the ecosystem
audience: ecosystem, developer, partner
category: ecosystem
order: 36
summary: The standards and tools shared across the Exponential ecosystem - PSR and the project's coding standards, naming prefixes, commit message prefixes, the file manifest, Git hooks, CI checks, accessibility, security and documentation standards.
keywords: standards, PSR, coding standards, naming prefixes, exp, sevenx, commit messages, filelist.md5, make hooks, CI, PHPStan, PHPUnit, accessibility, security, documentation
type: help
---
# Standards and tooling in the ecosystem

Packages that follow the same standards work together more easily and are easier to review.

## Contents

1. [Code standards](#code-standards)
2. [Repository conventions](#repository-conventions)
3. [Checks](#checks)
4. [Other standards](#other-standards)

## Code standards

- "Follow the PSR standards and our coding style" (`CONTRIBUTING.md`; [Coding standards](../../../standards/coding-standards.md)).
- Name new code by the prefix rule: `sevenx` or `exp` for 7x code, `exp` for new kernel code; existing identifiers
  such as `eZContentObject` are never renamed ([Naming prefixes](../../../standards/naming-prefixes-sevenx-and-exp.md)).
- PHP and Composer conventions: [PHP and Composer](../../../standards/php-and-composer.md).

## Repository conventions

| Convention | Source |
|---|---|
| Commit messages start with `Added:`, `Updated:`, `Fixed:`, `Removed:` or `Renamed:` | `CONTRIBUTING.md` |
| Every changed file updates its line in `share/filelist.md5` (`make manifest-fix`) | `CONTRIBUTING.md` |
| `make hooks` installs pre-commit and commit-msg hooks | `CONTRIBUTING.md` |
| Squash and rebase pull requests; never rewrite `main` | `CONTRIBUTING.md` |
| Published tags never change | `SECURITY.md` |

Source control and releases: [Source control and releases](../../../standards/source-control-and-releases.md).

## Checks

The CI runs the test suite on PHP 8.1 to 8.5, checks that PHP 8.0 boots, and runs static analysis and style checks
([Quality checks](../../../features/6.0/quality-checks.md), [CI](../../../bc/6.0/ci.md),
[Testing and CI](../../../standards/testing-and-ci.md)).

## Other standards

| Area | Page |
|---|---|
| Accessibility | [Accessibility](../../../standards/accessibility.md) |
| Security | [Security](../../../standards/security.md) |
| Privacy | [Privacy](../../../standards/privacy.md) |
| Internationalization | [Internationalization](../../../standards/internationalization.md) |
| APIs and protocols | [APIs and protocols](../../../standards/apis-and-protocols.md) |
| Documentation and publishing | [Documentation and publishing](../../../standards/documentation-and-publishing.md) |
| The administration's styling | [Styling the administration](../../../bc/6.0/admin4-styling.md) |

## Related pages

- [The PHP stack](README.md)
- [Standards](../../../standards/README.md)
- [Contributing code](../../community/contributing/contributing-code.md)
- [Commit messages](../../extension-development/part-5-extend-fork-contribute/commit-messages.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
