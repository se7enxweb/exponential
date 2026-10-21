---
title: Backups, updates and security
audience: local-business, businesses, customer
category: business-solutions
order: 83
summary: What the owner of a local business must know about the safety of an Exponential site without being technical - backups and a yearly restore test, keeping Exponential updated, HTTPS, accounts and two-step sign-in, roles for staff, what to do when staff come and go, and when something goes wrong.
keywords: backups, restore, updates, security, HTTPS, accounts, passwords, two-factor, two-step sign-in, roles, staff, offboarding, incident, local business
type: help
---
# Backups, updates and security

This page is for the owner of a local business who is not technical but is responsible for the website. You do not
need to do these tasks yourself; you need to know they are done, and to ask for proof.

## Contents

1. [Backups](#backups)
2. [Updates](#updates)
3. [HTTPS](#https)
4. [Accounts when staff come and go](#accounts-when-staff-come-and-go)
5. [Two-step sign-in](#two-step-sign-in)
6. [When something goes wrong](#when-something-goes-wrong)
7. [Questions for your host or partner](#questions-for-your-host-or-partner)

## Backups

A backup is only useful if it can be restored. A backup of an Exponential site includes the **database** and the
**files** (uploaded images and documents in the storage directory)
([Backups and restores](../../creators/website-creators/backups-and-restores.md)).

- Ask how often backups are made and how long they are kept.
- Ask once a year for a **restore test**: the site restored on a test address from a backup
  ([Restore drills](../../businesses/reliability/restore-drills.md)).
- Keep the right to receive a copy of a backup, for example when changing partner.

## Updates

Exponential publishes fixes as new versions; a published version never changes
([The support commitment](../../who-is-the-exponential-foundation.md#the-support-commitment)). Extensions have their own
versions. Someone must apply them.

- Agree who updates, and how often ([Updates and upgrades](../../creators/website-creators/updates-and-upgrades.md)).
- After an update, look at the front page, a form and, if you sell, the basket.
- Security updates should not wait for the next yearly review
  ([Security updates and disclosure](../../businesses/security/security-updates-and-disclosure.md)).

## HTTPS

Every page should open with `https://` and a lock in the browser, with the certificate renewed automatically. If the
browser ever warns that the site is not secure, call your host the same day
([Going live: hosts and HTTPS](../../creators/website-creators/going-live-hosts-https.md)).

## Accounts when staff come and go

| When | Do |
|---|---|
| Someone starts | give them their own account with the role they need, never a shared password ([Choosing roles for new staff](../../customers/day-to-day/choosing-roles-for-new-staff.md)) |
| They only change hours and specials | an editor role, not administrator ([Roles for your team](../../customers/day-to-day/roles-for-your-team.md)) |
| They handle shop orders | shop administration rights only |
| Someone leaves | disable their account the same day, and change passwords of outside services they knew ([Offboarding staff](../../customers/day-to-day/offboarding-staff.md)) |
| Always | at least two administrator accounts, so the business is never locked out |

Write the list of accounts (Exponential, host, domain, payment provider, map and CAPTCHA keys, analytics, platforms)
in one safe place.

## Two-step sign-in

Exponential supports two-step sign-in through `sevenx_authentication_2fa`, which is in the standard `composer.json`
([sevenx_authentication_2fa](../../../features/6.0/extensions/sevenx_authentication_2fa.md)). Ask your administrator
to require it at least for administrators and anyone handling orders
([Accounts, passwords and two-factor](../../customers/day-to-day/accounts-passwords-and-two-factor.md)).

## When something goes wrong

| Symptom | First step |
|---|---|
| The site does not open | check on another device and network, then call the host |
| A page shows an error | note the address, the time and the error reference shown, send to the partner ([When something goes wrong](../../when-something-goes-wrong.md)) |
| Forms stopped mailing | test once, then tell the host (mail) or partner |
| Strange content appeared, or you suspect a break-in | call the partner immediately; do not delete evidence ([Incident response for small teams](../../businesses/reliability/incident-response-for-small-teams.md)) |
| You are locked out | use the second administrator, or ask the partner |

Keep the contacts ready: [Escalation contacts template](../../customers/partners-and-hosts/escalation-contacts-template.md).

## Questions for your host or partner

1. When was the last backup, and when was a restore last tested?
2. Which Exponential version runs, and when was it last updated?
3. Who receives security notices?
4. Is two-step sign-in required for administrators?
5. Whom do I call on a weekend, and how fast will they answer?

## Related pages

- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [User accounts and passwords](../../businesses/security/user-accounts-and-passwords.md)
- [Weekly and monthly routine](weekly-and-monthly-routine.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
