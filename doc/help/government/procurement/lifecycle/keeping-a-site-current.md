---
title: Keeping a site current over a multi-year contract
audience: government, project-manager, admin
category: public-sector
order: 80
summary: A yearly rhythm for a public body to keep an Exponential site current through a contract of several years - updates, platform, access, content checks, documentation and training - with the admin pages and commands that support each task.
keywords: lifecycle management, multi-year contract, maintenance rhythm, yearly plan, updates, health checks, system information, content checks, documentation, governance, public sector
type: help
---
# Keeping a site current over a multi-year contract

This page is for the contract manager and the site owner in a public body. Many public websites are built well and
then left alone until they become a risk. This page gives a rhythm that keeps the site current year after year, with
the tools Exponential provides for each task. The operational detail is in [Operating a site](../../../../guides/operating-a-site.md)
and [10. After installing: the operations handbook](../../../../install/10-after-installing.md).

## Contents

1. [The rhythm](#the-rhythm)
2. [Every month](#every-month)
3. [Every quarter](#every-quarter)
4. [Every year](#every-year)
5. [Who owns what](#who-owns-what)
6. [Reporting from the supplier](#reporting-from-the-supplier)

## The rhythm

| Period | Focus |
|---|---|
| Continuous | security fixes as they are released |
| Monthly | health, logs, backups |
| Quarterly | access review, content checks, routine update |
| Yearly | platform, documentation, training, exit plan, contract review |

## Every month

- **Health.** **Setup > System information** (`/setup/info`) shows health checks and what needs attention
  ([System information](../../../../guides/system-information.md)).
- **Logs.** **Setup > Log files** lists the logs with an error digest and log health
  ([Log viewer](../../../../features/6.0/log-viewer.md)).
- **Audit.** The audit dashboard (`/audit/dashboard`) shows security events and alerts
  ([Audit trail](../../../../features/6.0/audit-trail.md)).
- **Backups.** Check that they ran, and restore one now and then
  ([Restore and restore tests](../../../hosting/operations/restore-and-restore-tests.md)).
- **Cronjobs.** **Setup > Cronjobs** shows whether the scheduled parts ran ([Cronjobs](../../../../guides/cronjobs.md)).

Hosts have a matching list: [Monthly operations](../../../hosting/checklists/monthly-operations.md).

## Every quarter

- **Access review.** Who has which role; remove leavers. **Check user accounts** (`/user/hygiene`) finds accounts
  unused for a number of days ([Check user accounts](../../../../features/6.0/user-hygiene.md)); roles are under
  `/role/list` ([Roles and policies](../../../../guides/roles-and-policies.md)). The government checklists section
  has a quarterly access review checklist.
- **Content checks.** Broken links and relations, unreferenced objects, missing files
  ([Content checks](../../../../features/6.0/content-checks.md), [Missing files](../../../../guides/missing-files.md)).
- **Routine update** to the newest 6.0.x if not done already ([Planning upgrades in the budget](planning-upgrades-in-the-budget.md)).

## Every year

- **Platform.** PHP, database and operating system versions still supported? ([PHP and platform support](php-and-platform-support.md))
- **Security review.** Hardening settings still as agreed ([Security hardening](../../../security-hardening.md));
  hosts: [Security review](../../../hosting/checklists/security-review.md).
- **Accessibility.** Re-test key pages and templates after design changes
  ([Testing accessibility](../../../creators/website-creators/testing-accessibility.md)); update your accessibility
  statement.
- **Documentation.** Is the site documentation (content model, roles, settings, runbooks) current? Is it in your
  hands, not only the supplier's?
- **Training.** New staff trained, material updated after upgrades (the training part of this procurement section).
- **Exit plan.** Run an export test and update the plan (the exit-plans part of this procurement section).
- **Contract review.** Service levels met? Costs as planned?

The annual review checklist in the government checklists section puts these on one page.

## Who owns what

| Task | Typical owner |
|---|---|
| Security fixes and routine updates | the supplier or in-house site administrator |
| Platform | the host |
| Access review | the site owner, with the site administrator |
| Content checks | the editorial lead |
| Accessibility | the site owner, with the designer |
| Documentation and exit plan | the contract manager |

Write the owners into the contract or the service description. A task without an owner does not happen.

## Reporting from the supplier

Ask for a short periodic report, for example:

```text
Site report - <site> - <period>
Versions: Exponential <x>, extensions <list>, PHP <x>, database <x>   (from Setup > System information)
Updates applied: <release, date>        Security updates: <release, date applied>
Backups: <last successful>, restore test <date>
Incidents: <list>                        Open risks: <list>
Next period: <planned work>
```

## Related pages

- [The product lifecycle](README.md)
- [Operating a site](../../../../guides/operating-a-site.md)
- [Maintaining your website](../../../creators/website-creators/maintaining-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
