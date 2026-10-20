---
title: Upgrades with your provider
audience: customer, businesses, hosting
category: customer-success
order: 3
summary: How a site owner works with a host or partner who carries out the upgrades - what to agree, what to ask before and after each upgrade, what evidence to expect, and the decisions that stay with you.
keywords: upgrade, provider, host, partner, maintenance contract, evidence, upgrade check, changelog, staging, rollback
type: help
---
# Upgrades with your provider

Most site owners do not upgrade the site themselves: a host, an implementation partner or a freelance developer
does it. This page is about your side of that arrangement: what to agree once, what to ask each time, and what proof
to expect. The provider's own procedure is in [Upgrading customer sites](../../hosting/operations/upgrading-customer-sites.md)
and, for partners, [Keeping customers current](../../partners/keeping-customers-current.md).

## Agree once

Write these points into the contract or the support agreement (see
[Maintenance agreement template](../partners-and-hosts/maintenance-agreement-template.md)):

| Point | What to settle |
|---|---|
| Who upgrades | Your provider, your own developer, or both, with a clear line |
| How often | At least as often as new 6.0.x releases appear ([Release line and support](release-line-and-support.md)) |
| Security releases | Within what time after release a security fix is applied; ask for "a released fix is applied within a time" ([Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md)) |
| PHP updates | Who moves the site to a newer PHP version, and when ([PHP upgrade runbook](../../hosting/runbooks/php-upgrade.md)) |
| Staging | Whether each upgrade is tried on a copy first |
| Windows | When planned work may happen, and how far ahead you are told |
| Evidence | What you receive after each upgrade (below) |

## Before each upgrade, ask

- [ ] Which version does the site run now, and which version will it run after?
- [ ] What changes for editors, for visitors and for the team? (The provider reads the changelog and the behavior
      change notes; ask for a summary in plain words.)
- [ ] Was it tried on a copy of the site first?
- [ ] When is the window, and will visitors see a maintenance page?
- [ ] Is a backup of database **and** files taken right before? "Back up first, every time"
      ([Backup schedules and retention](../../hosting/operations/backup-schedules-and-retention.md)).
- [ ] What is the way back if something fails?
- [ ] Which of our own extensions or custom templates could be affected?

## After each upgrade, expect

| Evidence | Where it comes from |
|---|---|
| The new version number | **Setup > System information** ([System information](../../../guides/system-information.md)) |
| A clean upgrade check, or a list of files changed on purpose | **Setup > Upgrade check** ([The upgrade check](../../../guides/upgrade-check.md)) |
| The deploy recorded | The audit trail records deploys and cache clears ([The audit trail](../../../features/6.0/audit-trail.md)) |
| Your key pages checked | Front page, sign-in, a publish, a form, the shop if you have one |
| Anything left to do | Settings to review, extensions waiting for a release |

Files the provider changed on purpose are expected in the upgrade check; ask which ones they are and why
([Upgrade check and file consistency](../../businesses/reliability/upgrade-check-and-file-consistency.md)).

## Your team's part

- **Test what only you know.** The provider checks that the site works; your editors know whether the right things
  work. Keep a short list of five to ten pages and actions and check them after every upgrade
  ([Upgrade day checklist](upgrade-day-checklist.md)).
- **Report differences quickly**, with the page address, the time and the error reference if one is shown
  ([Reporting problems](reporting-problems.md)).
- **Keep the record.** Note the date, the old and new version and who did it. The audit trail keeps the technical
  record; your note keeps the business one.

## Warning signs

- Upgrades are "not needed" for a long time while new releases appear. Fixes only reach you by upgrading.
- The provider cannot tell you which version the site runs.
- Changes to the code of Exponential itself are made on the live site and not kept anywhere. The upgrade check
  will list them; a provider who keeps such changes should keep them in version control and contribute general fixes
  back ([Contributing code back](../../partners/contributing-code-back.md)).

## Related pages

- [How updates work](how-updates-work.md)
- [What to ask your host](what-to-ask-your-host.md)
- [What to ask your partner](what-to-ask-your-partner.md)
- [Budgeting for upgrades and maintenance](../../businesses/costs/budgeting-for-upgrades-and-maintenance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
