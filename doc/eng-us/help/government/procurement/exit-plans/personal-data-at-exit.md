---
title: Personal data at exit
audience: government, project-manager, admin
category: public-sector
order: 100
summary: How to handle personal data when an Exponential site moves to a new supplier or system - finding where personal data lives, exporting only what the receiver needs, passwords, consent and audit records, secure transfer, and confirmed deletion by the old supplier.
keywords: personal data, data protection, GDPR, exit, data transfer, deletion, user accounts, passwords, consent records, audit trail, retention, data processing agreement
type: help
---
# Personal data at exit

This page is for project managers and data protection officers who plan the personal data part of an exit. An exit
moves data between parties and leaves copies behind; both are moments when personal data can be lost or exposed.
The facts on where Exponential stores personal data and what it exports come from
[Privacy standards](../../../../standards/privacy.md) and
[Leaving Exponential, section 5](../../../../install/migration/leaving-exponential.md#5-users-and-personal-data).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents
1. [Where personal data lives in a site](#where-personal-data-lives-in-a-site)
2. [Export only what the receiver needs](#export-only-what-the-receiver-needs)
3. [Passwords](#passwords)
4. [Consent and audit records](#consent-and-audit-records)
5. [Transfer and deletion](#transfer-and-deletion)
6. [Checklist](#checklist)

## Where personal data lives in a site

| Data | Where | Export |
|---|---|---|
| User accounts (name, e-mail, login) | user content objects and the `ezuser` table | site archive (user class), database dump |
| Form submissions | information collections | `ext:bccie:export` ([Collected information](../../../../guides/collected-information.md)) |
| E-mail preferences and consent log | `expmail_consent_log` and related tables | `exp:mailconsent`, `exp:mail:preferences export` |
| Newsletter subscribers (if cjw_newsletter is used) | the newsletter extension's tables | database dump |
| Audit trail | `var/<site>/log/audit/` | `exp:audit export` |
| Logs (error, request, security) | `var/log/` | files; addresses masked in some logs |
| Search phrases | search statistics | database dump; may contain personal data |
| Orders (if a shop is used) | shop tables | database dump |
| Sessions | session storage | not needed; never transfer |

The privacy page notes that there is no single "export all my data" function: these are separate tools.

## Export only what the receiver needs

- Agree in writing which personal data the receiving party needs for its purpose, and on which legal basis.
- Export that subset; keep the rest in your own archive with access controls, or delete it under your retention
  rules, as the leaving page advises.
- Remove inactive accounts before the move; **Users > Check user accounts** (`/user/hygiene`) finds accounts unused
  for a number of days ([Check user accounts](../../../../features/6.0/user-hygiene.md)).
- A test or staging copy at the new supplier should hold invented or minimized data.

## Passwords

The site archive never contains password hashes. Users set new passwords on the target (you send reset mails),
unless the target can verify standard PHP `password_hash()` values; then a database export of the `ezuser` hashes of
type 7 can be used. The leaving page asks you to decide this with your data protection advisor. Consider also
requiring two-step sign-in again on the target.

## Consent and audit records

- **Consent records** prove that people agreed to optional mail. Export them (`exp:mailconsent`) and keep them as
  long as you may need to prove consent; the receiving system must honor withdrawals.
- **Audit records** are kept for their retention period and pseudonymized after a set time
  ([Audit trail](../../../../features/6.0/audit-trail.md)). Export them with their signed manifest so they stay
  verifiable, and keep the keys in `settings/override/audit.ini.append.php` with them
  ([Keys and secrets handover](keys-and-secrets-handover.md)).

## Transfer and deletion

- Transfer exports over an encrypted channel, never as e-mail attachments, as the host offboarding checklist says
  ([Customer offboarding](../../../hosting/checklists/customer-offboarding.md)).
- If the receiving party processes personal data for you, have a data processing agreement in place before the
  transfer where the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) applies.
- The old supplier deletes production data, staging copies, backups and logs at the agreed dates, and confirms in
  writing. Backups may be kept to the end of an agreed retention period, not longer.

## Checklist

- [ ] Inventory of personal data in the site, from the table above.
- [ ] Subset for the receiver agreed, with legal basis; rest archived or deleted.
- [ ] Inactive accounts cleaned up before export.
- [ ] Password approach decided with the data protection advisor; users informed.
- [ ] Consent and audit records exported with manifests; keys kept.
- [ ] Encrypted transfer; processing agreement in place.
- [ ] Deletion by the old supplier confirmed in writing, with dates.

## Related pages
- [Exit plans: overview](README.md)
- [Privacy standards](../../../../standards/privacy.md)
- [Partners: protecting customer data](../../../partners/protecting-customer-data.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
