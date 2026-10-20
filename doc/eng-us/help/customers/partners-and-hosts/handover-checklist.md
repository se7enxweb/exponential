---
title: Handover checklist
audience: customer, partner, hosting
category: customer-success
order: 9
summary: What the owner of an Exponential site must receive when a partner or host hands the site over - at the end of a build, at the end of a contract, or to a new provider - access, code, data, settings, records and knowledge, with checks to confirm each item.
keywords: handover, checklist, offboarding, export, access, credentials, code, database, documentation, new provider, exit
type: help
---
# Handover checklist

A handover happens when a build ends and the site goes into your care, when a contract ends, or when one provider
replaces another. This checklist lists what you must receive, from the owner's side. Hosts work through their own
list ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)), and partners through the handover
phase of their playbook ([Implementation project playbook](../../partners/implementation-project-playbook.md)).

## Agree the plan first

- [ ] The handover date, and who does what until then.
- [ ] Whether the old provider keeps the site running until the new one has confirmed it works.
- [ ] How private items (settings with passwords) are transferred: never by plain e-mail.

## Access

- [ ] Administrator access for your own people, with personal accounts
      ([Administrator accounts and access](../healthy/admin-accounts-and-access.md)).
- [ ] The hosting and panel accounts, or the confirmation that they close on the agreed date.
- [ ] The domain and DNS, in your name, or transferred on the agreed date.
- [ ] The list of accounts the old provider held in your administration, so they can be disabled; a well-run host
      will "tell the customer which accounts were yours" ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)).
- [ ] The break-glass procedure, written down ([Change the administrator password](../../change-the-admin-password.md)).

## A complete export

A complete site is the database and the files ([Customer offboarding](../../hosting/checklists/customer-offboarding.md)):

- [ ] A database dump made with the engine's own tool.
- [ ] `var/storage` (and each site's storage, where separate).
- [ ] `settings/override` and `settings/siteaccess`, transferred privately; have the database password changed at
      the new host.
- [ ] Custom extensions and designs, or the addresses of their Git repositories.
- [ ] The audit trail's signing keys (`settings/override/audit.ini.append.php`) and, if you want the history, the
      audit records ([Backups that restore](../../businesses/reliability/backups-that-restore.md)).
- [ ] Taken with maintenance mode on, so database and files agree.

## Records

- [ ] The versions: Exponential, PHP, database engine and version, engine, extensions.
- [ ] A handover document: content model, roles, settings, cronjobs, backups, monitoring, known issues
      ([Project templates, handover document](../../leadership/project-templates.md)).
- [ ] The cron entries and what they run.
- [ ] Known issues and their upstream links, if any.
- [ ] Open change requests and their state ([Change requests](change-requests.md)).

## Knowledge

- [ ] Your editors were trained and know where the help is: the help dashboard of their administration.
- [ ] Your team knows how to report a problem and what to include ([Reporting problems](../healthy/reporting-problems.md)).
- [ ] The new provider, if any, had a walk-through with the old one.

## Checks after the handover

| Check | Good result |
|---|---|
| Restore the export on a test machine | It runs ([Restore test](../healthy/restore-test.md)) |
| **Setup > System information** on the new site | The expected version; no failed health check |
| **Setup > Upgrade check** | No unexpected modified files ([The upgrade check](../../../guides/upgrade-check.md)) |
| Your test list | Every item passes ([Upgrade day checklist](../healthy/upgrade-day-checklist.md)) |

## At the very end

- [ ] The old provider's accounts in your administration are disabled.
- [ ] The old provider deletes your data according to the contract and the law, and confirms it in writing
      (not legal advice; [Data retention and deletion](../../businesses/security/data-retention-and-deletion.md)).
- [ ] [Who does what](who-does-what.md) and your [escalation contacts](escalation-contacts-template.md) are updated.

> **Note**
> Exponential is under the GPL. A partner who builds a site and hands the code to you distributes it to you, and you
> receive the GPL rights, including the source ([Contracts and SLA basics](../../businesses/choosing/contracts-and-slas-basics.md)).
> Ownership of designs and content made for you is a contract question: agree it in writing.

## Related pages

- [Taking over a site](taking-over-a-site.md)
- [Choosing a host](choosing-a-host.md)
- [Moving a site between hosts](../../hosting/migrations/moving-a-site-between-hosts.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
