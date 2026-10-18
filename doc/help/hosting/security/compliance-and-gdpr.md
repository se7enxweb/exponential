---
title: Compliance and GDPR
audience: hosting, admin
category: hosting
order: 130
summary: What Exponential provides that helps a host and its customers meet data protection and e-mail law (GDPR, ePrivacy, CAN-SPAM, CASL, CCPA): privacy defaults in logs and the audit trail, the mail consent and suppression system, access and erasure commands, retention settings, and which decisions stay with the host and the customer. Not legal advice.
keywords: compliance, GDPR, data protection, ePrivacy, CAN-SPAM, CASL, CCPA, consent, mail preferences, suppression list, erasure, right of access, retention, audit trail, pseudonymization, pseudonymisation, masked IP, processor, controller, privacy notice, not legal advice
type: help
---
# Compliance and GDPR

This page is for the compliance staff, product managers and system administrators of a host, and for support staff who
receive a customer's data protection request. It describes **what the software provides**: privacy defaults, consent
records, access and erasure commands, and retention settings found in the repository. It does not say whether a site
complies with any law.

> **Warning**
> This page is not legal advice. Whether a site complies depends on what it processes, for whom, and on settings and
> documents only its owner can provide. The host and each customer must take their own legal counsel. The project's
> own law checklist says the same: "Ask a lawyer about your own case"
> ([E-mail preferences: the law checklist](../../../specifications/6.0/mail-preferences-compliance.md)).

## Contents

1. [Roles: who decides what](#roles-who-decides-what)
2. [Privacy defaults in the logs](#privacy-defaults-in-the-logs)
3. [The audit trail and personal data](#the-audit-trail-and-personal-data)
4. [Mail consent, unsubscribe and suppression](#mail-consent-unsubscribe-and-suppression)
5. [Requests from data subjects](#requests-from-data-subjects)
6. [Security of processing](#security-of-processing)
7. [What stays with the host and the customer](#what-stays-with-the-host-and-the-customer)
8. [Checklist](#checklist)

## Roles: who decides what

In a typical hosting arrangement the customer decides why and how personal data on their site is processed, and the
host runs the infrastructure on the customer's behalf. Which legal role each party has, and what contract between them
is required, is for the parties and their counsel to settle. For the host the practical consequence is: give the
customer the facts and the tools below, keep your own operational logs and backups under control, and support requests
when the customer asks.

## Privacy defaults in the logs

| Log | Default protection | Setting |
|---|---|---|
| `404.log`, `security.log` | client address masked (`203.0.x.x`, `2001:db8:x:x`); user agent as a short hash plus 60 characters; query values `***` in 404.log, secrets, e-mail and IP addresses masked in security.log | `site.ini [RequestLogSettings] MaskIP=enabled` |
| `csp.log` | address masked as in the request logs; URIs masked; samples cut to 40 characters | `[ContentSecurityPolicy]` |
| the not-found counter | keyed by a salted hash of the address; the address is never stored | |
| log viewer | masks e-mail addresses, IP addresses and secrets in what it shows | `logviewer.ini [RedactionSettings]` |
| `auth.log` | every step of two-step sign-in, never a code or a secret | |
| Velocity access log | Apache's combined format, files `0640` in a `0750` directory: "an access log names visitors" | `velocity.ini [LogSettings]` |

`MaskIP=disabled` writes full addresses; [Request logs](../../../features/6.0/request-logs.md#privacy) says to use it
"only for a site that has a reason and a legal basis for it". The web server's own access logs (Apache, nginx, the
panel's) are outside Exponential and follow the host's settings.

## The audit trail and personal data

From [4.12 Privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks) of the audit guide, which also
says it is not legal advice:

- **Minimization by default**: addresses truncated to /24 (IPv6 /48), sessions hashed, user agents reduced to browser
  and OS family, e-mail addresses and attempted logins of unknown users hashed; never passwords, tokens, reset keys,
  secrets or POST bodies; reads off.
- **Stricter**: per field `full | truncate | hash | off` in `[AuditPrivacySettings] Field[]`, `BeforeAfter=keys`,
  shorter retention; the [strict-privacy configuration](../../../bc/6.0/audit.md#64-strict-privacy).
- **Pseudonymization**: the index replaces personal fields by their hashed form after `PseudonymiseAfterDays` (90);
  the files are not rewritten, because that would break the chain.
- **Access request**: `exp:audit export --subject-user=<id>` exports every record by or about a user; review it before
  handing it out, because it holds other people's actions on that user too.
- **Erasure**: the guide describes keeping audit records for their retention period and answering with that period
  and the pseudonymization applied; deleting one would break the chain.

## Mail consent, unsubscribe and suppression

Since 6.0.15 every mail passes the mail gate ([13.13](../../../install/13-security-hardening.md#1313-mail-consent-and-suppression)):

- **Essential** categories (`security`, `orders`, `legal`, `admin`) always go out.
- **Optional** categories go only to recipients who switched them on, off until the person turns them on, with a
  footer, a one-click unsubscribe link and `List-Unsubscribe` headers (RFC 8058).
- **Consent log**: every change of a person's preferences with time, source, the text shown, IP address and acting
  user; double opt-in for newsletters and marketing.
- **Suppression list**: hard bounces, complaints, "stop all" and legal requests; only a salted hash of each address is
  stored.

The law checklist maps each requirement of CAN-SPAM, GDPR and ePrivacy, CCPA/CPRA and CASL to the code and the test
that proves it, and lists what is **left to the site** ([Left to the site](../../../specifications/6.0/mail-preferences-compliance.md#left-to-the-site)):

1. the postal address in the footer (`mailpreferences.ini [FooterSettings] OrganisationAddress`, empty after an
   installation) and the sender's name (`OrganisationName`);
2. a category for every commercial mail sent by custom code;
3. keeping `[GateSettings] SplitRecipients=enabled`;
4. DKIM signing of `List-Unsubscribe` and `List-Unsubscribe-Post` by the mail server;
5. a link to the privacy notice on the registration form, if the design wants one.

Point 4 is often the host's job, because the host runs the mail server; see [Mail delivery](../operations/mail-delivery.md)
and [Mail consent and bounces](../operations/mail-consent-and-bounces.md). Check the state of a site with:

```bash
php bin/php/console exp:mail:status
```

## Requests from data subjects

| Request | Tool |
|---|---|
| Access to mail data | the person's own download, or `./console exp:mail:preferences export --email=<address> --format=json --output=var/tmp/request.json` (mode `0600`) |
| Stop all mail | the unsubscribe link, or `exp:mail:suppression add --email=<address> --reason=legal` |
| Erase mail data | `./console exp:mail:preferences erase --email=<address> --yes`: preferences and pending confirmations removed, consent log anonymised, the withdrawal kept as proof |
| Account removal | removing a user account also erases its mail preferences (`expMailPreferences::eraseForRemovedAccount()`, closed on 2026-10-05 per the law checklist) |
| Audit records about a user | `exp:audit export --subject-user=<id>` |

Delete exported files when the request they answer is done; they hold addresses. Give `mailpreferences/export` and
`audit/read` to few people.

## Security of processing

What the software contributes, each described on its own page: bcrypt password hashes and password rules, two-step
sign-in, encrypted unsubscribe tokens, session protection, the audit trail with hash chains, security headers and a
Content Security Policy ([Hardening for hosts](hardening-for-hosts.md)). What the host contributes: isolation between
customers, encrypted backups, patching, access control for staff, and logs forwarded to systems with their own access
rules ([Multi-tenant isolation](multi-tenant-isolation.md)).

## What stays with the host and the customer

- Contracts between host and customer, and their roles under the law.
- The privacy notice, the record of processing activities, and the retention values stated there; the software's
  retention settings must then be set to match ([Log and data retention](log-and-data-retention.md)).
- Backups: the retention of personal data in backups, and how erasure is handled for data that also sits in a backup.
- Web server, mail server and panel logs outside Exponential.
- Third-party services a site embeds (maps, analytics, fonts), which the Content Security Policy makes visible but
  does not judge.
- Newsletter statistics: the cjw_newsletter extension counts opens and clicks only when the site switches it on
  (`[TrackingSettings] Tracking=disabled` by default) and per person only with consent; the checklist names what the
  site must state in its privacy notice.

## Checklist

- [ ] The customer knows which personal data the site's logs, audit trail and mail system keep, from this page and
      the linked sources.
- [ ] `MaskIP` left enabled unless the customer has a documented reason.
- [ ] Audit privacy and retention settings agreed with the customer; a stricter profile applied where asked.
- [ ] Mail footer filled in; `exp:mail:status` clean; DKIM signing on the host's mail server.
- [ ] A documented way to answer access and erasure requests, including exported files being deleted afterwards.
- [ ] Backup retention for personal data decided and written down.
- [ ] Legal questions referred to the customer's and the host's own counsel.

## Related pages

- [Security for hosts: overview](README.md)
- [Log and data retention](log-and-data-retention.md)
- [E-mail preferences: the law checklist](../../../specifications/6.0/mail-preferences-compliance.md)
- [E-mail preferences: the administrator's guide](../../../guides/mail-preferences-administrator.md)
- [Audit: privacy and GDPR tasks](../../../bc/6.0/audit.md#412-privacy-and-gdpr-tasks)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
