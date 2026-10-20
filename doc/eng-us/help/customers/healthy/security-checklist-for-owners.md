---
title: Security checklist for owners
audience: customer, businesses, admin
category: customer-success
order: 9
summary: A quarterly security checklist for the owner of an Exponential site, written as questions to ask and evidence to receive, with the page behind each item.
keywords: security checklist, quarterly review, owner, evidence, accounts, two-step sign-in, updates, audit trail, backups, headers
type: help
---
# Security checklist for owners

This checklist turns the technical checklists of [Security hardening](../../security-hardening.md) and the
installation book's [go-live checklist](../../../install/13-security-hardening.md) into questions an owner can ask
and evidence an owner can receive. Go through it once a quarter, and after every change of provider or of staff
with administrator rights.

## How to use it

Send the questions to the person responsible (your host, partner or administrator). Ask for a short answer and,
where it says "evidence", a screenshot or the output of the check. Keep the answers with the date; they show an
auditor, an insurer or a customer that security is looked after.

## Versions

- [ ] Which Exponential version runs, and is it the newest 6.0.x? Evidence: **Setup > System information**
      ([Release line and support](release-line-and-support.md)).
- [ ] Were the security fixes of the latest release applied, and when?
- [ ] Does the running PHP version still receive security support? **Setup > System information** warns when it
      does not ([System information](../../../guides/system-information.md)).
- [ ] Are all extensions maintained? Evidence: **Setup > Extensions** with versions.

## Accounts

- [ ] Does every person have their own account? Is the shared `admin` account retired as a sign-in
      ([Change the administrator password, account hygiene](../../change-the-admin-password.md))?
- [ ] Is two-step sign-in required for administrators ([Two-step sign-in](../../../guides/two-factor-login.md))?
- [ ] Who holds the Administrator role? Is the list as short as it can be?
- [ ] Were the accounts of people who left closed? Evidence: **Users > Check user accounts**
      ([Check user accounts](../../../features/6.0/user-hygiene.md)).
- [ ] Is there a written, tested way back in when no administrator can sign in (a break-glass procedure)?

## Settings and served files

- [ ] Do the five security headers appear on every page? Evidence: the `curl` check of
      [Security and audit](../../../guides/security-and-audit.md).
- [ ] Do private files (`.env`, `.git/config`, PHP files of extensions) answer 404 from outside? Same page.
- [ ] Is debug output off for everyone except, at most, one address with an expiry?
- [ ] Is the whole site on HTTPS?
- [ ] Is the administration restricted, for example on its own host name or behind a network rule
      ([Restricting the administration](../../hosting/security/restricting-the-administration.md))?

## Watching

- [ ] Is the audit trail intact? Evidence: the output of `exp:audit status` or `audit.php verify`
      ([The audit trail](../../../features/6.0/audit-trail.md)).
- [ ] Who receives the audit trail's alert mail (brute-force sign-ins, an administrator role granted, the audit
      switched off)? Is that a person who reads it?
- [ ] Are the logs read at least weekly ([Logs for owners](logs-for-owners.md))?

## Backups and recovery

- [ ] Are backups off the server, private and in several generations ([Backups](backups.md))?
- [ ] When was the last restore test, and what was its result ([Restore test](restore-test.md))?

## People and process

- [ ] Does the team know that a security problem is reported privately to security@se7enx.com, not in public
      ([Private security reports](private-security-reports.md))?
- [ ] Is there a named contact at the host for a suspected break-in, reachable outside office hours
      ([Escalation contacts template](../partners-and-hosts/escalation-contacts-template.md))?
- [ ] Is it agreed who receives the project's security announcements and who applies them?

## A record to keep

```text
Security review    date ____    done by ____
Version ____  newest 6.0.x ____  PHP ____ (supported until ____)
Administrators: ____ accounts, two-step sign-in: yes / no
Headers and private files checked: yes / no      Debug output off: yes / no
Audit trail intact: yes / no      Alert recipients: ____
Last restore test: ____           Result: ____
Open actions: ____ (who, by when)
```

## Related pages

- [Security basics](security-basics.md)
- [Security review (for hosts)](../../hosting/checklists/security-review.md)
- [Access reviews](../../businesses/security/access-reviews.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
