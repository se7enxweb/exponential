---
title: Handover of an integration
audience: integrator, admin, project-manager
category: integration
order: 460
summary: Handing an integration over to the people who will operate it - the handover package (code, documentation, runbook, monitoring, credentials inventory), the runbook's sections, transferring credentials safely, training, and the checklist that ends the project.
keywords: handover, hand-over, runbook, operations, documentation, credentials inventory, secret transfer, training, knowledge transfer, ownership, acceptance, end of project
type: help
---
# Handover of an integration

This page is for integrators finishing a project and for the customer's operators taking it over. An integration
that only its builder understands is a risk for the customer; the handover turns it into something the operators can
run, change and, if needed, give to another provider. Customers may move to another partner at any time
([Implementation project playbook](../partners/implementation-project-playbook.md#partner-specific-checks-per-phase));
a good handover makes that possible.

## Contents

1. [The handover package](#the-handover-package)
2. [The runbook](#the-runbook)
3. [Credentials](#credentials)
4. [Training](#training)
5. [The handover checklist](#the-handover-checklist)

## The handover package

| Item | Contents |
|---|---|
| Code | The integration extension(s) in the customer's repository, with history; the released version installed |
| Interface specifications | One per flow, updated to what was built |
| Settings inventory | Every setting the integration reads, its value per environment, and where it is set (`settings/override`, siteaccess, extension) |
| Credentials inventory | Every credential: what it is for, which account, where it is stored, when it expires, who may rotate it (never the secrets themselves in the document) |
| Service accounts and roles | Accounts, groups, roles and their policies |
| Schedules | Crontab lines, cronjob parts, queue workers |
| Monitoring | Checks, thresholds, alert recipients |
| Runbook | See below |
| Tests | How to run them; the acceptance cases |
| Open items | Known issues, planned changes, the next key rotation date |

## The runbook

The runbook answers the operator's questions at 3 o'clock in the morning. One section per flow, and in each:

```text
Flow: ERP products into Exponential
1  What it does (one paragraph) and who owns the data
2  Schedule: 02:15 full, hourly delta 07:00-19:00 (cronjob parts myintegration_import, myintegration_delta)
3  How to see that it ran: var/log/myintegration.log summary line; Setup > Cronjobs
4  Alerts and what they mean
   4.1 "No run since ..."          -> check crontab, Setup > Cronjobs (paused?), the part's output
   4.2 "Stopped: N rows for M"     -> the ERP file is truncated; ask the ERP team; do not force the run
   4.3 "failed > 0"                -> exp:logs --search=erp-product --file=var/log/error.log
5  How to run it by hand: php bin/php/console ext:myintegration:importproducts --file=... --dry-run -s <siteaccess>
6  How to pause and resume: Setup > Cronjobs, Pause on the part's card
7  How to rotate the credential: steps, and where it is stored
8  Contacts: ERP team, integrator support, escalation
```

## Credentials

- Transfer secrets through a channel agreed with the customer (their password manager or secret store), never by mail
  or chat.
- After handover, **rotate** every credential the integrator's staff knew: new API keys, new passwords, new signing
  secrets; revoke the old ones and check the audit for their use.
- Remove integrator staff accounts from the administration and the servers, unless a maintenance agreement keeps them;
  then keep them personal, never shared.

## Training

| Audience | Content | Length |
|---|---|---|
| Operators | Runbook walk-through, a simulated failure per flow, pausing and resuming, logs and audit | Half a day |
| Editors | Which fields are imported and must not be edited; how to report data problems | One hour |
| Developers (if the customer has them) | The extension's structure, tests, deployment, the upgrade procedure | Half a day |

## The handover checklist

- [ ] Code in the customer's repository; the installed version matches a release tag.
- [ ] Interface specifications, settings and credentials inventories updated and handed over.
- [ ] Runbook reviewed by an operator who did not build the integration.
- [ ] Monitoring alerts go to the customer's operators.
- [ ] Credentials rotated after transfer; integrator access removed or under a maintenance agreement.
- [ ] Operators trained; a simulated failure resolved by them.
- [ ] Acceptance signed; open items listed with owners.

## Related pages

- [Monitoring integrations](monitoring-integrations.md)
- [Handing the site over to editors](../creators/website-creators/handing-over-to-editors.md)
- [Integration project playbook](integration-project-playbook.md)
- [Protecting customer data](../partners/protecting-customer-data.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
