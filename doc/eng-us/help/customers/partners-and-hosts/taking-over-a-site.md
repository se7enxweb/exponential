---
title: Taking over a site
audience: customer, admin, partner
category: customer-success
order: 10
summary: What a new owner or site manager does in the first weeks after inheriting an Exponential site - find out what it runs, secure access, confirm backups, read the history, map the people, and decide what to fix first.
keywords: take over, inherit a site, new owner, new site manager, first weeks, inventory, access, backups, audit, version, extensions
type: help
---
# Taking over a site

You may inherit a site: a new job, a merger, a departed colleague, or a provider who left without much notice. This
page is the order of work for the first weeks, from the owner's side. If a proper handover is still possible, use
the [Handover checklist](handover-checklist.md) first.

## Week one: access and safety

1. **Get administrator access in your own name**, with two-step sign-in
   ([Administrator accounts and access](../healthy/admin-accounts-and-access.md)).
2. **List who else has administrator rights**, under **Users** and the Administrator role's assignments, and in
   **Users > Check user accounts** ([Check user accounts](../../../features/6.0/user-hygiene.md)). Disable accounts of
   people who left.
3. **Find out where the site is hosted**, who holds the hosting account and the domain, and whether you can reach
   them.
4. **Confirm a backup exists** of database and files, from the last few days, and that you can get a copy
   ([Backups](../healthy/backups.md)).

## Week two: what does it run?

| Find out | Where |
|---|---|
| Exponential version | **Setup > System information** ([System information](../../../guides/system-information.md)) |
| PHP, database, engine | Same page |
| Extensions and their versions | **Setup > Extensions** and **Info > About** |
| Whether files were changed from the release | **Setup > Upgrade check** ([The upgrade check](../../../guides/upgrade-check.md)) |
| Health warnings | The health checks at the top of **Setup > System information** |
| Scheduled jobs | **Setup > Cronjobs** |

Compare the version with the maintained line ([Release line and support](../healthy/release-line-and-support.md)).
A site on 5.x or older needs an upgrade project ([Upgrading](../../../guides/upgrading.md)).

> **Tip**
> **Copy report as text** on **Setup > System information** gives a summary for a support request without passwords,
> keys, tokens or full server paths ([System information](../../../guides/system-information.md)). It is a good first
> document to give a new partner.

## Week three: history and people

- **Read the recent history.** The audit trail records who did what: sign-ins, content changes, settings, deploys
  ([The audit trail](../../../features/6.0/audit-trail.md)). Ask your administrator for a summary of the last months.
- **Read the errors.** The error digest of **Setup > Log files** groups the distinct errors of the last 24 hours
  ([The log viewer](../../../features/6.0/log-viewer.md)).
- **Map the people**: editors, the host, any partner, and who did what. Fill in [Who does what](who-does-what.md) and
  the [escalation contacts](escalation-contacts-template.md).
- **Find the documents**: contracts, a handover document, the repository of the site's code.

## Week four: decide

Sort what you found into three lists:

| List | Examples |
|---|---|
| Now | Unknown administrator accounts, no working backup, an unsupported version with known security fixes waiting |
| This quarter | An upgrade to the newest 6.0.x, a restore test, two-step sign-in for all administrators |
| Later | Design changes, new features |

Then decide whether the current host and partner stay; [Choosing a partner](choosing-a-partner.md) and
[Choosing a host](choosing-a-host.md) help if not.

## If something looks wrong

Unknown administrator accounts, files changed without explanation or pages nobody published can be signs of a
compromise. Do not delete anything; call the host and treat it as severity 1
([When the site is down](../healthy/when-the-site-is-down.md), [Compromised site](../../hosting/runbooks/compromised-site.md)).

## Related pages

- [Security checklist for owners](../healthy/security-checklist-for-owners.md)
- [Reliability scorecard](../../businesses/reliability/reliability-scorecard.md)
- [Switching to Exponential](../../businesses/choosing/switching-to-exponential.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
