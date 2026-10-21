---
title: Onboarding customers
audience: reseller, partner, hosting
category: reselling
order: 360
summary: How a reseller onboards a new customer for an Exponential-based offer - the kickoff, collecting facts, setting up or taking over the site, accounts and roles, first backup and restore test, editor training, and the first 30 days - with an onboarding checklist and a welcome letter template.
keywords: onboarding, new customer, kickoff, welcome, take over, setup, accounts, roles, backup, restore test, training, first 30 days, inventory, checklist
type: help
---
# Onboarding customers

This page is for account managers and technical staff at resellers. Onboarding sets the tone of the whole
relationship: a customer who knows who to call, what is backed up and what happens next opens fewer anxious tickets.

## Contents

1. [Two starting points](#two-starting-points)
2. [The onboarding steps](#the-onboarding-steps)
3. [The first 30 days](#the-first-30-days)
4. [Welcome letter template](#welcome-letter-template)
5. [Onboarding checklist](#onboarding-checklist)

## Two starting points

| Start | What happens | Page |
|---|---|---|
| A new site | Install from a site package (`sevenx_multisite_clean` or `sevenx_site_clean` for a real site), configure, design, fill | [Site packages for hosts](../../hosting/provisioning/site-packages.md) |
| An existing site you take over | Receive the export, check it, move it, verify it | [Taking over a site](../../customers/partners-and-hosts/taking-over-a-site.md), [Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md) |

## The onboarding steps

1. **Kickoff call.** Introduce the contacts on both sides, the support channel and hours, the update rhythm.
2. **Collect the facts** into your site inventory: Exponential version, PHP, database, engine, extensions with
   versions, siteaccesses, host names ([Keeping customers current](keeping-customers-current.md)).
3. **Set up or take over** the site. For a takeover run **Setup > Upgrade check**, which compares the files with the
   release and the database with its schema and changes nothing ([The upgrade check](../../../guides/upgrade-check.md)).
4. **Accounts.** The customer gets their own administrator account; your staff get personal accounts, never a shared
   one ([Roles and policies](../../../guides/roles-and-policies.md)). Change any default password at once
   ([First sign-in and default passwords](../../hosting/security/first-sign-in-and-default-passwords.md)).
5. **Hardening.** Apply your standard hardening ([Security hardening](../../security-hardening.md)).
6. **First backup and a restore test.** A backup is the database and `var/storage`, `settings/override` and
   `settings/siteaccess`; restore it once to prove it works
   ([Restore and restore tests](../../hosting/operations/restore-and-restore-tests.md)).
7. **Monitoring** of the front page and the administration ([Health checks and uptime](../../hosting/operations/health-checks-and-uptime.md)).
8. **Editor training** ([Training reseller](../models/training-reseller.md)).
9. **Hand over the customer documentation** ([Documentation you can give your customers](documentation-for-your-customers.md)).

## The first 30 days

| Week | Do |
|---|---|
| 1 | Kickoff, facts, accounts, hardening, first backup |
| 2 | Restore test, monitoring, editor training |
| 3 | Check the logs (**Setup > Log files**) and fix what you find ([Log viewer](../../../features/6.0/log-viewer.md)) |
| 4 | First report to the customer: what was done, what is planned, open questions |

The customer's side of the same month is on [First 30 days as owner](../../customers/first-30-days-as-owner.md); send
them the link.

## Welcome letter template

```text
Subject: Welcome to <Company>: your Exponential site <example.com>

Your contacts: <name, role>, <name, role>
Support: <channel>, <days and hours, time zone>; urgent (site down): <phone, role>
What we do for you: <tier summary>
Updates: we apply Exponential releases <rhythm>; we announce windows <n> days ahead.
Backups: <frequency>, kept <days>; last restore test: <date>
Your accounts: you have your own administrator account; please do not share it.
Your documentation: <link to the customer manual>
Next steps: <training date>, <first report date>
```

## Onboarding checklist

- [ ] Contract signed, first invoice sent.
- [ ] Contacts and support channel agreed.
- [ ] Site inventory complete.
- [ ] Upgrade check run (takeover) or site installed from a clean package (new).
- [ ] Personal accounts; default passwords changed; customer has own administrator account.
- [ ] Hardening applied.
- [ ] Backup running; restore tested.
- [ ] Monitoring running.
- [ ] Editors trained; documentation handed over.
- [ ] First report sent after 30 days.

## Related pages

- [New customer site go-live](../../hosting/checklists/new-customer-site-go-live.md)
- [Onboarding checklist for staff](../../customers/day-to-day/onboarding-checklist.md)
- [Launch checklist for resellers](../checklists/launch-checklist.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
