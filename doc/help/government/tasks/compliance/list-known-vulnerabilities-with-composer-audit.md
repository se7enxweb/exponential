---
title: List known vulnerabilities with composer audit
audience: government, admin, developer, hosting
category: public-sector
order: 400
summary: Step by step - check the Composer dependencies locked for your Exponential installation against known vulnerability advisories with composer audit and see newer releases with composer outdated, because the project's CI runs no dependency scan, and file the result.
keywords: composer audit, composer outdated, vulnerabilities, dependencies, advisories, composer.lock, patching, supply chain, OWASP A06, vulnerability management, public sector
type: help
---
# List known vulnerabilities with composer audit

For administrators, developers and hosting teams. The project's workflows run no dependency vulnerability scan
([Security standards: Known gaps](../../../../standards/security.md#known-gaps)); the hardening guide recommends
`composer audit` instead ([Security hardening: Outdated dependencies](../../../security-hardening.md#outdated-dependencies-and-the-supply-chain)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Acting on findings](#acting-on-findings)
4. [Related pages](#related-pages)

## Before you start

- Run on a **copy** of the installation with the same `composer.lock` as production (a staging server or your
  repository checkout), not on production.
- These commands only read; do not run `composer update` on production as part of this task.

## Steps

From the installation root of the copy:

```bash
composer audit                 # known vulnerabilities in the locked dependencies
composer outdated --direct     # newer releases of the packages you require
```

Save both outputs with the date, for example `var/tmp/composer-audit-<YYYY-MM-DD>.txt` on the copy, and file them.

## Acting on findings

| Finding | Do |
|---|---|
| an advisory for Exponential or one of its extensions | update to the fixed release in the copy, test, deploy ("Patching cadence") |
| an advisory for a library | check whether a newer Exponential release pins a fixed version; otherwise update the library in the copy and test |
| no fix released | assess the risk; mitigate (disable the feature, request-shield rule); ask the project privately if it concerns Exponential |
| only newer releases, no advisory | plan them in the normal update cycle |

After any update: file consistency check, smoke test, audit verify.

## Related pages

- [Patching cadence](../../security/patching-cadence.md)
- [Run the file consistency check](run-the-file-consistency-check.md)
- [Upgrading](../../../../guides/upgrading.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
