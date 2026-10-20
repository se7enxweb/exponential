---
title: Incidents
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Index of the incident runbooks for public-sector Exponential sites - the common frame, site down, database unreachable, defacement, account compromise, data breach framing, floods, broken deploys, mass content errors, stale caches, mail, disk and certificates, and the review afterwards.
keywords: incidents, runbooks, outage, site down, database, defacement, compromise, data breach, DDoS, rollback, content error, cache, mail, disk full, certificate, post-incident review, index
type: help
---
# Incidents

This folder holds runbooks for the bad days of a public website. Each one starts with what to check, uses commands
that exist in the installation, and adds what a public body needs beyond the technical fix: roles, evidence,
communication with the public, and a record. The hosting companies' runbooks
([Hosting runbooks](../../../hosting/runbooks/README.md)) go deeper on the server side and are linked where useful.

## Contents
1. [Pages](#pages)
2. [How to use them](#how-to-use-them)
3. [Related pages](#related-pages)

## Pages

| Page | Use it when |
|---|---|
| [Incident response basics](incident-response-basics.md) | any incident: roles, severity, the first fifteen minutes, evidence, communication |
| [Site down](site-down.md) | the site does not serve pages |
| [Database unreachable](database-unreachable.md) | visitors get "We'll be right back" |
| [Defacement](defacement.md) | the site shows content nobody authorized |
| [Suspected account compromise](suspected-account-compromise.md) | an account may be used by someone else |
| [Data breach notification framing](data-breach-notification-framing.md) | personal data may be involved |
| [DDoS and probe floods](ddos-and-probe-floods.md) | a flood of requests slows or stops the site |
| [Broken deploy rollback](broken-deploy-rollback.md) | errors started right after a deploy or update |
| [Mass content error](mass-content-error.md) | many pages went wrong at once |
| [Stale cache](stale-cache.md) | old content or old code keeps showing |
| [Mail problems](mail-problems.md) | mail does not arrive, or a test copy mailed real people |
| [Disk full](disk-full.md) | no space left on the server |
| [Certificate expiry](certificate-expiry.md) | browsers warn about the certificate |
| [Post-incident review](post-incident-review.md) | after the incident: timeline, causes, actions |

## How to use them

1. Read [Incident response basics](incident-response-basics.md) once before you need it, and agree the roles and
   severity scale with your team.
2. During an incident, open the matching runbook and work top to bottom.
3. Keep notes with times; the audit trail fills in much of the rest.
4. Close with a [post-incident review](post-incident-review.md).

Practice: run one runbook on a test copy each quarter (for example take the site into maintenance and bring it back,
or restore a backup), so the commands are familiar before they matter.

Up: [Running Exponential in the public sector](../README.md). The section landing page is
[Governments: start here](../../government-start-here.md).

## Related pages
- [Monitoring](../monitoring/README.md)
- [Backups and recovery](../backups-and-recovery/README.md)
- [When something goes wrong (help)](../../../when-something-goes-wrong.md)
- [Security hardening](../../../security-hardening.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
