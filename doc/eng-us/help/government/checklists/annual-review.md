---
title: Annual review
audience: government, admin, project-manager, hosting
category: public-sector
order: 90
summary: The yearly review of an Exponential site in a public body - versions and platform, security, accessibility, privacy, records, content, operations, training, contracts and the exit plan - on one page with links to the detailed checklists.
keywords: annual review, yearly review, governance, versions, platform, security review, accessibility statement, privacy review, restore test, training, contract review, exit plan, public sector
type: help
---
# Annual review

This checklist is for the site owner, the site administrator and the contract manager of a public body, once a year.
It collects the yearly tasks from [Keeping a site current over a multi-year contract](../procurement/lifecycle/keeping-a-site-current.md)
and points to the detailed checklists.

## Contents

1. [Software and platform](#software-and-platform)
2. [Security and privacy](#security-and-privacy)
3. [Accessibility](#accessibility)
4. [Content and records](#content-and-records)
5. [Operations](#operations)
6. [People and contracts](#people-and-contracts)

## Software and platform

- [ ] The site runs a supported 6.0.x version (`SECURITY.md`; [Supported versions](../procurement/lifecycle/supported-versions.md)); note the version from **Setup > System information**.
- [ ] Every extension is current; the about page lists versions ([About and package pages](../../../features/6.0/about-and-package-pages.md)).
- [ ] PHP, database and operating system receive security updates ([PHP and platform support](../procurement/lifecycle/php-and-platform-support.md)).
- [ ] **Setup > Upgrade check** shows no unexpected changed files ([The upgrade check](../../../guides/upgrade-check.md)).
- [ ] Next year's upgrade work is in the budget ([Planning upgrades in the budget](../procurement/lifecycle/planning-upgrades-in-the-budget.md)).

## Security and privacy

- [ ] [Security checklist](security-checklist.md) worked through; changes since last year noted.
- [ ] The four [Quarterly access reviews](quarterly-access-review.md) were done.
- [ ] [Privacy checklist](privacy-checklist.md) worked through; privacy notice current; third parties still the same.
- [ ] Retention: form submissions, logs and backups older than your rules are removed.

## Accessibility

- [ ] Key pages and templates retested ([Testing accessibility](../../creators/website-creators/testing-accessibility.md)).
- [ ] Accessibility statement reviewed and dated; feedback received last year answered ([Accessibility checklist](accessibility-checklist.md)).

## Content and records

- [ ] Content checks clean: links, relations, unreferenced objects, missing files ([Content checks](../../../features/6.0/content-checks.md), [Missing files](../../../guides/missing-files.md)).
- [ ] Outdated pages reviewed, updated or removed; owners per section confirmed.
- [ ] Records exported and transferred as your schedule requires ([Records checklist](records-checklist.md)).
- [ ] Missing translations followed up ([Multilingual checklist](multilingual-checklist.md)).

## Operations

- [ ] At least one restore test this year ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)); see [Backup readiness](backup-readiness.md).
- [ ] Incident contacts and runbooks current ([Incident readiness](incident-readiness.md)).
- [ ] Cronjobs, log rotation and disk space healthy ([Operating a site](../../../guides/operating-a-site.md)).
- [ ] Certificates renew automatically ([Certificate expiry](../../hosting/runbooks/certificate-expiry.md)).

## People and contracts

- [ ] New staff trained; training material updated after upgrades ([Training in procurement](../procurement/training/README.md)).
- [ ] Service levels met; supplier reports received.
- [ ] Contract end dates and notice periods noted ([The end of a contract](../procurement/lifecycle/end-of-a-contract.md)).
- [ ] Exit plan updated and an export test run ([Exit readiness](exit-readiness.md)).
- [ ] Documentation of the site current and held by your organization.

## Related pages

- [Government checklists](README.md)
- [Keeping a site current over a multi-year contract](../procurement/lifecycle/keeping-a-site-current.md)
- [Monthly operations (for hosts)](../../hosting/checklists/monthly-operations.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
