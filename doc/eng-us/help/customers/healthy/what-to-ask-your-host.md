---
title: What to ask your host
audience: customer, hosting, businesses
category: customer-success
order: 15
summary: The questions a site owner should put to the company that hosts an Exponential site - versions, updates, backups, restores, monitoring, security, support and exit - with what a good answer contains, for a yearly review or a new contract.
keywords: host, hosting, questions, review, backups, updates, PHP, monitoring, support, exit, evidence
type: help
---
# What to ask your host

Your host runs the server, PHP, the database and often the updates. This page lists the questions to ask, with what
a good answer looks like. Use it when you choose a host (see also
[Choosing a host](../partners-and-hosts/choosing-a-host.md)), and once a year for the host you have
([Reviewing your provider each year](../partners-and-hosts/reviewing-your-provider-each-year.md)). The questions
for a new host before you sign are also in [Choosing hosting](../../businesses/choosing/choosing-hosting.md).

## The platform

| Ask | A good answer |
|---|---|
| Which PHP version runs our site, and until when is it supported? | A version from 8.0 up (8.1 or later for Velocity), with a date; the README recommends "the latest version of PHP available on your OS" ([Release line and support policy](../../businesses/reliability/release-line-and-support-policy.md)) |
| Which database engine and version? | A named engine; the project lists them in an official order ([Database choice for reliability](../../businesses/reliability/database-choice-for-reliability.md)) |
| Which web server or engine serves the site? | Exponential Velocity, FrankenPHP, Apache or nginx with PHP-FPM, or another named one |
| Does cron run for our site, how often? | Yes, with the cronjob parts named; background work fails quietly without it ([Cronjobs and background jobs](../../businesses/reliability/cronjobs-and-background-jobs.md)) |
| Does outgoing mail work, with what sender? | Yes; forms, receipts, password resets and newsletters depend on it |
| Is every name on HTTPS, and who renews certificates? | Yes; renewal watched ([Certificate expiry runbook](../../hosting/runbooks/certificate-expiry.md)) |

## Updates

- Who applies Exponential updates, and within what time after a release with security fixes?
- Who applies PHP and operating system security updates?
- Is each update tried on a copy first? Is a backup taken right before?
- What do we receive after an update? (See [Upgrades with your provider](upgrades-with-your-provider.md).)

## Backups and restores

- What is backed up (database and files together), how often, kept how long, stored where?
- Are copies encrypted and off the server?
- When was the last restore test, how long did it take, and can we see the record ([Restore test](restore-test.md))?
- Who may ask for a restore, and how fast is it done?

## Monitoring and incidents

- What is checked from outside, and what on the server ([Monitoring](monitoring.md))?
- Who is alerted outside office hours?
- What is your first response time for "the site is down", and for a suspected break-in?
- How do you tell us about an incident, and how soon?

## Security

- Is the administration restricted (own host name, network rule or similar)
  ([Restricting the administration](../../hosting/security/restricting-the-administration.md))?
- Is each customer's site isolated from the others ([Multi-tenant isolation](../../hosting/security/multi-tenant-isolation.md))?
- Do you run a security review before go-live and regularly ([Security review](../../hosting/checklists/security-review.md))?
- Which of your staff hold accounts in our administration?

## Support

- Which channels, which hours, which time zone?
- Where is the line between hosting support and application support? The hosting support guide recommends writing
  that line into the terms ([Support tiers](../../hosting/business/support-tiers.md)).
- What do you need from us in a report? (A good host asks for page, time and error reference.)

## Exit

- In what format and how fast do we receive a complete export: database, files, settings, versions
  ([Customer offboarding](../../hosting/checklists/customer-offboarding.md))?
- Are the domain and DNS in our name?
- What is deleted after we leave, and do we receive confirmation?

## Keep the answers

Keep the answers in writing, dated, next to the contract. A good host answers most of these from its own records in
a few days; the hosting section's [monthly operations checklist](../../hosting/checklists/monthly-operations.md) shows
what such a host checks each month.

## Related pages

- [What to ask your partner](what-to-ask-your-partner.md)
- [Who does what](../partners-and-hosts/who-does-what.md)
- [Contracts and SLAs](../partners-and-hosts/contracts-and-slas.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
